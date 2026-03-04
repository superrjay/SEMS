<?php
declare(strict_types=1);
require_once __DIR__.'/../../controllers/RecruitmentController.php';
$ctrl=new RecruitmentController();
if($_SERVER['REQUEST_METHOD']==='POST'&&validate_csrf_token($_POST['csrf_token']??'')){$a=$_POST['form_action']??'';
    if($a==='create_offer'){$ctrl->createOffer(['applicant_id'=>(int)$_POST['applicant_id'],'position_id'=>(int)$_POST['position_id'],'offered_salary'=>(float)$_POST['offered_salary'],'start_date'=>$_POST['start_date'],'employment_type'=>$_POST['employment_type'],'offer_date'=>date('Y-m-d'),'expiry_date'=>$_POST['expiry_date']?:null,'status'=>'Draft','remarks'=>trim($_POST['remarks']??'')]);flash('success','Offer created.');redirect($_SERVER['PHP_SELF']);}
    if($a==='update_offer'){$ctrl->updateOfferStatus((int)$_POST['offer_id'],$_POST['status']);flash('success','Offer updated.');redirect($_SERVER['PHP_SELF']);}
}
$data=$ctrl->offers(['status'=>$_GET['status']??'']);$showCreate=($_GET['action']??'')==='create';
require_once __DIR__.'/../../models/BaseModel.php';require_once __DIR__.'/../../models/Applicant.php';require_once __DIR__.'/../../models/JobPosition.php';
$apm=new \HR8\Models\Applicant();$jpm=new \HR8\Models\JobPosition();$applicants=$apm->getAllWithPosition(['status'=>'Interviewed']);$positions=$jpm->getActive();
$pageTitle='Job Offers';include __DIR__.'/../../includes/layout_top.php';
?>
<div class="page-hd uk-flex uk-flex-between uk-flex-middle uk-margin-bottom"><div><h3>Job Offers</h3><p>Create, send &amp; track offers</p></div><a href="?action=create" class="uk-button uk-button-primary uk-button-small"><span uk-icon="icon:plus;ratio:.8" class="uk-margin-small-right"></span>New Offer</a></div>
<?php if($showCreate):?>
<div class="uk-card uk-card-default"><div class="uk-card-header"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Create Offer</h5></div><div class="uk-card-body"><form method="POST" class="uk-form-stacked"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="create_offer">
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-2@m"><label class="uk-form-label">Applicant *</label><select name="applicant_id" class="uk-select uk-form-small" required><option value="">Select</option><?php foreach($applicants as $a):?><option value="<?=$a['applicant_id']?>"><?=sanitize_output($a['reference_no'].' - '.$a['first_name'].' '.$a['last_name'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Position *</label><select name="position_id" class="uk-select uk-form-small" required><option value="">Select</option><?php foreach($positions as $p):?><option value="<?=$p['position_id']?>"><?=sanitize_output($p['title'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Salary (₱)</label><input type="number" name="offered_salary" class="uk-input uk-form-small" step="0.01"></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Start Date</label><input type="date" name="start_date" class="uk-input uk-form-small"></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Type</label><select name="employment_type" class="uk-select uk-form-small"><option>Probationary</option><option>Full-Time</option><option>Part-Time</option><option>Contractual</option></select></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Expiry</label><input type="date" name="expiry_date" class="uk-input uk-form-small"></div>
<div class="uk-width-1-1"><label class="uk-form-label">Remarks</label><textarea name="remarks" class="uk-textarea uk-form-small" rows="2"></textarea></div>
<div class="uk-width-1-1 uk-margin-top"><button class="uk-button uk-button-primary uk-button-small">Create</button> <a href="?" class="uk-button uk-button-default uk-button-small">Cancel</a></div>
</div></form></div></div>
<?php else:?>
<div class="uk-card uk-card-default"><div class="uk-card-body np"><div class="uk-overflow-auto"><table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-margin-remove">
<thead><tr><th>Applicant</th><th>Position</th><th>Salary</th><th>Start</th><th>Type</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach($data['offers'] as $o):?><tr><td class="uk-text-bold" style="font-size:.84rem"><?=sanitize_output($o['applicant_name'])?></td><td style="font-size:.82rem"><?=sanitize_output($o['position_title'])?></td><td style="font-size:.82rem"><?=$o['offered_salary']?format_currency((float)$o['offered_salary']):'—'?></td><td style="font-size:.78rem"><?=format_date($o['start_date']??'')?></td><td><span class="bd bd-secondary"><?=sanitize_output($o['employment_type'])?></span></td><td><?=get_status_badge($o['status'])?></td>
<td class="uk-flex" style="gap:4px">
<?php if($o['status']==='Draft'):?><form method="POST"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="update_offer"><input type="hidden" name="offer_id" value="<?=$o['offer_id']?>"><input type="hidden" name="status" value="Sent"><button class="uk-icon-link" uk-icon="icon:mail;ratio:.8" uk-tooltip="Send"></button></form><?php endif;?>
<?php if($o['status']==='Sent'):?><form method="POST" class="uk-display-inline"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="update_offer"><input type="hidden" name="offer_id" value="<?=$o['offer_id']?>"><input type="hidden" name="status" value="Accepted"><button class="uk-icon-link" uk-icon="icon:check;ratio:.8" style="color:#16a34a" uk-tooltip="Accept"></button></form><form method="POST" class="uk-display-inline"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="update_offer"><input type="hidden" name="offer_id" value="<?=$o['offer_id']?>"><input type="hidden" name="status" value="Declined"><button class="uk-icon-link" uk-icon="icon:close;ratio:.8" style="color:#dc2626" uk-tooltip="Decline"></button></form><?php endif;?>
</td></tr><?php endforeach;?>
<?php if(empty($data['offers'])):?><tr><td colspan="7" class="uk-text-center uk-text-muted uk-padding-small">No offers</td></tr><?php endif;?>
</tbody></table></div></div></div>
<?php endif;?>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>