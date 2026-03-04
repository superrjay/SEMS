<?php
declare(strict_types=1);
require_once __DIR__.'/../../controllers/ClinicController.php';
$ctrl=new ClinicController();

if($_SERVER['REQUEST_METHOD']==='POST'&&validate_csrf_token($_POST['csrf_token']??'')){
    $a=$_POST['form_action']??'';
    if($a==='register_patient'){
        $id=$ctrl->storePatient([
            'student_number'=>trim($_POST['student_number']),
            'first_name'=>trim($_POST['first_name']),
            'last_name'=>trim($_POST['last_name']),
            'middle_name'=>trim($_POST['middle_name']??''),
            'email'=>trim($_POST['email']??''),
            'phone'=>trim($_POST['phone']??''),
            'date_of_birth'=>$_POST['date_of_birth']?:null,
            'gender'=>$_POST['gender']?:null,
            'program'=>trim($_POST['program']??''),
            'year_level'=>(int)($_POST['year_level']??1),
            'blood_type'=>$_POST['blood_type']??'Unknown',
            'allergies'=>trim($_POST['allergies']??''),
            'existing_conditions'=>trim($_POST['existing_conditions']??''),
            'emergency_contact_name'=>trim($_POST['emergency_contact_name']??''),
            'emergency_contact_phone'=>trim($_POST['emergency_contact_phone']??''),
            'emergency_contact_relation'=>trim($_POST['emergency_contact_relation']??'')
        ]);
        flash($id?'success':'error',$id?'Patient registered successfully.':'Registration failed.');
        redirect($_SERVER['PHP_SELF']);
    }
    if($a==='update_patient'){
        $pid=(int)$_POST['patient_id'];
        $r=$ctrl->updatePatient($pid,[
            'first_name'=>trim($_POST['first_name']),
            'last_name'=>trim($_POST['last_name']),
            'middle_name'=>trim($_POST['middle_name']??''),
            'email'=>trim($_POST['email']??''),
            'phone'=>trim($_POST['phone']??''),
            'date_of_birth'=>$_POST['date_of_birth']?:null,
            'gender'=>$_POST['gender']?:null,
            'program'=>trim($_POST['program']??''),
            'year_level'=>(int)($_POST['year_level']??1),
            'blood_type'=>$_POST['blood_type']??'Unknown',
            'allergies'=>trim($_POST['allergies']??''),
            'existing_conditions'=>trim($_POST['existing_conditions']??''),
            'emergency_contact_name'=>trim($_POST['emergency_contact_name']??''),
            'emergency_contact_phone'=>trim($_POST['emergency_contact_phone']??''),
            'emergency_contact_relation'=>trim($_POST['emergency_contact_relation']??''),
            'status'=>$_POST['status']??'Active'
        ]);
        flash($r?'success':'error',$r?'Patient updated.':'Update failed.');
        redirect($_SERVER['PHP_SELF'].'?view='.$pid);
    }
    if($a==='add_record'){
        $id=$ctrl->addMedicalRecord([
            'patient_id'=>(int)$_POST['patient_id'],
            'record_type'=>$_POST['record_type'],
            'title'=>trim($_POST['title']),
            'description'=>trim($_POST['description']??''),
            'findings'=>trim($_POST['findings']??''),
            'record_date'=>$_POST['record_date'],
            'attending_physician'=>trim($_POST['attending_physician']??'')
        ]);
        flash($id?'success':'error',$id?'Medical record added.':'Failed.');
        redirect($_SERVER['PHP_SELF'].'?view='.$_POST['patient_id']);
    }
}

$filters=['status'=>$_GET['status']??'','search'=>$_GET['search']??''];
$patients=$ctrl->getPatients($filters);
$showCreate=($_GET['action']??'')==='register';
$showView=isset($_GET['view']);
$showEdit=isset($_GET['edit']);
$viewPatient=$showView?$ctrl->getPatient((int)$_GET['view']):null;
$editPatient=$showEdit?$ctrl->getPatient((int)$_GET['edit']):null;
$medRecords=$viewPatient?$ctrl->getMedicalRecords((int)$_GET['view']):[];

