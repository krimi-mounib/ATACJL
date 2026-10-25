<?php
declare(strict_types=1);
require_once __DIR__.'/functions.php';
$lang=lang();
$u=currentUser();
$title=$pageTitle??APP_NAME;
$rtl=$lang==='ar';
$script=(string)($_SERVER['SCRIPT_NAME']??'/index.php');
$isAdminArea=str_contains($script,'/admin/');
$isMemberArea=str_contains($script,'/member/');
$name=trim((string)($u['full_name']??''));
$parts=preg_split('/\s+/',$name);
$initials=strtoupper(substr($parts[0]??'U',0,1).substr($parts[1]??'',0,1));
if($initials==='')$initials='U';
$avatar=(string)($u['profile_photo']??'');
$currentPath=(string)($_SERVER['REQUEST_URI']??'/index.php');
if(!str_starts_with($currentPath,'/')||str_starts_with($currentPath,'//'))$currentPath='/index.php';
$labels=['en'=>['home'=>'Home','about'=>'About','projects'=>'Projects','news'=>'News','events'=>'Events','gallery'=>'Gallery','contact'=>'Contact','search'=>'Search','all'=>'All','join'=>'Join the community','signin'=>'Sign in','language'=>'Language','admin'=>'Administration','member'=>'Member space','dashboard'=>'Dashboard','profile'=>'My profile','directory'=>'Member directory','documents'=>'Documents','logout'=>'Sign out','visit'=>'View public website','overview'=>'Overview','content'=>'Projects & content','memberships'=>'Memberships','users'=>'Users & members','participation'=>'Participation','volunteers'=>'Volunteers','team'=>'Team','messages'=>'Contact inbox','settings'=>'Site settings','audit'=>'Audit history','event_members'=>'Event assignments']];
$t=static fn(string $key):string=>$labels['en'][$key]??$key;
$navActive=static function(string $file)use($script):string{return basename($script)===$file?' aria-current="page"':'';};
$flashItems=flashes();
?>
<!doctype html>
<html lang="<?=e($lang)?>" dir="<?=$rtl?'rtl':'ltr'?>">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?> · <?=e(APP_NAME)?></title>
<meta name="description" content="<?=e(setting('site_full_name',APP_NAME))?>">
<meta name="theme-color" content="#174b91"><link rel="icon" type="image/svg+xml" href="<?=APP_URL?>/assets/images/favicon.svg"><meta name="translation-api" content="<?=e(APP_URL)?>/translate-api.php"><meta name="translation-explicit" content="<?=!empty($_SESSION['lang_explicit'])?'1':'0'?>">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?=APP_URL?>/assets/css/style.css">
</head>
<body class="<?=$rtl?'rtl ':''?><?= $isAdminArea?'admin-area':($isMemberArea?'member-area':'public-area')?>">
<?php if($isAdminArea): ?>
<div class="app-shell admin-app-shell">
  <aside class="app-sidebar" id="appSidebar" aria-label="<?=e($t('admin'))?> navigation">
    <a class="app-brand" href="<?=APP_URL?>/admin/index.php"><span class="app-brand-mark">A</span><span><strong>ATACJL</strong><small><?=e($t('admin'))?></small></span></a>
    <div class="sidebar-label"><?=e($t('overview'))?></div>
    <a class="sidebar-link<?=(basename($script)==='index.php' && $isAdminArea)?' is-active':''?>" href="<?=APP_URL?>/admin/index.php"><span class="nav-glyph">◫</span><?=e($t('overview'))?></a>
    <div class="sidebar-label">CONTENT & COMMUNITY</div>
    <a class="sidebar-link<?=basename($script)==='pages.php'?' is-active':''?>" href="<?=APP_URL?>/admin/pages.php"><span class="nav-glyph">▧</span>Page content</a>
    <a class="sidebar-link<?=basename($script)==='content.php'?' is-active':''?>" href="<?=APP_URL?>/admin/content.php?type=projects"><span class="nav-glyph">▤</span><?=e($t('content'))?></a>
    <a class="sidebar-link<?=basename($script)==='gallery.php'?' is-active':''?>" href="<?=APP_URL?>/admin/gallery.php"><span class="nav-glyph">▧</span><?=e($t('gallery'))?></a>
    <a class="sidebar-link<?=basename($script)==='memberships.php'?' is-active':''?>" href="<?=APP_URL?>/admin/memberships.php"><span class="nav-glyph">◎</span><?=e($t('memberships'))?></a>
    <a class="sidebar-link<?=basename($script)==='users.php'?' is-active':''?>" href="<?=APP_URL?>/admin/users.php"><span class="nav-glyph">♙</span><?=e($t('users'))?></a>
    <a class="sidebar-link<?=basename($script)==='participation.php'?' is-active':''?>" href="<?=APP_URL?>/admin/participation.php"><span class="nav-glyph">✓</span><?=e($t('participation'))?></a>
    <a class="sidebar-link<?=basename($script)==='volunteers.php'?' is-active':''?>" href="<?=APP_URL?>/admin/volunteers.php"><span class="nav-glyph">♡</span><?=e($t('volunteers'))?></a>
    <a class="sidebar-link<?=basename($script)==='team.php'?' is-active':''?>" href="<?=APP_URL?>/admin/team.php"><span class="nav-glyph">♧</span><?=e($t('team'))?></a>
    <div class="sidebar-label">OPERATIONS</div>
    <a class="sidebar-link<?=basename($script)==='event-members.php'?' is-active':''?>" href="<?=APP_URL?>/admin/index.php#event-management"><span class="nav-glyph">▦</span><?=e($t('event_members'))?></a>
    <a class="sidebar-link<?=basename($script)==='messages.php'?' is-active':''?>" href="<?=APP_URL?>/admin/messages.php"><span class="nav-glyph">✉</span><?=e($t('messages'))?></a>
    <a class="sidebar-link<?=basename($script)==='settings.php'?' is-active':''?>" href="<?=APP_URL?>/admin/settings.php"><span class="nav-glyph">⚙</span><?=e($t('settings'))?></a>
    <div class="sidebar-spacer"></div>
    <a class="sidebar-link" href="<?=APP_URL?>/index.php"><span class="nav-glyph">↗</span><?=e($t('visit'))?></a>
    <div class="sidebar-user"><span class="sidebar-avatar"><?=e($initials)?></span><span><strong><?=e($name?:'Administrator')?></strong><small><?=e($t('admin'))?></small></span></div>
  </aside>
  <div class="app-workspace">
    <header class="workspace-topbar"><button type="button" class="sidebar-toggle" data-sidebar-toggle aria-controls="appSidebar" aria-expanded="false" aria-label="Toggle navigation">☰</button><div class="workspace-breadcrumb"><span>ATACJL</span><span aria-hidden="true">/</span><strong><?=e($title)?></strong></div><div class="topbar-tools"><form class="language-switch" action="<?=APP_URL?>/lang.php" method="get"><input type="hidden" name="return" value="<?=e($currentPath)?>"><label class="sr-only" for="admin-language"><?=e($t('language'))?></label><select name="lang" id="admin-language" data-language-switch aria-label="<?=e($t('language'))?>"><?php foreach(['en'=>'English','fr'=>'Français','ar'=>'العربية','de'=>'Deutsch','it'=>'Italiano','es'=>'Español'] as $code=>$label):?><option value="<?=$code?>" data-no-translate <?=$lang===$code?'selected':''?>><?=$label?></option><?php endforeach;?></select><button type="submit" class="language-submit" aria-label="Apply language">↵</button></form><span class="topbar-user"><span class="sidebar-avatar"><?=e($initials)?></span><span><?=e($name?:'Administrator')?></span></span><form method="post" action="<?=APP_URL?>/logout.php"><?=csrfField()?><button class="text-action" type="submit"><?=e($t('logout'))?></button></form></div></header>
    <main class="admin-main">
      <?php if($flashItems):?><div class="flash-stack" role="status" aria-live="polite"><?php foreach($flashItems as $f):?><div class="inline-flash <?=e($f[0])?>"><?=e($f[1])?></div><?php endforeach;?></div><?php endif;?>
