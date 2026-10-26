<?php
declare(strict_types=1);
require_once __DIR__.'/_common.php';
$counts=[
 'members'=>adminSafeScalar("SELECT COUNT(*) FROM users WHERE role='member' AND is_active=1 AND is_banned=0",[],'active members'),
 'pending_memberships'=>adminSafeScalar("SELECT COUNT(*) FROM membership_applications WHERE status='pending'",[],'pending memberships'),
 'pending_claims'=>adminSafeScalar("SELECT COUNT(*) FROM participation_claims WHERE status='pending'",[],'pending claims'),
 'upcoming_events'=>adminSafeScalar("SELECT COUNT(*) FROM events WHERE is_published=1 AND event_date>=CURDATE()",[],'upcoming events'),
 'new_messages'=>adminSafeScalar("SELECT COUNT(*) FROM contact_submissions WHERE status='new'",[],'new messages'),
 'projects'=>adminSafeScalar('SELECT COUNT(*) FROM projects',[],'projects'),
 'published_news'=>adminSafeScalar('SELECT COUNT(*) FROM news WHERE is_published=1',[],'published news'),
 'volunteer_applications'=>adminSafeScalar("SELECT COUNT(*) FROM volunteer_applications WHERE status='pending'",[],'volunteer applications')
];
$pendingApplications=adminSafeRows("SELECT a.id,a.created_at,u.full_name,u.email FROM membership_applications a JOIN users u ON u.id=a.user_id WHERE a.status='pending' ORDER BY a.created_at ASC LIMIT 5",[],'pending membership list');
$pendingClaims=adminSafeRows("SELECT c.id,c.participation_date,u.full_name,e.title_en FROM participation_claims c JOIN users u ON u.id=c.member_id JOIN events e ON e.id=c.event_id WHERE c.status='pending' ORDER BY c.created_at ASC LIMIT 5",[],'pending claims list');
$events=adminSafeRows("SELECT e.id,e.title_en,e.event_date,e.is_published,(SELECT COUNT(*) FROM event_members em WHERE em.event_id=e.id) AS assigned_count,(SELECT COUNT(*) FROM event_rsvps r WHERE r.event_id=e.id) AS rsvp_count FROM events e ORDER BY (e.event_date>=CURDATE()) DESC,e.event_date ASC LIMIT 6",[],'event overview');
$recentActivity=adminSafeRows('SELECT a.action,a.entity,a.created_at,u.full_name FROM audit_logs a LEFT JOIN users u ON u.id=a.admin_id ORDER BY a.created_at DESC LIMIT 6',[],'recent audit activity');
$pageTitle='Overview';
require dirname(__DIR__).'/includes/header.php';
?>
<section class="section admin-dashboard">
  <div class="container">
    <div class="admin-welcome"><div><p class="eyebrow">ADMIN WORKSPACE</p><h1 class="section-title">Good to see you, <?=e((string)($admin['full_name']??'Administrator'))?>.</h1><p class="copy">Here is what's happening across ATACJL and what may need your attention today.</p></div><div class="admin-actions"><a class="btn ghost" href="pages.php">Edit page content</a><a class="btn primary" href="content.php?type=projects">＋ Create content</a></div></div>
    <div class="dashboard-cards">
      <a class="dash-card dashboard-metric" href="users.php"><span class="dash-icon">♙</span><small>Active members</small><strong><?=$counts['members']?></strong><span class="metric-foot">Manage accounts →</span></a>
      <a class="dash-card dashboard-metric" href="memberships.php"><span class="dash-icon">◎</span><small>Memberships to review</small><strong><?=$counts['pending_memberships']?></strong><span class="metric-foot">Review applications →</span></a>
      <a class="dash-card dashboard-metric" href="participation.php"><span class="dash-icon">✓</span><small>Pending activity claims</small><strong><?=$counts['pending_claims']?></strong><span class="metric-foot">Verify participation →</span></a>
      <a class="dash-card dashboard-metric" href="messages.php"><span class="dash-icon">✉</span><small>New contact messages</small><strong><?=$counts['new_messages']?></strong><span class="metric-foot">Open inbox →</span></a>
    </div>
    <div class="dashboard-content-grid">
      <section class="admin-panel">
        <div class="admin-panel-head"><div><p class="eyebrow">NEEDS ATTENTION</p><h2>Membership applications</h2></div><a class="text-link" href="memberships.php">View all →</a></div>
        <?php if($pendingApplications):?><div class="attention-list"><?php foreach($pendingApplications as $app):?><div class="attention-row"><span class="attention-avatar"><?=e(strtoupper(mb_substr((string)$app['full_name'],0,1)))?></span><div class="attention-main"><strong><?=e($app['full_name'])?></strong><small><?=e($app['email'])?> · <?=e(date('M j, Y',strtotime($app['created_at'])))?></small></div><span class="status-chip status-pending">Pending</span><a class="mini-action" href="memberships.php#application-<?=$app['id']?>" aria-label="Review application for <?=e($app['full_name'])?>">→</a></div><?php endforeach;?></div><?php else:?><div class="empty-state compact"><span class="empty-icon">✓</span><h3>You're all caught up</h3><p>No membership applications are waiting for review.</p></div><?php endif;?>
      </section>
      <section class="admin-panel">
        <div class="admin-panel-head"><div><p class="eyebrow">MEMBER ACTIVITY</p><h2>Participation claims</h2></div><a class="text-link" href="participation.php">View all →</a></div>
        <?php if($pendingClaims):?><div class="attention-list"><?php foreach($pendingClaims as $claim):?><div class="attention-row"><span class="attention-avatar">✓</span><div class="attention-main"><strong><?=e($claim['full_name'])?></strong><small><?=e($claim['title_en'])?> · <?=e(date('M j, Y',strtotime($claim['participation_date'])))?></small></div><span class="status-chip status-pending">Pending</span></div><?php endforeach;?></div><?php else:?><div class="empty-state compact"><span class="empty-icon">✓</span><h3>No pending claims</h3><p>New participation verification requests will appear here.</p></div><?php endif;?>
      </section>
    </div>
    <section class="admin-panel admin-events-panel" id="event-management">
      <div class="admin-panel-head"><div><p class="eyebrow">PROGRAMME</p><h2>Event management</h2><p class="muted">Monitor registrations and manage member assignments.</p></div><a class="btn ghost" href="content.php?type=events">Manage events</a></div>
      <?php if($events):?><div class="table-wrap"><table><thead><tr><th>Event</th><th>Date</th><th>Status</th><th>RSVPs</th><th>Assigned members</th><th>Actions</th></tr></thead><tbody><?php foreach($events as $ev):?><tr><td><strong><?=e($ev['title_en'])?></strong></td><td><?=e(date('M j, Y',strtotime($ev['event_date'])))?></td><td><span class="status-chip <?=((int)$ev['is_published'])?'status-approved':'status-draft'?>"><?=((int)$ev['is_published'])?'Published':'Draft'?></span></td><td><?=(int)$ev['rsvp_count']?></td><td><?=(int)$ev['assigned_count']?></td><td><div class="admin-actions"><a class="btn ghost" href="event-members.php?event_id=<?=$ev['id']?>">Manage members</a><a class="btn ghost" href="content.php?type=events&id=<?=$ev['id']?>">Edit</a><?php if((int)$ev['is_published']):?><a class="btn ghost" href="<?=APP_URL?>/event-detail.php?id=<?=$ev['id']?>" target="_blank" rel="noopener">View ↗</a><?php endif;?></div></td></tr><?php endforeach;?></tbody></table></div><?php else:?><div class="empty-state compact"><h3>No events yet</h3><p>Create the first event to start managing participation.</p><a class="btn primary" href="content.php?type=events">Create event</a></div><?php endif;?>
    </section>
    <div class="dashboard-lower-grid">
      <section class="admin-panel"><div class="admin-panel-head"><div><p class="eyebrow">PUBLISHING</p><h2>Content overview</h2></div><a class="text-link" href="content.php?type=projects">Open content →</a></div><div class="content-stats"><a href="content.php?type=projects"><span class="content-stat-icon">▧</span><span><strong><?=$counts['projects']?></strong><small>Projects</small></span><b>→</b></a><a href="content.php?type=news"><span class="content-stat-icon">▤</span><span><strong><?=$counts['published_news']?></strong><small>Published stories</small></span><b>→</b></a><a href="content.php?type=events"><span class="content-stat-icon">▦</span><span><strong><?=$counts['upcoming_events']?></strong><small>Upcoming events</small></span><b>→</b></a><a href="volunteers.php"><span class="content-stat-icon">♡</span><span><strong><?=$counts['volunteer_applications']?></strong><small>Volunteer applications</small></span><b>→</b></a></div></section>
      <section class="admin-panel"><div class="admin-panel-head"><div><p class="eyebrow">TRACEABILITY</p><h2>Recent activity</h2></div><span class="muted">Latest 6 actions</span></div><?php if($recentActivity):?><div class="activity-list"><?php foreach($recentActivity as $a):?><div class="activity-row"><span class="activity-dot"></span><div><strong><?=e(ucwords(str_replace('_',' ',(string)$a['action'])))?> · <?=e(ucwords(str_replace('_',' ',(string)$a['entity'])))?></strong><small><?=e($a['full_name']??'Former administrator')?> · <?=e(date('M j, H:i',strtotime($a['created_at'])))?></small></div></div><?php endforeach;?></div><?php else:?><div class="empty-state compact"><h3>No activity recorded yet</h3><p>Administrative changes will be shown here when logged.</p></div><?php endif;?></section>
    </div>
  </div>
</section>
<?php require dirname(__DIR__).'/includes/footer.php'; ?>
