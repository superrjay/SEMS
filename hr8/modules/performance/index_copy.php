<?php
declare(strict_types=1);
require_once __DIR__ . '/../../controllers/PerformanceController.php';
$ctrl = new PerformanceController();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && validate_csrf_token($_POST['csrf_token'] ?? '')) {
    $a = $_POST['form_action'] ?? '';
    if ($a === 'create_eval') {
        $id = $ctrl->createEvaluation(['employee_id'=>(int)$_POST['employee_id'],'evaluation_period'=>$_POST['evaluation_period'],'evaluation_date'=>$_POST['evaluation_date'],'job_knowledge'=>(int)$_POST['job_knowledge'],'work_quality'=>(int)$_POST['work_quality'],'productivity'=>(int)$_POST['productivity'],'communication'=>(int)$_POST['communication'],'teamwork'=>(int)$_POST['teamwork'],'attendance'=>(int)$_POST['attendance'],'initiative'=>(int)$_POST['initiative'],'strengths'=>trim($_POST['strengths']??''),'areas_for_improvement'=>trim($_POST['areas_for_improvement']??''),'goals'=>trim($_POST['goals']??''),'status'=>'Draft']);
        flash($id?'success':'error',$id?'Evaluation created.':'Failed.'); redirect($_SERVER['PHP_SELF']);
    }
    if ($a === 'submit_eval') { $ctrl->updateEvaluation((int)$_POST['eval_id'],['status'=>'Submitted']); flash('success','Submitted.'); redirect($_SERVER['PHP_SELF']); }
}
$filters=['status'=>$_GET['status']??'','period'=>$_GET['period']??''];
$data=$ctrl->evaluations($filters); $showCreate=($_GET['action']??'')==='create'; $employees=$showCreate?$ctrl->getEmployees():[];
$showView=isset($_GET['view']); $evalDetail=$showView?$ctrl->showEvaluation((int)$_GET['view']):null;
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Performance - HR8</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head><body>
<?php include __DIR__.'/../../includes/navbar.php'; ?><div class="container-fluid"><div class="row">
    <?php include __DIR__.'/../../includes/sidebar.php'; ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
<?php if($msg=flash('success')):?><div class="alert alert-success alert-dismissible fade show"><?=sanitize_output($msg)?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif;?>
<div class="d-flex justify-content-between align-items-center mb-4"><div><h4 class="fw-bold mb-1"><i class="fas fa-chart-line me-2 text-warning"></i>Performance Evaluations</h4><p class="text-muted mb-0">Employee performance reviews &amp; ratings</p></div><a href="?action=create" class="btn btn-warning text-white"><i class="fas fa-plus me-2"></i>New Evaluation</a></div>

<?php if($showView && $evalDetail): ?>
<div class="card border-0 shadow-sm"><div class="card-header bg-white d-flex justify-content-between"><h5 class="mb-0">Evaluation: <?=sanitize_output($evalDetail['employee_name'])?></h5><a href="?" class="btn btn-outline-secondary btn-sm">Back</a></div><div class="card-body">
<div class="row mb-3"><div class="col-md-6"><table class="table table-sm"><tr><th>Employee</th><td><?=sanitize_output($evalDetail['employee_name'])?> (<?=sanitize_output($evalDetail['employee_no'])?>)</td></tr><tr><th>Period</th><td><?=sanitize_output($evalDetail['evaluation_period'])?></td></tr><tr><th>Evaluator</th><td><?=sanitize_output($evalDetail['evaluator_name']??'N/A')?></td></tr><tr><th>Date</th><td><?=format_date($evalDetail['evaluation_date'])?></td></tr><tr><th>Status</th><td><?=get_status_badge($evalDetail['status'])?></td></tr></table></div>
<div class="col-md-6"><table class="table table-sm"><tr><th>Overall Rating</th><td><h4><?=$evalDetail['overall_rating']?>/5.00</h4></td></tr><tr><th>Grade</th><td><?=get_status_badge($evalDetail['overall_grade']??'N/A')?></td></tr></table>
<div class="row g-2 text-center"><?php foreach(['job_knowledge'=>'Knowledge','work_quality'=>'Quality','productivity'=>'Productivity','communication'=>'Communication','teamwork'=>'Teamwork','attendance'=>'Attendance','initiative'=>'Initiative'] as $k=>$label):?><div class="col"><small class="text-muted d-block"><?=$label?></small><h5 class="mb-0"><?=$evalDetail[$k]??'-'?></h5></div><?php endforeach;?></div></div></div>
<div class="row"><div class="col-md-4"><h6>Strengths</h6><p><?=nl2br(sanitize_output($evalDetail['strengths']??'N/A'))?></p></div><div class="col-md-4"><h6>Areas for Improvement</h6><p><?=nl2br(sanitize_output($evalDetail['areas_for_improvement']??'N/A'))?></p></div><div class="col-md-4"><h6>Goals</h6><p><?=nl2br(sanitize_output($evalDetail['goals']??'N/A'))?></p></div></div>
</div></div>

