<?php
declare(strict_types=1);
require_once __DIR__.'/config/db.php';require_once __DIR__.'/config/auth.php';require_once __DIR__.'/config/paths.php';require_once __DIR__.'/includes/helpers.php';
use Clinic\Config\Auth;use Clinic\Config\Database;
Auth::requireAuth();$db=Database::getConnection();
$s=[];
$s['patients']=(int)$db->query("SELECT COUNT(*) FROM patients WHERE status='Active'")->fetchColumn();
$s['consult_today']=(int)$db->query("SELECT COUNT(*) FROM consultations WHERE DATE(consultation_date)=CURDATE()")->fetchColumn();
$s['low_stock']=(int)$db->query("SELECT COUNT(*) FROM medicines WHERE stock_quantity<=reorder_level AND stock_quantity>0")->fetchColumn();
$s['open_incidents']=(int)$db->query("SELECT COUNT(*) FROM health_incidents WHERE status IN ('Open','Under Review')")->fetchColumn();

$recent=$db->query("SELECT c.*,CONCAT(p.first_name,' ',p.last_name) as patient_name,p.student_number FROM consultations c LEFT JOIN patients p ON c.patient_id=p.patient_id ORDER BY c.consultation_date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$logs=$db->query("SELECT al.*,CONCAT(u.first_name,' ',u.last_name) as user_name FROM audit_logs al LEFT JOIN users u ON al.user_id=u.user_id ORDER BY al.created_at DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle='Dashboard';
include __DIR__.'/includes/layout_top.php';
?>
<div class="page-hd uk-flex uk-flex-between uk-flex-middle uk-margin-bottom">
    <div><h3>Dashboard</h3><p>Welcome back, <?=sanitize_output($_SESSION['user_data']['first_name']??'User')?>.</p></div>
</div>
<div class="uk-child-width-1-2 uk-child-width-1-4@m uk-grid-small" uk-grid>
    <div><div class="st"><div class="uk-flex uk-flex-between uk-flex-middle"><div><div class="st-v" style="color:#0f766e"><?=$s['patients']?></div><div class="st-l">Active Patients</div></div><div class="st-i" style="background:#ccfbf1"><span uk-icon="icon:users;ratio:1.1" style="color:#0f766e"></span></div></div></div></div>
    <div><div class="st"><div class="uk-flex uk-flex-between uk-flex-middle"><div><div class="st-v" style="color:#1e40af"><?=$s['consult_today']?></div><div class="st-l">Today's Consults</div></div><div class="st-i" style="background:#dbeafe"><span uk-icon="icon:bolt;ratio:1.1" style="color:#1e40af"></span></div></div></div></div>
    <div><div class="st"><div class="uk-flex uk-flex-between uk-flex-middle"><div><div class="st-v" style="color:#92400e"><?=$s['low_stock']?></div><div class="st-l">Low Stock Meds</div></div><div class="st-i" style="background:#fef3c7"><span uk-icon="icon:grid;ratio:1.1" style="color:#92400e"></span></div></div></div></div>
    <div><div class="st"><div class="uk-flex uk-flex-between uk-flex-middle"><div><div class="st-v" style="color:#991b1b"><?=$s['open_incidents']?></div><div class="st-l">Open Incidents</div></div><div class="st-i" style="background:#fee2e2"><span uk-icon="icon:warning;ratio:1.1" style="color:#991b1b"></span></div></div></div></div>
</div>
<div class="uk-grid-small uk-margin-top" uk-grid>
<div class="uk-width-3-5@m">
    <div class="uk-card uk-card-default"><div class="uk-card-header uk-flex uk-flex-between uk-flex-middle"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Recent Consultations</h5><a href="<?=get_module_path('consultations')?>/index.php" class="uk-button uk-button-text" style="font-size:.78rem">View all &rarr;</a></div>
    <div class="uk-card-body np"><table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-margin-remove"><thead><tr><th>Patient</th><th>Complaint</th><th>Date</th><th>Status</th></tr></thead><tbody>
    <?php foreach($recent as $c):?><tr>
    <td><span class="uk-text-bold" style="font-size:.84rem"><?=sanitize_output($c['patient_name']??'—')?></span><br><span class="uk-text-meta" style="font-size:.72rem"><?=sanitize_output($c['student_number']??'')?></span></td>
    <td style="font-size:.82rem"><?=sanitize_output(substr($c['chief_complaint']??'',0,35))?><?=strlen($c['chief_complaint']??'')>35?'...':''?></td>
    <td class="uk-text-muted" style="font-size:.78rem"><?=format_date($c['consultation_date'],'M d, h:i A')?></td>
    <td><?=get_status_badge($c['status'])?></td></tr><?php endforeach;?>
    <?php if(empty($recent)):?><tr><td colspan="4" class="uk-text-center uk-text-muted uk-padding-small">No consultations yet</td></tr><?php endif;?>
    </tbody></table></div></div>
</div>
<div class="uk-width-2-5@m">
    <div class="uk-card uk-card-default"><div class="uk-card-header"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Activity</h5></div>
    <div class="uk-card-body" style="max-height:350px;overflow-y:auto;padding:16px 24px">
    <?php foreach($logs as $l):?><div class="uk-flex" style="gap:10px;margin-bottom:14px"><div class="av" style="width:30px;height:30px;font-size:.6rem;flex-shrink:0"><?=strtoupper(substr($l['user_name']??'S',0,1))?></div><div><div style="font-size:.82rem"><span class="uk-text-bold"><?=sanitize_output($l['user_name']??'System')?></span> <?=sanitize_output($l['action'])?></div><div class="uk-text-meta" style="font-size:.72rem"><?=format_date($l['created_at'],'M d, h:i A')?> &middot; <?=sanitize_output($l['module'])?></div></div></div><?php endforeach;?>
    <?php if(empty($logs)):?><div class="uk-text-center uk-text-muted uk-padding-small" style="font-size:.84rem">No activity yet</div><?php endif;?>
    </div></div>
</div></div>
<h5 class="uk-margin-top" style="font-weight:600;font-size:.9rem">Quick Actions</h5>
<div class="uk-child-width-1-3 uk-child-width-1-6@m uk-grid-small" uk-grid>
    <div><a href="<?=get_module_path('medical_records')?>/index.php?action=register" class="qa"><span uk-icon="icon:user;ratio:1.2"></span><div style="font-size:.78rem;margin-top:8px">New Patient</div></a></div>
    <div><a href="<?=get_module_path('consultations')?>/index.php?action=create" class="qa"><span uk-icon="icon:bolt;ratio:1.2"></span><div style="font-size:.78rem;margin-top:8px">New Consult</div></a></div>
    <div><a href="<?=get_module_path('medicine_inventory')?>/dispensing.php" class="qa"><span uk-icon="icon:grid;ratio:1.2"></span><div style="font-size:.78rem;margin-top:8px">Dispense</div></a></div>
    <div><a href="<?=get_module_path('medical_clearance')?>/index.php?action=create" class="qa"><span uk-icon="icon:check;ratio:1.2"></span><div style="font-size:.78rem;margin-top:8px">Clearance</div></a></div>
    <div><a href="<?=get_module_path('health_incidents')?>/index.php?action=create" class="qa"><span uk-icon="icon:warning;ratio:1.2"></span><div style="font-size:.78rem;margin-top:8px">Report Incident</div></a></div>
    <div><a href="<?=get_module_path('user_management')?>/audit.php" class="qa"><span uk-icon="icon:history;ratio:1.2"></span><div style="font-size:.78rem;margin-top:8px">Audit Trail</div></a></div>
</div>
<?php include __DIR__.'/includes/layout_bottom.php'; ?>
