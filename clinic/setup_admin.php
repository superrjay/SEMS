<?php
declare(strict_types=1);
require_once __DIR__.'/config/db.php';
use Clinic\Config\Database;
$message='';$success=false;$users=[];
try{$db=Database::getConnection();$pw=$_POST['password']??'admin123';$hash=password_hash($pw,PASSWORD_DEFAULT);
    $stmt=$db->prepare("UPDATE users SET password_hash=?");$stmt->execute([$hash]);$count=$stmt->rowCount();
    $message="Updated {$count} user(s) with password: <strong>{$pw}</strong>";$success=true;
    $users=$db->query("SELECT u.email,r.role_name FROM users u JOIN roles r ON u.role_id=r.role_id ORDER BY u.user_id")->fetchAll(PDO::FETCH_ASSOC);
}catch(\Exception $e){$message="Error: ".$e->getMessage();}
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Setup — Clinic+</title>
<style>body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f0fdf4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif}
.box{width:100%;max-width:480px;padding:20px}.inner{background:#fff;border-radius:16px;padding:40px;box-shadow:0 8px 40px rgba(0,0,0,.05);border:1px solid #e5e7eb}
h3{font-weight:700;margin:0 0 4px;color:#0f766e}.ok{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;padding:12px;border-radius:8px;margin:12px 0;font-size:.84rem}
.err{background:#fef2f2;color:#991b1b;border:1px solid #fecaca;padding:12px;border-radius:8px;margin:12px 0;font-size:.84rem}
table{width:100%;border-collapse:collapse;margin:12px 0}th,td{padding:8px 12px;border-bottom:1px solid #f0f0f0;font-size:.84rem;text-align:left}
th{color:#999;font-size:.72rem;text-transform:uppercase}code{background:#f0fdfa;padding:2px 8px;border-radius:4px;font-size:.78rem;color:#0f766e}
.warn{background:#fef3c7;color:#92400e;border:1px solid #fde68a;padding:10px;border-radius:8px;font-size:.82rem;margin:12px 0}
a.btn{display:block;padding:10px;background:#0f766e;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:.85rem;text-align:center}
</style></head><body>
<div class="box"><div class="inner"><h3>Clinic+ Setup</h3><p style="color:#888;font-size:.82rem">Password reset utility</p>
<?php if($message):?><div class="<?=$success?'ok':'err'?>"><?=$message?></div><?php endif;?>
<?php if(!empty($users)):?><table><thead><tr><th>Email</th><th>Role</th></tr></thead><tbody>
<?php foreach($users as $u):?><tr><td><code><?=htmlspecialchars($u['email'])?></code></td><td><?=htmlspecialchars($u['role_name'])?></td></tr><?php endforeach;?>
</tbody></table><div class="warn"><strong>Delete this file</strong> after setup!</div>
<a href="/clinic/login.php" class="btn">Go to Login</a><?php endif;?>
<hr style="border:0;border-top:1px solid #eee;margin:16px 0">
<form method="POST" style="display:flex;gap:8px"><input type="text" name="password" value="admin123" style="flex:1;padding:8px 12px;border:1px solid #e5e5e5;border-radius:8px;font-size:.84rem"><button style="padding:8px 16px;border:1px solid #e5e5e5;border-radius:8px;background:#fff;cursor:pointer;font-size:.84rem">Reset</button></form>
</div></div></body></html>