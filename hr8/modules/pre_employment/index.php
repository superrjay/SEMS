<?php
declare(strict_types=1);
require_once __DIR__.'/../../controllers/PreEmploymentController.php';
$ctrl=new PreEmploymentController();
if($_SERVER['REQUEST_METHOD']==='POST'&&validate_csrf_token($_POST['csrf_token']??'')){
    $a=$_POST['form_action']??'';
    if($a==='create'){$id=$ctrl->store(['first_name'=>trim($_POST['first_name']),'last_name'=>trim($_POST['last_name']),'middle_name'=>trim($_POST['middle_name']??''),'email'=>trim($_POST['email']),'phone'=>trim($_POST['phone']??''),'address'=>trim($_POST['address']??''),'date_of_birth'=>$_POST['date_of_birth']?:null,'gender'=>$_POST['gender']?:null,'civil_status'=>$_POST['civil_status']?:'Single','position_applied_id'=>(int)$_POST['position_applied_id']?:null,'source'=>trim($_POST['source']??''),'notes'=>trim($_POST['notes']??'')]);flash($id?'success':'error',$id?'Applicant created.':'Failed.');redirect($_SERVER['PHP_SELF']);}
    if($a==='update_status'){$ctrl->updateStatus((int)$_POST['applicant_id'],$_POST['status']);flash('success','Status updated.');redirect($_SERVER['PHP_SELF'].'?view='.$_POST['applicant_id']);}
    if($a==='screen'){$ctrl->screen((int)$_POST['applicant_id'],['screening_date'=>$_POST['screening_date'],'documents_complete'=>isset($_POST['documents_complete'])?1:0,'qualifications_met'=>isset($_POST['qualifications_met'])?1:0,'remarks'=>trim($_POST['remarks']??''),'result'=>$_POST['result']]);flash('success','Screening recorded.');redirect($_SERVER['PHP_SELF'].'?view='.$_POST['applicant_id']);}
}
$filters=['status'=>$_GET['status']??'','search'=>$_GET['search']??'','position_id'=>$_GET['position_id']??''];
$data=$ctrl->index($filters);$showCreate=($_GET['action']??'')==='create';$showView=isset($_GET['view']);$viewData=$showView?$ctrl->show((int)$_GET['view']):null;$positions=$ctrl->getPositions();
$pageTitle='Pre-Employment';include __DIR__.'/../../includes/layout_top.php';
?>
<div class="page-hd uk-flex uk-flex-between uk-flex-middle uk-margin-bottom">
<div><h3>Pre-Employment Management</h3><p>Applicant profiling, screening &amp; document submission</p></div>
<a href="?action=create" class="uk-button uk-button-primary uk-button-small"><span uk-icon="icon:plus;ratio:.8" class="uk-margin-small-right"></span>New Applicant</a></div>

<!-- Status Pills -->
<div class="uk-flex uk-flex-wrap" style="gap:8px;margin-bottom:20px">
<?php foreach(['New','Screening','Shortlisted','For Interview','Hired','Rejected'] as $st):$cnt=$data['status_counts'][$st]??0;$active=$filters['status']===$st;?>
<a href="?status=<?=$st?>" class="uk-button uk-button-small" style="border-radius:20px;font-size:.75rem;<?=$active?'background:#1e3a5f;color:#fff':'background:#f3f4f6;color:#555'?>"><?=$st?> <span style="margin-left:4px;opacity:.7"><?=$cnt?></span></a>
<?php endforeach;?>
<a href="?" class="uk-button uk-button-small" style="border-radius:20px;font-size:.75rem;background:#fff;color:#999;border:1px solid #e5e5e5">Clear</a>
</div>

