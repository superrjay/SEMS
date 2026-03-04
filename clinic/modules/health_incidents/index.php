<?php
declare(strict_types=1);
require_once __DIR__.'/../../controllers/ClinicController.php';
$ctrl=new ClinicController();

if($_SERVER['REQUEST_METHOD']==='POST'&&validate_csrf_token($_POST['csrf_token']??'')){
    $a=$_POST['form_action']??'';
    if($a==='create_incident'){
        $id=$ctrl->storeIncident([
            'patient_id'=>$_POST['patient_id']?(int)$_POST['patient_id']:null,
            'incident_date'=>$_POST['incident_date'].' '.$_POST['incident_time'],
            'location'=>trim($_POST['location']??''),
            'incident_type'=>$_POST['incident_type'],
            'severity'=>$_POST['severity']??'Minor',
            'description'=>trim($_POST['description']),
            'immediate_action'=>trim($_POST['immediate_action']??''),
            'outcome'=>trim($_POST['outcome']??''),
            'referred_to'=>trim($_POST['referred_to']??''),
            'witnesses'=>trim($_POST['witnesses']??''),
            'status'=>$_POST['status']??'Open'
        ]);
        flash($id?'success':'error',$id?'Incident reported.':'Failed.');
        redirect($_SERVER['PHP_SELF']);
    }
    if($a==='update_incident'){
        $iid=(int)$_POST['incident_id'];
        $r=$ctrl->updateIncident($iid,[
            'immediate_action'=>trim($_POST['immediate_action']??''),
            'outcome'=>trim($_POST['outcome']??''),
            'referred_to'=>trim($_POST['referred_to']??''),
            'status'=>$_POST['status']
        ]);
        flash($r?'success':'error',$r?'Incident updated.':'Update failed.');
        redirect($_SERVER['PHP_SELF'].'?view='.$iid);
    }
}

$filters=['status'=>$_GET['status']??'','severity'=>$_GET['severity']??''];
$incidents=$ctrl->getIncidents($filters);
$showCreate=($_GET['action']??'')==='create';
$showView=isset($_GET['view']);
$viewInc=null;
if($showView){$all=$ctrl->getIncidents([]);foreach($all as $i)if($i['incident_id']==(int)$_GET['view'])$viewInc=$i;}
$patients=$ctrl->getAllPatients();

$pageTitle='Health Incidents';include __DIR__.'/../../includes/layout_top.php';
?>

<div class="page-hd">
<div><h3>Health Incident Reporting</h3><p>Report and track health incidents, injuries &amp; emergencies on campus</p></div>
<a href="?action=create" class="btn btn-p"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Report Incident</a>
</div>

<!-- Status + Severity Pills -->
<div class="pills">
<?php $allInc=$ctrl->getIncidents([]);
$stCnts=['Open'=>0,'Under Review'=>0,'Resolved'=>0,'Closed'=>0];
foreach($allInc as $ii){$stCnts[$ii['status']]=$stCnts[$ii['status']]??0;$stCnts[$ii['status']]++;}
foreach(['Open','Under Review','Resolved','Closed'] as $st):$active=$filters['status']===$st;?>
<a href="?status=<?=urlencode($st)?>" class="pill <?=$active?'on':''?>"><?=$st?> <span style="opacity:.6;margin-left:4px"><?=$stCnts[$st]?></span></a>
<?php endforeach;?>
<span style="color:#ddd;margin:0 4px">|</span>
<?php foreach(['Minor','Moderate','Severe','Critical'] as $sv):$active=$filters['severity']===$sv;?>
<a href="?severity=<?=$sv?>" class="pill <?=$active?'on':''?>"><?=$sv?></a>
<?php endforeach;?>
<a href="?" class="pill">Clear</a>
</div>

