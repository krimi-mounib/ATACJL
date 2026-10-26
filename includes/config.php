<?php
declare(strict_types=1);
function envv(string $key,string $default=''):string{static $e=null;if($e===null){$e=[];$file=__DIR__.'/../.env';if(is_file($file)){foreach(file($file,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line){$line=trim($line);if($line===''||str_starts_with($line,'#')||!str_contains($line,'='))continue;[$k,$v]=explode('=',$line,2);$e[trim($k)]=trim($v," \t\n\r\0\x0B\"");}}}return $e[$key]??$default;}
function appUrl():string{$env=envv('APP_URL','');if($env!=='')return rtrim($env,'/');$scheme=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http';$host=$_SERVER['HTTP_HOST']??'127.0.0.1:8000';return rtrim($scheme.'://'.$host,'/');}
define('APP_NAME',envv('APP_NAME','ATACJL'));define('APP_URL',appUrl());define('ROOT_PATH',dirname(__DIR__));date_default_timezone_set('Africa/Tunis');
if(envv('APP_ENV','production')==='production'){ini_set('display_errors','0');ini_set('log_errors','1');ini_set('error_log',ROOT_PATH.'/storage/php-error.log');}

set_exception_handler(function(Throwable $e): void {
    try { $reference = strtoupper(bin2hex(random_bytes(4))); }
    catch (Throwable $ignored) { $reference = 'ATACJL-ERROR'; }
    error_log('[ATACJL error '.$reference.'] '.$e);
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
    }
    $home = htmlspecialchars(APP_URL.'/index.php', ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="color-scheme" content="light dark"><title>Something went wrong · ATACJL</title><style>
      :root{color-scheme:light;--bg:#F5EBDD;--surface:#FFFFFF;--text:#423A34;--muted:#6D6257;--brand:#173B59;--border:#E0D4C4}
      @media(prefers-color-scheme:dark){:root{color-scheme:dark;--bg:#191A18;--surface:#242521;--text:#F5EBDD;--muted:#C9C0B4;--brand:#8DB4DC;--border:#3A3933}}
      *{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;background:var(--bg);color:var(--text);font:16px/1.6 system-ui,-apple-system,Segoe UI,sans-serif}
      main{width:min(100%,560px);padding:clamp(24px,5vw,42px);border:1px solid var(--border);border-radius:24px;background:var(--surface);box-shadow:0 24px 60px rgba(0,0,0,.1)}
      .mark{display:inline-grid;place-items:center;width:46px;height:46px;margin-bottom:18px;border-radius:14px;background:var(--brand);color:#fff;font-weight:900}h1{margin:0 0 12px;font-size:clamp(1.8rem,5vw,2.4rem);line-height:1.12}p{color:var(--muted)}.actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:24px}a,button{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:10px 16px;border:1px solid var(--border);border-radius:999px;background:var(--surface);color:var(--text);text-decoration:none;font:inherit;font-weight:750;cursor:pointer}a.primary{background:var(--brand);border-color:var(--brand);color:#fff}small{color:var(--muted)}
      </style></head><body><main><span class="mark">A</span><h1>Something went wrong</h1><p>ATACJL could not complete this request. Your private data has not been shown on this page. Please try again; if the issue continues, contact the site administrator with the reference below.</p><small>Reference: '.htmlspecialchars($reference, ENT_QUOTES, 'UTF-8').'</small><div class="actions"><a class="primary" href="'.$home.'">Return to website</a><button type="button" onclick="window.location.reload()">Try again</button></div></main></body></html>';
});
