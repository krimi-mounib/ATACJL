<?php
declare(strict_types=1);
require_once __DIR__.'/includes/functions.php';
$lang=lang();
$stats=[
  'members'=>(int)db()->query("SELECT COUNT(*) FROM users WHERE role='member' AND is_active=1 AND is_banned=0")->fetchColumn(),
  'projects'=>(int)db()->query('SELECT COUNT(*) FROM projects')->fetchColumn(),
  'events'=>(int)db()->query('SELECT COUNT(*) FROM events WHERE event_date>=CURDATE() AND is_published=1')->fetchColumn(),
  'news'=>(int)db()->query('SELECT COUNT(*) FROM news WHERE is_published=1')->fetchColumn()
];
$projects=db()->query('SELECT * FROM projects ORDER BY id DESC LIMIT 3')->fetchAll();
$news=db()->query('SELECT * FROM news WHERE is_published=1 ORDER BY published_at DESC LIMIT 3')->fetchAll();
$events=db()->query('SELECT * FROM events WHERE event_date>=CURDATE() AND is_published=1 ORDER BY event_date ASC LIMIT 3')->fetchAll();
$homeWelcome=setting('home_welcome','Culture, youth and community in action.');
$homeIntro=setting('home_intro','Discover the projects, cultural activities and community initiatives bringing people together through ATACJL.');
$pageTitle='Home';
require __DIR__.'/includes/header.php';
?>
<section class="hero">
  <div class="container hero-grid">
    <div class="hero-copy">
      <p class="eyebrow"><?=e(setting('home_eyebrow',"ATACJL · Hammam Sousse, Tunisia"))?></p>
      <h1 class="display"><?=e($homeWelcome)?></h1>
      <p class="lead"><?=e($homeIntro)?></p>
      <div class="actions"><a class="btn primary" href="projects.php"><?=e(setting('home_cta_primary',"Explore our projects"))?> <span aria-hidden="true">↗</span></a><a class="btn ghost" href="volunteer.php"><?=e(setting('home_cta_secondary',"Get involved"))?></a></div>
      <p class="hero-trust"><span class="trust-dot"></span> <?=e(setting('home_trust_line',"A local community. Shared ideas. Meaningful participation."))?></p>
    </div>
    <div class="hero-art" aria-label="ATACJL community and culture illustration">
      <div class="hero-art-mark"><img src="<?=APP_URL?>/assets/images/logo.png" alt="ATACJL logo"></div>
      <div class="hero-note note-top"><span class="hero-note-mark">✳</span><span><strong><?=e(setting('home_hero_note_one_title',"Culture & creativity"))?></strong><small><?=e(setting('home_hero_note_one_text',"Ideas brought to life together"))?></small></span></div>
      <div class="hero-note note-bottom"><span class="hero-note-mark">↗</span><span><strong><?=e(setting('home_hero_note_two_title',"Youth & participation"))?></strong><small><?=e(setting('home_hero_note_two_text',"Make room for every voice"))?></small></span></div>
      <span class="hero-art-caption"><?=e(setting('home_hero_art_caption',"HAMMAM SOUSSE · TUNISIA"))?></span>
    </div>
  </div>
</section>
<section class="stats" aria-label="ATACJL at a glance">
  <div class="stat"><strong><?=$stats['members']?></strong><span><?=e(setting('home_stats_label_members',"Active members"))?></span></div>
  <div class="stat"><strong><?=$stats['projects']?></strong><span><?=e(setting('home_stats_label_projects',"Projects"))?></span></div>
  <div class="stat"><strong><?=$stats['events']?></strong><span><?=e(setting('home_stats_label_events',"Upcoming events"))?></span></div>
  <div class="stat"><strong><?=$stats['news']?></strong><span><?=e(setting('home_stats_label_news',"Published stories"))?></span></div>
</section>
<section class="section">
  <div class="container">
    <div class="section-head"><div><p class="eyebrow"><?=e(setting('home_projects_eyebrow',"Our work"))?></p><h2 class="section-title"><?=e(setting('home_projects_title',"Projects shaped by community."))?></h2><p class="copy"><?=e(setting('home_projects_intro',"Explore initiatives and activities that create opportunities for culture, youth and shared experiences."))?></p></div><a href="projects.php" class="btn ghost"><?=e(setting('home_projects_link','All projects'))?> <span aria-hidden="true">→</span></a></div>
    <?php if($projects):?><div class="grid-3"><?php foreach($projects as $p):?><article class="card"><div class="card-media"><?php if($p['featured_image']):?><img loading="lazy" src="<?=e($p['featured_image'])?>" alt="<?=e(localTitle($p,$lang))?>"><?php else:?><div class="media-placeholder" aria-hidden="true"><span><?=e(mb_substr(localTitle($p,$lang),0,1))?></span></div><?php endif;?></div><div class="card-body"><span class="tag"><?=e($p['category'])?></span><h3><?=e(localTitle($p,$lang))?></h3><p><?=e(mb_strimwidth(strip_tags(localDescription($p,$lang)),0,145,'…'))?></p><a class="text-link" href="project-detail.php?id=<?=$p['id']?>"><?=e(setting('home_projects_card_link','Discover project'))?> <span aria-hidden="true">→</span></a></div></article><?php endforeach;?></div><?php else:?><div class="empty-state"><span class="empty-icon">✳</span><h3><?=e(setting('home_projects_empty_title','Projects are on the way'))?></h3><p><?=e(setting('home_projects_empty_text',"Check back soon to explore ATACJL's latest community initiatives."))?></p></div><?php endif;?>
  </div>
