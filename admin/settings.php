<?php
declare(strict_types=1);
require_once __DIR__.'/_common.php';

$groups = [
 'contact'=>[
  'label'=>'Contact information',
  'description'=>'The public contact details and social links displayed on the Contact page.',
  'fields'=>[
   'address'=>['label'=>'Address','type'=>'textarea'],
   'phone'=>['label'=>'Phone number','type'=>'text'],
   'email'=>['label'=>'Public contact email','type'=>'email'],
  ],
 ],
 'social'=>[
  'label'=>'Social links & map',
  'description'=>'Only valid links are displayed publicly. Leave a social link empty to hide it.',
  'fields'=>[
   'social_facebook'=>['label'=>'Facebook URL','type'=>'url'],
   'social_instagram'=>['label'=>'Instagram URL','type'=>'url'],
   'social_youtube'=>['label'=>'YouTube URL','type'=>'url'],
   'map_embed_url'=>['label'=>'Map embed URL','type'=>'url'],
  ],
 ],
 'translation'=>[
  'label'=>'Translation provider',
  'description'=>'Configure a LibreTranslate-compatible /translate endpoint. Your saved server endpoint is tried first; the public fallback mirrors may be unavailable.',
  'fields'=>[
   'translation_api_url'=>['label'=>'Translation endpoint URL','type'=>'url'],
   'translation_api_key'=>['label'=>'Translation API key (optional)','type'=>'password'],
  ],
 ],
];
$error='';
if(post()){
 verifyCsrf();
 $pdo=null;
 try{
  $values=[];
  foreach($groups as $group){
   foreach($group['fields'] as $key=>$definition){
    if($key==='translation_api_key' && trim((string)($_POST[$key]??''))==='')continue; // blank means keep saved key
    $values[$key]=clean($key,10000);
   }
  }
  foreach(['email'=>'Email address'] as $key=>$label){
   if(($values[$key]??'')!==''&&!filter_var($values[$key],FILTER_VALIDATE_EMAIL))throw new RuntimeException('Enter a valid public contact email address.');
  }
  foreach(['social_facebook'=>'Facebook URL','social_instagram'=>'Instagram URL','social_youtube'=>'YouTube URL','map_embed_url'=>'Map embed URL','translation_api_url'=>'Translation endpoint URL'] as $key=>$label){
   $url=trim((string)($values[$key]??''));
   if($url==='')continue;
   $scheme=strtolower((string)parse_url($url,PHP_URL_SCHEME));
   if(!filter_var($url,FILTER_VALIDATE_URL)||!in_array($scheme,['http','https'],true))throw new RuntimeException($label.' must be a complete HTTP or HTTPS URL.');
  }
  $pdo=db();
  $pdo->beginTransaction();
  $save=$pdo->prepare('INSERT INTO site_settings(setting_key,setting_value) VALUES(?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
  foreach($values as $key=>$value)$save->execute([$key,$value]);
  adminAudit('update','site_settings',null,['keys'=>array_keys($values)]);
  $pdo->commit();
  flash('success','Site settings saved successfully.');
  redirect('settings.php');
 }catch(Throwable $exception){
  if($pdo instanceof PDO && $pdo->inTransaction())$pdo->rollBack();
  error_log('ATACJL site settings save failed: '.$exception->getMessage());
  flash('error',$exception instanceof RuntimeException?$exception->getMessage():'Site settings could not be saved. Your previous values have been kept; please try again.');
  redirect('settings.php');
 }
}
$settings=[];
foreach(db()->query('SELECT setting_key,setting_value FROM site_settings')->fetchAll() as $row)$settings[$row['setting_key']]=$row['setting_value'];
if(trim((string)($settings['translation_api_url']??''))==='' || (($settings['translation_api_url']??'')==='https://libretranslate.com/translate'&&trim((string)($settings['translation_api_key']??''))==='') || (($settings['translation_api_url']??'')==='https://translate.argosopentech.com/translate'&&trim((string)($settings['translation_api_key']??''))===''))$settings['translation_api_url']=envv('TRANSLATION_API_URL','https://translate.cutie.dating/translate');
$pageTitle='Site settings';
require dirname(__DIR__).'/includes/header.php';
?>
<section class="section"><div class="container">
 <div class="section-head"><div><p class="eyebrow">Administration</p><h1 class="section-title">Site settings</h1><p class="copy">Manage contact details, external links, the map and translation connection here. Page wording lives in <strong>Content Management → Edit public pages</strong>, so the same text is not edited in two different places.</p></div></div>
 <form class="form-shell page-editor-shell" style="max-width:none" method="post" data-disable-on-submit>
  <?=csrfField()?>
  <?php foreach($groups as $groupKey=>$group):?>
   <section class="page-editor-group" style="padding:18px;margin-bottom:16px">
    <div class="admin-panel-head"><div><p class="eyebrow"><?=e(strtoupper($groupKey))?></p><h2><?=e($group['label'])?></h2><p class="muted"><?=e($group['description'])?></p></div></div>
    <div class="grid-2">
     <?php foreach($group['fields'] as $key=>$definition):$value=(string)($settings[$key]??'');$isSecret=$definition['type']==='password';?>
      <div class="field"><label for="setting-<?=e($key)?>"><?=e($definition['label'])?></label>
       <?php if($definition['type']==='textarea'):?><textarea id="setting-<?=e($key)?>" name="<?=e($key)?>" rows="3"><?=e($value)?></textarea>
       <?php elseif($isSecret):?><input id="setting-<?=e($key)?>" type="password" name="<?=e($key)?>" value="" autocomplete="new-password" placeholder="Leave blank to keep the saved key">
       <?php else:?><input id="setting-<?=e($key)?>" type="<?=e($definition['type'])?>" name="<?=e($key)?>" value="<?=e($value)?>" <?=in_array($definition['type'],['email','url'],true)?'inputmode="'.($definition['type']==='email'?'email':'url').'"':''?>><?php endif;?>
       <?php if($key==='translation_api_key'):?><small class="help">The saved key is never displayed here. Enter a new key only to replace it; a blank field preserves the current key or environment fallback.</small><?php endif;?>
      </div>
     <?php endforeach;?>
    </div>
    <?php if($groupKey==='translation'):?><div class="translation-provider-help"><p>Connection test sends a short test sentence to your configured server endpoint, then uses the browser fallback mirrors if the server endpoint is unavailable. Do not put API keys into public page content.</p><button class="btn ghost" type="button" data-translation-test>Test translation connection</button><p class="help" data-translation-test-result aria-live="polite"></p></div><?php endif;?>
   </section>
  <?php endforeach;?>
  <button class="btn primary" type="submit">Save site settings</button>
 </form>
</div></section>
<script>
document.querySelector('[data-translation-test]')?.addEventListener('click',async()=>{
 const result=document.querySelector('[data-translation-test-result]');
 const button=document.querySelector('[data-translation-test]');
 if(!result||!button)return;
 result.textContent='Testing the saved provider and browser fallback…';button.disabled=true;
 try{
  const api=document.querySelector('meta[name="translation-api"]')?.content;
  const service=window.ATACJLTranslation;
  if(!service)throw new Error('The translation helper did not load. Reload the page and try again.');
  const data=await service.translateTexts(['ATACJL translation connection test'],'fr',api);
  result.textContent='Connected via '+data.provider+': '+data.translatedTexts[0];
 }catch(error){
  result.textContent='Translation test failed: '+(error instanceof Error?error.message:'provider unavailable')+'. Check the endpoint, network connection and API key.';
 }finally{button.disabled=false;}
});
</script>
<?php require dirname(__DIR__).'/includes/footer.php'; ?>