<?php elseif($showCreate):?>
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><h5 class="mb-0">New Performance Evaluation</h5></div><div class="card-body">
<form method="POST"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="create_eval"><div class="row g-3">
<div class="col-md-4"><label class="form-label">Employee *</label><select name="employee_id" class="form-select" required><option value="">Select</option><?php foreach($employees as $e):?><option value="<?=$e['employee_id']?>"><?=sanitize_output($e['employee_no'].' - '.$e['first_name'].' '.$e['last_name'])?></option><?php endforeach;?></select></div>
<div class="col-md-4"><label class="form-label">Period *</label><input type="text" name="evaluation_period" class="form-control" placeholder="e.g., Q1 2026, Annual 2025" required></div>
<div class="col-md-4"><label class="form-label">Date *</label><input type="date" name="evaluation_date" class="form-control" value="<?=date('Y-m-d')?>" required></div>
<div class="col-12"><hr><h6>Scores (1 = Lowest, 5 = Highest)</h6></div>
<?php foreach(['job_knowledge'=>'Job Knowledge','work_quality'=>'Work Quality','productivity'=>'Productivity','communication'=>'Communication','teamwork'=>'Teamwork','attendance'=>'Attendance','initiative'=>'Initiative'] as $k=>$l):?>
<div class="col-md-3 col-6"><label class="form-label"><?=$l?></label><select name="<?=$k?>" class="form-select" required><option value="">-</option><?php for($i=1;$i<=5;$i++):?><option value="<?=$i?>"><?=$i?></option><?php endfor;?></select></div>
<?php endforeach;?>
<div class="col-md-4"><label class="form-label">Strengths</label><textarea name="strengths" class="form-control" rows="3"></textarea></div>
<div class="col-md-4"><label class="form-label">Areas for Improvement</label><textarea name="areas_for_improvement" class="form-control" rows="3"></textarea></div>
<div class="col-md-4"><label class="form-label">Goals</label><textarea name="goals" class="form-control" rows="3"></textarea></div>
<div class="col-12"><button class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Evaluation</button> <a href="?" class="btn btn-outline-secondary">Cancel</a></div>
</div></form></div></div>

<?php else:?>
<div class="card border-0 shadow-sm"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0">
<thead class="table-light"><tr><th>Employee</th><th>Period</th><th>Rating</th><th>Grade</th><th>Evaluator</th><th>Date</th><th>Status</th><th>Act</th></tr></thead><tbody>
<?php foreach($data['evaluations'] as $ev):?><tr>
<td><strong><?=sanitize_output($ev['employee_name'])?></strong><br><small class="text-muted"><?=sanitize_output($ev['employee_no'])?></small></td>
<td><?=sanitize_output($ev['evaluation_period'])?></td>
<td><strong><?=$ev['overall_rating']?></strong>/5</td>
<td><?=get_status_badge($ev['overall_grade']??'N/A')?></td>
<td><?=sanitize_output($ev['evaluator_name']??'N/A')?></td>
<td><?=format_date($ev['evaluation_date'])?></td>
<td><?=get_status_badge($ev['status'])?></td>
<td><a href="?view=<?=$ev['eval_id']?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
<?php if($ev['status']==='Draft'):?><form method="POST" class="d-inline"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="submit_eval"><input type="hidden" name="eval_id" value="<?=$ev['eval_id']?>"><button class="btn btn-sm btn-outline-success" title="Submit"><i class="fas fa-paper-plane"></i></button></form><?php endif;?></td>
</tr><?php endforeach;?>
<?php if(empty($data['evaluations'])):?><tr><td colspan="8" class="text-center text-muted py-4">No evaluations</td></tr><?php endif;?>
</tbody></table></div></div></div>
<?php endif;?>
</main></div></div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script></body></html>