</section>
<section class="section alt">
  <div class="container">
    <div class="section-head"><div><p class="eyebrow"><?=e(setting('home_news_eyebrow',"Latest stories"))?></p><h2 class="section-title"><?=e(setting('home_news_title',"News from our community."))?></h2><p class="copy"><?=e(setting('home_news_intro',"Updates, highlights and stories from the work taking place around ATACJL."))?></p></div><a href="news.php" class="btn ghost"><?=e(setting('home_news_link','All news'))?> <span aria-hidden="true">→</span></a></div>
    <?php if($news):?><div class="grid-3"><?php foreach($news as $n):?><article class="card"><div class="card-media"><?php if($n['featured_image']):?><img loading="lazy" src="<?=e($n['featured_image'])?>" alt="<?=e(localTitle($n,$lang))?>"><?php else:?><div class="media-placeholder media-news" aria-hidden="true"><span>ATACJL · STORIES</span></div><?php endif;?></div><div class="card-body"><div class="meta-row"><span class="tag"><?=e($n['category'])?></span><time datetime="<?=e(date('Y-m-d',strtotime($n['published_at'])))?>"><?=e(date('M j, Y',strtotime($n['published_at'])))?></time></div><h3><?=e(localTitle($n,$lang))?></h3><p><?=e(mb_strimwidth(strip_tags(localBody($n,$lang)),0,145,'…'))?></p><a class="text-link" href="article.php?id=<?=$n['id']?>"><?=e(setting('home_news_card_link','Read story'))?> <span aria-hidden="true">→</span></a></div></article><?php endforeach;?></div><?php else:?><div class="empty-state"><span class="empty-icon">✳</span><h3><?=e(setting('home_news_empty_title','Stories coming soon'))?></h3><p><?=e(setting('home_news_empty_text','New updates from ATACJL will appear here.'))?></p></div><?php endif;?>
  </div>
</section>
<section class="section">
  <div class="container">
    <div class="section-head"><div><p class="eyebrow"><?=e(setting('home_events_eyebrow',"Come together"))?></p><h2 class="section-title"><?=e(setting('home_events_title',"Be part of what's next."))?></h2><p class="copy"><?=e(setting('home_events_intro',"Take part in an event, meet the community, or contribute your time and ideas."))?></p></div><a href="events.php" class="btn ghost"><?=e(setting('home_events_link','View all events'))?> <span aria-hidden="true">→</span></a></div>
    <?php if($events):?><div class="grid-3"><?php foreach($events as $ev):?><article class="card event-card"><div class="event-date-block"><strong><?=e(date('d',strtotime($ev['event_date'])))?></strong><span><?=e(date('M',strtotime($ev['event_date'])))?></span></div><div class="card-media"><?php if($ev['featured_image']):?><img loading="lazy" src="<?=e($ev['featured_image'])?>" alt="<?=e(localTitle($ev,$lang))?>"><?php else:?><div class="media-placeholder media-event" aria-hidden="true"><span>COMMUNITY EVENT</span></div><?php endif;?></div><div class="card-body"><span class="event-location">⌖ <?=e($ev['location'])?> · <?=e(substr((string)$ev['event_time'],0,5))?></span><h3><?=e(localTitle($ev,$lang))?></h3><p><?=e(mb_strimwidth(strip_tags(localDescription($ev,$lang)),0,130,'…'))?></p><a class="text-link" href="event-detail.php?id=<?=$ev['id']?>"><?=e(setting('home_events_card_link','Event details'))?> <span aria-hidden="true">→</span></a></div></article><?php endforeach;?></div><?php else:?><div class="empty-state"><span class="empty-icon">⌁</span><h3><?=e(setting('home_events_empty_title','No upcoming events listed'))?></h3><p><?=e(setting('home_events_empty_text',"Follow ATACJL's updates and come back to see future activities."))?></p></div><?php endif;?>
  </div>
</section>
<section class="section community-section"><div class="container"><div class="cta"><div><p class="eyebrow"><?=e(setting('home_cta_eyebrow',"Participate"))?></p><h2 class="section-title"><?=e(setting('home_cta_title',"Bring your time, skills and ideas."))?></h2><p><?=e(setting('home_cta_intro',"Communities grow when people take part. Find a way to contribute that fits you."))?></p></div><div class="actions"><a class="btn ghost" href="membership.php"><?=e(setting('home_cta_member_link',"Become a member"))?></a><a class="btn coral" href="volunteer.php"><?=e(setting('home_cta_volunteer_link',"Volunteer with us"))?></a></div></div></div></section>
<?php require __DIR__.'/includes/footer.php'; ?>
