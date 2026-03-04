<?php
declare(strict_types=1);
require_once __DIR__.'/../../controllers/EmployeeRecordsController.php';
$ctrl=new EmployeeRecordsController();
if($_SERVER['REQUEST_METHOD']==='POST'&&validate_csrf_token($_POST['csrf_token']??'')){
    $id=$ctrl->store(['first_name'=>trim($_POST['first_name']),'last_name'=>trim($_POST['last_name']),'middle_name'=>trim($_POST['middle_name']??''),'email'=>trim($_POST['email']),'phone'=>trim($_POST['phone']??''),'address'=>trim($_POST['address']??''),'date_of_birth'=>$_POST['date_of_birth']?:null,'gender'=>$_POST['gender']?:null,'civil_status'=>$_POST['civil_status']?:'Single','position_id'=>(int)$_POST['position_id']?:null,'department_id'=>(int)$_POST['department_id']?:null,'employment_type'=>$_POST['employment_type'],'employment_status'=>$_POST['employment_status'],'date_hired'=>$_POST['date_hired'],'salary_grade'=>trim($_POST['salary_grade']??''),'basic_salary'=>(float)$_POST['basic_salary']?:null,'sss_no'=>trim($_POST['sss_no']??''),'philhealth_no'=>trim($_POST['philhealth_no']??''),'pagibig_no'=>trim($_POST['pagibig_no']??''),'tin_no'=>trim($_POST['tin_no']??''),'emergency_contact_name'=>trim($_POST['emergency_contact_name']??''),'emergency_contact_phone'=>trim($_POST['emergency_contact_phone']??''),'emergency_contact_relation'=>trim($_POST['emergency_contact_relation']??'')]);
    flash($id?'success':'error',$id?'Employee created.':'Failed.');redirect(get_module_path('employee_records').'/index.php'.($id?"?view=$id":''));
}
$positions=$ctrl->getPositions();$departments=$ctrl->getDepartments();
$pageTitle='New Employee';include __DIR__.'/../../includes/layout_top.php';
?>
<a href="<?=get_module_path('employee_records')?>/index.php" class="uk-button uk-button-text uk-margin-bottom" style="font-size:.82rem">&larr; Back</a>
<form method="POST" class="uk-form-stacked"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>">
<div class="uk-card uk-card-default uk-margin-bottom"><div class="uk-card-header"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Personal Information</h5></div><div class="uk-card-body"><div class="uk-grid-small" uk-grid>
<div class="uk-width-1-3@m"><label class="uk-form-label">First Name *</label><input type="text" name="first_name" class="uk-input uk-form-small" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Middle</label><input type="text" name="middle_name" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Last Name *</label><input type="text" name="last_name" class="uk-input uk-form-small" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Email *</label><input type="email" name="email" class="uk-input uk-form-small" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Phone</label><input type="text" name="phone" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">DOB</label><input type="date" name="date_of_birth" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Gender</label><select name="gender" class="uk-select uk-form-small"><option value="">-</option><option>Male</option><option>Female</option></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Civil Status</label><select name="civil_status" class="uk-select uk-form-small"><option>Single</option><option>Married</option><option>Widowed</option><option>Separated</option></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Address</label><input type="text" name="address" class="uk-input uk-form-small"></div>
</div></div></div>
<div class="uk-card uk-card-default uk-margin-bottom"><div class="uk-card-header"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Employment</h5></div><div class="uk-card-body"><div class="uk-grid-small" uk-grid>
<div class="uk-width-1-3@m"><label class="uk-form-label">Position</label><select name="position_id" class="uk-select uk-form-small"><option value="">-</option><?php foreach($positions as $p):?><option value="<?=$p['position_id']?>"><?=sanitize_output($p['title'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Department</label><select name="department_id" class="uk-select uk-form-small"><option value="">-</option><?php foreach($departments as $d):?><option value="<?=$d['department_id']?>"><?=sanitize_output($d['department_name'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Date Hired *</label><input type="date" name="date_hired" class="uk-input uk-form-small" value="<?=date('Y-m-d')?>" required></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Type</label><select name="employment_type" class="uk-select uk-form-small"><option>Probationary</option><option>Full-Time</option><option>Part-Time</option><option>Contractual</option></select></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Status</label><select name="employment_status" class="uk-select uk-form-small"><option>Probationary</option><option>Active</option><option>Regular</option></select></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Salary Grade</label><input type="text" name="salary_grade" class="uk-input uk-form-small"></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Basic Salary</label><input type="number" name="basic_salary" class="uk-input uk-form-small" step=".01"></div>
</div></div></div>
<div class="uk-card uk-card-default uk-margin-bottom"><div class="uk-card-header"><h5 class="uk-margin-remove" style="font-weight:600;font-size:.9rem">Gov IDs &amp; Emergency</h5></div><div class="uk-card-body"><div class="uk-grid-small" uk-grid>
<div class="uk-width-1-4@m"><label class="uk-form-label">SSS</label><input type="text" name="sss_no" class="uk-input uk-form-small"></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">PhilHealth</label><input type="text" name="philhealth_no" class="uk-input uk-form-small"></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Pag-IBIG</label><input type="text" name="pagibig_no" class="uk-input uk-form-small"></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">TIN</label><input type="text" name="tin_no" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Emergency Contact</label><input type="text" name="emergency_contact_name" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Contact Phone</label><input type="text" name="emergency_contact_phone" class="uk-input uk-form-small"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Relationship</label><input type="text" name="emergency_contact_relation" class="uk-input uk-form-small"></div>
</div></div></div>
<button class="uk-button uk-button-primary">Create Employee</button>
<a href="<?=get_module_path('employee_records')?>/index.php" class="uk-button uk-button-default">Cancel</a>
</form>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>