$pageTitle='Medical Records';include __DIR__.'/../../includes/layout_top.php';
?>

<div class="page-hd">
<div><h3>Student Medical Records</h3><p>Patient profiles, medical history &amp; health documents</p></div>
<a href="?action=register" class="btn btn-p"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Register Patient</a>
</div>

<!-- Status Pills -->
<div class="pills">
<?php
$allP=$ctrl->getPatients([]);
$counts=['Active'=>0,'Inactive'=>0,'Graduated'=>0];
foreach($allP as $pp){$counts[$pp['status']]=$counts[$pp['status']]??0;$counts[$pp['status']]++;}
foreach(['Active','Inactive','Graduated'] as $st):$active=$filters['status']===$st;?>
<a href="?status=<?=$st?>" class="pill <?=$active?'on':''?>"><?=$st?> <span style="opacity:.6;margin-left:4px"><?=$counts[$st]??0?></span></a>
<?php endforeach;?>
<a href="?" class="pill">All <span style="opacity:.6;margin-left:4px"><?=count($allP)?></span></a>
</div>

<?php if($showView&&$viewPatient):$p=$viewPatient;?>
<!-- ===================== DETAIL VIEW ===================== -->
<a href="?" class="btn-txt" style="margin-bottom:14px;display:inline-block">&larr; Back to list</a>
<div class="g g2" style="align-items:start">
<!-- Patient Info Card -->
<div>
<div class="card">
<div class="card-hd"><h5><?=sanitize_output($p['first_name'].' '.$p['last_name'])?></h5><div><?=get_status_badge($p['status'])?></div></div>
<div class="card-bd">
<table class="tbl" style="margin:0"><tbody>
<tr><td style="width:38%;color:#999;font-weight:600;border:0;padding:5px 0;font-size:.82rem">Student No.</td><td style="border:0;padding:5px 0;font-size:.84rem"><code><?=sanitize_output($p['student_number'])?></code></td></tr>
<tr><td style="color:#999;font-weight:600;border:0;padding:5px 0;font-size:.82rem">Program</td><td style="border:0;padding:5px 0;font-size:.84rem"><?=sanitize_output($p['program']??'—')?> — Year <?=$p['year_level']??''?></td></tr>
<tr><td style="color:#999;font-weight:600;border:0;padding:5px 0;font-size:.82rem">Email</td><td style="border:0;padding:5px 0;font-size:.84rem"><?=sanitize_output($p['email']??'—')?></td></tr>
<tr><td style="color:#999;font-weight:600;border:0;padding:5px 0;font-size:.82rem">Phone</td><td style="border:0;padding:5px 0;font-size:.84rem"><?=sanitize_output($p['phone']??'—')?></td></tr>
<tr><td style="color:#999;font-weight:600;border:0;padding:5px 0;font-size:.82rem">Date of Birth</td><td style="border:0;padding:5px 0;font-size:.84rem"><?=format_date($p['date_of_birth'])?></td></tr>
<tr><td style="color:#999;font-weight:600;border:0;padding:5px 0;font-size:.82rem">Gender</td><td style="border:0;padding:5px 0;font-size:.84rem"><?=sanitize_output($p['gender']??'—')?></td></tr>
<tr><td style="color:#999;font-weight:600;border:0;padding:5px 0;font-size:.82rem">Blood Type</td><td style="border:0;padding:5px 0;font-size:.84rem"><?=get_status_badge($p['blood_type']??'Unknown')?></td></tr>
<tr><td style="color:#999;font-weight:600;border:0;padding:5px 0;font-size:.82rem">Allergies</td><td style="border:0;padding:5px 0;font-size:.84rem;color:<?=empty($p['allergies'])?'#ccc':'#dc2626'?>"><?=sanitize_output($p['allergies']?:'None reported')?></td></tr>
<tr><td style="color:#999;font-weight:600;border:0;padding:5px 0;font-size:.82rem">Existing Conditions</td><td style="border:0;padding:5px 0;font-size:.84rem"><?=sanitize_output($p['existing_conditions']?:'None reported')?></td></tr>
</tbody></table>
<div style="margin-top:16px;padding-top:14px;border-top:1px solid #f0f0f0">
<div class="text-xs text-muted text-bold" style="margin-bottom:6px">EMERGENCY CONTACT</div>
<div class="text-sm"><?=sanitize_output($p['emergency_contact_name']??'—')?> (<?=sanitize_output($p['emergency_contact_relation']??'—')?>)</div>
<div class="text-sm text-muted"><?=sanitize_output($p['emergency_contact_phone']??'')?></div>
</div>
<div class="flex gap-8 mt-16"><a href="?edit=<?=$p['patient_id']?>" class="btn btn-sm btn-s">Edit Profile</a></div>
</div></div>
</div>