<?php elseif($isMemberArea): ?>
<div class="app-shell member-app-shell">
  <aside class="app-sidebar member-sidebar" id="appSidebar" aria-label="<?=e($t('member'))?> navigation">
    <a class="app-brand" href="<?=APP_URL?>/member/index.php"><span class="app-brand-mark">A</span><span><strong>ATACJL</strong><small><?=e($t('member'))?></small></span></a>
    <div class="sidebar-label">YOUR SPACE</div>
    <a class="sidebar-link<?=(basename($script)==='index.php' && $isMemberArea)?' is-active':''?>" href="<?=APP_URL?>/member/index.php"><span class="nav-glyph">◫</span><?=e($t('dashboard'))?></a>
    <a class="sidebar-link<?=basename($script)==='profile.php'?' is-active':''?>" href="<?=APP_URL?>/member/profile.php"><span class="nav-glyph">♙</span><?=e($t('profile'))?></a>
    <a class="sidebar-link<?=basename($script)==='events.php'?' is-active':''?>" href="<?=APP_URL?>/member/events.php"><span class="nav-glyph">▦</span><?=e($t('events'))?></a>
    <?php if(in_array((string)($u['role']??'user'),['member','admin'],true)):?><a class="sidebar-link<?=basename($script)==='directory.php'?' is-active':''?>" href="<?=APP_URL?>/member/directory.php"><span class="nav-glyph">♧</span><?=e($t('directory'))?></a>
    <a class="sidebar-link<?=basename($script)==='documents.php'?' is-active':''?>" href="<?=APP_URL?>/member/documents.php"><span class="nav-glyph">▤</span><?=e($t('documents'))?></a><?php endif;?>
    <?php if(($u['role']??'')==='admin'):?><div class="sidebar-label">ADMIN TOOLS</div>
    <a class="sidebar-link" href="<?=APP_URL?>/admin/index.php"><span class="nav-glyph">⚙</span>Administration dashboard</a>
    <a class="sidebar-link" href="<?=APP_URL?>/admin/pages.php"><span class="nav-glyph">▧</span>Edit public pages</a><?php endif;?>
    <div class="sidebar-spacer"></div>
    <a class="sidebar-link" href="<?=APP_URL?>/index.php"><span class="nav-glyph">↗</span><?=e($t('visit'))?></a>
    <div class="sidebar-user"><span class="sidebar-avatar"><?=e($initials)?></span><span><strong><?=e($name?:'Member')?></strong><small><?=e(ucfirst((string)($u['role']??'user')))?></small></span></div>
  </aside>
  <div class="app-workspace">
    <header class="workspace-topbar"><button type="button" class="sidebar-toggle" data-sidebar-toggle aria-controls="appSidebar" aria-expanded="false" aria-label="Toggle navigation">☰</button><div class="workspace-breadcrumb"><span>ATACJL</span><span aria-hidden="true">/</span><strong><?=e($title)?></strong></div><div class="topbar-tools"><form class="language-switch" action="<?=APP_URL?>/lang.php" method="get"><input type="hidden" name="return" value="<?=e($currentPath)?>"><label class="sr-only" for="member-language"><?=e($t('language'))?></label><select name="lang" id="member-language" data-language-switch aria-label="<?=e($t('language'))?>"><?php foreach(['en'=>'English','fr'=>'Français','ar'=>'العربية','de'=>'Deutsch','it'=>'Italiano','es'=>'Español'] as $code=>$label):?><option value="<?=$code?>" data-no-translate <?=$lang===$code?'selected':''?>><?=$label?></option><?php endforeach;?></select><button type="submit" class="language-submit" aria-label="Apply language">↵</button></form><span class="topbar-user"><span class="sidebar-avatar"><?=e($initials)?></span><span><?=e($name?:'Member')?></span></span><form method="post" action="<?=APP_URL?>/logout.php"><?=csrfField()?><button class="text-action" type="submit"><?=e($t('logout'))?></button></form></div></header>
    <main class="member-main">
      <?php if($flashItems):?><div class="flash-stack" role="status" aria-live="polite"><?php foreach($flashItems as $f):?><div class="inline-flash <?=e($f[0])?>"><?=e($f[1])?></div><?php endforeach;?></div><?php endif;?>
