<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/includes/functions.php';
$u = requireRole('member', 'admin');
$pageTitle = 'Member Directory';
$members = [];
$directoryError = false;

try {
    // Older installs may not have every optional profile field yet. Detect available
    // columns so a missing bio/photo field cannot take down the entire directory.
    $columns = db()->query('SHOW COLUMNS FROM `users`')->fetchAll(PDO::FETCH_COLUMN, 0);
    $available = array_fill_keys(array_map('strval', $columns), true);
    $required = ['id', 'full_name', 'role'];
    foreach ($required as $column) {
        if (!isset($available[$column])) {
            throw new RuntimeException('The users table is missing a required column: '.$column);
        }
    }

    $optional = ['occupation', 'bio', 'profile_photo'];
    $select = ['`full_name`'];
    foreach ($optional as $column) {
        $select[] = isset($available[$column]) ? '`'.$column.'`' : 'NULL AS `'.$column.'`';
    }
    $where = ["`role` = 'member'"];
    if (isset($available['is_active'])) $where[] = '`is_active` = 1';
    if (isset($available['is_banned'])) $where[] = '`is_banned` = 0';
    $sql = 'SELECT '.implode(', ', $select).' FROM `users` WHERE '.implode(' AND ', $where).' ORDER BY `full_name` ASC';
    $members = db()->query($sql)->fetchAll();
} catch (Throwable $error) {
    error_log('ATACJL member directory failed: '.$error->getMessage());
    $directoryError = true;
}

require dirname(__DIR__).'/includes/header.php';
?>
<section class="section">
  <div class="container">
    <p class="eyebrow">Community</p>
    <h1 class="section-title">Member directory.</h1>
    <?php if ($directoryError): ?>
      <div class="empty-state compact" role="status">
        <h3>The member directory is temporarily unavailable.</h3>
        <p>The application recorded the technical details for the administrator. Please check the database schema and <code>storage/php-error.log</code>.</p>
      </div>
    <?php elseif ($members): ?>
      <div class="grid-3" style="margin-top:30px">
        <?php foreach ($members as $member): ?>
          <article class="feature">
            <?php if (!empty($member['profile_photo'])): ?>
              <img class="user-avatar" src="<?=e((string)$member['profile_photo'])?>" alt="" loading="lazy">
            <?php endif; ?>
            <h3><?=e((string)($member['full_name'] ?? 'Member'))?></h3>
            <?php if (!empty($member['occupation'])): ?><b><?=e((string)$member['occupation'])?></b><?php endif; ?>
            <?php if (!empty($member['bio'])): ?><p><?=e((string)$member['bio'])?></p><?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-state compact" style="margin-top:30px">
        <h3>No approved members to display yet.</h3>
        <p>Members will appear here when their accounts are active.</p>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php require dirname(__DIR__).'/includes/footer.php'; ?>