<!-- Medical Records Card -->
<div>
<div class="card">
<div class="card-hd"><h5>Medical Records</h5><a href="#add-record" uk-toggle class="btn btn-sm btn-p">+ Add Record</a></div>
<div class="card-bd np">
<?php if(!empty($medRecords)):?>
<table class="tbl"><thead><tr><th>Type</th><th>Title</th><th>Date</th><th>Physician</th></tr></thead><tbody>
<?php foreach($medRecords as $mr):?><tr>
<td><?=get_status_badge($mr['record_type'])?></td>
<td><span class="text-bold"><?=sanitize_output($mr['title'])?></span><?php if($mr['findings']):?><br><span class="text-xs text-muted"><?=sanitize_output(substr($mr['findings'],0,60))?><?=strlen($mr['findings'])>60?'...':''?></span><?php endif;?></td>
<td class="text-xs text-muted"><?=format_date($mr['record_date'])?></td>
<td class="text-xs"><?=sanitize_output($mr['attending_physician']??'—')?></td>
</tr><?php endforeach;?>
</tbody></table>
<?php else:?>
<div class="text-center text-muted" style="padding:28px;font-size:.84rem">No medical records yet</div>
<?php endif;?>
</div></div>
</div>
</div>

<!-- Add Record Modal -->
<div id="add-record" uk-modal><div class="uk-modal-dialog"><div class="uk-modal-header"><h5 class="uk-modal-title" style="font-size:.95rem">Add Medical Record</h5></div>
<form method="POST"><div class="uk-modal-body">
<input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="add_record"><input type="hidden" name="patient_id" value="<?=$p['patient_id']?>">
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-2"><label class="uk-form-label">Record Type *</label><select name="record_type" class="uk-select uk-form-small" required><option>Physical Exam</option><option>Lab Result</option><option>Vaccination</option><option>Dental</option><option>Vision</option><option>Xray</option><option>Medical History</option><option>Other</option></select></div>
<div class="uk-width-1-2"><label class="uk-form-label">Record Date *</label><input type="date" name="record_date" class="uk-input uk-form-small" value="<?=date('Y-m-d')?>" required></div>
<div class="uk-width-1-1"><label class="uk-form-label">Title *</label><input type="text" name="title" class="uk-input uk-form-small" placeholder="e.g. Annual Physical Examination" required></div>
<div class="uk-width-1-1"><label class="uk-form-label">Description</label><textarea name="description" class="uk-textarea uk-form-small" rows="2" placeholder="General description of the record"></textarea></div>
<div class="uk-width-1-1"><label class="uk-form-label">Findings</label><textarea name="findings" class="uk-textarea uk-form-small" rows="2" placeholder="Results, observations, diagnoses..."></textarea></div>
<div class="uk-width-1-1"><label class="uk-form-label">Attending Physician</label><input type="text" name="attending_physician" class="uk-input uk-form-small" placeholder="Dr. Name"></div>
</div></div>
<div class="uk-modal-footer uk-text-right"><button class="uk-button uk-button-default uk-modal-close uk-button-small" type="button">Cancel</button> <button class="uk-button uk-button-primary uk-button-small">Save Record</button></div>
</form></div></div>