<?php else: ?>
<header class="site-header">
  <div class="container nav">
    <a class="brand" href="<?=APP_URL?>/index.php"><img src="<?=APP_URL?>/assets/images/logo.png" alt="<?=e(APP_NAME)?> logo"><span><?=e(APP_NAME)?><small><?=e(setting('site_tagline','Culture · Youth · Community'))?></small></span></a>
    <button class="nav-toggle" data-nav-toggle aria-controls="publicNavigation" aria-expanded="false" aria-label="Open navigation">☰</button>
    <nav id="publicNavigation" data-nav aria-label="Primary navigation">
      <a href="<?=APP_URL?>/index.php"<?=$navActive('index.php')?>><?=e($t('home'))?></a><a href="<?=APP_URL?>/about.php"<?=$navActive('about.php')?>><?=e($t('about'))?></a><a href="<?=APP_URL?>/projects.php"<?=$navActive('projects.php')?>><?=e($t('projects'))?></a><a href="<?=APP_URL?>/news.php"<?=$navActive('news.php')?>><?=e($t('news'))?></a><a href="<?=APP_URL?>/events.php"<?=$navActive('events.php')?>><?=e($t('events'))?></a><a href="<?=APP_URL?>/gallery.php"<?=$navActive('gallery.php')?>><?=e($t('gallery'))?></a><a href="<?=APP_URL?>/contact.php"<?=$navActive('contact.php')?>><?=e($t('contact'))?></a>
      <form class="site-search" action="<?=APP_URL?>/search.php" method="get"><input name="q" value="<?=e((string)($_GET['q']??''))?>" placeholder="<?=e($t('search'))?>…" aria-label="<?=e($t('search'))?>"><select name="type" aria-label="Search type"><option value="all" <?=($_GET['type']??'all')==='all'?'selected':''?>><?=e($t('all'))?></option><option value="projects" <?=($_GET['type']??'')==='projects'?'selected':''?>><?=e($t('projects'))?></option><option value="news" <?=($_GET['type']??'')==='news'?'selected':''?>><?=e($t('news'))?></option><option value="events" <?=($_GET['type']??'')==='events'?'selected':''?>><?=e($t('events'))?></option><?php if($u&&in_array((string)$u['role'],['member','admin'],true)):?><option value="members" <?=($_GET['type']??'')==='members'?'selected':''?>>Members</option><?php endif;?></select><button type="submit" aria-label="<?=e($t('search'))?>">⌕</button></form>
      <form class="language-switch public-language" action="<?=APP_URL?>/lang.php" method="get"><input type="hidden" name="return" value="<?=e($currentPath)?>"><label class="sr-only" for="public-language"><?=e($t('language'))?></label><select name="lang" id="public-language" data-language-switch aria-label="<?=e($t('language'))?>"><?php foreach(['en'=>'EN','fr'=>'FR','ar'=>'ع','de'=>'DE','it'=>'IT','es'=>'ES'] as $code=>$label):?><option value="<?=$code?>" data-no-translate <?=$lang===$code?'selected':''?>><?=$label?></option><?php endforeach;?></select><button type="submit" class="language-submit" aria-label="Apply language">↵</button></form>
      <?php if($u):?><div class="user-menu"><button class="user-menu-toggle" type="button" aria-label="Open account menu" aria-expanded="false"><?php if($avatar):?><img class="user-avatar" src="<?=e($avatar)?>" alt=""><?php else:?><span class="user-avatar-initials"><?=e($initials)?></span><?php endif;?></button><div class="user-menu-panel"><div class="user-menu-header"><?php if($avatar):?><img class="user-avatar user-avatar-large" src="<?=e($avatar)?>" alt=""><?php else:?><span class="user-avatar-initials user-avatar-large"><?=e($initials)?></span><?php endif;?><div><strong><?=e($name?:'User')?></strong><small><?=e(ucfirst((string)$u['role']))?></small></div></div><a href="<?=APP_URL?>/member/index.php"><?=e($t('dashboard'))?></a><a href="<?=APP_URL?>/member/profile.php"><?=e($t('profile'))?></a><?php if($u['role']==='admin'):?><a href="<?=APP_URL?>/admin/index.php"><?=e($t('admin'))?></a><?php endif;?><form method="post" action="<?=APP_URL?>/logout.php" class="inline"><?=csrfField()?><button type="submit" class="link-btn"><?=e($t('logout'))?></button></form></div></div><?php else:?><a class="nav-join" href="<?=APP_URL?>/login.php"><?=e($t('signin'))?></a><?php endif;?>
    </nav>
  </div>
</header>
<?php foreach($flashItems as $f):?><div class="flash <?=e($f[0])?>" role="status" aria-live="polite"><?=e($f[1])?></div><?php endforeach;?>
<main class="site-main">
<?php endif; ?>
