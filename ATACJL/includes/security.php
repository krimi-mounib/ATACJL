<?php
declare(strict_types=1);require_once __DIR__.'/config.php';
function startSecureSession():void{if(session_status()===PHP_SESSION_ACTIVE)return;session_name(envv('SESSION_NAME','atacjl_session'));session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off','httponly'=>true,'samesite'=>'Lax']);session_start();}
function csrf():string{startSecureSession();if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function csrfField():string{return '<input type="hidden" name="csrf" value="'.htmlspecialchars(csrf(),ENT_QUOTES,'UTF-8').'">';}
function verifyCsrf():void{startSecureSession();if(!hash_equals((string)($_SESSION['csrf']??''),(string)($_POST['csrf']??''))){http_response_code(419);exit('Invalid request token. Please reload the page and try again.');}}
function e(?string $v):string{return htmlspecialchars((string)($v??''),ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function redirect(string $path):never{header('Location: '.APP_URL.'/'.ltrim($path,'/'));exit;}
function flash(string $type,string $message):void{startSecureSession();$_SESSION['flash'][]=[$type,$message];}
function flashes():array{startSecureSession();$x=$_SESSION['flash']??[];unset($_SESSION['flash']);return $x;}
function abortServer(string $public='Something went wrong. Please try again later.'):never{error_log($public);http_response_code(500);exit($public);}
