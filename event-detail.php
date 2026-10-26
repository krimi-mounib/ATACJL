<?php
declare(strict_types=1);
require_once __DIR__.'/includes/functions.php';
$lang=lang();
$id=(int)($_GET['id']??0);
$s=db()->prepare('SELECT * FROM events WHERE id=? AND is_published=1');$s->execute([$id]);$event=$s->fetch();
if(!$event){http_response_code(404);$pageTitle='Event not found';require __DIR__.'/includes/header.php';?><section class="section"><div class="container empty-state"><span class="empty-icon">⌁</span><h1>Event not found</h1><p>This event may have been removed or is no longer published.</p><a class="btn primary" href="events.php">Browse events</a></div></section><?php require __DIR__.'/includes/footer.php';exit;}
$error='';
if(post()){
  verifyCsrf();
  if(isset($_POST['action'])&&$_POST['action']==='rsvp'){
    $u=requireVerifiedUser();
    $pdo=db();
    try{
      $pdo->beginTransaction();
      // Lock the event row while checking capacity to avoid oversubscribing concurrent requests.
      $locked=$pdo->prepare('SELECT capacity FROM events WHERE id=? AND is_published=1 AND event_date>=CURDATE() FOR UPDATE');
      $locked->execute([$id]);$freshEvent=$locked->fetch();
      if(!$freshEvent)throw new RuntimeException('This event is no longer available for registration.');
      $already=$pdo->prepare('SELECT id FROM event_rsvps WHERE event_id=? AND user_id=? LIMIT 1');
      $already->execute([$id,$u['id']]);
      if($already->fetch())throw new RuntimeException('You are already registered for this event.');
      if($freshEvent['capacity']!==null&&(int)$freshEvent['capacity']>0){
        $count=$pdo->prepare('SELECT COUNT(*) FROM event_rsvps WHERE event_id=?');$count->execute([$id]);
        if((int)$count->fetchColumn()>=(int)$freshEvent['capacity'])throw new RuntimeException('This event is full.');
      }
      // event_rsvps has four columns in this insert; the previous five-placeholder statement failed.
      $insert=$pdo->prepare('INSERT INTO event_rsvps(event_id,user_id,name,email) VALUES(?,?,?,?)');
      $insert->execute([$id,$u['id'],$u['full_name'],$u['email']]);
      $pdo->commit();
      flash('success','Your place has been reserved. Your registration is confirmed.');
      redirect('event-detail.php?id='.$id);
    }catch(Throwable $e){
      if($pdo->inTransaction())$pdo->rollBack();
      if($e instanceof RuntimeException){$error=$e->getMessage();}
      elseif($e instanceof PDOException&&((string)($e->errorInfo[0]??$e->getCode()))==='23000'){$error='You are already registered for this event.';}
      else{error_log('Event RSVP failed: '.$e->getMessage());$error='We could not complete your registration. Please try again.';}
    }
  }elseif(isset($_POST['like'])){
    toggleLike('event',$id);
    redirect('event-detail.php?id='.$id);
  }elseif(isset($_POST['comment'])){
    $u=requireVerifiedUser();$body=clean('body',2000);
    if($body!=='')db()->prepare('INSERT INTO comments(user_id,content_type,content_id,body) VALUES(?,?,?,?)')->execute([$u['id'],'event',$id,$body]);
    redirect('event-detail.php?id='.$id.'#discussion');
  }
}
$comments=commentsFor('event',$id);
$rsvpCountStmt=db()->prepare('SELECT COUNT(*) FROM event_rsvps WHERE event_id=?');$rsvpCountStmt->execute([$id]);$rsvpCount=(int)$rsvpCountStmt->fetchColumn();
$alreadyRegistered=false;
if($user=currentUser()){$mine=db()->prepare('SELECT id FROM event_rsvps WHERE event_id=? AND user_id=? LIMIT 1');$mine->execute([$id,$user['id']]);$alreadyRegistered=(bool)$mine->fetchColumn();}
$pastEvent=$event['event_date']<date('Y-m-d');
$full=$event['capacity']!==null&&(int)$event['capacity']>0&&$rsvpCount>=(int)$event['capacity'];
$pageTitle=localTitle($event,$lang);require __DIR__.'/includes/header.php';
?>
<section class="section"><div class="container event-detail-layout">
  <div class="event-detail-main"><p class="eyebrow">ATACJL EVENT · <?=e(date('l, F j, Y',strtotime($event['event_date'])))?></p><h1 class="section-title"><?=e(localTitle($event,$lang))?></h1><p class="event-detail-subtitle">An opportunity to meet, participate and create something together.</p>
    <?php if($event['featured_image']):?><img class="detail-image" src="<?=e($event['featured_image'])?>" alt="<?=e(localTitle($event,$lang))?>"><?php else:?><div class="event-detail-placeholder"><span class="hero-art-mark"><img src="<?=APP_URL?>/assets/images/logo.png" alt="ATACJL"></span><strong>Culture · Youth · Community</strong></div><?php endif;?>
    <div class="event-description"><p><?=nl2br(e(localDescription($event,$lang)))?></p></div>
    <section class="discussion-panel" id="discussion"><div class="discussion-heading"><div><p class="eyebrow">COMMUNITY</p><h2>Discussion</h2></div><span class="pill"><?=count($comments)?> comments</span></div>
      <?php if(currentUser()&&((int)currentUser()['email_verified']||currentUser()['role']==='admin')):?><form method="post" class="comment-form"><?=csrfField()?><label for="event-comment">Join the conversation</label><textarea id="event-comment" name="body" maxlength="2000" placeholder="Share a comment or question…" required></textarea><button class="btn primary" name="comment" value="1">Post comment</button></form><?php else:?><p class="copy">Sign in with a verified account to join the discussion.</p><a class="btn ghost" href="<?=APP_URL?>/login.php">Sign in to comment</a><?php endif;?>
      <?php if($comments):?><div class="comment-list"><?php foreach($comments as $c):?><article class="comment"><div class="comment-person"><span class="attention-avatar"><?=e(strtoupper(mb_substr((string)$c['full_name'],0,1)))?></span><div><strong><?=e($c['full_name'])?></strong><div class="comment-meta"><?=e(date('M j, Y · H:i',strtotime($c['created_at'])))?></div></div></div><p><?=nl2br(e($c['body']))?></p></article><?php endforeach;?></div><?php else:?><div class="empty-state compact"><h3>Start the conversation</h3><p>Be the first to leave a comment about this event.</p></div><?php endif;?>
    </section>
  </div>
  <aside class="event-detail-aside"><div class="event-summary-card"><p class="eyebrow">EVENT DETAILS</p><div class="event-detail-fact"><span class="fact-icon">▦</span><div><small>Date</small><strong><?=e(date('l, F j, Y',strtotime($event['event_date'])))?></strong></div></div><div class="event-detail-fact"><span class="fact-icon">◷</span><div><small>Time</small><strong><?=e(substr((string)$event['event_time'],0,5))?></strong></div></div><div class="event-detail-fact"><span class="fact-icon">⌖</span><div><small>Location</small><strong><?=e($event['location'])?></strong></div></div><div class="event-detail-fact"><span class="fact-icon">♧</span><div><small>Registration</small><strong><?php if($event['capacity']):?><?=max(0,(int)$event['capacity']-$rsvpCount)?> of <?=$event['capacity']?> places left<?php else:?>Open registration<?php endif;?></strong></div></div><div class="capacity-track"><span style="width:<?=($event['capacity']&&((int)$event['capacity']>0))?min(100,round(($rsvpCount/(int)$event['capacity'])*100)):0?>%"></span></div><p class="muted"><?=$rsvpCount?> <?=($rsvpCount===1)?'person has':'people have'?> registered.</p>
    <?php if($pastEvent):?><div class="registration-state registration-full"><span class="state-check">✓</span><div><strong>Event has ended</strong><small>Registration is closed for past events.</small></div></div><?php elseif($alreadyRegistered):?><div class="registration-state"><span class="state-check">✓</span><div><strong>You're registered</strong><small>Your place is reserved for this event.</small></div></div><?php elseif($full):?><div class="registration-state registration-full"><span class="state-check">!</span><div><strong>Event is full</strong><small>Registration has reached capacity.</small></div></div><?php elseif(currentUser()&&((int)currentUser()['email_verified']||currentUser()['role']==='admin')):?><form method="post" class="rsvp-form"><?=csrfField()?><input type="hidden" name="action" value="rsvp"><button class="btn primary" type="submit">Reserve my place <span aria-hidden="true">→</span></button><small>You'll receive an on-screen confirmation.</small></form><?php elseif(currentUser()):?><p class="muted">Your account must be verified before you can register.</p><?php else:?><a class="btn primary rsvp-cta" href="<?=APP_URL?>/login.php">Sign in to RSVP <span aria-hidden="true">→</span></a><small class="login-note">New to ATACJL? <a href="<?=APP_URL?>/register.php">Create an account</a>.</small><?php endif;?>
    <?php if($error):?><div class="inline-flash error" role="alert"><?=e($error)?></div><?php endif;?></div><a class="back-link" href="<?=APP_URL?>/events.php">← All events</a></aside>
</div></section>
<?php require __DIR__.'/includes/footer.php'; ?>
