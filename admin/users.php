<?php
declare(strict_types=1);
require_once __DIR__.'/_common.php';
$error='';
if(post()){
 verifyCsrf();$action=$_POST['action']??'';$id=(int)($_POST['id']??0);
 try{
  if($action==='save'){
   $name=clean('full_name',150);$email=emailField('email');$username=mb_strtolower(trim((string)($_POST['username']??'')));$role=$_POST['role']??'user';$password=(string)($_POST['password']??'');
   if(mb_strlen($name)<2||!$email||!preg_match('/^[a-z0-9._-]{3,80}$/',$username)||!in_array($role,['user','member','admin'],true))throw new RuntimeException('Please provide valid account details.');
   if($id){db()->prepare('UPDATE users SET username=?,full_name=?,email=?,role=? WHERE id=?')->execute([$username,$name,$email,$role,$id]);if($password!==''){if(!validPassword($password))throw new RuntimeException('Password must be at least 8 characters and contain a letter and a number.');db()->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($password,PASSWORD_DEFAULT),$id]);}}
   else{if(!validPassword($password))throw new RuntimeException('A new account needs a password of at least 8 characters and a number.');db()->prepare('INSERT INTO users(username,full_name,email,password_hash,role,email_verified) VALUES(?,?,?,?,?,1)')->execute([$username,$name,$email,password_hash($password,PASSWORD_DEFAULT),$role]);$id=(int)db()->lastInsertId();}
   adminAudit('save','user',$id,['role'=>$role]);flash('success','User saved.');redirect('users.php');
  }
  if($action==='status'&&$id&&$id!==$admin['id']){
   $field=$_POST['field']??'';$value=(int)($_POST['value']??0);
   if(in_array($field,['email_verified','is_active','is_banned'],true)){db()->prepare("UPDATE users SET $field=? WHERE id=?")->execute([$value,$id]);adminAudit('status_update','user',$id,[$field=>$value]);flash('success','User status updated.');}
   redirect('users.php');
  }
  if($action==='delete'&&$id&&$id!==$admin['id']){db()->prepare('DELETE FROM users WHERE id=?')->execute([$id]);adminAudit('delete','user',$id);flash('success','User removed.');redirect('users.php');}
 }catch(Throwable $e){error_log($e->getMessage());$error=$e->getMessage();}
}
$edit=null;if(isset($_GET['id'])){$s=db()->prepare('SELECT * FROM users WHERE id=?');$s->execute([(int)$_GET['id']]);$edit=$s->fetch();}
$users=db()->query('SELECT id,username,full_name,email,role,email_verified,is_active,is_banned,created_at,last_login FROM users ORDER BY created_at DESC')->fetchAll();
$pageTitle='Users & members';require dirname(__DIR__).'/includes/header.php';
?>
<section class="section"><div class="container"><div class="section-head"><div><p class="eyebrow">Administration</p><h1 class="section-title">Users, members & admins.</h1></div></div>
<?php if($error):?><div class="flash error" style="position:static;margin:20px 0"><?=e($error)?></div><?php endif;?>
<div class="form-shell" style="max-width:none"><h2><?=$edit?'Edit account':'Create account'?></h2><form method="post"><?=csrfField()?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?=$edit['id']??0?>"><div class="grid-3">
<div class="field"><label>Full name</label><input name="full_name" value="<?=e($edit['full_name']??'')?>" required></div>
<div class="field"><label>Username</label><input name="username" value="<?=e($edit['username']??'')?>" required></div>
<div class="field"><label>Email</label><input type="email" name="email" value="<?=e($edit['email']??'')?>" required></div>
<div class="field"><label>Role</label><select name="role"><option value="user" <?=($edit['role']??'user')==='user'?'selected':''?>>User</option><option value="member" <?=($edit['role']??'')==='member'?'selected':''?>>Member</option><option value="admin" <?=($edit['role']??'')==='admin'?'selected':''?>>Admin</option></select></div>
<div class="field"><label>Password <?=$edit?'(leave blank to keep)':''?></label><input type="password" name="password" <?=!$edit?'required':''?>></div></div><button class="btn primary">Save account</button><?php if($edit):?><a class="btn ghost" href="users.php">Cancel</a><?php endif;?></form></div>
<div class="table-wrap"><table><thead><tr><th>User</th><th>Role</th><th>Verified</th><th>Active</th><th>Banned</th><th>Actions</th></tr></thead><tbody>
<?php foreach($users as $r):?><tr><td><b><?=e($r['full_name'])?></b><br><small><?=e($r['username'])?> · <?=e($r['email'])?></small></td><td><span class="pill"><?=e(ucfirst($r['role']))?></span></td><td><span class="status-chip <?=((int)$r['email_verified'])?'status-approved':'status-pending'?>"><?=((int)$r['email_verified'])?'Verified':'Pending'?></span></td><td><span class="status-chip <?=((int)$r['is_active'])?'status-active':'status-draft'?>"><?=((int)$r['is_active'])?'Active':'Inactive'?></span></td><td><span class="status-chip <?=((int)$r['is_banned'])?'status-rejected':'status-active'?>"><?=((int)$r['is_banned'])?'Banned':'No'?></span></td><td>
<?php if($r['id']!==$admin['id']):?><div class="admin-actions"><a class="btn ghost" href="users.php?id=<?=$r['id']?>">Edit</a>
<form method="post"><?=csrfField()?><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?=$r['id']?>"><input type="hidden" name="field" value="email_verified"><input type="hidden" name="value" value="<?=((int)$r['email_verified'])?0:1?>"><button class="btn ghost"><?=((int)$r['email_verified'])?'Unverify':'Verify email'?></button></form>
<form method="post"><?=csrfField()?><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?=$r['id']?>"><input type="hidden" name="field" value="is_active"><input type="hidden" name="value" value="<?=((int)$r['is_active'])?0:1?>"><button class="btn ghost"><?=((int)$r['is_active'])?'Deactivate':'Activate'?></button></form>
<form method="post"><?=csrfField()?><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?=$r['id']?>"><input type="hidden" name="field" value="is_banned"><input type="hidden" name="value" value="<?=((int)$r['is_banned'])?0:1?>"><button class="btn <?=((int)$r['is_banned'])?'ghost':'coral'?>"><?=((int)$r['is_banned'])?'Unban':'Ban'?></button></form>
<form method="post"><?=csrfField()?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn coral" data-confirm="Delete this account permanently?">Delete</button></form></div><?php else:?><span>Current admin</span><?php endif;?></td></tr><?php endforeach;?></tbody></table></div></div></section>
<?php require dirname(__DIR__).'/includes/footer.php';
