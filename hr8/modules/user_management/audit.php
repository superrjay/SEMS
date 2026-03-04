<?php
declare(strict_types=1);
require_once __DIR__.'/../../config/db.php';require_once __DIR__.'/../../config/auth.php';require_once __DIR__.'/../../config/paths.php';require_once __DIR__.'/../../includes/helpers.php';require_once __DIR__.'/../../models/BaseModel.php';require_once __DIR__.'/../../models/JobPosition.php';
use HR8\Config\Auth;use HR8\Models\AuditLog;
Auth::requireRole(['Admin','HR Manager']);$audit=new AuditLog();
$filters=['module'=>$_GET['module']??'','search'=>$_GET['search']??''];$logs=$audit->getAll($filters,200);$modules=$audit->getModules();
$pageTitle='Audit Trail';include __DIR__.'/../../includes/layout_top.php';
?>
<div class="page-hd uk-margin-bottom"><h3>Audit Trail</h3><p>System activity logs &amp; user actions</p></div>
<div class="filter-bar uk-margin-bottom"><form class="uk-grid-small uk-flex-middle" uk-grid>
<div class="uk-width-1-5@m"><select name="module" class="uk-select uk-form-small"><option value="">All Modules</option><?php foreach($modules as $m):?><option <?=$filters['module']===$m?'selected':''?>><?=sanitize_output($m)?></option><?php endforeach;?></select></div>
<div class="uk-width-1-4@m"><input type="text" name="search" class="uk-input uk-form-small" placeholder="Search action..." value="<?=sanitize_output($filters['search'])?>"></div>
<div class="uk-width-auto"><button class="uk-button uk-button-default uk-button-small">Filter</button> <a href="?" class="uk-button uk-button-text uk-button-small">Reset</a></div>
</form></div>
<div class="uk-card uk-card-default"><div class="uk-card-body np"><div class="uk-overflow-auto"><table class="uk-table uk-table-hover uk-table-small uk-table-divider uk-margin-remove">
<thead><tr><th>#</th><th>User</th><th>Module</th><th>Action</th><th>Record</th><th>IP</th><th>Time</th></tr></thead><tbody>
<?php foreach($logs as $l):?><tr>
<td style="font-size:.78rem;color:#999"><?=$l['log_id']?></td>
<td class="uk-text-bold" style="font-size:.82rem"><?=sanitize_output($l['user_name']??'System')?></td>
<td><span class="bd bd-secondary"><?=sanitize_output($l['module'])?></span></td>
<td style="font-size:.82rem"><?=sanitize_output($l['action'])?></td>
<td style="font-size:.78rem;color:#888"><?=sanitize_output(($l['record_type']??'').($l['record_id']?' #'.$l['record_id']:''))?></td>
<td style="font-size:.72rem;color:#aaa"><?=sanitize_output($l['ip_address']??'')?></td>
<td style="font-size:.72rem;color:#888"><?=format_date($l['created_at'],'M d h:i:s A')?></td>
</tr><?php endforeach;?>
<?php if(empty($logs)):?><tr><td colspan="7" class="uk-text-center uk-text-muted uk-padding-small">No logs</td></tr><?php endif;?>
</tbody></table></div></div></div>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>