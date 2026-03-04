<?php
declare(strict_types=1);
require_once __DIR__.'/../../controllers/ClinicController.php';
$ctrl=new ClinicController();

if($_SERVER['REQUEST_METHOD']==='POST'&&validate_csrf_token($_POST['csrf_token']??'')){
    $a=$_POST['form_action']??'';
    if($a==='dispense'){
        $id=$ctrl->dispense([
            'patient_id'=>(int)$_POST['patient_id'],
            'consultation_id'=>$_POST['consultation_id']?(int)$_POST['consultation_id']:null,
            'medicine_id'=>(int)$_POST['medicine_id'],
            'quantity'=>(int)$_POST['quantity'],
            'dosage_instructions'=>trim($_POST['dosage_instructions']??''),
            'remarks'=>trim($_POST['remarks']??'')
        ]);
        if($id){flash('success','Medicine dispensed successfully.');}
        else{flash('error','Failed — insufficient stock or invalid data.');}
        redirect($_SERVER['PHP_SELF']);
    }
}

$logs=$ctrl->getDispensingLogs();
$patients=$ctrl->getAllPatients();
$medicines=$ctrl->getAvailableMedicines();

$pageTitle='Medicine Dispensing';include __DIR__.'/../../includes/layout_top.php';
?>

<div class="page-hd">
<div><h3>Medicine Dispensing</h3><p>Dispense medicines to patients and track dispensing history</p></div>
<div class="flex gap-8">
<a href="<?=get_module_path('medicine_inventory')?>/index.php" class="btn btn-s">← Inventory</a>
<a href="#dispense-form" class="btn btn-p" onclick="document.getElementById('dispense-panel').style.display='block';this.style.display='none'"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Dispense Medicine</a>
</div>
</div>

<!-- Dispense Form (hidden by default) -->
<div id="dispense-panel" style="display:none">
<div class="card mb-24"><div class="card-hd"><h5>Dispense Medicine</h5><button class="btn-icon" onclick="this.closest('#dispense-panel').style.display='none'">&times;</button></div>
<div class="card-bd"><form method="POST" class="uk-form-stacked">
<input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="dispense">
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-3@m"><label class="uk-form-label">Patient *</label><select name="patient_id" class="uk-select uk-form-small" required><option value="">Select patient</option><?php foreach($patients as $pp):?><option value="<?=$pp['patient_id']?>"><?=sanitize_output($pp['student_number'].' — '.$pp['last_name'].', '.$pp['first_name'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Medicine *</label><select name="medicine_id" class="uk-select uk-form-small" required><option value="">Select medicine</option><?php foreach($medicines as $m):?><option value="<?=$m['medicine_id']?>"><?=sanitize_output($m['name'].' ('.$m['generic_name'].')')?> — Stock: <?=$m['stock_quantity']?></option><?php endforeach;?></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Quantity *</label><input type="number" name="quantity" class="uk-input uk-form-small" min="1" value="1" required></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Dosage Instructions</label><input type="text" name="dosage_instructions" class="uk-input uk-form-small" placeholder="e.g. 1 tablet 3x daily after meals"></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Consultation ID (optional)</label><input type="number" name="consultation_id" class="uk-input uk-form-small" placeholder="Link to consultation"></div>
<div class="uk-width-1-1"><label class="uk-form-label">Remarks</label><input type="text" name="remarks" class="uk-input uk-form-small" placeholder="Additional notes"></div>
<div class="uk-width-1-1 uk-margin-top"><button class="btn btn-p">Dispense</button> <button type="button" class="btn btn-s" onclick="document.getElementById('dispense-panel').style.display='none'">Cancel</button></div>
</div></form></div></div>
</div>

<!-- Dispensing Log -->
<div class="card"><div class="card-hd"><h5>Dispensing Log</h5><span class="text-xs text-muted"><?=count($logs)?> record(s)</span></div>
<div class="card-bd np"><div class="overflow-x">
<table class="tbl"><thead><tr><th>Date</th><th>Patient</th><th>Medicine</th><th>Qty</th><th>Dosage Instructions</th><th>Dispensed By</th><th>Remarks</th></tr></thead><tbody>
<?php foreach($logs as $l):?><tr>
<td class="text-xs text-muted" style="white-space:nowrap"><?=format_date($l['dispensed_date'],'M d, Y')?><br><?=format_date($l['dispensed_date'],'h:i A')?></td>
<td><span class="text-bold"><?=sanitize_output($l['patient_name']??'—')?></span><br><span class="text-xs text-muted"><?=sanitize_output($l['student_number']??'')?></span></td>
<td class="text-sm"><?=sanitize_output($l['medicine_name']??'—')?></td>
<td class="text-bold"><?=$l['quantity']?></td>
<td class="text-sm text-muted"><?=sanitize_output($l['dosage_instructions']??'—')?></td>
<td class="text-xs text-muted"><?=sanitize_output($l['dispensed_by_name']??'—')?></td>
<td class="text-xs text-muted"><?=sanitize_output($l['remarks']??'')?></td>
</tr><?php endforeach;?>
<?php if(empty($logs)):?><tr><td colspan="7" class="empty">No dispensing records yet</td></tr><?php endif;?>
</tbody></table>
</div></div></div>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>