<?php if($showView&&$viewData):$ap=$viewData['applicant'];?>
<!-- DETAIL VIEW -->
<a href="?" class="uk-button uk-button-text uk-margin-bottom" style="font-size:.82rem">&larr; Back to list</a>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-2@m">
<div class="uk-card uk-card-default"><div class="uk-card-header"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem"><?=sanitize_output($ap['first_name'].' '.$ap['last_name'])?></h5><div class="uk-text-meta"><code><?=sanitize_output($ap['reference_no'])?></code> &middot; <?=get_status_badge($ap['status'])?></div></div>
<div class="uk-card-body" style="padding:20px 24px"><table class="uk-table uk-table-small uk-margin-remove" style="font-size:.84rem">
<tr><th style="width:35%;color:#999;border:0;padding:4px 0">Position</th><td style="border:0;padding:4px 0"><?=sanitize_output($ap['position_title']??'—')?></td></tr>
<tr><th style="color:#999;border:0;padding:4px 0">Department</th><td style="border:0;padding:4px 0"><?=sanitize_output($ap['department_name']??'—')?></td></tr>
<tr><th style="color:#999;border:0;padding:4px 0">Email</th><td style="border:0;padding:4px 0"><?=sanitize_output($ap['email'])?></td></tr>
<tr><th style="color:#999;border:0;padding:4px 0">Phone</th><td style="border:0;padding:4px 0"><?=sanitize_output($ap['phone']??'—')?></td></tr>
<tr><th style="color:#999;border:0;padding:4px 0">Gender</th><td style="border:0;padding:4px 0"><?=sanitize_output($ap['gender']??'—')?></td></tr>
<tr><th style="color:#999;border:0;padding:4px 0">Applied</th><td style="border:0;padding:4px 0"><?=format_date($ap['application_date']??'')?></td></tr>
</table></div></div></div>
<div class="uk-width-1-2@m">
<div class="uk-card uk-card-default"><div class="uk-card-header"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Update Status</h5></div>
<div class="uk-card-body"><form method="POST" class="uk-flex uk-flex-middle" style="gap:8px"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="update_status"><input type="hidden" name="applicant_id" value="<?=$ap['applicant_id']?>">
<select name="status" class="uk-select uk-form-small" style="border-radius:8px"><?php foreach(['New','Screening','Shortlisted','For Interview','Interviewed','For Exam','Examined','Ranked','Offered','Hired','Rejected','Withdrawn','Pooled'] as $ss):?><option <?=$ap['status']===$ss?'selected':''?>><?=$ss?></option><?php endforeach;?></select>
<button class="uk-button uk-button-primary uk-button-small">Save</button></form></div></div>
<div class="uk-card uk-card-default uk-margin-top"><div class="uk-card-header uk-flex uk-flex-between uk-flex-middle"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Screening</h5>
<a class="uk-button uk-button-text uk-button-small" style="font-size:.78rem" href="#add-screen" uk-toggle>+ Add</a></div>
<div class="uk-card-body np">
<?php if(!empty($viewData['screenings'])):?><table class="uk-table uk-table-small uk-table-divider uk-margin-remove"><thead><tr><th>Date</th><th>Docs</th><th>Quals</th><th>Result</th></tr></thead><tbody>
<?php foreach($viewData['screenings'] as $sc):?><tr><td style="font-size:.82rem"><?=format_date($sc['screening_date'])?></td><td><?=$sc['documents_complete']?'<span uk-icon="icon:check;ratio:.8" style="color:#16a34a"></span>':'<span uk-icon="icon:close;ratio:.8" style="color:#dc2626"></span>'?></td><td><?=$sc['qualifications_met']?'<span uk-icon="icon:check;ratio:.8" style="color:#16a34a"></span>':'<span uk-icon="icon:close;ratio:.8" style="color:#dc2626"></span>'?></td><td><?=get_status_badge($sc['result'])?></td></tr><?php endforeach;?>
</tbody></table><?php else:?><div class="uk-padding-small uk-text-center uk-text-muted" style="font-size:.82rem">No screenings yet</div><?php endif;?>
</div></div>
<!-- Screening Modal -->
<div id="add-screen" uk-modal><div class="uk-modal-dialog"><div class="uk-modal-header"><h5 class="uk-modal-title" style="font-size:.95rem">Add Screening</h5></div>
<form method="POST"><div class="uk-modal-body"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="screen"><input type="hidden" name="applicant_id" value="<?=$ap['applicant_id']?>">
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-2"><label class="uk-form-label">Date</label><input type="date" name="screening_date" class="uk-input uk-form-small" value="<?=date('Y-m-d')?>" required></div>
<div class="uk-width-1-2"><label class="uk-form-label">Result</label><select name="result" class="uk-select uk-form-small" required><option>Pending</option><option>Passed</option><option>Failed</option></select></div>
<div class="uk-width-1-2"><label><input type="checkbox" name="documents_complete" class="uk-checkbox"> Docs Complete</label></div>
<div class="uk-width-1-2"><label><input type="checkbox" name="qualifications_met" class="uk-checkbox"> Qualifications Met</label></div>
<div class="uk-width-1-1"><label class="uk-form-label">Remarks</label><textarea name="remarks" class="uk-textarea uk-form-small" rows="2"></textarea></div>
</div></div><div class="uk-modal-footer uk-text-right"><button class="uk-button uk-button-default uk-modal-close uk-button-small" type="button">Cancel</button> <button class="uk-button uk-button-primary uk-button-small">Save</button></div></form></div></div>
</div></div>

