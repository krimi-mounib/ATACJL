<?php
declare(strict_types=1);
function envv(string $key,string $default=''):string{static $e=null;if($e===null){$e=[];$file=__DIR__.'/../.env';if(is_file($file)){foreach(file($file,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line){$line=trim($line);if($line===''||str_starts_with($line,'#')||!str_contains($line,'='))continue;[$k,$v]=explode('=',$line,2);$e[trim($k)]=trim($v," \t\n\r\0\x0B\"");}}}return $e[$key]??$default;}
function appUrl():string{$env=envv('APP_URL','');if($env!=='')return rtrim($env,'/');$scheme=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http';$host=$_SERVER['HTTP_HOST']??'127.0.0.1:8000';return rtrim($scheme.'://'.$host,'/');}
define('APP_NAME',envv('APP_NAME','ATACJL'));define('APP_URL',appUrl());define('ROOT_PATH',dirname(__DIR__));date_default_timezone_set('Africa/Tunis');
if(envv('APP_ENV','production')==='production'){ini_set('display_errors','0');ini_set('log_errors','1');ini_set('error_log',ROOT_PATH.'/storage/php-error.log');}

set_exception_handler(function(Throwable $e): void { error_log((string)$e); if(!headers_sent()) http_response_code(500); echo '<!doctype html><html><body style="font-family:Arial;padding:40px"><h1>Something went wrong</h1><p>Please try again later or contact an administrator.</p></body></html>'; });
