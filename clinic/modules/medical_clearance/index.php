<?php
declare(strict_types=1);
require_once __DIR__.'/../../controllers/ClinicController.php';
$ctrl=new ClinicController();

if($_SERVER['REQUEST_METHOD']==='POST'&&validate_csrf_token($_POST['csrf_token']??'')){
    $a=$_POST['form_action']??'';
    if($a==='create_clearance'){
        $id=$ctrl->storeClearance([
            'patient_id'=>(int)$_POST['patient_id'],
            'purpose'=>$_POST['purpose'],
            'purpose_details'=>trim($_POST['purpose_details']??''),
            'exam_date'=>$_POST['exam_date'],
            'bp'=>trim($_POST['bp']??''),
            'temp'=>trim($_POST['temp']??''),
            'hr'=>trim($_POST['hr']??''),
            'weight'=>trim($_POST['weight']??''),
            'height'=>trim($_POST['height']??''),
            'findings'=>trim($_POST['findings']??''),
            'recommendation'=>trim($_POST['recommendation']??''),
            'status'=>$_POST['status']??'Pending',
            'issued_date'=>$_POST['status']!=='Pending'?date('Y-m-d'):null,
            'valid_until'=>$_POST['valid_until']?:null,
            'remarks'=>trim($_POST['remarks']??'')
        ]);
        flash($id?'success':'error',$id?'Clearance created.':'Failed.');
        redirect($_SERVER['PHP_SELF']);
    }
    if($a==='update_clearance'){
        $cid=(int)$_POST['clearance_id'];
        $d=['status'=>$_POST['status'],'findings'=>trim($_POST['findings']??''),'recommendation'=>trim($_POST['recommendation']??''),'remarks'=>trim($_POST['remarks']??''),'valid_until'=>$_POST['valid_until']?:null];
        if($_POST['status']!=='Pending'&&empty($_POST['issued_date'])) $d['issued_date']=date('Y-m-d');
        $r=$ctrl->updateClearance($cid,$d);
        flash($r?'success':'error',$r?'Clearance updated.':'Update failed.');
        redirect($_SERVER['PHP_SELF']);
    }
}

$filters=['status'=>$_GET['status']??''];
$clearances=$ctrl->getClearances($filters);
$showCreate=($_GET['action']??'')==='create';
$patients=$ctrl->getAllPatients();

$pageTitle='Medical Clearance';include __DIR__.'/../../includes/layout_top.php';
?>

<div class="page-hd">
<div><h3>Medical Clearance Issuance</h3><p>Issue and manage medical clearances for enrollment, OJT, sports &amp; more</p></div>
<a href="?action=create" class="btn btn-p"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> New Clearance</a>
</div>

<!-- Status Pills -->
<div class="pills">
<?php $cnts=['Pending'=>0,'Cleared'=>0,'Not Cleared'=>0,'Conditional'=>0];
foreach($clearances as $cl)$cnts[$cl['status']]=$cnts[$cl['status']]??0+1;
// recount properly
$cnts=['Pending'=>0,'Cleared'=>0,'Not Cleared'=>0,'Conditional'=>0];
foreach($ctrl->getClearances([]) as $cl){$cnts[$cl['status']]=$cnts[$cl['status']]??0;$cnts[$cl['status']]++;}
foreach(['Pending','Cleared','Not Cleared','Conditional'] as $st):$active=$filters['status']===$st;?>
<a href="?status=<?=urlencode($st)?>" class="pill <?=$active?'on':''?>"><?=$st?> <span style="opacity:.6;margin-left:4px"><?=$cnts[$st]??0?></span></a>
<?php endforeach;?>
<a href="?" class="pill">All</a>
</div>

<?php if($showCreate):?>
<!-- ===================== CREATE CLEARANCE ===================== -->
<a href="?" class="btn-txt" style="margin-bottom:14px;display:inline-block">&larr; Back</a>
<div class="card"><div class="card-hd"><h5>Issue Medical Clearance</h5></div>
<div class="card-bd"><form method="POST" class="uk-form-stacked">
<input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="create_clearance">

<div class="text-xs text-muted text-bold mb-16" style="text-transform:uppercase;letter-spacing:1px">Patient &amp; Purpose</div>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-2@m"><label class="uk-form-label">Patient *</label><select name="patient_id" class="uk-select uk-form-small" required><option value="">Select patient</option><?php foreach($patients as $pp):?><option value="<?=$pp['patient_id']?>"><?=sanitize_output($pp['student_number'].' — '.$pp['last_name'].', '.$pp['first_name'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Purpose *</label><select name="purpose" class="uk-select uk-form-small" required><option>Enrollment</option><option>OJT</option><option>Sports</option><option>Graduation</option><option>Employment</option><option>Field Trip</option><option>Other</option></select></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Exam Date *</label><input type="date" name="exam_date" class="uk-input uk-form-small" value="<?=date('Y-m-d')?>" required></div>
<div class="uk-width-1-1"><label class="uk-form-label">Purpose Details</label><input type="text" name="purpose_details" class="uk-input uk-form-small" placeholder="Additional details about the clearance purpose"></div>
</div>

