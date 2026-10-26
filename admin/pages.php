<?php
declare(strict_types=1);
require_once __DIR__.'/_common.php';
$groups = [
 'site'=>['label'=>'Site-wide','description'=>'Shared brand details and footer content that appears across the entire website.','fields'=>['site_full_name'=>'Organization full name','site_tagline'=>'Brand tagline','footer_location'=>'Footer location','footer_explore_title'=>'Footer navigation heading','footer_about_link'=>'Footer About link','footer_projects_link'=>'Footer Projects link','footer_events_link'=>'Footer Events link','footer_news_link'=>'Footer News link','footer_gallery_link'=>'Footer Gallery link','footer_participate_title'=>'Footer participation heading','footer_member_link'=>'Footer membership link','footer_volunteer_link'=>'Footer volunteer link','footer_contact_link'=>'Footer contact link','footer_register_link'=>'Footer registration link','footer_member_access_link'=>'Member access link']],
 'home'=>['label'=>'Home page','description'=>'Hero, introduction, section headings, calls to action and homepage labels.','fields'=>[
  'home_eyebrow'=>'Hero eyebrow','home_welcome'=>'Hero headline','home_intro'=>'Hero introduction','home_cta_primary'=>'Primary hero button','home_cta_secondary'=>'Secondary hero button','home_trust_line'=>'Trust line','home_hero_note_one_title'=>'Hero card 1 title','home_hero_note_one_text'=>'Hero card 1 text','home_hero_note_two_title'=>'Hero card 2 title','home_hero_note_two_text'=>'Hero card 2 text','home_hero_art_caption'=>'Hero illustration caption','home_stats_label_members'=>'Members statistic label','home_stats_label_projects'=>'Projects statistic label','home_stats_label_events'=>'Events statistic label','home_stats_label_news'=>'News statistic label','home_projects_eyebrow'=>'Projects section eyebrow','home_projects_title'=>'Projects section heading','home_projects_intro'=>'Projects section introduction','home_projects_link'=>'Projects section link','home_projects_card_link'=>'Project card link label','home_projects_empty_title'=>'Projects empty-state heading','home_projects_empty_text'=>'Projects empty-state text','home_news_card_link'=>'News card link label','home_news_empty_title'=>'News empty-state heading','home_news_empty_text'=>'News empty-state text','home_events_card_link'=>'Events card link label','home_events_empty_title'=>'Events empty-state heading','home_events_empty_text'=>'Events empty-state text','home_news_eyebrow'=>'News section eyebrow','home_news_title'=>'News section heading','home_news_intro'=>'News section introduction','home_news_link'=>'News section link','home_events_eyebrow'=>'Events section eyebrow','home_events_title'=>'Events section heading','home_events_intro'=>'Events section introduction','home_events_link'=>'Events section link','home_cta_eyebrow'=>'Closing section eyebrow','home_cta_title'=>'Closing section heading','home_cta_intro'=>'Closing section text','home_cta_member_link'=>'Membership button','home_cta_volunteer_link'=>'Volunteer button']],
 'about'=>['label'=>'About page','description'=>'Organization story, mission, values, introduction and closing links.','fields'=>['about_eyebrow'=>'Page eyebrow','about_intro'=>'Introduction','about_purpose_eyebrow'=>'Purpose eyebrow','about_purpose_title'=>'Purpose heading','about_purpose_text'=>'Purpose text','about_story_title'=>'Story heading','about_history'=>'Organization story','about_mission_title'=>'Mission heading','about_mission'=>'Mission text','about_values_title'=>'Values heading','about_values'=>'Values text','about_cta_title'=>'Closing heading','about_cta_text'=>'Closing text','about_cta_projects_link'=>'Projects button','about_cta_contact_link'=>'Contact button']],
 'projects'=>['label'=>'Projects page','description'=>'Page introduction and empty-state text. Individual projects are managed under Projects & content.','fields'=>['projects_eyebrow'=>'Page eyebrow','projects_title'=>'Page heading','projects_intro'=>'Page introduction','projects_card_link'=>'Project card link label','projects_started_label'=>'Start date prefix','projects_empty_title'=>'Empty-state heading','projects_empty_text'=>'Empty-state text','projects_empty_link'=>'Empty-state link']],
 'news'=>['label'=>'News page','description'=>'Page introduction and empty-state text. Individual news posts are managed under Projects & content.','fields'=>['news_eyebrow'=>'Page eyebrow','news_title'=>'Page heading','news_intro'=>'Page introduction','news_card_link'=>'News card link label','news_empty_title'=>'Empty-state heading','news_empty_text'=>'Empty-state text','news_empty_link'=>'Empty-state link']],
 'events'=>['label'=>'Events page','description'=>'Page introduction, upcoming/past headings and empty-state text. Individual events are managed under Projects & content.','fields'=>['events_eyebrow'=>'Page eyebrow','events_title'=>'Page heading','events_intro'=>'Page introduction','events_upcoming_title'=>'Upcoming section heading','events_upcoming_intro'=>'Upcoming section text','events_details_link'=>'Event details link label','events_capacity_label'=>'Capacity label','events_empty_title'=>'Empty-state heading','events_empty_text'=>'Empty-state text','events_empty_link'=>'Empty-state link','events_past_eyebrow'=>'Past section eyebrow','events_past_title'=>'Past section heading','events_past_intro'=>'Past section text']],
 'gallery'=>['label'=>'Gallery page','description'=>'Page introduction, album section and empty-state text. Albums and photos are managed under Gallery.','fields'=>['gallery_eyebrow'=>'Page eyebrow','gallery_title'=>'Page heading','gallery_intro'=>'Page introduction','gallery_empty_title'=>'Empty-state heading','gallery_empty_text'=>'Empty-state text','gallery_empty_link'=>'Empty-state link','gallery_more_eyebrow'=>'More moments eyebrow','gallery_more_title'=>'More moments heading','gallery_photo_singular'=>'Singular photo label','gallery_photo_plural'=>'Plural photo label','gallery_album_empty_title'=>'Empty album heading','gallery_album_empty_text'=>'Empty album text']],
 'contact'=>['label'=>'Contact page','description'=>'Contact page headings, form labels and feedback messages. Address, phone, email and social links are under Site settings.','fields'=>['contact_eyebrow'=>'Page eyebrow','contact_title'=>'Page heading','contact_intro'=>'Page introduction','contact_form_title'=>'Form heading','contact_form_hint'=>'Form hint','contact_name_label'=>'Name field label','contact_email_label'=>'Email field label','contact_subject_label'=>'Subject field label','contact_message_label'=>'Message field label','contact_submit_label'=>'Submit button label','contact_info_eyebrow'=>'Contact details eyebrow','contact_info_title'=>'Contact details heading','contact_address_label'=>'Address label','contact_phone_label'=>'Phone label','contact_email_info_label'=>'Email label','contact_validation_error'=>'Validation error message','contact_success_message'=>'Success message','contact_failure_message'=>'Failure message']]
];
$defaults=[
 'home_projects_link'=>'All projects','home_news_link'=>'All news','home_events_link'=>'View all events','home_projects_card_link'=>'Discover project','home_projects_empty_title'=>'Projects are on the way','home_projects_empty_text'=>"Check back soon to explore ATACJL's latest community initiatives.",'home_news_card_link'=>'Read story','home_news_empty_title'=>'Stories coming soon','home_news_empty_text'=>'New updates from ATACJL will appear here.','home_events_card_link'=>'Event details','home_events_empty_title'=>'No upcoming events listed','home_events_empty_text'=>'Follow ATACJL updates and come back to see future activities.','projects_card_link'=>'Explore project','projects_started_label'=>'Started','projects_empty_title'=>'Projects are on the way','projects_empty_text'=>"ATACJL's project stories will appear here as they are published.",'projects_empty_link'=>'Contact the association',
 'news_card_link'=>'Read story','news_empty_title'=>'Stories coming soon','news_empty_text'=>"When ATACJL publishes updates and community stories, you'll find them here.",'news_empty_link'=>'Explore our projects',
 'events_upcoming_title'=>'Coming up','events_upcoming_intro'=>'Upcoming published activities','events_details_link'=>'Event details','events_capacity_label'=>'Capacity','events_empty_title'=>'No upcoming events','events_empty_text'=>'There are no published events scheduled right now. Check the past activities below and come back for upcoming announcements.','events_empty_link'=>'Ask us about activities','events_past_eyebrow'=>'FROM THE ARCHIVE','events_past_title'=>'Past activities','events_past_intro'=>'A look back at published ATACJL events.',
 'gallery_empty_title'=>'Our gallery is growing','gallery_empty_text'=>'Photos from ATACJL activities will appear here as albums and images are added.','gallery_empty_link'=>'Explore activities','gallery_more_eyebrow'=>'MORE MOMENTS','gallery_more_title'=>'More from ATACJL','gallery_photo_singular'=>'photo','gallery_photo_plural'=>'photos','gallery_album_empty_title'=>'No photos in this album yet','gallery_album_empty_text'=>'Images added to this album will be shown here.',
 'contact_name_label'=>'Name','contact_email_label'=>'Email','contact_subject_label'=>'Subject','contact_message_label'=>'Message','contact_submit_label'=>'Send message','contact_address_label'=>'Address','contact_phone_label'=>'Phone','contact_email_info_label'=>'Email','contact_validation_error'=>'Please complete all fields with valid information.','contact_success_message'=>'Your message has been received.','contact_failure_message'=>'The message could not be sent right now. Please try again later.'
];
if(post()){
 verifyCsrf();
 $postedTab=(string)($_POST['tab']??'home');
 if(!isset($groups[$postedTab])){$postedTab='home';}
 $pdo=null;
 try{
  $pdo=db();
  $pdo->beginTransaction();
  $save=$pdo->prepare('INSERT INTO site_settings(setting_key,setting_value) VALUES(?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
  foreach($groups[$postedTab]['fields'] as $key=>$label){
   $value=clean($key,20000);
   $save->execute([$key,$value]);
  }
  adminAudit('update','page_content',null,['page'=>$postedTab]);
  $pdo->commit();
  flash('success','Page content saved. Open the public website in another tab to review your changes.');
 }catch(Throwable $error){
  if($pdo instanceof PDO && $pdo->inTransaction())$pdo->rollBack();
  error_log('ATACJL page editor save failed: '.$error->getMessage());
  flash('error','Page content could not be saved. Your last saved version has been kept; please check the connection and try again.');
 }
 redirect('pages.php?tab='.rawurlencode($postedTab));
}
$tab=(string)($_GET['tab']??'home');if(!isset($groups[$tab]))$tab='home';
$fieldSection=static function(string $key,string $page):string{
 if($page==='site'){
  if(str_starts_with($key,'site_'))return 'Organization identity';
  if($key==='footer_location')return 'Footer location';
  if(in_array($key,['footer_explore_title','footer_about_link','footer_projects_link','footer_events_link','footer_news_link','footer_gallery_link'],true))return 'Footer · Explore links';
  return 'Footer · Participation links';
 }
 if($page==='home'){
  if(in_array($key,['home_eyebrow','home_welcome','home_intro','home_cta_primary','home_cta_secondary','home_trust_line'],true)||str_starts_with($key,'home_hero_'))return 'Hero & introduction';
  if(str_starts_with($key,'home_stats_'))return 'Homepage statistics';
  foreach(['projects'=>'Projects section','news'=>'News section','events'=>'Events section'] as $prefix=>$label)if(str_starts_with($key,'home_'.$prefix.'_'))return $label;
  if(str_starts_with($key,'home_cta_'))return 'Closing call to action';
  return 'Homepage content';
 }
 if($page==='about'){
  if(in_array($key,['about_eyebrow','about_intro'],true))return 'Page introduction';
  if(str_contains($key,'purpose'))return 'Purpose';
  if(str_contains($key,'story')||str_contains($key,'history'))return 'Organization story';
  if(str_contains($key,'mission'))return 'Mission';
  if(str_contains($key,'values'))return 'Values';
  if(str_starts_with($key,'about_cta_'))return 'Closing call to action';
 }
 if($page==='events'){
  if(str_starts_with($key,'events_past_'))return 'Past activity archive';
  if(str_starts_with($key,'events_upcoming_'))return 'Upcoming activities';
  if(str_starts_with($key,'events_empty_'))return 'Empty state';
  if(in_array($key,['events_details_link','events_capacity_label'],true))return 'Event details & labels';
  if(in_array($key,['events_eyebrow','events_title','events_intro'],true))return 'Page introduction';
 }
 if($page==='gallery'){
  if(in_array($key,['gallery_eyebrow','gallery_title','gallery_intro'],true))return 'Page introduction';
  if(str_starts_with($key,'gallery_empty_'))return 'Empty state';
  return 'Album & photo labels';
 }
 if($page==='contact'){
  if(in_array($key,['contact_eyebrow','contact_title','contact_intro'],true))return 'Page introduction';
  if(str_starts_with($key,'contact_info_')||str_starts_with($key,'contact_address_')||str_starts_with($key,'contact_phone_')||$key==='contact_email_info_label')return 'Contact information';
  if(str_starts_with($key,'contact_form_')||str_starts_with($key,'contact_name_')||str_starts_with($key,'contact_email_')||str_starts_with($key,'contact_subject_')||str_starts_with($key,'contact_message_')||str_starts_with($key,'contact_submit_'))return 'Contact form';
  return 'Feedback messages';
 }
 if(in_array($page,['projects','news'],true)){
  if(str_ends_with($key,'_eyebrow')||str_ends_with($key,'_title')||str_ends_with($key,'_intro'))return 'Page introduction';
  if(str_contains($key,'empty'))return 'Empty state';
  return 'Card & link labels';
 }
 return 'Page content';
};
$fieldGroups=[];
foreach($groups[$tab]['fields'] as $key=>$label){$fieldGroups[$fieldSection($key,$tab)][$key]=$label;}
$stored=[];foreach(db()->query('SELECT setting_key,setting_value FROM site_settings')->fetchAll() as $row)$stored[$row['setting_key']]=$row['setting_value'];
$pageTitle='Page content editor';require dirname(__DIR__).'/includes/header.php';
?>
<section class="section"><div class="container">
 <div class="section-head"><div><p class="eyebrow">CONTENT MANAGEMENT</p><h1 class="section-title">Edit public pages.</h1><p class="copy">Change the wording shown on each public page without editing PHP files. Dynamic projects, news posts, events, albums and photos have their own create, edit and delete screens.</p></div><a class="btn ghost" href="<?=APP_URL?>/index.php" target="_blank" rel="noopener">Preview website ↗</a></div>
 <nav class="page-editor-tabs" aria-label="Choose a page"><?php foreach($groups as $key=>$group):?><a class="btn <?= $tab===$key?'primary':'ghost' ?>" href="pages.php?tab=<?=e($key)?>"><?=e($group['label'])?></a><?php endforeach;?></nav>
 <div class="form-shell page-editor-shell" style="max-width:none">
  <div class="admin-panel-head"><div><p class="eyebrow"><?=e(strtoupper($tab))?></p><h2><?=e($groups[$tab]['label'])?></h2><p class="muted"><?=e($groups[$tab]['description'])?></p></div></div>
  <div class="page-editor-note">Only settings for the selected page are shown. Open the section you need, make your edits, then save once at the bottom. Collapsed sections keep their current values.</div>
  <form method="post" data-disable-on-submit><?=csrfField()?><input type="hidden" name="tab" value="<?=e($tab)?>">
   <?php $sectionIndex=0;foreach($fieldGroups as $sectionLabel=>$fields):?>
    <details class="page-editor-group" <?=$sectionIndex===0?'open':''?>>
     <summary><span><?=e($sectionLabel)?></span><small><?=count($fields)?> <?=count($fields)===1?'field':'fields'?></small></summary>
     <div class="page-editor-group-content"><div class="grid-2">
      <?php foreach($fields as $key=>$label):$value=$stored[$key]??($defaults[$key]??'');$valueLength=function_exists('mb_strlen')?mb_strlen($value):strlen($value);$isLong=($valueLength>110||str_ends_with($key,'_intro')||str_ends_with($key,'_text')||in_array($key,['home_intro','home_trust_line','about_history','about_mission','about_values','about_purpose_text','contact_intro','contact_form_hint','events_empty_text'],true));?>
       <div class="field"><label for="page-<?=e($key)?>"><?=e($label)?></label><?php if($isLong):?><textarea id="page-<?=e($key)?>" name="<?=e($key)?>" rows="4"><?=e($value)?></textarea><?php else:?><input id="page-<?=e($key)?>" name="<?=e($key)?>" value="<?=e($value)?>"><?php endif;?></div>
      <?php endforeach;?>
     </div></div>
    </details>
   <?php $sectionIndex++;endforeach;?>
   <button class="btn primary" type="submit">Save <?=e($groups[$tab]['label'])?></button>
  </form>
 </div>
 <p class="muted">English is the content baseline. If a section contains content in another language, the public language selector translates visible text through the configured translation API; it does not overwrite the saved content.</p>
</div></section>
<?php require dirname(__DIR__).'/includes/footer.php'; ?>
