<?php
$script=(string)($_SERVER['SCRIPT_NAME']??'');
if(str_contains($script,'/admin/')): ?>
    </main>
    <footer class="workspace-footer"><span>© <?=date('Y')?> ATACJL</span><span>Administration workspace · Hammam Sousse</span></footer>
  </div>
</div>
<?php elseif(str_contains($script,'/member/')): ?>
    </main>
    <footer class="workspace-footer"><span>© <?=date('Y')?> ATACJL</span><span>Member space · Hammam Sousse</span></footer>
  </div>
</div>
<?php else: ?>
</main>
<footer class="footer">
  <div class="container footer-grid">
    <div class="footer-about"><a class="footer-brand" href="<?=APP_URL?>/index.php"><img src="<?=APP_URL?>/assets/images/logo.png" alt="ATACJL logo"><span><strong>ATACJL</strong><small><?=e(setting('site_tagline','Culture · Youth · Community'))?></small></span></a><p><?=e(setting('site_full_name','Tunisian Association for Cultural Animation, Youth and Recreation in Hammam Sousse.'))?></p><p class="footer-location"><?=e(setting('footer_location','Hammam Sousse · Sousse · Tunisia'))?></p></div>
    <div><h4><?=e(setting('footer_explore_title','Explore'))?></h4><a href="<?=APP_URL?>/about.php"><?=e(setting('footer_about_link','About ATACJL'))?></a><a href="<?=APP_URL?>/projects.php"><?=e(setting('footer_projects_link','Projects'))?></a><a href="<?=APP_URL?>/events.php"><?=e(setting('footer_events_link','Events'))?></a><a href="<?=APP_URL?>/news.php"><?=e(setting('footer_news_link','News'))?></a><a href="<?=APP_URL?>/gallery.php"><?=e(setting('footer_gallery_link','Gallery'))?></a></div>
    <div><h4><?=e(setting('footer_participate_title','Take part'))?></h4><a href="<?=APP_URL?>/membership.php"><?=e(setting('footer_member_link','Become a member'))?></a><a href="<?=APP_URL?>/volunteer.php"><?=e(setting('footer_volunteer_link','Volunteer'))?></a><a href="<?=APP_URL?>/contact.php"><?=e(setting('footer_contact_link','Contact'))?></a><a href="<?=APP_URL?>/register.php"><?=e(setting('footer_register_link','Create account'))?></a></div>
  </div>
  <div class="container footer-bottom"><span>© <?=date('Y')?> ATACJL · Hammam Sousse, Tunisia</span><a href="<?=APP_URL?>/login.php"><?=e(setting('footer_member_access_link','Member access'))?></a></div>
</footer>
<?php endif; ?>
<script src="<?=APP_URL?>/assets/js/app.js" defer></script>
</body></html>