<div class="text-xs text-muted text-bold mb-16 mt-24" style="text-transform:uppercase;letter-spacing:1px">Physical Exam</div>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-5@m"><label class="uk-form-label">BP (mmHg)</label><input type="text" name="bp" class="uk-input uk-form-small" placeholder="120/80"></div>
<div class="uk-width-1-5@m"><label class="uk-form-label">Temp (°C)</label><input type="text" name="temp" class="uk-input uk-form-small" placeholder="36.5"></div>
<div class="uk-width-1-5@m"><label class="uk-form-label">Heart Rate</label><input type="text" name="hr" class="uk-input uk-form-small" placeholder="72"></div>
<div class="uk-width-1-5@m"><label class="uk-form-label">Weight (kg)</label><input type="text" name="weight" class="uk-input uk-form-small" placeholder="65"></div>
<div class="uk-width-1-5@m"><label class="uk-form-label">Height (cm)</label><input type="text" name="height" class="uk-input uk-form-small" placeholder="165"></div>
</div>

<div class="text-xs text-muted text-bold mb-16 mt-24" style="text-transform:uppercase;letter-spacing:1px">Assessment</div>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-1"><label class="uk-form-label">Findings</label><textarea name="findings" class="uk-textarea uk-form-small" rows="2" placeholder="Physical examination findings"></textarea></div>
<div class="uk-width-1-1"><label class="uk-form-label">Recommendation</label><textarea name="recommendation" class="uk-textarea uk-form-small" rows="2" placeholder="Physician's recommendation"></textarea></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Status</label><select name="status" class="uk-select uk-form-small"><option>Pending</option><option>Cleared</option><option>Not Cleared</option><option>Conditional</option></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Valid Until</label><input type="date" name="valid_until" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Remarks</label><input type="text" name="remarks" class="uk-input uk-form-small"></div>
<div class="uk-width-1-1 uk-margin-top"><button class="btn btn-p">Issue Clearance</button> <a href="?" class="btn btn-s">Cancel</a></div>
</div></form></div></div>

<?php else:?>
<!-- ===================== CLEARANCE TABLE ===================== -->
<div class="card"><div class="card-bd np"><div class="overflow-x">
<table class="tbl"><thead><tr><th>Student</th><th>Purpose</th><th>Exam Date</th><th>Findings</th><th>Status</th><th>Issued By</th><th>Valid Until</th><th></th></tr></thead><tbody>
<?php foreach($clearances as $cl):?><tr>
<td><span class="text-bold"><?=sanitize_output($cl['patient_name']??'—')?></span><br><span class="text-xs text-muted"><?=sanitize_output($cl['student_number']??'')?></span></td>
<td><?=get_status_badge($cl['purpose'])?><?php if($cl['purpose_details']):?><br><span class="text-xs text-muted"><?=sanitize_output(substr($cl['purpose_details'],0,30))?></span><?php endif;?></td>
<td class="text-xs text-muted"><?=format_date($cl['exam_date'])?></td>
<td class="text-sm"><?=sanitize_output(substr($cl['findings']??'—',0,40))?></td>
<td><?=get_status_badge($cl['status'])?></td>
<td class="text-xs text-muted"><?=sanitize_output($cl['issued_by_name']??'—')?></td>
<td class="text-xs text-muted"><?=format_date($cl['valid_until'])?></td>
<td>
<!-- Inline update -->
<form method="POST" style="display:inline-flex;gap:4px;align-items:center">
<input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>">
<input type="hidden" name="form_action" value="update_clearance">
<input type="hidden" name="clearance_id" value="<?=$cl['clearance_id']?>">
<input type="hidden" name="findings" value="<?=sanitize_output($cl['findings']??'')?>">
<input type="hidden" name="recommendation" value="<?=sanitize_output($cl['recommendation']??'')?>">
<input type="hidden" name="remarks" value="<?=sanitize_output($cl['remarks']??'')?>">
<input type="hidden" name="valid_until" value="<?=$cl['valid_until']??''?>">
<select name="status" class="uk-select uk-form-small" style="border-radius:6px;width:auto;font-size:.72rem;padding:2px 6px;height:28px">
<?php foreach(['Pending','Cleared','Not Cleared','Conditional'] as $ss):?><option <?=$cl['status']===$ss?'selected':''?>><?=$ss?></option><?php endforeach;?></select>
<button class="btn btn-sm btn-p" style="padding:3px 8px;font-size:.7rem">Go</button>
</form>
</td>
</tr><?php endforeach;?>
<?php if(empty($clearances)):?><tr><td colspan="8" class="empty">No clearances found. <a href="?action=create">Issue one</a></td></tr><?php endif;?>
</tbody></table>
</div></div></div>
<?php endif;?>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>
