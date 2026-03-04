<?php
declare(strict_types=1);
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../config/auth.php';
require_once __DIR__.'/../../config/paths.php';
require_once __DIR__.'/../../includes/helpers.php';
use Clinic\Config\Auth;use Clinic\Config\Database;
Auth::requireRole(['Admin']);
$db=Database::getConnection();

if($_SERVER['REQUEST_METHOD']==='POST'&&validate_csrf_token($_POST['csrf_token']??'')){
    $a=$_POST['form_action']??'';
    if($a==='create_user'){
        $hash=password_hash(trim($_POST['password']),PASSWORD_DEFAULT);
        $stmt=$db->prepare("INSERT INTO users (first_name,last_name,email,password_hash,role_id,status) VALUES (?,?,?,?,?,?)");
        $r=$stmt->execute([trim($_POST['first_name']),trim($_POST['last_name']),trim($_POST['email']),$hash,(int)$_POST['role_id'],$_POST['status']??'active']);
        if($r){log_audit($db,'User Management','Created User','user',(int)$db->lastInsertId());flash('success','User created.');}
        else flash('error','Failed.');
        redirect($_SERVER['PHP_SELF']);
    }
    if($a==='update_user'){
        $uid=(int)$_POST['user_id'];
        $d=['first_name'=>trim($_POST['first_name']),'last_name'=>trim($_POST['last_name']),'email'=>trim($_POST['email']),'role_id'=>(int)$_POST['role_id'],'status'=>$_POST['status']];
        $sets=[];$params=[];foreach($d as $k=>$v){$sets[]="{$k}=?";$params[]=$v;}
        if(!empty($_POST['password'])){$sets[]="password_hash=?";$params[]=password_hash(trim($_POST['password']),PASSWORD_DEFAULT);}
        $params[]=$uid;
        $db->prepare("UPDATE users SET ".implode(',',$sets)." WHERE user_id=?")->execute($params);
        log_audit($db,'User Management','Updated User','user',$uid);
        flash('success','User updated.');redirect($_SERVER['PHP_SELF']);
    }
}

$users=$db->query("SELECT u.*,r.role_name FROM users u JOIN roles r ON u.role_id=r.role_id ORDER BY u.user_id")->fetchAll(PDO::FETCH_ASSOC);
$roles=$db->query("SELECT * FROM roles ORDER BY role_id")->fetchAll(PDO::FETCH_ASSOC);
$showCreate=($_GET['action']??'')==='create';
$showEdit=isset($_GET['edit']);
$editUser=null;
if($showEdit){$stmt=$db->prepare("SELECT * FROM users WHERE user_id=?");$stmt->execute([(int)$_GET['edit']]);$editUser=$stmt->fetch(PDO::FETCH_ASSOC);}

$pageTitle='User Management';include __DIR__.'/../../includes/layout_top.php';
?>

<div class="page-hd">
<div><h3>User Management</h3><p>Manage system users, roles &amp; access</p></div>
<div class="flex gap-8">
<a href="<?=get_module_path('user_management')?>/audit.php" class="btn btn-s">Audit Trail</a>
<a href="?action=create" class="btn btn-p"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add User</a>
</div>
</div>

<?php if($showCreate||($showEdit&&$editUser)):$u=$editUser;$isEdit=(bool)$u;?>
<a href="?" class="btn-txt" style="margin-bottom:14px;display:inline-block">&larr; Back</a>
<div class="card"><div class="card-hd"><h5><?=$isEdit?'Edit':'Add'?> User</h5></div>
<div class="card-bd"><form method="POST" class="uk-form-stacked">
<input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>">
<input type="hidden" name="form_action" value="<?=$isEdit?'update_user':'create_user'?>">
<?php if($isEdit):?><input type="hidden" name="user_id" value="<?=$u['user_id']?>"><?php endif;?>
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-3@m"><label class="uk-form-label">First Name *</label><input type="text" name="first_name" class="uk-input uk-form-small" value="<?=sanitize_output($u['first_name']??'')?>" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Last Name *</label><input type="text" name="last_name" class="uk-input uk-form-small" value="<?=sanitize_output($u['last_name']??'')?>" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Email *</label><input type="email" name="email" class="uk-input uk-form-small" value="<?=sanitize_output($u['email']??'')?>" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label"><?=$isEdit?'New Password (leave blank to keep)':'Password *'?></label><input type="password" name="password" class="uk-input uk-form-small" <?=$isEdit?'':'required'?>></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Role *</label><select name="role_id" class="uk-select uk-form-small" required><?php foreach($roles as $r):?><option value="<?=$r['role_id']?>" <?=($u['role_id']??'')==$r['role_id']?'selected':''?>><?=sanitize_output($r['role_name'])?> — <?=sanitize_output($r['description']??'')?></option><?php endforeach;?></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Status</label><select name="status" class="uk-select uk-form-small"><?php foreach(['active','inactive','locked'] as $ss):?><option <?=($u['status']??'active')===$ss?'selected':''?>><?=$ss?></option><?php endforeach;?></select></div>
<div class="uk-width-1-1 uk-margin-top"><button class="btn btn-p"><?=$isEdit?'Save Changes':'Create User'?></button> <a href="?" class="btn btn-s">Cancel</a></div>
</div></form></div></div>

<?php else:?>
<div class="card"><div class="card-bd np"><div class="overflow-x">
<table class="tbl"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th></th></tr></thead><tbody>
<?php foreach($users as $u):?><tr>
<td class="text-muted"><?=$u['user_id']?></td>
<td class="text-bold"><?=sanitize_output($u['first_name'].' '.$u['last_name'])?></td>
<td class="text-sm"><code><?=sanitize_output($u['email'])?></code></td>
<td><?=get_status_badge($u['role_name'])?></td>
<td><span class="bd bd-<?=$u['status']==='active'?'success':($u['status']==='locked'?'danger':'secondary')?>"><?=sanitize_output($u['status'])?></span></td>
<td class="text-xs text-muted"><?=format_date($u['last_login'],'M d, Y h:i A')?></td>
<td><a href="?edit=<?=$u['user_id']?>" class="btn-txt" style="font-size:.78rem">Edit</a></td>
</tr><?php endforeach;?>
</tbody></table>
</div></div></div>
<?php endif;?>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>