<?php if($showView&&$viewInc):$i=$viewInc;?>
<!-- ===================== DETAIL VIEW ===================== -->
<a href="?" class="btn-txt" style="margin-bottom:14px;display:inline-block">&larr; Back to list</a>
<div class="g g2" style="align-items:start">
<div>
<div class="card">
<div class="card-hd"><h5>Incident #<?=$i['incident_id']?></h5><div class="flex gap-8"><?=get_status_badge($i['severity'])?> <?=get_status_badge($i['status'])?></div></div>
<div class="card-bd">
<div class="flex gap-16 flex-wrap mb-16">
<div><span class="text-xs text-muted">Type</span><div class="text-sm text-bold"><?=get_status_badge($i['incident_type'])?></div></div>
<div><span class="text-xs text-muted">Date/Time</span><div class="text-sm"><?=format_date($i['incident_date'],'M d, Y — h:i A')?></div></div>
<div><span class="text-xs text-muted">Location</span><div class="text-sm"><?=sanitize_output($i['location']??'—')?></div></div>
<div><span class="text-xs text-muted">Patient</span><div class="text-sm text-bold"><?=sanitize_output($i['patient_name']??'Unknown')?></div><div class="text-xs text-muted"><?=sanitize_output($i['student_number']??'')?></div></div>
<div><span class="text-xs text-muted">Reported By</span><div class="text-sm"><?=sanitize_output($i['reported_by_name']??'—')?></div></div>
</div>

<div class="text-xs text-muted text-bold mb-8" style="text-transform:uppercase;letter-spacing:1px">Description</div>
<p class="text-sm mb-16" style="background:#f8fafc;padding:12px 16px;border-radius:8px"><?=nl2br(sanitize_output($i['description']))?></p>

<?php if($i['immediate_action']):?><div class="text-xs text-muted text-bold mb-8" style="text-transform:uppercase;letter-spacing:1px">Immediate Action Taken</div><p class="text-sm mb-16"><?=nl2br(sanitize_output($i['immediate_action']))?></p><?php endif;?>
<?php if($i['outcome']):?><div class="text-xs text-muted text-bold mb-8" style="text-transform:uppercase;letter-spacing:1px">Outcome</div><p class="text-sm mb-16"><?=nl2br(sanitize_output($i['outcome']))?></p><?php endif;?>
<?php if($i['referred_to']):?><div class="text-xs text-muted text-bold mb-8" style="text-transform:uppercase;letter-spacing:1px">Referred To</div><p class="text-sm mb-16"><?=sanitize_output($i['referred_to'])?></p><?php endif;?>
<?php if($i['witnesses']):?><div class="text-xs text-muted text-bold mb-8" style="text-transform:uppercase;letter-spacing:1px">Witnesses</div><p class="text-sm mb-16"><?=sanitize_output($i['witnesses'])?></p><?php endif;?>
</div></div>
</div>

<!-- Update Panel -->
<div>
<div class="card"><div class="card-hd"><h5>Update Incident</h5></div>
<div class="card-bd"><form method="POST" class="uk-form-stacked">
<input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="update_incident"><input type="hidden" name="incident_id" value="<?=$i['incident_id']?>">
<div class="uk-margin"><label class="uk-form-label">Status</label><select name="status" class="uk-select uk-form-small"><?php foreach(['Open','Under Review','Resolved','Closed'] as $ss):?><option <?=$i['status']===$ss?'selected':''?>><?=$ss?></option><?php endforeach;?></select></div>
<div class="uk-margin"><label class="uk-form-label">Immediate Action Taken</label><textarea name="immediate_action" class="uk-textarea uk-form-small" rows="3"><?=sanitize_output($i['immediate_action']??'')?></textarea></div>
<div class="uk-margin"><label class="uk-form-label">Outcome</label><textarea name="outcome" class="uk-textarea uk-form-small" rows="3"><?=sanitize_output($i['outcome']??'')?></textarea></div>
<div class="uk-margin"><label class="uk-form-label">Referred To</label><input type="text" name="referred_to" class="uk-input uk-form-small" value="<?=sanitize_output($i['referred_to']??'')?>" placeholder="Hospital, specialist..."></div>
<button class="btn btn-p">Save Changes</button>
</form></div></div>
</div>
</div>

<?php elseif($showCreate):?>
<!-- ===================== REPORT INCIDENT ===================== -->
<a href="?" class="btn-txt" style="margin-bottom:14px;display:inline-block">&larr; Back</a>
<div class="card"><div class="card-hd"><h5>Report Health Incident</h5></div>
<div class="card-bd"><form method="POST" class="uk-form-stacked">
<input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="create_incident">

