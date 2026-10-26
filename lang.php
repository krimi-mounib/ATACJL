<?php
declare(strict_types=1);
require_once __DIR__.'/includes/security.php';
$l=(string)($_GET['lang']??'en');
if(in_array($l,['en','fr','ar','de','it','es'],true)){
    startSecureSession();
    $_SESSION['lang']=$l;
    $_SESSION['lang_explicit']=true;
}
$return=(string)($_GET['return']??($_SERVER['HTTP_REFERER']??'/index.php'));
// Only redirect to a local relative path to prevent open redirects.
if(!str_starts_with($return,'/')||str_starts_with($return,'//')||str_contains($return,"\\")||preg_match('/[\r\n]/',$return))$return='/index.php';
header('Location: '.$return, true, 303);
exit;