<?php elseif($showEdit&&$editPatient):$p=$editPatient;?>
<!-- ===================== EDIT FORM ===================== -->
<a href="?view=<?=$p['patient_id']?>" class="btn-txt" style="margin-bottom:14px;display:inline-block">&larr; Back to profile</a>
<div class="card"><div class="card-hd"><h5>Edit Patient — <?=sanitize_output($p['first_name'].' '.$p['last_name'])?></h5></div>
<div class="card-bd"><form method="POST" class="uk-form-stacked">
<input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="update_patient"><input type="hidden" name="patient_id" value="<?=$p['patient_id']?>">
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-3@m"><label class="uk-form-label">First Name *</label><input type="text" name="first_name" class="uk-input uk-form-small" value="<?=sanitize_output($p['first_name'])?>" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Middle Name</label><input type="text" name="middle_name" class="uk-input uk-form-small" value="<?=sanitize_output($p['middle_name']??'')?>"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Last Name *</label><input type="text" name="last_name" class="uk-input uk-form-small" value="<?=sanitize_output($p['last_name'])?>" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Email</label><input type="email" name="email" class="uk-input uk-form-small" value="<?=sanitize_output($p['email']??'')?>"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Phone</label><input type="text" name="phone" class="uk-input uk-form-small" value="<?=sanitize_output($p['phone']??'')?>"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Date of Birth</label><input type="date" name="date_of_birth" class="uk-input uk-form-small" value="<?=$p['date_of_birth']??''?>"></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Gender</label><select name="gender" class="uk-select uk-form-small"><option value="">Select</option><?php foreach(['Male','Female','Other'] as $g):?><option <?=($p['gender']??'')===$g?'selected':''?>><?=$g?></option><?php endforeach;?></select></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Program</label><input type="text" name="program" class="uk-input uk-form-small" value="<?=sanitize_output($p['program']??'')?>"></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Year Level</label><select name="year_level" class="uk-select uk-form-small"><?php for($i=1;$i<=5;$i++):?><option <?=($p['year_level']??1)==$i?'selected':''?>><?=$i?></option><?php endfor;?></select></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Blood Type</label><select name="blood_type" class="uk-select uk-form-small"><?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-','Unknown'] as $bt):?><option <?=($p['blood_type']??'')===$bt?'selected':''?>><?=$bt?></option><?php endforeach;?></select></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Allergies</label><textarea name="allergies" class="uk-textarea uk-form-small" rows="2"><?=sanitize_output($p['allergies']??'')?></textarea></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Existing Conditions</label><textarea name="existing_conditions" class="uk-textarea uk-form-small" rows="2"><?=sanitize_output($p['existing_conditions']??'')?></textarea></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Emergency Contact Name</label><input type="text" name="emergency_contact_name" class="uk-input uk-form-small" value="<?=sanitize_output($p['emergency_contact_name']??'')?>"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Emergency Phone</label><input type="text" name="emergency_contact_phone" class="uk-input uk-form-small" value="<?=sanitize_output($p['emergency_contact_phone']??'')?>"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Relation</label><input type="text" name="emergency_contact_relation" class="uk-input uk-form-small" value="<?=sanitize_output($p['emergency_contact_relation']??'')?>"></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Status</label><select name="status" class="uk-select uk-form-small"><?php foreach(['Active','Inactive','Graduated'] as $ss):?><option <?=($p['status']??'')===$ss?'selected':''?>><?=$ss?></option><?php endforeach;?></select></div>
<div class="uk-width-1-1 uk-margin-top"><button class="btn btn-p">Save Changes</button> <a href="?view=<?=$p['patient_id']?>" class="btn btn-s">Cancel</a></div>
</div></form></div></div>

