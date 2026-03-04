<?php
declare(strict_types=1);
require_once __DIR__.'/../../controllers/ClinicController.php';
$ctrl=new ClinicController();

if($_SERVER['REQUEST_METHOD']==='POST'&&validate_csrf_token($_POST['csrf_token']??'')){
    $a=$_POST['form_action']??'';
    if($a==='add_medicine'){
        $id=$ctrl->storeMedicine([
            'name'=>trim($_POST['name']),
            'generic_name'=>trim($_POST['generic_name']??''),
            'category'=>$_POST['category']??'Other',
            'dosage_form'=>$_POST['dosage_form']??'Tablet',
            'unit'=>trim($_POST['unit']??'pcs'),
            'stock_quantity'=>(int)$_POST['stock_quantity'],
            'reorder_level'=>(int)($_POST['reorder_level']??10),
            'expiry_date'=>$_POST['expiry_date']?:null,
            'supplier'=>trim($_POST['supplier']??''),
            'unit_cost'=>(float)($_POST['unit_cost']??0)
        ]);
        flash($id?'success':'error',$id?'Medicine added.':'Failed.');
        redirect($_SERVER['PHP_SELF']);
    }
    if($a==='update_medicine'){
        $mid=(int)$_POST['medicine_id'];
        $r=$ctrl->updateMedicine($mid,[
            'name'=>trim($_POST['name']),
            'generic_name'=>trim($_POST['generic_name']??''),
            'category'=>$_POST['category'],
            'dosage_form'=>$_POST['dosage_form'],
            'unit'=>trim($_POST['unit']??'pcs'),
            'stock_quantity'=>(int)$_POST['stock_quantity'],
            'reorder_level'=>(int)$_POST['reorder_level'],
            'expiry_date'=>$_POST['expiry_date']?:null,
            'supplier'=>trim($_POST['supplier']??''),
            'unit_cost'=>(float)$_POST['unit_cost']
        ]);
        flash($r?'success':'error',$r?'Medicine updated.':'Update failed.');
        redirect($_SERVER['PHP_SELF']);
    }
}

$medicines=$ctrl->getMedicines();
$showAdd=($_GET['action']??'')==='add';
$showEdit=isset($_GET['edit']);
$editMed=null;
if($showEdit){foreach($medicines as $m)if($m['medicine_id']==(int)$_GET['edit'])$editMed=$m;}

// Stats
$stats=['total'=>count($medicines),'available'=>0,'low'=>0,'out'=>0,'expired'=>0];
foreach($medicines as $m){
    if($m['status']==='Available')$stats['available']++;
    elseif($m['status']==='Low Stock')$stats['low']++;
    elseif($m['status']==='Out of Stock')$stats['out']++;
    elseif($m['status']==='Expired')$stats['expired']++;
}

$pageTitle='Medicine Inventory';include __DIR__.'/../../includes/layout_top.php';
?>

<div class="page-hd">
<div><h3>Medicine Inventory</h3><p>Track medicine stock, expiry dates &amp; reorder levels</p></div>
<div class="flex gap-8">
<a href="<?=get_module_path('medicine_inventory')?>/dispensing.php" class="btn btn-s"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/></svg> Dispensing Log</a>
<a href="?action=add" class="btn btn-p"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Medicine</a>
</div>
</div>

<div class="st-row">
<div class="st"><div class="st-v" style="color:#0f766e"><?=$stats['available']?></div><div class="st-l">Available</div></div>
<div class="st"><div class="st-v" style="color:#92400e"><?=$stats['low']?></div><div class="st-l">Low Stock</div></div>
<div class="st"><div class="st-v" style="color:#991b1b"><?=$stats['out']?></div><div class="st-l">Out of Stock</div></div>
<div class="st"><div class="st-v" style="color:#1f2937"><?=$stats['expired']?></div><div class="st-l">Expired</div></div>
</div>

