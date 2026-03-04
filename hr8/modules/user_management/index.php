<?php
declare(strict_types=1);
require_once __DIR__.'/../../config/db.php';require_once __DIR__.'/../../config/auth.php';require_once __DIR__.'/../../config/paths.php';require_once __DIR__.'/../../includes/helpers.php';
use HR8\Config\Auth;use HR8\Config\Database;
Auth::requireRole(['Admin','HR Manager']);$db=Database::getConnection();
if($_SERVER['REQUEST_METHOD']==='POST'&&validate_csrf_token($_POST['csrf_token']??'')){$a=$_POST['form_action']??'';
    if($a==='create'){$h=password_hash($_POST['password'],PASSWORD_DEFAULT);$db->prepare("INSERT INTO users (first_name,last_name,email,password_hash,role_id,department_id) VALUES (?,?,?,?,?,?)")->execute([trim($_POST['first_name']),trim($_POST['last_name']),trim($_POST['email']),$h,(int)$_POST['role_id'],(int)$_POST['department_id']?:null]);log_audit($db,'User Management','Created User','user',(int)$db->lastInsertId());flash('success','User created.');redirect($_SERVER['PHP_SELF']);}
    if($a==='toggle'){$u=$db->prepare("SELECT status FROM users WHERE user_id=?");$u->execute([(int)$_POST['user_id']]);$c=$u->fetchColumn();$n=$c==='active'?'inactive':'active';$db->prepare("UPDATE users SET status=? WHERE user_id=?")->execute([$n,(int)$_POST['user_id']]);flash('success','Updated.');redirect($_SERVER['PHP_SELF']);}
}
$users=$db->query("SELECT u.*,r.role_name,d.department_name FROM users u JOIN roles r ON u.role_id=r.role_id LEFT JOIN departments d ON u.department_id=d.department_id ORDER BY u.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$roles=$db->query("SELECT * FROM roles ORDER BY role_id")->fetchAll(PDO::FETCH_ASSOC);$depts=$db->query("SELECT * FROM departments WHERE is_active=1 ORDER BY department_name")->fetchAll(PDO::FETCH_ASSOC);$showCreate=($_GET['action']??'')==='create';
$pageTitle='Users';include __DIR__.'/../../includes/layout_top.php';
?>
<div class="page-hd uk-flex uk-flex-between uk-flex-middle uk-margin-bottom"><div><h3>User Management</h3><p>System users &amp; role assignments</p></div><a href="?action=create" class="uk-button uk-button-primary uk-button-small"><span uk-icon="icon:plus;ratio:.8" class="uk-margin-small-right"></span>New User</a></div>
<?php if($showCreate):?>
<div class="uk-card uk-card-default"><div class="uk-card-body"><form method="POST" class="uk-form-stacked"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="create">
<div class="uk-grid-small" uk-grid>
<div class="uk-width-1-3@m"><label class="uk-form-label">First Name *</label><input type="text" name="first_name" class="uk-input uk-form-small" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Last Name *</label><input type="text" name="last_name" class="uk-input uk-form-small" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Email *</label><input type="email" name="email" class="uk-input uk-form-small" required></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Password *</label><input type="password" name="password" class="uk-input uk-form-small" required minlength="6"></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Role *</label><select name="role_id" class="uk-select uk-form-small" required><?php foreach($roles as $r):?><option value="<?=$r['role_id']?>"><?=sanitize_output($r['role_name'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-3@m"><label class="uk-form-label">Department</label><select name="department_id" class="uk-select uk-form-small"><option value="">—</option><?php foreach($depts as $d):?><option value="<?=$d['department_id']?>"><?=sanitize_output($d['department_name'])?></option><?php endforeach;?></select></div>
<div class="uk-width-1-1 uk-margin-top"><button class="uk-button uk-button-primary uk-button-small">Create</button> <a href="?" class="uk-button uk-button-default uk-button-small">Cancel</a></div>
</div></form></div></div>
<?php else:?>
<div class="uk-card uk-card-default"><div class="uk-card-body np"><div class="uk-overflow-auto"><table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-margin-remove">
<thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Status</th><th>Last Login</th><th></th></tr></thead><tbody>
<?php foreach($users as $u):?><tr><td class="uk-text-bold" style="font-size:.84rem"><?=sanitize_output($u['first_name'].' '.$u['last_name'])?></td><td style="font-size:.82rem"><?=sanitize_output($u['email'])?></td><td><span class="bd bd-info"><?=sanitize_output($u['role_name'])?></span></td><td style="font-size:.82rem"><?=sanitize_output($u['department_name']??'—')?></td><td><?=$u['status']==='active'?'<span class="bd bd-success">Active</span>':'<span class="bd bd-secondary">Inactive</span>'?></td><td style="font-size:.78rem"><?=$u['last_login']?format_date($u['last_login'],'M d h:i A'):'Never'?></td>
<td><form method="POST"><input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>"><input type="hidden" name="form_action" value="toggle"><input type="hidden" name="user_id" value="<?=$u['user_id']?>"><button class="uk-button uk-button-text uk-button-small" style="font-size:.75rem"><?=$u['status']==='active'?'Deactivate':'Activate'?></button></form></td></tr><?php endforeach;?>
</tbody></table></div></div></div>
<?php endif;?>
<?php include __DIR__.'/../../includes/layout_bottom.php';?>