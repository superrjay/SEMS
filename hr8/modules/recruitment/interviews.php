<?php
declare(strict_types=1);
require_once __DIR__.'/../../controllers/RecruitmentController.php';
$ctrl=new RecruitmentController();
if($_SERVER['REQUEST_METHOD']==='POST'&&validate_csrf_token($_POST['csrf_token']??'')){$a=$_POST['form_action']??'';
    if($a==='schedule'){$ctrl->scheduleInterview(['applicant_id'=>(int)$_POST['applicant_id'],'interviewer_id'=>(int)$_POST['interviewer_id'],'interview_date'=>$_POST['interview_date'],'interview_type'=>$_POST['interview_type'],'location'=>trim($_POST['location']??''),'status'=>'Scheduled','notes'=>trim($_POST['notes']??'')]);flash('success','Interview scheduled.');redirect($_SERVER['PHP_SELF']);}
    if($a==='complete'){$ctrl->updateInterview((int)$_POST['interview_id'],['status'=>'Completed']);flash('success','Marked complete.');redirect($_SERVER['PHP_SELF']);}
    if($a==='evaluate'){$ctrl->submitEvaluation(['interview_id'=>(int)$_POST['interview_id'],'communication_score'=>(int)$_POST['communication_score'],'technical_score'=>(int)$_POST['technical_score'],'experience_score'=>(int)$_POST['experience_score'],'cultural_fit_score'=>(int)$_POST['cultural_fit_score'],'strengths'=>trim($_POST['strengths']??''),'weaknesses'=>trim($_POST['weaknesses']??''),'recommendation'=>$_POST['recommendation'],'remarks'=>trim($_POST['remarks']??'')]);flash('success','Evaluation saved.');redirect($_SERVER['PHP_SELF']);}
}
$filters=['status'=>$_GET['status']??''];$data=$ctrl->interviews($filters);$showSched=($_GET['action']??'')==='schedule';$showEval=isset($_GET['evaluate']);
$applicants=$showSched?$ctrl->getApplicantsForInterview():[];$users=($showSched||$showEval)?$ctrl->getUsers():[];
$pageTitle='Interviews';include __DIR__.'/../../includes/layout_top.php';
?>
<div class="page-hd uk-flex uk-flex-between uk-flex-middle uk-margin-bottom"><div><h3>Interviews</h3><p>Schedule, track &amp; evaluate candidate interviews</p></div><a href="?action=schedule" class="uk-button uk-button-primary uk-button-small"><span uk-icon="icon:plus;ratio:.8" class="uk-margin-small-right"></span>Schedule</a></div>
<?php if($showSched):?>
<div class="uk-card uk-card-default"><div class="uk-card-header"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Schedule Interview</h5></div><div class="uk-card-body"><form method="POST" class="uk-form-stacked"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="schedule">
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-2@m"><label class="uk-form-label">Applicant *</label><select name="applicant_id" class="uk-select uk-form-small" required><option value="">Select</option><?php foreach($applicants as $a):?><option value="<?=$a['applicant_id']?>"><?=sanitize_output($a['reference_no'].' - '.$a['first_name'].' '.$a['last_name'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Interviewer *</label><select name="interviewer_id" class="uk-select uk-form-small" required><option value="">Select</option><?php foreach($users as $u):?><option value="<?=$u['user_id']?>"><?=sanitize_output($u['first_name'].' '.$u['last_name'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Date/Time *</label><input type="datetime-local" name="interview_date" class="uk-input uk-form-small" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Type</label><select name="interview_type" class="uk-select uk-form-small"><option>Initial</option><option>Technical</option><option>Panel</option><option>Final</option></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Location</label><input type="text" name="location" class="uk-input uk-form-small"></div>
<div class="uk-width-1-1"><label class="uk-form-label">Notes</label><textarea name="notes" class="uk-textarea uk-form-small" rows="2"></textarea></div>
<div class="uk-width-1-1 uk-margin-top"><button class="uk-button uk-button-primary uk-button-small">Schedule</button> <a href="?" class="uk-button uk-button-default uk-button-small">Cancel</a></div>
</div></form></div></div>
<?php elseif($showEval):?>
<div class="uk-card uk-card-default"><div class="uk-card-header"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Evaluate Interview</h5></div><div class="uk-card-body"><form method="POST" class="uk-form-stacked"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="evaluate"><input type="hidden" name="interview_id" value="<?=(int)$_GET['evaluate']?>">
<div class="uk-grid-small" uk-grid>
<?php foreach(['communication_score'=>'Communication','technical_score'=>'Technical','experience_score'=>'Experience','cultural_fit_score'=>'Cultural Fit'] as $k=>$l):?>
<div class="uk-width-1-4@m"><label class="uk-form-label"><?=$l?> (1-10)</label><input type="number" name="<?=$k?>" class="uk-input uk-form-small" min="1" max="10" required></div>
<?php endforeach;?>
<div class="uk-width-1-2@m"><label class="uk-form-label">Strengths</label><textarea name="strengths" class="uk-textarea uk-form-small" rows="2"></textarea></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Weaknesses</label><textarea name="weaknesses" class="uk-textarea uk-form-small" rows="2"></textarea></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Recommendation</label><select name="recommendation" class="uk-select uk-form-small" required><option value="">Select</option><option>Highly Recommended</option><option>Recommended</option><option>With Reservation</option><option>Not Recommended</option></select></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Remarks</label><input type="text" name="remarks" class="uk-input uk-form-small"></div>
<div class="uk-width-1-1 uk-margin-top"><button class="uk-button uk-button-primary uk-button-small">Submit</button> <a href="?" class="uk-button uk-button-default uk-button-small">Cancel</a></div>
</div></form></div></div>
<?php else:?>
<div class="uk-flex" style="gap:8px;margin-bottom:16px"><?php foreach([''=> 'All','Scheduled'=>'Scheduled','Completed'=>'Completed','Cancelled'=>'Cancelled'] as $k=>$v):?><a href="?status=<?=$k?>" class="uk-button uk-button-small" style="border-radius:20px;font-size:.75rem;<?=$filters['status']===$k?'background:#1e3a5f;color:#fff':'background:#f3f4f6;color:#555'?>"><?=$v?></a><?php endforeach;?></div>
<div class="uk-card uk-card-default"><div class="uk-card-body np"><div class="uk-overflow-auto"><table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-margin-remove">
<thead><tr><th>Applicant</th><th>Position</th><th>Interviewer</th><th>Date</th><th>Type</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach($data['interviews'] as $i):?><tr><td><span class="uk-text-bold" style="font-size:.84rem"><?=sanitize_output($i['applicant_name'])?></span><br><span class="uk-text-muted" style="font-size:.72rem"><?=sanitize_output($i['reference_no'])?></span></td><td style="font-size:.82rem"><?=sanitize_output($i['position_title']??'—')?></td><td style="font-size:.82rem"><?=sanitize_output($i['interviewer_name']??'—')?></td><td style="font-size:.78rem"><?=format_date($i['interview_date'],'M d, h:i A')?></td><td><span class="bd bd-info"><?=sanitize_output($i['interview_type'])?></span></td><td><?=get_status_badge($i['status'])?></td>
<td class="uk-flex" style="gap:4px"><?php if($i['status']==='Scheduled'):?><form method="POST"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="complete"><input type="hidden" name="interview_id" value="<?=$i['interview_id']?>"><button class="uk-icon-link" uk-icon="icon:check;ratio:.8" uk-tooltip="Complete"></button></form><a href="?evaluate=<?=$i['interview_id']?>" class="uk-icon-link" uk-icon="icon:star;ratio:.8" uk-tooltip="Evaluate"></a><?php endif;?></td></tr><?php endforeach;?>
<?php if(empty($data['interviews'])):?><tr><td colspan="7" class="uk-text-center uk-text-muted uk-padding-small">No interviews</td></tr><?php endif;?>
</tbody></table></div></div></div>
<?php endif;?>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>