<?php
declare(strict_types=1);
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../config/auth.php';
require_once __DIR__.'/../../config/paths.php';
require_once __DIR__.'/../../includes/helpers.php';
require_once __DIR__.'/../../models/BaseModel.php';
require_once __DIR__.'/../../models/Clinic.php';
use Clinic\Config\Auth;
Auth::requireRole(['Admin']);

$auditLog=new Clinic\Models\AuditLog();
$filters=['module'=>$_GET['module']??'','search'=>$_GET['search']??''];
$logs=$auditLog->getAll($filters, 200);
$modules=$auditLog->getModules();

$pageTitle='Audit Trail';include __DIR__.'/../../includes/layout_top.php';
?>

<div class="page-hd">
<div><h3>User Activity Logs &amp; Audit Trail</h3><p>Track all user actions across the system</p></div>
<a href="<?=get_module_path('user_management')?>/index.php" class="btn btn-s">← Users</a>
</div>

<!-- Filter -->
<div class="filter-bar mb-16"><form class="flex flex-middle gap-8 flex-wrap">
<select name="module" class="uk-select uk-form-small" style="border-radius:8px;max-width:180px">
<option value="">All Modules</option>
<?php foreach($modules as $m):?><option <?=$filters['module']===$m?'selected':''?>><?=sanitize_output($m)?></option><?php endforeach;?>
</select>
<input type="text" name="search" class="uk-input uk-form-small" style="border-radius:8px;max-width:240px" placeholder="Search action..." value="<?=sanitize_output($filters['search'])?>">
<button class="btn btn-sm">Filter</button>
<?php if($filters['module']||$filters['search']):?><a href="?" class="btn btn-sm btn-txt">Clear</a><?php endif;?>
</form></div>

<div class="card"><div class="card-hd"><h5>Activity Log</h5><span class="text-xs text-muted"><?=count($logs)?> entries</span></div>
<div class="card-bd np"><div class="overflow-x">
<table class="tbl"><thead><tr><th>ID</th><th>Date/Time</th><th>User</th><th>Module</th><th>Action</th><th>Record</th><th>IP Address</th></tr></thead><tbody>
<?php foreach($logs as $l):?><tr>
<td class="text-xs text-muted"><?=$l['log_id']?></td>
<td class="text-xs text-muted" style="white-space:nowrap"><?=format_date($l['created_at'],'M d, Y')?><br><?=format_date($l['created_at'],'h:i:s A')?></td>
<td><span class="text-bold text-sm"><?=sanitize_output($l['user_name']??'System')?></span></td>
<td><span class="bd bd-info"><?=sanitize_output($l['module'])?></span></td>
<td class="text-sm"><?=sanitize_output($l['action'])?></td>
<td class="text-xs text-muted"><?php if($l['record_type']):?><?=sanitize_output($l['record_type'])?> #<?=$l['record_id']?><?php else:?>—<?php endif;?></td>
<td class="text-xs text-muted" style="font-family:monospace"><?=sanitize_output($l['ip_address']??'—')?></td>
</tr><?php endforeach;?>
<?php if(empty($logs)):?><tr><td colspan="7" class="empty">No activity recorded yet</td></tr><?php endif;?>
</tbody></table>
</div></div></div>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>
