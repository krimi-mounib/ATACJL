<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/includes/functions.php';
$admin=requireRole('admin');

/** Safe read helpers keep the admin workspace usable when an older DB is missing a newer optional table. */
function adminSafeRows(string $sql, array $params=[], string $context='admin query'): array {
    try { $stmt=db()->prepare($sql); $stmt->execute($params); return $stmt->fetchAll(); }
    catch (Throwable $error) { error_log('ATACJL '.$context.' failed: '.$error->getMessage()); return []; }
}
function adminSafeScalar(string $sql, array $params=[], string $context='admin metric'): int {
    try { $stmt=db()->prepare($sql); $stmt->execute($params); return (int)$stmt->fetchColumn(); }
    catch (Throwable $error) { error_log('ATACJL '.$context.' failed: '.$error->getMessage()); return 0; }
}
function adminAudit(string $action,string $entity,?int $id=null,array $details=[]):void {
    try { db()->prepare('INSERT INTO audit_logs(admin_id,action,entity,entity_id,details) VALUES(?,?,?,?,?)')->execute([currentUser()['id'],$action,$entity,$id,json_encode($details)]); }
    catch (Throwable $error) { error_log('ATACJL audit log failed: '.$error->getMessage()); }
}
