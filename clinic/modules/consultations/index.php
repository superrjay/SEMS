<?php
declare(strict_types=1);
require_once __DIR__.'/../../controllers/ClinicController.php';
$ctrl=new ClinicController();

if($_SERVER['REQUEST_METHOD']==='POST'&&validate_csrf_token($_POST['csrf_token']??'')){
    $a=$_POST['form_action']??'';
    if($a==='create_consultation'){
        $id=$ctrl->storeConsultation([
            'patient_id'=>(int)$_POST['patient_id'],
            'consultation_date'=>$_POST['consultation_date'].' '.$_POST['consultation_time'],
            'chief_complaint'=>trim($_POST['chief_complaint']),
            'symptoms'=>trim($_POST['symptoms']??''),
            'vital_signs_bp'=>trim($_POST['vital_signs_bp']??''),
            'vital_signs_temp'=>trim($_POST['vital_signs_temp']??''),
            'vital_signs_hr'=>trim($_POST['vital_signs_hr']??''),
            'vital_signs_rr'=>trim($_POST['vital_signs_rr']??''),
            'vital_signs_weight'=>trim($_POST['vital_signs_weight']??''),
            'diagnosis'=>trim($_POST['diagnosis']??''),
            'treatment'=>trim($_POST['treatment']??''),
            'prescription'=>trim($_POST['prescription']??''),
            'follow_up_date'=>$_POST['follow_up_date']?:null,
            'follow_up_notes'=>trim($_POST['follow_up_notes']??''),
            'attending_doctor'=>$_POST['attending_doctor']?(int)$_POST['attending_doctor']:null,
            'nurse_on_duty'=>$_POST['nurse_on_duty']?(int)$_POST['nurse_on_duty']:null,
            'status'=>$_POST['status']??'Ongoing'
        ]);
        flash($id?'success':'error',$id?'Consultation recorded.':'Failed.');
        redirect($_SERVER['PHP_SELF']);
    }
    if($a==='update_consultation'){
        $cid=(int)$_POST['consultation_id'];
        $r=$ctrl->updateConsultation($cid,[
            'diagnosis'=>trim($_POST['diagnosis']??''),
            'treatment'=>trim($_POST['treatment']??''),
            'prescription'=>trim($_POST['prescription']??''),
            'follow_up_date'=>$_POST['follow_up_date']?:null,
            'follow_up_notes'=>trim($_POST['follow_up_notes']??''),
            'status'=>$_POST['status']
        ]);
        flash($r?'success':'error',$r?'Consultation updated.':'Update failed.');
        redirect($_SERVER['PHP_SELF'].'?view='.$cid);
    }
}

$filters=['status'=>$_GET['status']??'','search'=>$_GET['search']??''];
$consultations=$ctrl->getConsultations($filters);
$showCreate=($_GET['action']??'')==='create';
$showView=isset($_GET['view']);
$viewData=$showView?$ctrl->getConsultation((int)$_GET['view']):null;
$patients=$ctrl->getAllPatients();
$doctors=$ctrl->getDoctors();
$nurses=$ctrl->getNurses();

$pageTitle='Consultations';include __DIR__.'/../../includes/layout_top.php';
?>

<div class="page-hd">
<div><h3>Consultation &amp; Treatment Logs</h3><p>Patient consultations, vitals, diagnoses &amp; treatments</p></div>
<a href="?action=create" class="btn btn-p"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> New Consultation</a>
</div>

<div class="pills">
<?php foreach(['Ongoing','Completed','Follow-up','Referred'] as $st):$cnt=0;foreach($consultations as $cc)if($cc['status']===$st)$cnt++;$active=$filters['status']===$st;?>
<a href="?status=<?=$st?>" class="pill <?=$active?'on':''?>"><?=$st?> <span style="opacity:.6;margin-left:4px"><?=$cnt?></span></a>
<?php endforeach;?>
<a href="?" class="pill">All</a>
</div>

