<?php
declare(strict_types=1);
require_once __DIR__ . '/../../controllers/PerformanceController.php';
$ctrl = new PerformanceController();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && validate_csrf_token($_POST['csrf_token'] ?? '')) {
    $ctrl->createDisciplinary(['employee_id'=>(int)$_POST['employee_id'],'type'=>$_POST['type'],'subject'=>trim($_POST['subject']),'description'=>trim($_POST['description']??''),'incident_date'=>$_POST['incident_date']?:null,'action_taken'=>trim($_POST['action_taken']??''),'status'=>'Active']);
    flash('success','Record created.'); redirect($_SERVER['PHP_SELF']);
}
$data=$ctrl->disciplinaryRecords(['type'=>$_GET['type']??'']); $showCreate=($_GET['action']??'')==='create'; $employees=$showCreate?$ctrl->getEmployees():[];
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Service Records - HR8</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head><body>
<?php include __DIR__.'/../../includes/navbar.php'; ?><div class="container-fluid"><div class="row"><?php include __DIR__.'/../../includes/sidebar.php'; ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
<?php if($msg=flash('success')):?><div class="alert alert-success alert-dismissible fade show"><?=sanitize_output($msg)?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif;?>
<div class="d-flex justify-content-between align-items-center mb-4"><div><h4 class="fw-bold mb-1"><i class="fas fa-gavel me-2 text-danger"></i>Service &amp; Disciplinary Records</h4><p class="text-muted mb-0">Warnings, commendations, awards &amp; memos</p></div><a href="?action=create" class="btn btn-danger"><i class="fas fa-plus me-2"></i>New Record</a></div>
<?php if($showCreate):?>
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><div class="row g-3">
<div class="col-md-4"><label class="form-label">Employee *</label><select name="employee_id" class="form-select" required><option value="">Select</option><?php foreach($employees as $e):?><option value="<?=$e['employee_id']?>"><?=sanitize_output($e['employee_no'].' - '.$e['first_name'].' '.$e['last_name'])?></option><?php endforeach;?></select></div>
<div class="col-md-4"><label class="form-label">Type *</label><select name="type" class="form-select" required><option>Verbal Warning</option><option>Written Warning</option><option>Suspension</option><option>Commendation</option><option>Award</option><option>Memo</option></select></div>
<div class="col-md-4"><label class="form-label">Incident Date</label><input type="date" name="incident_date" class="form-control"></div>
<div class="col-md-6"><label class="form-label">Subject *</label><input type="text" name="subject" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Action Taken</label><input type="text" name="action_taken" class="form-control"></div>
<div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
<div class="col-12"><button class="btn btn-primary"><i class="fas fa-save me-2"></i>Save</button> <a href="?" class="btn btn-outline-secondary">Cancel</a></div>
</div></form></div></div>
<?php else:?>
<div class="card border-0 shadow-sm"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0">
<thead class="table-light"><tr><th>Employee</th><th>Type</th><th>Subject</th><th>Date</th><th>Status</th><th>Issued By</th></tr></thead><tbody>
<?php foreach($data['records'] as $r):?><tr>
<td><strong><?=sanitize_output($r['employee_name'])?></strong></td>
<td><?php $colors=['Commendation'=>'success','Award'=>'primary','Verbal Warning'=>'warning','Written Warning'=>'danger','Suspension'=>'danger','Memo'=>'info']; ?><span class="badge bg-<?=$colors[$r['type']]??'secondary'?>"><?=sanitize_output($r['type'])?></span></td>
<td><?=sanitize_output($r['subject'])?></td>
<td><?=format_date($r['incident_date']??'')?></td>
<td><?=get_status_badge($r['status'])?></td>
<td><?=sanitize_output($r['issued_by_name']??'N/A')?></td>
</tr><?php endforeach;?>
<?php if(empty($data['records'])):?><tr><td colspan="6" class="text-center text-muted py-4">No records</td></tr><?php endif;?>
</tbody></table></div></div></div>
<?php endif;?>
</main></div></div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script></body></html>