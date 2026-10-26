<?php
declare(strict_types=1);require_once __DIR__.'/auth.php';
function post():bool{return ($_SERVER['REQUEST_METHOD']??'GET')==='POST';}
function clean(string $key,int $max=10000):string{return mb_substr(trim((string)($_POST[$key]??'')),0,$max);}
function emailField(string $key):string{$v=trim((string)($_POST[$key]??''));return filter_var($v,FILTER_VALIDATE_EMAIL)?mb_strtolower($v):'';}
function lang():string{$allowed=['en','fr','ar','de','it','es'];$l=$_GET['lang']??$_SESSION['lang']??'en';if(in_array($l,$allowed,true))$_SESSION['lang']=$l;return $_SESSION['lang']??'en';}
function field(string $base,string $language):string{return $base.'_'.($language==='ar'?'ar':$language);}
function localisedField(array $row,string $base,string $lang):string{
    $preferred=field($base,$lang);
    $value=trim((string)($row[$preferred]??''));
    if($value!=='')return $value;
    $fallbacks=['en','fr','ar','de','it','es'];
    foreach($fallbacks as $fallback){$candidate=trim((string)($row[$base.'_'.$fallback]??''));if($candidate!=='')return $candidate;}
    return '';
}
function localTitle(array $row,string $lang):string{return localisedField($row,'title',$lang);}
function localBody(array $row,string $lang):string{return localisedField($row,'body',$lang);}
function localDescription(array $row,string $lang):string{return localisedField($row,'description',$lang);}
function setting(string $key,string $default=''):string{static $cache=[];if(array_key_exists($key,$cache))return $cache[$key];$s=db()->prepare('SELECT setting_value FROM site_settings WHERE setting_key=?');$s->execute([$key]);$value=$s->fetchColumn();return $cache[$key]=$value===false?$default:(string)$value;}
function likeCount(string $type,int $id):int{$s=db()->prepare('SELECT COUNT(*) FROM likes WHERE content_type=? AND content_id=?');$s->execute([$type,$id]);return (int)$s->fetchColumn();}
function likedByMe(string $type,int $id):bool{$u=currentUser();if(!$u)return false;$s=db()->prepare('SELECT 1 FROM likes WHERE user_id=? AND content_type=? AND content_id=?');$s->execute([$u['id'],$type,$id]);return(bool)$s->fetchColumn();}
function commentsFor(string $type,int $id):array{$s=db()->prepare("SELECT c.*,u.full_name,u.profile_photo FROM comments c JOIN users u ON u.id=c.user_id WHERE c.content_type=? AND c.content_id=? AND c.status='visible' ORDER BY c.created_at DESC");$s->execute([$type,$id]);return $s->fetchAll();}
function toggleLike(string $type,int $id):void{$u=requireVerifiedUser();$allowed=['project','news','event','gallery'];if(!in_array($type,$allowed,true))return;$s=db()->prepare('SELECT id FROM likes WHERE user_id=? AND content_type=? AND content_id=?');$s->execute([$u['id'],$type,$id]);if($s->fetch())db()->prepare('DELETE FROM likes WHERE user_id=? AND content_type=? AND content_id=?')->execute([$u['id'],$type,$id]);else db()->prepare('INSERT INTO likes(user_id,content_type,content_id) VALUES(?,?,?)')->execute([$u['id'],$type,$id]);}
function imgbbUpload(array $file):string{if(($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK)throw new RuntimeException('Image upload failed.');if(($file['size']??0)>8*1024*1024)throw new RuntimeException('Image must be smaller than 8 MB.');$mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);$allowed=['image/jpeg','image/png','image/gif','image/webp'];if(!in_array($mime,$allowed,true))throw new RuntimeException('Only JPG, PNG, GIF or WebP images are allowed.');$api=envv('IMGBB_API_KEY','');if($api==='')throw new RuntimeException('Image storage is not configured.');$ch=curl_init('https://api.imgbb.com/1/upload?key='.rawurlencode($api));curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>['image'=>base64_encode((string)file_get_contents($file['tmp_name']))],CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>30]);$raw=curl_exec($ch);$err=curl_error($ch);$code=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);if($raw===false||$err||$code<200||$code>=300)throw new RuntimeException('Image storage service is unavailable.');$json=json_decode($raw,true);$url=$json['data']['display_url']??$json['data']['url']??'';if(!$url)throw new RuntimeException('Image storage returned an invalid response.');return $url;}
function safeFileUpload(string $field):?string{if(empty($_FILES[$field])||($_FILES[$field]['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE)return null;return imgbbUpload($_FILES[$field]);}
function validPassword(string $p):bool{return mb_strlen($p)>=8&&preg_match('/[A-Za-z]/',$p)&&preg_match('/\d/',$p);}
