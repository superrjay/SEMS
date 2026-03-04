<?php
declare(strict_types=1);
require_once __DIR__.'/../../controllers/ClearanceController.php';
$ctrl=new ClearanceController();
if($_SERVER['REQUEST_METHOD']==='POST'&&validate_csrf_token($_POST['csrf_token']??'')){$a=$_POST['form_action']??'';
    if($a==='create'){$id=$ctrl->store(['employee_id'=>(int)$_POST['employee_id'],'separation_type'=>$_POST['separation_type'],'effective_date'=>$_POST['effective_date'],'last_working_day'=>$_POST['last_working_day']?:null,'reason'=>trim($_POST['reason']??'')]);flash($id?'success':'error',$id?'Clearance initiated.':'Failed.');redirect($id?$_SERVER['PHP_SELF']."?view=$id":$_SERVER['PHP_SELF']);}
    if($a==='sign'){$ctrl->updateSignatory((int)$_POST['signatory_id'],['status'=>$_POST['status'],'remarks'=>trim($_POST['remarks']??'')]);flash('success','Updated.');redirect($_SERVER['PHP_SELF'].'?view='.$_POST['clearance_id']);}
    if($a==='complete'){$ctrl->updateClearance((int)$_POST['clearance_id'],['status'=>'Completed']);flash('success','Clearance completed.');redirect($_SERVER['PHP_SELF']);}
    if($a==='exit_interview'){$ctrl->saveExitInterview((int)$_POST['clearance_id'],['exit_interview_notes'=>trim($_POST['exit_interview_notes'])]);flash('success','Exit interview saved.');redirect($_SERVER['PHP_SELF'].'?view='.$_POST['clearance_id']);}
}
$data=$ctrl->index(['status'=>$_GET['status']??'']);$showCreate=($_GET['action']??'')==='create';$showView=isset($_GET['view']);$viewData=$showView?$ctrl->show((int)$_GET['view']):null;
require_once __DIR__.'/../../models/BaseModel.php';require_once __DIR__.'/../../models/Employee.php';
$empModel=new \HR8\Models\Employee();$activeEmps=$showCreate?$empModel->getAllWithDetails():[];
$pageTitle='Clearance';include __DIR__.'/../../includes/layout_top.php';
?>
<div class="page-hd uk-flex uk-flex-between uk-flex-middle uk-margin-bottom"><div><h3>Post-Employment &amp; Clearance</h3><p>Separation, exit interviews &amp; clearance routing</p></div><a href="?action=create" class="uk-button uk-button-primary uk-button-small"><span uk-icon="icon:plus;ratio:.8" class="uk-margin-small-right"></span>New Clearance</a></div>

<?php if($showView&&$viewData):$cl=$viewData['clearance'];?>
<a href="?" class="uk-button uk-button-text uk-margin-bottom" style="font-size:.82rem">&larr; Back</a>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-2@m"><div class="uk-card uk-card-default"><div class="uk-card-header"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Clearance Info</h5></div><div class="uk-card-body" style="padding:16px 24px"><table class="uk-table uk-table-small uk-margin-remove" style="font-size:.84rem">
<tr><th style="width:35%;color:#999;border:0;padding:4px 0">Employee</th><td style="border:0;padding:4px 0"><?=sanitize_output($cl['employee_name'])?> (<?=sanitize_output($cl['employee_no'])?>)</td></tr>
<tr><th style="color:#999;border:0;padding:4px 0">Type</th><td style="border:0;padding:4px 0"><span class="bd bd-danger"><?=sanitize_output($cl['separation_type'])?></span></td></tr>
<tr><th style="color:#999;border:0;padding:4px 0">Effective</th><td style="border:0;padding:4px 0"><?=format_date($cl['effective_date'])?></td></tr>
<tr><th style="color:#999;border:0;padding:4px 0">Status</th><td style="border:0;padding:4px 0"><?=get_status_badge($cl['status'])?></td></tr>
<tr><th style="color:#999;border:0;padding:4px 0">Reason</th><td style="border:0;padding:4px 0"><?=sanitize_output($cl['reason']??'—')?></td></tr>
</table></div></div></div>
<div class="uk-width-1-2@m"><div class="uk-card uk-card-default"><div class="uk-card-header"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Exit Interview</h5></div><div class="uk-card-body">
<?php if($cl['exit_interview_done']):?><div class="bd bd-success uk-margin-small-bottom">Completed <?=format_date($cl['exit_interview_date']??'')?></div><p style="font-size:.84rem"><?=nl2br(sanitize_output($cl['exit_interview_notes']??''))?></p>
<?php else:?><form method="POST"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="exit_interview"><input type="hidden" name="clearance_id" value="<?=$cl['clearance_id']?>"><textarea name="exit_interview_notes" class="uk-textarea uk-form-small uk-margin-small-bottom" rows="3" placeholder="Notes..."></textarea><button class="uk-button uk-button-primary uk-button-small">Save</button></form><?php endif;?>
</div></div></div></div>

