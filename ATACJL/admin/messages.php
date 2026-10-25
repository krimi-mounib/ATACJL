<?php
declare(strict_types=1);
require_once __DIR__.'/_common.php';
if(post()){
  verifyCsrf();$id=(int)($_POST['id']??0);$status=$_POST['status']??'read';
  if($id>0&&in_array($status,['read','replied'],true)){
    try { db()->prepare('UPDATE contact_submissions SET status=? WHERE id=?')->execute([$status,$id]); adminAudit('message_'.$status,'contact_submission',$id); flash('success',$status==='replied'?'Message marked as replied.':'Message marked as read.'); }
    catch(Throwable $error) { error_log('ATACJL contact inbox update failed: '.$error->getMessage()); flash('error','The contact inbox schema is incomplete. Apply database/migrate_v7.sql and try again.'); }
  }
  redirect('messages.php');
}
$rows=adminSafeRows("SELECT * FROM contact_submissions ORDER BY FIELD(status,'new','read','replied'),created_at DESC LIMIT 300",[],'contact inbox');
$pageTitle='Contact inbox';require dirname(__DIR__).'/includes/header.php';
?>
<section class="section"><div class="container"><div class="section-head"><div><p class="eyebrow">INBOX</p><h1 class="section-title">Contact messages</h1><p class="copy">Review incoming requests and open your email app to respond. Replies sent externally are not automatically tracked by ATACJL.</p></div><span class="pill"><?=count(array_filter($rows,static fn($r)=>$r['status']==='new'))?> new</span></div>
<?php if($rows):?><div class="table-wrap"><table><thead><tr><th>From</th><th>Subject</th><th>Message</th><th>Received</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><strong><?=e($r['name'])?></strong><br><a class="text-link" href="mailto:<?=e($r['email'])?>"><?=e($r['email'])?></a></td><td><?=e($r['subject'])?></td><td class="message-preview"><?=nl2br(e($r['message']))?></td><td><?=e(date('M j, Y · H:i',strtotime($r['created_at'])))?></td><td><span class="status-chip status-<?=e($r['status']==='new'?'pending':($r['status']==='replied'?'approved':'draft'))?>"><?=e(ucfirst($r['status']))?></span></td><td><div class="admin-actions"><a class="btn primary" href="mailto:<?=e($r['email'])?>?subject=<?=rawurlencode('Re: '.$r['subject'])?>">Reply by email</a><?php if($r['status']!=='read'):?><form method="post"><?=csrfField()?><input type="hidden" name="id" value="<?=$r['id']?>"><input type="hidden" name="status" value="read"><button class="btn ghost">Mark read</button></form><?php endif;?><?php if($r['status']!=='replied'):?><form method="post"><?=csrfField()?><input type="hidden" name="id" value="<?=$r['id']?>"><input type="hidden" name="status" value="replied"><button class="btn ghost">Mark replied</button></form><?php endif;?></div></td></tr><?php endforeach;?></tbody></table></div><?php else:?><div class="empty-state"><span class="empty-icon">✉</span><h2>Your inbox is empty or unavailable</h2><p>New submissions will appear here. If you expected existing messages, check <code>storage/php-error.log</code> and confirm that the database migrations have been applied.</p></div><?php endif;?></div></section>
<?php require dirname(__DIR__).'/includes/footer.php'; ?>
