<?php
declare(strict_types=1);
require_once __DIR__.'/../../config/db.php';require_once __DIR__.'/../../config/auth.php';require_once __DIR__.'/../../config/paths.php';require_once __DIR__.'/../../includes/helpers.php';require_once __DIR__.'/../../models/BaseModel.php';require_once __DIR__.'/../../models/JobPosition.php';
use HR8\Config\Auth;use HR8\Models\JobPosition;use HR8\Models\Department;
Auth::requireRole(['Admin','HR Manager','HR Staff']);$pm=new JobPosition();$dm=new Department();
if($_SERVER['REQUEST_METHOD']==='POST'&&validate_csrf_token($_POST['csrf_token']??'')){$a=$_POST['form_action']??'';
    if($a==='create'){$pm->create(['title'=>trim($_POST['title']),'department_id'=>(int)$_POST['department_id']?:null,'description'=>trim($_POST['description']??''),'requirements'=>trim($_POST['requirements']??''),'employment_type'=>$_POST['employment_type'],'salary_grade'=>trim($_POST['salary_grade']??''),'slots'=>(int)$_POST['slots'],'is_active'=>1,'created_by'=>Auth::getUserId()]);flash('success','Position created.');redirect($_SERVER['PHP_SELF']);}
    if($a==='toggle'){$p=$pm->find((int)$_POST['position_id']);$pm->update((int)$_POST['position_id'],['is_active'=>$p['is_active']?0:1]);flash('success','Updated.');redirect($_SERVER['PHP_SELF']);}
}
$all=$pm->getAllWithDepartment();$depts=$dm->getActive();$showCreate=($_GET['action']??'')==='create';
$pageTitle='Job Positions';include __DIR__.'/../../includes/layout_top.php';
?>
<div class="page-hd uk-flex uk-flex-between uk-flex-middle uk-margin-bottom"><div><h3>Job Positions</h3><p>Manage open positions &amp; requirements</p></div><a href="?action=create" class="uk-button uk-button-primary uk-button-small"><span uk-icon="icon:plus;ratio:.8" class="uk-margin-small-right"></span>New Position</a></div>
<?php if($showCreate):?>
<div class="uk-card uk-card-default"><div class="uk-card-header"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Create Position</h5></div><div class="uk-card-body"><form method="POST" class="uk-form-stacked"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="create">
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-2@m"><label class="uk-form-label">Title *</label><input type="text" name="title" class="uk-input uk-form-small" required></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Department</label><select name="department_id" class="uk-select uk-form-small"><option value="">Select</option><?php foreach($depts as $d):?><option value="<?=$d['department_id']?>"><?=sanitize_output($d['department_name'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Type</label><select name="employment_type" class="uk-select uk-form-small"><option>Full-Time</option><option>Part-Time</option><option>Contractual</option><option>Probationary</option></select></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Salary Grade</label><input type="text" name="salary_grade" class="uk-input uk-form-small"></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Slots</label><input type="number" name="slots" class="uk-input uk-form-small" value="1" min="1"></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Description</label><textarea name="description" class="uk-textarea uk-form-small" rows="3"></textarea></div>
<div class="uk-width-1-1 uk-margin-top"><button class="uk-button uk-button-primary uk-button-small">Save</button> <a href="?" class="uk-button uk-button-default uk-button-small">Cancel</a></div>
</div></form></div></div>
<?php else:?>
<div class="uk-card uk-card-default"><div class="uk-card-body np"><div class="uk-overflow-auto"><table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-margin-remove">
<thead><tr><th>Title</th><th>Department</th><th>Type</th><th>Slots</th><th>Applicants</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach($all as $p):?><tr><td class="uk-text-bold" style="font-size:.84rem"><?=sanitize_output($p['title'])?></td><td style="font-size:.82rem"><?=sanitize_output($p['department_name']??'—')?></td><td><span class="bd bd-secondary"><?=sanitize_output($p['employment_type'])?></span></td><td><?=$p['slots']?></td><td><span class="bd bd-info"><?=$p['active_applicants']?></span></td><td><?=$p['is_active']?'<span class="bd bd-success">Active</span>':'<span class="bd bd-secondary">Inactive</span>'?></td>
<td><form method="POST" class="uk-display-inline"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="toggle"><input type="hidden" name="position_id" value="<?=$p['position_id']?>"><button class="uk-button uk-button-text uk-button-small" style="font-size:.78rem"><?=$p['is_active']?'Deactivate':'Activate'?></button></form></td></tr><?php endforeach;?>
</tbody></table></div></div></div>
<?php endif;?>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>