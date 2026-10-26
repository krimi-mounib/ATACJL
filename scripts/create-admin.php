<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){
    http_response_code(404);
    exit("Not found\n");
}
require_once dirname(__DIR__).'/includes/db.php';
function promptValue(string $label):string{fwrite(STDOUT,$label);return trim((string)fgets(STDIN));}
function promptPassword(string $label):string{
    fwrite(STDOUT,$label);
    $hidden=false;
    if(PHP_OS_FAMILY!=='Windows'&&function_exists('exec')){@exec('stty -echo 2>/dev/null',$out,$code);$hidden=$code===0;}
    try{$value=rtrim((string)fgets(STDIN),"\r\n");}
    finally{if($hidden){@exec('stty echo 2>/dev/null');fwrite(STDOUT,PHP_EOL);}}
    return $value;
}
$username=strtolower(promptValue('Admin username: '));
$fullName=promptValue('Admin full name: ');
$email=strtolower(promptValue('Admin email: '));
$password=promptPassword('Password (12+ characters, letters and numbers): ');
if(!preg_match('/^[a-z0-9._-]{3,80}$/',$username)||mb_strlen($fullName)<2||!filter_var($email,FILTER_VALIDATE_EMAIL)||mb_strlen($password)<12||!preg_match('/[A-Za-z]/',$password)||!preg_match('/\d/',$password)){
    fwrite(STDERR,"Invalid input. Username must be 3–80 characters; email valid; password 12+ characters and contain letters and numbers.\n");exit(1);
}
try{
    $pdo=db();
    $check=$pdo->prepare('SELECT id FROM users WHERE username=? OR email=? LIMIT 1');$check->execute([$username,$email]);
    if($check->fetch()) {fwrite(STDERR,"That username or email is already in use. No changes made.\n");exit(1);}
    $stmt=$pdo->prepare("INSERT INTO users(username,full_name,email,password_hash,role,email_verified,is_active,is_banned) VALUES(?,?,?,?,'admin',1,1,0)");
    $stmt->execute([$username,$fullName,$email,password_hash($password,PASSWORD_DEFAULT)]);
    fwrite(STDOUT,"Administrator created successfully. Sign in at /login.php and keep the password in a password manager.\n");
}catch(Throwable $e){fwrite(STDERR,"Could not create the administrator. Confirm .env database settings and schema, then retry.\n");error_log('CLI admin creation failed: '.$e->getMessage());exit(1);}
