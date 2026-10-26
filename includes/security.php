<?php
declare(strict_types=1);require_once __DIR__.'/config.php';
function startSecureSession():void{if(session_status()===PHP_SESSION_ACTIVE)return;session_name(envv('SESSION_NAME','atacjl_session'));session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off','httponly'=>true,'samesite'=>'Lax']);session_start();}
function csrf():string{startSecureSession();if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function csrfField():string{return '<input type="hidden" name="csrf" value="'.htmlspecialchars(csrf(),ENT_QUOTES,'UTF-8').'">';}
function verifyCsrf():void{startSecureSession();if(!hash_equals((string)($_SESSION['csrf']??''),(string)($_POST['csrf']??''))){http_response_code(419);exit('Invalid request token. Please reload the page and try again.');}}
function e(?string $v):string{return htmlspecialchars((string)($v??''),ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
/**
 * Resolve an internal redirect safely.
 *
 * Bare filenames are relative to the current workspace directory when that
 * target exists (for example admin/users.php -> admin/users.php). Paths that
 * include a directory (admin/index.php, member/profile.php) remain relative
 * to the application root. This preserves the convention used throughout the
 * public pages while fixing POST redirects from nested workspaces.
 */
function redirectLocation(string $path):string{
    $path=trim($path);
    $parts=parse_url($path);
    if($path===''||$parts===false||isset($parts['scheme'])||isset($parts['host'])||isset($parts['user'])||isset($parts['pass'])||preg_match('/[\r\n\\\\]/',$path)){
        $path='index.php';
        $parts=parse_url($path)?:['path'=>'index.php'];
    }

    $route=(string)($parts['path']??'');
    $decodedRoute=rawurldecode($route);
    if($route===''||preg_match('#(^|/)\.\.(?:/|$)#',$decodedRoute)){
        $route='index.php';
        $path='index.php';
        $parts=parse_url($path)?:['path'=>'index.php'];
    }

    $isAppRootRelative=str_starts_with($route,'/')||str_contains(trim($route,'/'),'/');
    if(!$isAppRootRelative){
        $scriptPath=(string)(parse_url((string)($_SERVER['SCRIPT_NAME']??''),PHP_URL_PATH)?:'');
        $appPath=(string)(parse_url(APP_URL,PHP_URL_PATH)?:'');
        $appPath=rtrim($appPath,'/');
        if($appPath!==''&&($scriptPath===$appPath||str_starts_with($scriptPath,$appPath.'/'))){
            $scriptPath=substr($scriptPath,strlen($appPath));
        }
        $currentDir=trim(dirname($scriptPath),'/');
        $candidate=($currentDir!==''&&$currentDir!=='.'?$currentDir.'/':'').ltrim($route,'/');
        $candidatePath=ROOT_PATH.'/'.$candidate;
        if(is_file($candidatePath)){
            $route=$candidate;
        }
    }

    $route=ltrim($route,'/');
    if($route==='')$route='index.php';
    $suffix='';
    $originalPath=(string)($parts['path']??'');
    if(str_starts_with($path,$originalPath))$suffix=substr($path,strlen($originalPath));
    // Keep the redirect on this application; never accept an external host.
    return rtrim(APP_URL,'/').'/'.$route.$suffix;
}
function redirect(string $path):never{
    header('Location: '.redirectLocation($path),true,303);
    exit;
}
function flash(string $type,string $message):void{startSecureSession();$_SESSION['flash'][]=[$type,$message];}
function flashes():array{startSecureSession();$x=$_SESSION['flash']??[];unset($_SESSION['flash']);return $x;}
function abortServer(string $public='Something went wrong. Please try again later.'):never{error_log($public);http_response_code(500);exit($public);}