<div class="text-xs text-muted text-bold mb-16" style="text-transform:uppercase;letter-spacing:1px">Incident Details</div>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-3@m"><label class="uk-form-label">Incident Type *</label><select name="incident_type" class="uk-select uk-form-small" required><option>Injury</option><option>Illness</option><option>Allergic Reaction</option><option>Fainting</option><option>Seizure</option><option>Mental Health</option><option>Accident</option><option>Other</option></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Severity *</label><select name="severity" class="uk-select uk-form-small" required>
<option>Minor</option><option>Moderate</option><option>Severe</option><option>Critical</option></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Patient (if known)</label><select name="patient_id" class="uk-select uk-form-small"><option value="">Unknown / Not registered</option><?php foreach($patients as $pp):?><option value="<?=$pp['patient_id']?>"><?=sanitize_output($pp['student_number'].' — '.$pp['last_name'].', '.$pp['first_name'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Date *</label><input type="date" name="incident_date" class="uk-input uk-form-small" value="<?=date('Y-m-d')?>" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Time *</label><input type="time" name="incident_time" class="uk-input uk-form-small" value="<?=date('H:i')?>" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Location</label><input type="text" name="location" class="uk-input uk-form-small" placeholder="Building, room, area..."></div>
<div class="uk-width-1-1"><label class="uk-form-label">Description *</label><textarea name="description" class="uk-textarea uk-form-small" rows="3" required placeholder="Describe what happened in detail"></textarea></div>
</div>

<div class="text-xs text-muted text-bold mb-16 mt-24" style="text-transform:uppercase;letter-spacing:1px">Response &amp; Action</div>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-1"><label class="uk-form-label">Immediate Action Taken</label><textarea name="immediate_action" class="uk-textarea uk-form-small" rows="2" placeholder="First aid given, medications administered..."></textarea></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Outcome</label><textarea name="outcome" class="uk-textarea uk-form-small" rows="2" placeholder="Patient status after treatment"></textarea></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Witnesses</label><textarea name="witnesses" class="uk-textarea uk-form-small" rows="2" placeholder="Names and contact info of witnesses"></textarea></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Referred To</label><input type="text" name="referred_to" class="uk-input uk-form-small" placeholder="Hospital, specialist..."></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Status</label><select name="status" class="uk-select uk-form-small"><option>Open</option><option>Under Review</option><option>Resolved</option><option>Closed</option></select></div>
<div class="uk-width-1-1 uk-margin-top"><button class="btn btn-p">Submit Report</button> <a href="?" class="btn btn-s">Cancel</a></div>
</div></form></div></div>

<?php else:?>
<!-- ===================== INCIDENTS TABLE ===================== -->
<div class="card"><div class="card-bd np"><div class="overflow-x">
<table class="tbl"><thead><tr><th>ID</th><th>Date/Time</th><th>Patient</th><th>Type</th><th>Severity</th><th>Location</th><th>Status</th><th>Reported By</th><th></th></tr></thead><tbody>
<?php foreach($incidents as $i):?><tr>
<td class="text-bold">#<?=$i['incident_id']?></td>
<td class="text-xs text-muted" style="white-space:nowrap"><?=format_date($i['incident_date'],'M d, Y')?><br><?=format_date($i['incident_date'],'h:i A')?></td>
<td><span class="text-bold"><?=sanitize_output($i['patient_name']??'Unknown')?></span><?php if($i['student_number']):?><br><span class="text-xs text-muted"><?=sanitize_output($i['student_number'])?></span><?php endif;?></td>
<td><?=get_status_badge($i['incident_type'])?></td>
<td><?=get_status_badge($i['severity'])?></td>
<td class="text-xs text-muted"><?=sanitize_output($i['location']??'—')?></td>
<td><?=get_status_badge($i['status'])?></td>
<td class="text-xs text-muted"><?=sanitize_output($i['reported_by_name']??'—')?></td>
<td><a href="?view=<?=$i['incident_id']?>" class="btn-txt" style="font-size:.78rem">View →</a></td>
</tr><?php endforeach;?>
<?php if(empty($incidents)):?><tr><td colspan="9" class="empty">No incidents reported. <a href="?action=create">Report one</a></td></tr><?php endif;?>
</tbody></table>
</div></div></div>
<?php endif;?>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>
