<?php
declare(strict_types=1);
require_once __DIR__.'/includes/functions.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Allow: POST'); http_response_code(405);
    echo json_encode(['error'=>'POST required']); exit;
}
// Translation is public, but cap per-session usage and release PHP's session lock before waiting on a provider.
if (session_status() === PHP_SESSION_ACTIVE) {
    $now=time(); $rate=$_SESSION['atacjl_translation_rate']??['start'=>$now,'count'=>0];
    if (!is_array($rate) || ($now-(int)($rate['start']??0))>=60) $rate=['start'=>$now,'count'=>0];
    if ((int)($rate['count']??0)>=30) { session_write_close(); http_response_code(429); echo json_encode(['error'=>'Translation request limit reached for this session. Please wait a minute and retry.']); exit; }
    $rate['count']=(int)$rate['count']+1; $_SESSION['atacjl_translation_rate']=$rate; session_write_close();
}
$payload = json_decode((string)file_get_contents('php://input'), true);
if (!is_array($payload)) { http_response_code(400); echo json_encode(['error'=>'Invalid JSON body']); exit; }
$target = (string)($payload['target'] ?? '');
$allowed = ['en','fr','ar','de','it','es'];
if (!in_array($target, $allowed, true)) { http_response_code(422); echo json_encode(['error'=>'Unsupported target language']); exit; }
$texts = $payload['texts'] ?? ($payload['text'] ?? null);
$single = is_string($texts);
if ($single) $texts = [$texts];
if (!is_array($texts) || count($texts) < 1 || count($texts) > 40) {
    http_response_code(422); echo json_encode(['error'=>'Send between 1 and 40 text fragments']); exit;
}
$cleanTexts=[]; $totalChars=0;
foreach ($texts as $item) {
    if (!is_string($item)) { http_response_code(422); echo json_encode(['error'=>'Every text fragment must be a string']); exit; }
    $item=trim($item); $length=mb_strlen($item); $totalChars += $length;
    if ($length > 4500 || $totalChars > 12000) { http_response_code(413); echo json_encode(['error'=>'Translation batch is too large']); exit; }
    $cleanTexts[]=$item;
}
$endpoint=trim(setting('translation_api_url',''));
if ($endpoint==='') $endpoint=envv('TRANSLATION_API_URL','https://translate.cutie.dating/translate');
$key=trim(setting('translation_api_key',''));
if ($key==='') $key=envv('TRANSLATION_API_KEY','');
// Existing v4 databases may still point at libretranslate.com with no paid key; transparently use the community mirror in that case.
if ($key==='' && preg_match('~^https?://libretranslate\.com/translate/?$~i',$endpoint)) $endpoint='https://translate.cutie.dating/translate';
if ($key==='' && preg_match('~^https?://translate\.argosopentech\.com/translate/?$~i',$endpoint)) $endpoint='https://translate.cutie.dating/translate';
if (!filter_var($endpoint,FILTER_VALIDATE_URL) || !in_array(strtolower((string)parse_url($endpoint,PHP_URL_SCHEME)),['https','http'],true)) {
    http_response_code(503); echo json_encode(['error'=>'Translation provider URL is not configured correctly']); exit;
}
// Accept either a full /translate URL or a provider base URL.
if (!preg_match('~/translate/?$~i', $endpoint)) $endpoint=rtrim($endpoint,'/').'/translate';
$body=['q'=>$single ? $cleanTexts[0] : $cleanTexts,'source'=>'auto','target'=>$target,'format'=>'text'];
if ($key!=='') $body['api_key']=$key;
$jsonBody=json_encode($body,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
$result=false; $status=0; $providerError='';
if (function_exists('curl_init')) {
    $ch=curl_init($endpoint);
    curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$jsonBody,CURLOPT_RETURNTRANSFER=>true,CURLOPT_HTTPHEADER=>['Content-Type: application/json','Accept: application/json'],CURLOPT_CONNECTTIMEOUT=>2,CURLOPT_TIMEOUT=>5]);
    $result=curl_exec($ch); $status=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE); $providerError=(string)curl_error($ch); curl_close($ch);
} else {
    $ctx=stream_context_create(['http'=>['method'=>'POST','header'=>"Content-Type: application/json\r\nAccept: application/json\r\n",'content'=>$jsonBody,'timeout'=>5,'ignore_errors'=>true]]);
    $result=@file_get_contents($endpoint,false,$ctx);
    foreach (($http_response_header??[]) as $h) if (preg_match('/^HTTP\/\S+\s+(\d+)/',$h,$m)) $status=(int)$m[1];
}
if ($result===false || $status<200 || $status>=300) {
    $decoded=is_string($result)?json_decode($result,true):null;
    $detail=is_array($decoded)?(string)($decoded['error']??''):'';
    if ($status===401 || $status===403) $message='Translation provider rejected the request. Check whether its API key is required and configured.';
    elseif ($status===429) $message='Translation provider rate limit reached. Try again shortly or use a dedicated API instance.';
    elseif ($status===0) $message='Translation provider could not be reached'.($providerError!==''?' (network error).':'.');
    else $message='Translation provider returned HTTP '.$status.($detail!==''?': '.$detail:'.');
    http_response_code(502); echo json_encode(['error'=>$message],JSON_UNESCAPED_UNICODE); exit;
}
$data=json_decode((string)$result,true);
if (!is_array($data) || !array_key_exists('translatedText',$data)) { http_response_code(502); echo json_encode(['error'=>'Translation provider returned an unexpected response']); exit; }
$translated=$data['translatedText'];
if ($single && is_string($translated)) { echo json_encode(['translatedText'=>$translated],JSON_UNESCAPED_UNICODE); exit; }
if (is_string($translated)) $translated=[$translated];
if (!is_array($translated) || count($translated)!==count($cleanTexts)) { http_response_code(502); echo json_encode(['error'=>'Translation provider returned an incomplete batch']); exit; }
echo json_encode(['translatedTexts'=>array_values(array_map(static fn($v)=>is_string($v)?$v:'', $translated))],JSON_UNESCAPED_UNICODE);