<?php elseif($showCreate):?>
<!-- CREATE FORM -->
<a href="?" class="uk-button uk-button-text uk-margin-bottom" style="font-size:.82rem">&larr; Back</a>
<div class="uk-card uk-card-default"><div class="uk-card-header"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">New Applicant</h5></div>
<div class="uk-card-body"><form method="POST" class="uk-form-stacked"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="create">
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-3@m"><label class="uk-form-label">First Name *</label><input type="text" name="first_name" class="uk-input uk-form-small" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Middle Name</label><input type="text" name="middle_name" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Last Name *</label><input type="text" name="last_name" class="uk-input uk-form-small" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Email *</label><input type="email" name="email" class="uk-input uk-form-small" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Phone</label><input type="text" name="phone" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Date of Birth</label><input type="date" name="date_of_birth" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Gender</label><select name="gender" class="uk-select uk-form-small"><option value="">Select</option><option>Male</option><option>Female</option><option>Other</option></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Civil Status</label><select name="civil_status" class="uk-select uk-form-small"><option>Single</option><option>Married</option><option>Widowed</option><option>Separated</option></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Position *</label><select name="position_applied_id" class="uk-select uk-form-small" required><option value="">Select</option><?php foreach($positions as $p):?><option value="<?=$p['position_id']?>"><?=sanitize_output($p['title'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-1"><label class="uk-form-label">Address</label><input type="text" name="address" class="uk-input uk-form-small"></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Source</label><input type="text" name="source" class="uk-input uk-form-small" placeholder="JobStreet, LinkedIn, Referral..."></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Notes</label><input type="text" name="notes" class="uk-input uk-form-small"></div>
<div class="uk-width-1-1 uk-margin-top"><button class="uk-button uk-button-primary uk-button-small">Save Applicant</button> <a href="?" class="uk-button uk-button-default uk-button-small">Cancel</a></div>
</div></form></div></div>

<?php else:?>
<!-- TABLE -->
<div class="filter-bar uk-margin-bottom"><form class="uk-grid-small uk-flex-middle" uk-grid>
<div class="uk-width-1-4@m"><input type="text" name="search" class="uk-input uk-form-small" placeholder="Search name, email..." value="<?=sanitize_output($filters['search'])?>" style="border-radius:8px"></div>
<div class="uk-width-1-5@m"><select name="position_id" class="uk-select uk-form-small" style="border-radius:8px"><option value="">All Positions</option><?php foreach($positions as $p):?><option value="<?=$p['position_id']?>" <?=$filters['position_id']==(string)$p['position_id']?'selected':''?>><?=sanitize_output($p['title'])?></option><?php endforeach;?></select></div>
<div class="uk-width-auto"><button class="uk-button uk-button-default uk-button-small">Filter</button></div>
</form></div>
<div class="uk-card uk-card-default"><div class="uk-card-body np"><div class="uk-overflow-auto"><table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-margin-remove">
<thead><tr><th>Ref #</th><th>Name</th><th>Position</th><th>Email</th><th>Status</th><th>Date</th><th></th></tr></thead><tbody>
<?php foreach($data['applicants'] as $ap):?><tr>
<td><code><?=sanitize_output($ap['reference_no'])?></code></td>
<td class="uk-text-bold" style="font-size:.84rem"><?=sanitize_output($ap['first_name'].' '.$ap['last_name'])?></td>
<td style="font-size:.82rem"><?=sanitize_output($ap['position_title']??'—')?></td>
<td style="font-size:.82rem" class="uk-text-muted"><?=sanitize_output($ap['email'])?></td>
<td><?=get_status_badge($ap['status'])?></td>
<td class="uk-text-muted" style="font-size:.78rem"><?=format_date($ap['application_date'])?></td>
<td><a href="?view=<?=$ap['applicant_id']?>" class="uk-icon-link" uk-icon="icon:chevron-right;ratio:.8"></a></td>
</tr><?php endforeach;?>
<?php if(empty($data['applicants'])):?><tr><td colspan="7" class="uk-text-center uk-text-muted uk-padding-small">No applicants found</td></tr><?php endif;?>
</tbody></table></div></div></div>
<?php endif;?>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>