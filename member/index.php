<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/includes/functions.php';
$u=requireRole('user','member','admin');
$id=(int)$u['id'];
$counts=['registrations'=>0,'pending'=>0,'verified'=>0];
$s=db()->prepare('SELECT COUNT(*) FROM event_rsvps WHERE user_id=?');$s->execute([$id]);$counts['registrations']=(int)$s->fetchColumn();
if($u['role']==='member'){
  $s=db()->prepare("SELECT COUNT(*) FROM participation_claims WHERE member_id=? AND status='pending'");$s->execute([$id]);$counts['pending']=(int)$s->fetchColumn();
  $s=db()->prepare("SELECT COUNT(*) FROM participation_claims WHERE member_id=? AND status='verified'");$s->execute([$id]);$counts['verified']=(int)$s->fetchColumn();
}
$application=null;
if($u['role']==='user'){$s=db()->prepare('SELECT status,created_at FROM membership_applications WHERE user_id=? ORDER BY created_at DESC LIMIT 1');$s->execute([$id]);$application=$s->fetch()?:null;}
$s=db()->prepare("SELECT e.id,e.title_en,e.event_date,e.event_time,e.location,e.featured_image FROM events e JOIN event_rsvps r ON r.event_id=e.id WHERE r.user_id=? AND e.event_date>=CURDATE() ORDER BY e.event_date ASC LIMIT 3");$s->execute([$id]);$registeredEvents=$s->fetchAll();
$pageTitle='Member dashboard';require dirname(__DIR__).'/includes/header.php';
?>
<section class="section member-dashboard-section">
  <div class="container">
    <div class="member-welcome-panel"><div><p class="eyebrow">YOUR ATACJL SPACE</p><h1 class="section-title">Welcome back, <?=e(strtok(trim((string)$u['full_name']),' ' )?:'there')?>.</h1><p class="copy">Your place to manage your profile, stay connected to activities and follow your participation.</p></div><a class="btn primary" href="profile.php">Manage my profile <span aria-hidden="true">→</span></a></div>
    <?php if(!(int)$u['email_verified']&&$u['role']!=='admin'):?><div class="portal-notice notice-warning"><strong>Account verification pending</strong><span>Your account is read-only until an administrator verifies your email. You can update your profile while you wait.</span></div><?php endif;?>
    <?php if($u['role']==='user'):?><div class="portal-notice"><strong>Membership</strong><span><?php if($application):?>Your latest application status is <span class="status-chip status-<?=e($application['status'])?>"><?=e(ucfirst($application['status']))?></span><?php else:?>Interested in becoming an approved member of ATACJL?<?php endif;?></span><?php if(!$application||$application['status']==='rejected'):?><a class="text-link" href="<?=APP_URL?>/membership.php">View membership options →</a><?php endif;?></div><?php endif;?>
    <div class="dashboard-cards member-stats-grid">
      <div class="dash-card"><div class="dash-icon">▦</div><small>Event registrations</small><strong><?=$counts['registrations']?></strong><a class="text-link" href="events.php">Explore events →</a></div>
      <div class="dash-card"><div class="dash-icon">✓</div><small>Verified activities</small><strong><?=$counts['verified']?></strong><a class="text-link" href="profile.php#activity-history">View history →</a></div>
      <div class="dash-card"><div class="dash-icon">◷</div><small>Pending claims</small><strong><?=$counts['pending']?></strong><a class="text-link" href="profile.php#pending-claims">Track claims →</a></div>
      <div class="dash-card"><div class="dash-icon">♙</div><small>Account type</small><strong class="role-metric"><?=e(ucfirst((string)$u['role']))?></strong><a class="text-link" href="profile.php">Profile settings →</a></div>
    </div>
    <div class="portal-section-head"><div><p class="eyebrow">YOUR SCHEDULE</p><h2>Upcoming registered events</h2></div><a class="btn ghost" href="events.php">Browse events</a></div>
    <?php if($registeredEvents):?><div class="grid-3"><?php foreach($registeredEvents as $ev):?><article class="card"><div class="card-media"><?php if($ev['featured_image']):?><img loading="lazy" src="<?=e($ev['featured_image'])?>" alt="<?=e(localTitle($ev,'en'))?>"><?php else:?><div class="media-placeholder media-event" aria-hidden="true"><span>ATACJL EVENT</span></div><?php endif;?></div><div class="card-body"><span class="tag"><?=e(date('D, M j',strtotime($ev['event_date'])))?> · <?=e(substr((string)$ev['event_time'],0,5))?></span><h3><?=e(localTitle($ev,lang()))?></h3><p><?=e($ev['location'])?></p><a class="text-link" href="<?=APP_URL?>/event-detail.php?id=<?=$ev['id']?>">View registration details →</a></div></article><?php endforeach;?></div><?php else:?><div class="empty-state compact"><span class="empty-icon">▦</span><h3>No upcoming registrations</h3><p>Explore upcoming activities and reserve a place that interests you.</p><a class="btn primary" href="events.php">Explore events</a></div><?php endif;?>
  </div>
</section>
<?php require dirname(__DIR__).'/includes/footer.php'; ?>