<?php if($showView&&$viewData):$c=$viewData;?>
<!-- ===================== DETAIL VIEW ===================== -->
<a href="?" class="btn-txt" style="margin-bottom:14px;display:inline-block">&larr; Back to list</a>
<div class="g g2" style="align-items:start">
<div>
<!-- Patient + Vitals -->
<div class="card">
<div class="card-hd"><h5>Consultation Details</h5><?=get_status_badge($c['status'])?></div>
<div class="card-bd">
<div class="flex gap-16 flex-wrap mb-16">
<div><span class="text-xs text-muted">Patient</span><div class="text-bold"><?=sanitize_output($c['patient_name']??'—')?></div><div class="text-xs text-muted"><?=sanitize_output($c['student_number']??'')?></div></div>
<div><span class="text-xs text-muted">Date/Time</span><div class="text-sm"><?=format_date($c['consultation_date'],'M d, Y — h:i A')?></div></div>
<div><span class="text-xs text-muted">Doctor</span><div class="text-sm"><?=sanitize_output($c['doctor_name']??'—')?></div></div>
<div><span class="text-xs text-muted">Nurse</span><div class="text-sm"><?=sanitize_output($c['nurse_name']??'—')?></div></div>
</div>
<?php if($c['blood_type']||$c['allergies']):?>
<div class="flex gap-16 mb-16" style="padding:10px 14px;background:#fef2f2;border-radius:8px;font-size:.82rem">
<div><span class="text-bold" style="color:#991b1b">Blood:</span> <?=sanitize_output($c['blood_type']??'?')?></div>
<?php if($c['allergies']):?><div><span class="text-bold" style="color:#991b1b">Allergies:</span> <?=sanitize_output($c['allergies'])?></div><?php endif;?>
</div>
<?php endif;?>

<div class="text-xs text-muted text-bold mb-8" style="text-transform:uppercase;letter-spacing:1px">Vital Signs</div>
<div class="g" style="grid-template-columns:repeat(5,1fr);margin-bottom:20px">
<div class="st" style="padding:12px;text-align:center"><div class="text-bold" style="color:#0f766e"><?=sanitize_output($c['vital_signs_bp']??'—')?></div><div class="text-xs text-muted">BP (mmHg)</div></div>
<div class="st" style="padding:12px;text-align:center"><div class="text-bold" style="color:#0f766e"><?=sanitize_output($c['vital_signs_temp']??'—')?></div><div class="text-xs text-muted">Temp (°C)</div></div>
<div class="st" style="padding:12px;text-align:center"><div class="text-bold" style="color:#0f766e"><?=sanitize_output($c['vital_signs_hr']??'—')?></div><div class="text-xs text-muted">HR (bpm)</div></div>
<div class="st" style="padding:12px;text-align:center"><div class="text-bold" style="color:#0f766e"><?=sanitize_output($c['vital_signs_rr']??'—')?></div><div class="text-xs text-muted">RR (/min)</div></div>
<div class="st" style="padding:12px;text-align:center"><div class="text-bold" style="color:#0f766e"><?=sanitize_output($c['vital_signs_weight']??'—')?></div><div class="text-xs text-muted">Weight (kg)</div></div>
</div>

<div class="text-xs text-muted text-bold mb-8" style="text-transform:uppercase;letter-spacing:1px">Chief Complaint</div>
<p class="text-sm mb-16"><?=nl2br(sanitize_output($c['chief_complaint']))?></p>

<?php if($c['symptoms']):?><div class="text-xs text-muted text-bold mb-8" style="text-transform:uppercase;letter-spacing:1px">Symptoms</div><p class="text-sm mb-16"><?=nl2br(sanitize_output($c['symptoms']))?></p><?php endif;?>
<?php if($c['diagnosis']):?><div class="text-xs text-muted text-bold mb-8" style="text-transform:uppercase;letter-spacing:1px">Diagnosis</div><p class="text-sm mb-16"><?=nl2br(sanitize_output($c['diagnosis']))?></p><?php endif;?>
<?php if($c['treatment']):?><div class="text-xs text-muted text-bold mb-8" style="text-transform:uppercase;letter-spacing:1px">Treatment</div><p class="text-sm mb-16"><?=nl2br(sanitize_output($c['treatment']))?></p><?php endif;?>
<?php if($c['prescription']):?><div class="text-xs text-muted text-bold mb-8" style="text-transform:uppercase;letter-spacing:1px">Prescription</div><p class="text-sm mb-16" style="background:#f0fdfa;padding:10px 14px;border-radius:8px"><?=nl2br(sanitize_output($c['prescription']))?></p><?php endif;?>
</div></div>
</div>

