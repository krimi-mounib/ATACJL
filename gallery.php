<?php
declare(strict_types=1);
require_once __DIR__.'/includes/functions.php';
$lang=lang();
$albums=db()->query('SELECT a.*,i.image_path featured_url FROM gallery_albums a LEFT JOIN gallery_items i ON i.id=a.featured_item_id ORDER BY a.id DESC')->fetchAll();
$items=db()->query('SELECT * FROM gallery_items ORDER BY uploaded_at DESC')->fetchAll();
if(post()){
  verifyCsrf();$id=(int)($_POST['id']??0);
  if(isset($_POST['like']))toggleLike('gallery',$id);
  if(isset($_POST['comment'])){$u=requireVerifiedUser();$body=clean('body',2000);if($body)db()->prepare('INSERT INTO comments(user_id,content_type,content_id,body) VALUES(?,?,?,?)')->execute([$u['id'],'gallery',$id,$body]);}
  redirect('gallery.php');
}
$caption=static function(array $item,string $language):string{$value=trim((string)($item['caption_'.$language]??''));if($value!=='')return $value;return trim((string)($item['caption_en']??''));};
$pageTitle='Gallery';require __DIR__.'/includes/header.php';
?>
<section class="section"><div class="container"><div class="public-page-heading"><p class="eyebrow"><?=e(setting('gallery_eyebrow',"MOMENTS & MEMORIES"))?></p><h1 class="section-title"><?=e(setting('gallery_title',"ATACJL in pictures."))?></h1><p class="copy"><?=e(setting('gallery_intro',"A look at the people, gatherings and activities that bring the community together."))?></p></div>
<?php if(!$albums&&!$items):?><div class="empty-state"><span class="empty-icon">▧</span><h2><?=e(setting('gallery_empty_title','Our gallery is growing'))?></h2><p><?=e(setting('gallery_empty_text','Photos from ATACJL activities will appear here as albums and images are added.'))?></p><a class="btn ghost" href="<?=APP_URL?>/events.php"><?=e(setting('gallery_empty_link','Explore activities'))?></a></div><?php endif;?>
<?php foreach($albums as $a):$albumItems=array_values(array_filter($items,static fn($item)=>(int)($item['album_id']??0)===(int)$a['id']));?><section class="gallery-group"><div class="gallery-album-heading"><div><span class="tag"><?=e($a['category'])?></span><h2><?=e($a['title'])?></h2><?php if($a['description']):?><p class="copy"><?=e($a['description'])?></p><?php endif;?><span class="muted"><?=count($albumItems)?> <?=e(setting(count($albumItems)===1?'gallery_photo_singular':'gallery_photo_plural',count($albumItems)===1?'photo':'photos'))?></span></div><?php if($a['featured_url']):?><img class="gallery-featured" loading="lazy" src="<?=e($a['featured_url'])?>" alt="Featured image for <?=e($a['title'])?>"><?php endif;?></div>
<?php if($albumItems):?><div class="grid-3 gallery-grid"><?php foreach($albumItems as $g):?><article class="card"><div class="card-media"><img loading="lazy" src="<?=e($g['image_path'])?>" alt="<?=e($caption($g,$lang)?:$a['title'])?>"></div><div class="card-body"><?php if($caption($g,$lang)):?><p><?=e($caption($g,$lang))?></p><?php endif;?><form method="post"><?=csrfField()?><input type="hidden" name="id" value="<?=$g['id']?>"><button class="like-btn" name="like" value="1">♡ Like · <?=likeCount('gallery',(int)$g['id'])?></button></form></div></article><?php endforeach;?></div><?php else:?><div class="empty-state compact"><h3><?=e(setting('gallery_album_empty_title','No photos in this album yet'))?></h3><p><?=e(setting('gallery_album_empty_text','Images added to this album will be shown here.'))?></p></div><?php endif;?></section><?php endforeach;?>
<?php $unassigned=array_values(array_filter($items,static fn($item)=>empty($item['album_id'])));if($unassigned):?><section class="gallery-group"><div class="section-head"><div><p class="eyebrow"><?=e(setting('gallery_more_eyebrow','MORE MOMENTS'))?></p><h2><?=e(setting('gallery_more_title','More from ATACJL'))?></h2></div></div><div class="grid-3 gallery-grid"><?php foreach($unassigned as $g):?><article class="card"><div class="card-media"><img loading="lazy" src="<?=e($g['image_path'])?>" alt="<?=e($caption($g,$lang)?:$g['category'])?>"></div><div class="card-body"><span class="tag"><?=e($g['category'])?></span><?php if($caption($g,$lang)):?><p><?=e($caption($g,$lang))?></p><?php endif;?></div></article><?php endforeach;?></div></section><?php endif;?>
</div></section>
<?php require __DIR__.'/includes/footer.php'; ?>
