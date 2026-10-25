<?php
declare(strict_types=1);require_once __DIR__.'/db.php';require_once __DIR__.'/security.php';startSecureSession();
function currentUser():?array{static $u='unset';if($u!=='unset')return $u;$id=$_SESSION['user_id']??null;if(!$id)return $u=null;$s=db()->prepare('SELECT * FROM users WHERE id=? AND is_active=1 AND is_banned=0');$s->execute([$id]);return $u=$s->fetch()?:null;}
function loginUser(array $u):void{session_regenerate_id(true);$_SESSION['user_id']=(int)$u['id'];$_SESSION['csrf']=bin2hex(random_bytes(32));}
function logoutUser():void{$_SESSION=[];if(ini_get('session.use_cookies')){$p=session_get_cookie_params();setcookie(session_name(),'',['expires'=>time()-42000,'path'=>$p['path'],'secure'=>$p['secure'],'httponly'=>$p['httponly'],'samesite'=>$p['samesite']??'Lax']);}session_destroy();}
function requireLogin():array{$u=currentUser();if(!$u){flash('error','Please log in to continue.');redirect('login.php');}return $u;}
function requireRole(string ...$roles):array{$u=requireLogin();if(!in_array($u['role'],$roles,true)){http_response_code(403);exit('Forbidden: you do not have permission to access this area.');}return $u;}
function requireVerifiedUser():array{$u=requireLogin();if(!(int)$u['email_verified']&&$u['role']!=='admin'){flash('error','Your account is waiting for admin email verification.');redirect('member/profile.php');}return $u;}