<!-- Update Panel -->
<div>
<div class="card">
<div class="card-hd"><h5>Update Consultation</h5></div>
<div class="card-bd"><form method="POST" class="uk-form-stacked">
<input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="update_consultation"><input type="hidden" name="consultation_id" value="<?=$c['consultation_id']?>">
<div class="uk-margin"><label class="uk-form-label">Status *</label><select name="status" class="uk-select uk-form-small"><?php foreach(['Ongoing','Completed','Follow-up','Referred'] as $ss):?><option <?=$c['status']===$ss?'selected':''?>><?=$ss?></option><?php endforeach;?></select></div>
<div class="uk-margin"><label class="uk-form-label">Diagnosis</label><textarea name="diagnosis" class="uk-textarea uk-form-small" rows="2"><?=sanitize_output($c['diagnosis']??'')?></textarea></div>
<div class="uk-margin"><label class="uk-form-label">Treatment</label><textarea name="treatment" class="uk-textarea uk-form-small" rows="2"><?=sanitize_output($c['treatment']??'')?></textarea></div>
<div class="uk-margin"><label class="uk-form-label">Prescription</label><textarea name="prescription" class="uk-textarea uk-form-small" rows="2"><?=sanitize_output($c['prescription']??'')?></textarea></div>
<div class="uk-margin"><label class="uk-form-label">Follow-up Date</label><input type="date" name="follow_up_date" class="uk-input uk-form-small" value="<?=$c['follow_up_date']??''?>"></div>
<div class="uk-margin"><label class="uk-form-label">Follow-up Notes</label><textarea name="follow_up_notes" class="uk-textarea uk-form-small" rows="2"><?=sanitize_output($c['follow_up_notes']??'')?></textarea></div>
<button class="btn btn-p">Save Changes</button>
</form></div></div>
<?php if($c['follow_up_date']):?>
<div class="card" style="margin-top:16px"><div class="card-bd" style="text-align:center">
<div class="text-xs text-muted text-bold">FOLLOW-UP SCHEDULED</div>
<div class="text-bold" style="font-size:1.2rem;color:#0f766e;margin-top:4px"><?=format_date($c['follow_up_date'],'F d, Y')?></div>
<?php if($c['follow_up_notes']):?><div class="text-sm text-muted mt-8"><?=sanitize_output($c['follow_up_notes'])?></div><?php endif;?>
</div></div>
<?php endif;?>
</div>
</div>

<?php elseif($showCreate):?>
<!-- ===================== CREATE FORM ===================== -->
<a href="?" class="btn-txt" style="margin-bottom:14px;display:inline-block">&larr; Back</a>
<div class="card"><div class="card-hd"><h5>New Consultation</h5></div>
<div class="card-bd"><form method="POST" class="uk-form-stacked">
<input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="create_consultation">

<div class="text-xs text-muted text-bold mb-16" style="text-transform:uppercase;letter-spacing:1px">Patient &amp; Schedule</div>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-2@m"><label class="uk-form-label">Patient *</label><select name="patient_id" class="uk-select uk-form-small" required><option value="">Select patient</option><?php foreach($patients as $pp):?><option value="<?=$pp['patient_id']?>"><?=sanitize_output($pp['student_number'].' — '.$pp['last_name'].', '.$pp['first_name'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Date *</label><input type="date" name="consultation_date" class="uk-input uk-form-small" value="<?=date('Y-m-d')?>" required></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Time *</label><input type="time" name="consultation_time" class="uk-input uk-form-small" value="<?=date('H:i')?>" required></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Doctor</label><select name="attending_doctor" class="uk-select uk-form-small"><option value="">Select</option><?php foreach($doctors as $d):?><option value="<?=$d['user_id']?>"><?=sanitize_output($d['name'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Nurse on Duty</label><select name="nurse_on_duty" class="uk-select uk-form-small"><option value="">Select</option><?php foreach($nurses as $n):?><option value="<?=$n['user_id']?>"><?=sanitize_output($n['name'])?></option><?php endforeach;?></select></div>
</div>

