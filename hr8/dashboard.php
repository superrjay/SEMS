<?php
declare(strict_types=1);
require_once __DIR__.'/config/db.php';require_once __DIR__.'/config/auth.php';require_once __DIR__.'/config/paths.php';require_once __DIR__.'/includes/helpers.php';
use HR8\Config\Auth;use HR8\Config\Database;
Auth::requireAuth();$db=Database::getConnection();
$s=[];
$s['active']=(int)$db->query("SELECT COUNT(*) FROM employees WHERE employment_status IN ('Active','Regular','Probationary') AND is_archived=0")->fetchColumn();
$s['total']=(int)$db->query("SELECT COUNT(*) FROM employees WHERE is_archived=0")->fetchColumn();
$s['applicants']=(int)$db->query("SELECT COUNT(*) FROM applicants WHERE status='New'")->fetchColumn();
$s['interviews']=(int)$db->query("SELECT COUNT(*) FROM interview_schedules WHERE status='Scheduled'")->fetchColumn();
$s['clearance']=(int)$db->query("SELECT COUNT(*) FROM clearance_requests WHERE status IN ('Pending','In Progress')")->fetchColumn();
$s['positions']=(int)$db->query("SELECT COUNT(*) FROM job_positions WHERE is_active=1")->fetchColumn();
$logs=$db->query("SELECT al.*,CONCAT(u.first_name,' ',u.last_name) as user_name FROM audit_logs al LEFT JOIN users u ON al.user_id=u.user_id ORDER BY al.created_at DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
$apps=$db->query("SELECT a.*,jp.title as position_title FROM applicants a LEFT JOIN job_positions jp ON a.position_applied_id=jp.position_id ORDER BY a.created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$pageTitle='Dashboard';
include __DIR__.'/includes/layout_top.php';
?>
<div class="page-hd uk-flex uk-flex-between uk-flex-middle uk-margin-bottom">
    <div><h3>Dashboard</h3><p>Welcome back, <?=sanitize_output($_SESSION['user_data']['first_name']??'User')?>.</p></div>
</div>
<div class="uk-child-width-1-2 uk-child-width-1-4@m uk-grid-small" uk-grid>
    <div><div class="st"><div class="uk-flex uk-flex-between uk-flex-middle"><div><div class="st-v" style="color:#1e40af"><?=$s['active']?></div><div class="st-l">Active Employees</div></div><div class="st-i" style="background:#dbeafe"><span uk-icon="icon:users;ratio:1.1" style="color:#1e40af"></span></div></div></div></div>
    <div><div class="st"><div class="uk-flex uk-flex-between uk-flex-middle"><div><div class="st-v" style="color:#166534"><?=$s['applicants']?></div><div class="st-l">New Applicants</div></div><div class="st-i" style="background:#dcfce7"><span uk-icon="icon:user;ratio:1.1" style="color:#166534"></span></div></div></div></div>
    <div><div class="st"><div class="uk-flex uk-flex-between uk-flex-middle"><div><div class="st-v" style="color:#92400e"><?=$s['interviews']?></div><div class="st-l">Pending Interviews</div></div><div class="st-i" style="background:#fef3c7"><span uk-icon="icon:calendar;ratio:1.1" style="color:#92400e"></span></div></div></div></div>
    <div><div class="st"><div class="uk-flex uk-flex-between uk-flex-middle"><div><div class="st-v" style="color:#991b1b"><?=$s['clearance']?></div><div class="st-l">Pending Clearance</div></div><div class="st-i" style="background:#fee2e2"><span uk-icon="icon:sign-out;ratio:1.1" style="color:#991b1b"></span></div></div></div></div>
</div>
<div class="uk-grid-small uk-margin-top" uk-grid>
<div class="uk-width-3-5@m">
    <div class="uk-card uk-card-default"><div class="uk-card-header uk-flex uk-flex-between uk-flex-middle"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Recent Applicants</h5><a href="<?=get_module_path('pre_employment')?>/index.php" class="uk-button uk-button-text" style="font-size:.78rem">View all &rarr;</a></div>
    <div class="uk-card-body np"><table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-margin-remove"><thead><tr><th>Ref</th><th>Name</th><th>Position</th><th>Status</th></tr></thead><tbody>
    <?php foreach($apps as $a):?><tr><td><code><?=sanitize_output($a['reference_no'])?></code></td><td class="uk-text-bold" style="font-size:.84rem"><?=sanitize_output($a['first_name'].' '.$a['last_name'])?></td><td style="font-size:.82rem"><?=sanitize_output($a['position_title']??"\xe2\x80\x94")?></td><td><?=get_status_badge($a['status'])?></td></tr><?php endforeach;?>
    <?php if(empty($apps)):?><tr><td colspan="4" class="uk-text-center uk-text-muted uk-padding-small">No applicants yet</td></tr><?php endif;?>
    </tbody></table></div></div>
</div>
<div class="uk-width-2-5@m">
    <div class="uk-card uk-card-default"><div class="uk-card-header"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Activity</h5></div>
    <div class="uk-card-body" style="max-height:350px;overflow-y:auto;padding:16px 24px">
    <?php foreach($logs as $l):?><div class="uk-flex" style="gap:10px;margin-bottom:14px"><div class="av" style="width:30px;height:30px;font-size:.6rem;flex-shrink:0"><?=strtoupper(substr($l['user_name']??'S',0,1))?></div><div><div style="font-size:.82rem"><span class="uk-text-bold"><?=sanitize_output($l['user_name']??'System')?></span> <?=sanitize_output($l['action'])?></div><div class="uk-text-meta" style="font-size:.72rem"><?=format_date($l['created_at'],'M d, h:i A')?> &middot; <?=sanitize_output($l['module'])?></div></div></div><?php endforeach;?>
    </div></div>
</div></div>
<h5 class="uk-margin-top" style="font-weight:600;font-size:.9rem">Quick Actions</h5>
<div class="uk-child-width-1-3 uk-child-width-1-6@m uk-grid-small" uk-grid>
    <div><a href="<?=get_module_path('pre_employment')?>/index.php?action=create" class="qa"><span uk-icon="icon:user;ratio:1.2"></span><div style="font-size:.78rem;margin-top:8px">New Applicant</div></a></div>
    <div><a href="<?=get_module_path('recruitment')?>/interviews.php?action=schedule" class="qa"><span uk-icon="icon:calendar;ratio:1.2"></span><div style="font-size:.78rem;margin-top:8px">Interview</div></a></div>
    <div><a href="<?=get_module_path('employee_records')?>/create.php" class="qa"><span uk-icon="icon:users;ratio:1.2"></span><div style="font-size:.78rem;margin-top:8px">New Employee</div></a></div>
    <div><a href="<?=get_module_path('performance')?>/index.php?action=create" class="qa"><span uk-icon="icon:star;ratio:1.2"></span><div style="font-size:.78rem;margin-top:8px">Evaluation</div></a></div>
    <div><a href="<?=get_module_path('post_employment')?>/index.php?action=create" class="qa"><span uk-icon="icon:sign-out;ratio:1.2"></span><div style="font-size:.78rem;margin-top:8px">Clearance</div></a></div>
    <div><a href="<?=get_module_path('user_management')?>/audit.php" class="qa"><span uk-icon="icon:history;ratio:1.2"></span><div style="font-size:.78rem;margin-top:8px">Audit Trail</div></a></div>
</div>
<?php include __DIR__.'/includes/layout_bottom.php'; ?>