<?php if($showAdd||($showEdit&&$editMed)):$m=$editMed;$isEdit=(bool)$m;?>
<!-- ===================== ADD / EDIT FORM ===================== -->
<a href="?" class="btn-txt" style="margin-bottom:14px;display:inline-block">&larr; Back</a>
<div class="card"><div class="card-hd"><h5><?=$isEdit?'Edit':'Add New'?> Medicine</h5></div>
<div class="card-bd"><form method="POST" class="uk-form-stacked">
<input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>">
<input type="hidden" name="form_action" value="<?=$isEdit?'update_medicine':'add_medicine'?>">
<?php if($isEdit):?><input type="hidden" name="medicine_id" value="<?=$m['medicine_id']?>"><?php endif;?>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-2@m"><label class="uk-form-label">Brand Name *</label><input type="text" name="name" class="uk-input uk-form-small" value="<?=sanitize_output($m['name']??'')?>" required></div>
<div class="uk-width-1-2@m"><label class="uk-form-label">Generic Name</label><input type="text" name="generic_name" class="uk-input uk-form-small" value="<?=sanitize_output($m['generic_name']??'')?>"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Category</label><select name="category" class="uk-select uk-form-small">
<?php foreach(['Analgesic','Antibiotic','Antiviral','Antiseptic','Vitamins','First Aid','Antacid','Antihistamine','Antifungal','Other'] as $cat):?><option <?=($m['category']??'')===$cat?'selected':''?>><?=$cat?></option><?php endforeach;?></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Dosage Form</label><select name="dosage_form" class="uk-select uk-form-small">
<?php foreach(['Tablet','Capsule','Syrup','Cream','Ointment','Injection','Drops','Inhaler','Other'] as $df):?><option <?=($m['dosage_form']??'')===$df?'selected':''?>><?=$df?></option><?php endforeach;?></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Unit</label><input type="text" name="unit" class="uk-input uk-form-small" value="<?=sanitize_output($m['unit']??'pcs')?>" placeholder="pcs, ml, bottle..."></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Stock Qty *</label><input type="number" name="stock_quantity" class="uk-input uk-form-small" value="<?=$m['stock_quantity']??0?>" min="0" required></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Reorder Level</label><input type="number" name="reorder_level" class="uk-input uk-form-small" value="<?=$m['reorder_level']??10?>" min="0"></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Expiry Date</label><input type="date" name="expiry_date" class="uk-input uk-form-small" value="<?=$m['expiry_date']??''?>"></div>
<div class="uk-width-1-4@m"><label class="uk-form-label">Unit Cost (₱)</label><input type="number" name="unit_cost" class="uk-input uk-form-small" value="<?=$m['unit_cost']??0?>" step="0.01" min="0"></div>
<div class="uk-width-1-1"><label class="uk-form-label">Supplier</label><input type="text" name="supplier" class="uk-input uk-form-small" value="<?=sanitize_output($m['supplier']??'')?>"></div>
<div class="uk-width-1-1 uk-margin-top"><button class="btn btn-p"><?=$isEdit?'Save Changes':'Add Medicine'?></button> <a href="?" class="btn btn-s">Cancel</a></div>
</div></form></div></div>

<?php else:?>
<!-- ===================== INVENTORY TABLE ===================== -->
<div class="card"><div class="card-bd np"><div class="overflow-x">
<table class="tbl"><thead><tr><th>Medicine</th><th>Category</th><th>Form</th><th>Stock</th><th>Reorder Lvl</th><th>Expiry</th><th>Cost</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach($medicines as $m):
    $expSoon=$m['expiry_date']&&strtotime($m['expiry_date'])<=strtotime('+90 days');
?><tr>
<td><span class="text-bold"><?=sanitize_output($m['name'])?></span><br><span class="text-xs text-muted"><?=sanitize_output($m['generic_name']??'')?></span></td>
<td><?=get_status_badge($m['category'])?></td>
<td class="text-sm"><?=sanitize_output($m['dosage_form'])?></td>
<td class="text-bold" style="color:<?=$m['stock_quantity']<=($m['reorder_level']??10)?'#dc2626':'#166534'?>"><?=$m['stock_quantity']?> <span class="text-xs text-muted"><?=sanitize_output($m['unit']??'')?></span></td>
<td class="text-sm text-muted"><?=$m['reorder_level']?></td>
<td class="text-xs <?=$expSoon?'text-bold':''?>" style="<?=$expSoon?'color:#dc2626':''?>"><?=format_date($m['expiry_date'])?></td>
<td class="text-sm"><?=format_currency((float)$m['unit_cost'])?></td>
<td><?=get_status_badge($m['status'])?></td>
<td><a href="?edit=<?=$m['medicine_id']?>" class="btn-txt" style="font-size:.78rem">Edit</a></td>
</tr><?php endforeach;?>
<?php if(empty($medicines)):?><tr><td colspan="9" class="empty">No medicines in inventory. <a href="?action=add">Add one</a></td></tr><?php endif;?>
</tbody></table>
</div></div></div>
<?php endif;?>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>