<div class="uk-card uk-card-default uk-margin-top"><div class="uk-card-header"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Clearance Signatories</h5></div><div class="uk-card-body np"><table class="uk-table uk-table-middle uk-table-divider uk-margin-remove">
<thead><tr><th>Department</th><th>Status</th><th>Signed</th><th>Remarks</th><th></th></tr></thead><tbody>
<?php foreach($viewData['signatories'] as $sg):?><tr>
<td style="font-size:.84rem;font-weight:600"><?=sanitize_output($sg['department_name'])?></td>
<td><?=get_status_badge($sg['status'])?></td>
<td style="font-size:.78rem"><?=$sg['signed_at']?format_date($sg['signed_at'],'M d h:i A'):'—'?></td>
<td style="font-size:.82rem"><?=sanitize_output($sg['remarks']??'')?></td>
<td><?php if($sg['status']==='Pending'):?><form method="POST" class="uk-flex" style="gap:4px"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="sign"><input type="hidden" name="signatory_id" value="<?=$sg['signatory_id']?>"><input type="hidden" name="clearance_id" value="<?=$cl['clearance_id']?>"><input type="hidden" name="status" value="Cleared"><input type="text" name="remarks" class="uk-input uk-form-small" placeholder="Remarks" style="width:140px;border-radius:6px"><button class="uk-button uk-button-primary uk-button-small" style="white-space:nowrap">Clear</button></form><?php endif;?></td>
</tr><?php endforeach;?></tbody></table></div></div>
<?php $allCleared=!array_filter($viewData['signatories'],fn($x)=>$x['status']==='Pending');if($allCleared&&$cl['status']!=='Completed'):?>
<form method="POST" class="uk-margin-top"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="complete"><input type="hidden" name="clearance_id" value="<?=$cl['clearance_id']?>"><button class="uk-button uk-button-primary">Complete Clearance</button></form>
<?php endif;?>

<?php elseif($showCreate):?>
<div class="uk-card uk-card-default"><div class="uk-card-body"><form method="POST" class="uk-form-stacked"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="create">
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-2@m"><label class="uk-form-label">Employee *</label><select name="employee_id" class="uk-select uk-form-small" required><option value="">Select</option><?php foreach($activeEmps as $e):?><option value="<?=$e['employee_id']?>"><?=sanitize_output($e['employee_no'].' - '.$e['first_name'].' '.$e['last_name'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Type *</label><select name="separation_type" class="uk-select uk-form-small" required><option>Resignation</option><option>Retirement</option><option>Termination</option><option>End of Contract</option><option>AWOL</option></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Effective Date *</label><input type="date" name="effective_date" class="uk-input uk-form-small" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Last Working Day</label><input type="date" name="last_working_day" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Reason</label><input type="text" name="reason" class="uk-input uk-form-small"></div>
<div class="uk-width-1-1 uk-margin-top"><button class="uk-button uk-button-primary uk-button-small">Initiate Clearance</button> <a href="?" class="uk-button uk-button-default uk-button-small">Cancel</a></div>
</div></form></div></div>

<?php else:?>
<div class="uk-flex" style="gap:8px;margin-bottom:16px">
<?php $fs=$_GET['status']??'';foreach([''=> 'All','Pending'=>'Pending','In Progress'=>'In Progress','Completed'=>'Completed'] as $k=>$v):?><a href="?status=<?=urlencode($k)?>" class="uk-button uk-button-small" style="border-radius:20px;font-size:.75rem;<?=$fs===$k?'background:#1e3a5f;color:#fff':'background:#f3f4f6;color:#555'?>"><?=$v?></a><?php endforeach;?></div>
<div class="uk-card uk-card-default"><div class="uk-card-body np"><div class="uk-overflow-auto"><table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-margin-remove">
<thead><tr><th>Employee</th><th>Type</th><th>Effective</th><th>Exit</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach($data['clearances'] as $c):?><tr><td><span class="uk-text-bold" style="font-size:.84rem"><?=sanitize_output($c['employee_name'])?></span><br><span class="uk-text-muted" style="font-size:.72rem"><?=sanitize_output($c['employee_no'])?></span></td><td><span class="bd bd-danger"><?=sanitize_output($c['separation_type'])?></span></td><td style="font-size:.78rem"><?=format_date($c['effective_date'])?></td><td><?=$c['exit_interview_done']?'<span uk-icon="icon:check;ratio:.8" style="color:#16a34a"></span>':'<span uk-icon="icon:close;ratio:.8" style="color:#ccc"></span>'?></td><td><?=get_status_badge($c['status'])?></td><td><a href="?view=<?=$c['clearance_id']?>" class="uk-icon-link" uk-icon="icon:chevron-right;ratio:.8"></a></td></tr><?php endforeach;?>
<?php if(empty($data['clearances'])):?><tr><td colspan="6" class="uk-text-center uk-text-muted uk-padding-small">No clearance requests</td></tr><?php endif;?>
</tbody></table></div></div></div>
<?php endif;?>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>