<?php elseif($showCreate):?>
<!-- ===================== REGISTER PATIENT FORM ===================== -->
<a href="?" class="btn-txt" style="margin-bottom:14px;display:inline-block">&larr; Back</a>
<div class="card"><div class="card-hd"><h5>Register New Patient</h5></div>
<div class="card-bd"><form method="POST" class="uk-form-stacked">
<input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="register_patient">
<div class="text-xs text-muted text-bold mb-16" style="text-transform:uppercase;letter-spacing:1px">Basic Information</div>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-3@m"><label class="uk-form-label">Student Number *</label><input type="text" name="student_number" class="uk-input uk-form-small" placeholder="e.g. 2025-00001" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">First Name *</label><input type="text" name="first_name" class="uk-input uk-form-small" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Last Name *</label><input type="text" name="last_name" class="uk-input uk-form-small" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Middle Name</label><input type="text" name="middle_name" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Email</label><input type="email" name="email" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Phone</label><input type="text" name="phone" class="uk-input uk-form-small"></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Date of Birth</label><input type="date" name="date_of_birth" class="uk-input uk-form-small"></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Gender</label><select name="gender" class="uk-select uk-form-small"><option value="">Select</option><option>Male</option><option>Female</option><option>Other</option></select></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Program</label><input type="text" name="program" class="uk-input uk-form-small" placeholder="e.g. BSCS"></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Year Level</label><select name="year_level" class="uk-select uk-form-small"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select></div>
</div>
<div class="text-xs text-muted text-bold mb-16 mt-24" style="text-transform:uppercase;letter-spacing:1px">Health Information</div>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-4@m"><label class="uk-form-label">Blood Type</label><select name="blood_type" class="uk-select uk-form-small"><option>Unknown</option><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option><option>O+</option><option>O-</option></select></div>
<div class="uk-width-3-4@m"><label class="uk-form-label">Allergies</label><textarea name="allergies" class="uk-textarea uk-form-small" rows="1" placeholder="List known allergies, or leave blank if none"></textarea></div>
<div class="uk-width-1-1"><label class="uk-form-label">Existing Conditions</label><textarea name="existing_conditions" class="uk-textarea uk-form-small" rows="2" placeholder="Asthma, diabetes, etc."></textarea></div>
</div>
<div class="text-xs text-muted text-bold mb-16 mt-24" style="text-transform:uppercase;letter-spacing:1px">Emergency Contact</div>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-3@m"><label class="uk-form-label">Contact Name</label><input type="text" name="emergency_contact_name" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Contact Phone</label><input type="text" name="emergency_contact_phone" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Relation</label><input type="text" name="emergency_contact_relation" class="uk-input uk-form-small" placeholder="Parent, Sibling..."></div>
<div class="uk-width-1-1 uk-margin-top"><button class="btn btn-p">Register Patient</button> <a href="?" class="btn btn-s">Cancel</a></div>
</div>
</form></div></div>

<?php else:?>
<!-- ===================== TABLE LIST ===================== -->
<div class="filter-bar"><form class="flex flex-middle gap-8 flex-wrap">
<input type="text" name="search" class="uk-input uk-form-small" style="border-radius:8px;max-width:260px" placeholder="Search name, student no..." value="<?=sanitize_output($filters['search'])?>">
<button class="btn btn-sm">Filter</button>
<?php if($filters['search']||$filters['status']):?><a href="?" class="btn btn-sm btn-txt">Clear</a><?php endif;?>
</form></div>

<div class="card"><div class="card-bd np"><div class="overflow-x">
<table class="tbl"><thead><tr><th>Student No.</th><th>Name</th><th>Program</th><th>Gender</th><th>Blood Type</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach($patients as $p):?><tr>
<td><code><?=sanitize_output($p['student_number'])?></code></td>
<td class="text-bold"><?=sanitize_output($p['first_name'].' '.$p['last_name'])?></td>
<td class="text-sm text-muted"><?=sanitize_output($p['program']??'—')?> — Yr <?=$p['year_level']??''?></td>
<td class="text-sm"><?=sanitize_output($p['gender']??'—')?></td>
<td><?=get_status_badge($p['blood_type']??'Unknown')?></td>
<td><?=get_status_badge($p['status'])?></td>
<td><a href="?view=<?=$p['patient_id']?>" class="btn-txt" style="font-size:.78rem">View →</a></td>
</tr><?php endforeach;?>
<?php if(empty($patients)):?><tr><td colspan="7" class="empty">No patients found. <a href="?action=register">Register a patient</a></td></tr><?php endif;?>
</tbody></table>
</div></div></div>
<?php endif;?>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>
