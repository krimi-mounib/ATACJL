<?php
declare(strict_types=1);
require_once __DIR__.'/_common.php';
if(post()){
  verifyCsrf();
  $id=(int)($_POST['id']??0); $status=(string)($_POST['status']??'');
  if($id>0 && in_array($status,['approved','rejected'],true)){
    try { db()->prepare('UPDATE volunteer_applications SET status=? WHERE id=?')->execute([$status,$id]); adminAudit('volunteer_'.$status,'volunteer_application',$id); flash('success','Volunteer application updated.'); }
    catch(Throwable $error){ error_log('ATACJL volunteer update failed: '.$error->getMessage()); flash('error','Volunteer applications are not available yet. Apply database/migrate_v7.sql and try again.'); }
  }
  redirect('volunteers.php');
}
$rows=adminSafeRows('SELECT * FROM volunteer_applications ORDER BY created_at DESC',[],'volunteer applications');
$pageTitle='Volunteers'; require dirname(__DIR__).'/includes/header.php';
?>
<section class="section"><div class="container"><p class="eyebrow">Applications</p><h1 class="section-title">Volunteer applications.</h1>
<?php if(!$rows):?><div class="empty-state compact" style="margin-top:24px"><h3>No volunteer applications found</h3><p>If you expected existing applications, check <code>storage/php-error.log</code> and apply the v7 compatibility migration.</p></div>
<?php else:?><div class="table-wrap" style="margin-top:30px"><table><thead><tr><th>Applicant</th><th>Interest</th><th>Availability</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?=e($r['full_name']??'')?><br><?=e($r['email']??'')?></td><td><?=e($r['area_of_interest']??'—')?></td><td><?=e($r['availability']??'—')?></td><td><span class="status-chip status-<?=e($r['status']??'pending')?>"><?=e(ucfirst((string)($r['status']??'pending')))?></span></td><td><form method="post" class="admin-actions"><?=csrfField()?><input type="hidden" name="id" value="<?=(int)$r['id']?>"><button class="btn primary" name="status" value="approved">Approve</button><button class="btn coral" name="status" value="rejected">Reject</button></form></td></tr><?php endforeach;?></tbody></table></div><?php endif;?></div></section>
<?php require dirname(__DIR__).'/includes/footer.php'; ?>
