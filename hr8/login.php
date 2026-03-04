<?php
declare(strict_types=1);
require_once __DIR__.'/config/db.php';require_once __DIR__.'/config/auth.php';require_once __DIR__.'/includes/helpers.php';
use HR8\Config\Auth;use HR8\Config\Database;
if(Auth::isLoggedIn()) redirect('/hr8/dashboard.php');
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!validate_csrf_token($_POST['csrf_token']??''))$errors[]="Invalid session.";
    $email=trim($_POST['email']??'');$password=$_POST['password']??'';
    if(empty($email)||empty($password))$errors[]="Enter both email and password.";
    if(empty($errors)){try{$db=Database::getConnection();
        $st=$db->prepare("SELECT u.*,r.role_name FROM users u JOIN roles r ON u.role_id=r.role_id WHERE u.email=? AND u.status='active'");$st->execute([$email]);$u=$st->fetch(PDO::FETCH_ASSOC);
        if($u&&password_verify($password,$u['password_hash'])){Auth::login((int)$u['user_id'],$u['role_name'],['first_name'=>$u['first_name'],'last_name'=>$u['last_name'],'email'=>$u['email']]);
            $db->prepare("UPDATE users SET last_login=NOW() WHERE user_id=?")->execute([$u['user_id']]);log_audit($db,'System','Login','user',(int)$u['user_id']);flash('success','Welcome back, '.$u['first_name'].'!');redirect('/hr8/dashboard.php');}
        else{$errors[]="Invalid email or password.";}
    }catch(PDOException $e){$errors[]="Login unavailable.";}}
}
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Login — HR8</title><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.21.6/dist/css/uikit.min.css">
<style>body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f4f6f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif}
.lc{width:100%;max-width:380px;padding:20px}.lc-inner{background:#fff;border-radius:16px;padding:48px 36px 36px;box-shadow:0 8px 40px rgba(0,0,0,.05);border:1px solid #e8e8e8}
.logo{font-size:2.2rem;font-weight:800;color:#1e3a5f;text-align:center;letter-spacing:-.5px}.logo b{color:#2d5986}
</style></head><body>
<div class="lc"><div class="lc-inner">
<div class="logo">HR<b>8</b></div>
<p class="uk-text-center uk-text-muted" style="font-size:.82rem;margin-top:4px">Human Resource Management</p>
<?php if($m=flash('success')):?><div class="uk-alert-success uk-margin-top" uk-alert style="border-radius:8px"><p><?=sanitize_output($m)?></p></div><?php endif;?>
<?php if(!empty($errors)):?><div class="uk-alert-danger uk-margin-top" uk-alert style="border-radius:8px"><?php foreach($errors as $e):?><p style="margin:0"><?=sanitize_output($e)?></p><?php endforeach;?></div><?php endif;?>
<form method="POST" class="uk-form-stacked uk-margin-top">
<input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>">
<div class="uk-margin"><label class="uk-form-label" style="font-size:.78rem;font-weight:600;color:#555">Email</label><input class="uk-input" type="email" name="email" value="<?=sanitize_output($_POST['email']??'')?>" placeholder="admin@hr8.com" required style="border-radius:8px"></div>
<div class="uk-margin"><label class="uk-form-label" style="font-size:.78rem;font-weight:600;color:#555">Password</label><input class="uk-input" type="password" name="password" placeholder="Enter password" required style="border-radius:8px"></div>
<button type="submit" class="uk-button uk-width-1-1 uk-margin-small-top" style="border-radius:8px;background:#1e3a5f;color:#fff;font-weight:600;height:44px">Sign In</button>
</form>
<p class="uk-text-center uk-text-muted uk-margin-small-top" style="font-size:.72rem">Run <a href="/hr8/setup_admin.php" style="color:#2d5986">setup_admin.php</a> first &middot; admin@hr8.com / admin123</p>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/uikit@3.21.6/dist/js/uikit.min.js"></script></body></html>