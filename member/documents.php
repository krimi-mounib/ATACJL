<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/functions.php';
requireRole('member','admin');
$pageTitle='Member Documents';
require __DIR__.'/../includes/header.php';
?>
<section class="section"><div class="container"><div class="portal-page-heading"><div><p class="eyebrow">MEMBER RESOURCES</p><h1 class="section-title">Documents</h1><p class="copy">A future home for official member resources and documents shared by ATACJL.</p></div></div><div class="documents-coming-soon"><div class="documents-icon">▤</div><div><span class="status-chip status-draft">Not enabled yet</span><h2>Private document access is being prepared</h2><p>Document storage is not enabled in this release. This section should only become available once files can be stored outside the public web root and every download is checked against the member's permissions.</p><a class="text-link" href="profile.php">Return to your member profile →</a></div></div></div></section>
<?php require __DIR__.'/../includes/footer.php'; ?>