<div class="text-xs text-muted text-bold mb-16 mt-24" style="text-transform:uppercase;letter-spacing:1px">Vital Signs</div>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-5@m"><label class="uk-form-label">BP (mmHg)</label><input type="text" name="vital_signs_bp" class="uk-input uk-form-small" placeholder="120/80"></div>
<div class="uk-width-1-5@m"><label class="uk-form-label">Temp (°C)</label><input type="text" name="vital_signs_temp" class="uk-input uk-form-small" placeholder="36.5"></div>
<div class="uk-width-1-5@m"><label class="uk-form-label">Heart Rate</label><input type="text" name="vital_signs_hr" class="uk-input uk-form-small" placeholder="72"></div>
<div class="uk-width-1-5@m"><label class="uk-form-label">Resp Rate</label><input type="text" name="vital_signs_rr" class="uk-input uk-form-small" placeholder="18"></div>
<div class="uk-width-1-5@m"><label class="uk-form-label">Weight (kg)</label><input type="text" name="vital_signs_weight" class="uk-input uk-form-small" placeholder="65"></div>
</div>

<div class="text-xs text-muted text-bold mb-16 mt-24" style="text-transform:uppercase;letter-spacing:1px">Complaint &amp; Assessment</div>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-1"><label class="uk-form-label">Chief Complaint *</label><textarea name="chief_complaint" class="uk-textarea uk-form-small" rows="2" required placeholder="Main reason for visit"></textarea></div>
<div class="uk-width-1-1"><label class="uk-form-label">Symptoms</label><textarea name="symptoms" class="uk-textarea uk-form-small" rows="2" placeholder="Onset, duration, severity..."></textarea></div>
<div class="uk-width-1-1"><label class="uk-form-label">Diagnosis</label><textarea name="diagnosis" class="uk-textarea uk-form-small" rows="2" placeholder="Initial assessment / diagnosis"></textarea></div>
</div>

<div class="text-xs text-muted text-bold mb-16 mt-24" style="text-transform:uppercase;letter-spacing:1px">Treatment &amp; Follow-up</div>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-1"><label class="uk-form-label">Treatment Given</label><textarea name="treatment" class="uk-textarea uk-form-small" rows="2" placeholder="Medications administered, procedures performed..."></textarea></div>
<div class="uk-width-1-1"><label class="uk-form-label">Prescription</label><textarea name="prescription" class="uk-textarea uk-form-small" rows="2" placeholder="Medications prescribed for take-home"></textarea></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Follow-up Date</label><input type="date" name="follow_up_date" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Follow-up Notes</label><input type="text" name="follow_up_notes" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Status</label><select name="status" class="uk-select uk-form-small"><option>Ongoing</option><option>Completed</option><option>Follow-up</option><option>Referred</option></select></div>
<div class="uk-width-1-1 uk-margin-top"><button class="btn btn-p">Save Consultation</button> <a href="?" class="btn btn-s">Cancel</a></div>
</div>
</form></div></div>

<?php else:?>
<!-- ===================== TABLE LIST ===================== -->
<div class="filter-bar"><form class="flex flex-middle gap-8 flex-wrap">
<input type="text" name="search" class="uk-input uk-form-small" style="border-radius:8px;max-width:260px" placeholder="Search patient name, student no..." value="<?=sanitize_output($filters['search'])?>">
<button class="btn btn-sm">Filter</button>
<?php if($filters['search']||$filters['status']):?><a href="?" class="btn btn-sm btn-txt">Clear</a><?php endif;?>
</form></div>

<div class="card"><div class="card-bd np"><div class="overflow-x">
<table class="tbl"><thead><tr><th>Date/Time</th><th>Patient</th><th>Chief Complaint</th><th>Doctor</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach($consultations as $c):?><tr>
<td class="text-xs text-muted" style="white-space:nowrap"><?=format_date($c['consultation_date'],'M d, Y')?><br><?=format_date($c['consultation_date'],'h:i A')?></td>
<td><span class="text-bold"><?=sanitize_output($c['patient_name']??'—')?></span><br><span class="text-xs text-muted"><?=sanitize_output($c['student_number']??'')?></span></td>
<td class="text-sm"><?=sanitize_output(substr($c['chief_complaint'],0,50))?><?=strlen($c['chief_complaint'])>50?'...':''?></td>
<td class="text-sm text-muted"><?=sanitize_output($c['doctor_name']??'—')?></td>
<td><?=get_status_badge($c['status'])?></td>
<td><a href="?view=<?=$c['consultation_id']?>" class="btn-txt" style="font-size:.78rem">View →</a></td>
</tr><?php endforeach;?>
<?php if(empty($consultations)):?><tr><td colspan="6" class="empty">No consultations found. <a href="?action=create">Create one</a></td></tr><?php endif;?>
</tbody></table>
</div></div></div>
<?php endif;?>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>
