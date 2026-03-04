<?php
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';
use HR8\Config\Database;
$message = ''; $success = false; $users = [];
try {
    $db = Database::getConnection();
    $pw = $_POST['password'] ?? ($_GET['auto'] ?? 'admin123');
    $hash = password_hash($pw, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password_hash = ?"); $stmt->execute([$hash]);
    $count = $stmt->rowCount();
    $message = "Updated {$count} user(s). Password: <strong>{$pw}</strong>";
    $success = true;
    $users = $db->query("SELECT u.email, r.role_name FROM users u JOIN roles r ON u.role_id = r.role_id ORDER BY u.user_id")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $message = "Error: " . $e->getMessage(); }
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>HR8 Setup</title><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.21.6/dist/css/uikit.min.css">
<style>body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f4f6f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif}</style>
</head><body>
<div style="width:100%;max-width:440px;padding:20px">
<div style="background:#fff;border-radius:16px;padding:40px;box-shadow:0 8px 40px rgba(0,0,0,.05);border:1px solid #e8e8e8">
<h3 style="font-weight:700;margin:0 0 4px">HR8 Setup</h3>
<p class="uk-text-muted uk-text-small">Password reset utility</p>
<?php if($message):?><div class="<?=$success?'uk-alert-success':'uk-alert-danger'?>" uk-alert style="border-radius:8px"><p><?=$message?></p></div><?php endif;?>
<?php if(!empty($users)):?>
<table class="uk-table uk-table-small uk-table-divider"><thead><tr><th>Email</th><th>Role</th></tr></thead><tbody>
<?php foreach($users as $u):?><tr><td><code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:.78rem"><?=htmlspecialchars($u['email'])?></code></td><td style="font-size:.82rem"><?=htmlspecialchars($u['role_name'])?></td></tr><?php endforeach;?>
</tbody></table>
<div class="uk-alert-warning" uk-alert style="border-radius:8px"><p style="font-size:.82rem"><strong>Delete this file</strong> after setup!</p></div>
<a href="/hr8/login.php" class="uk-button uk-button-primary uk-width-1-1" style="border-radius:8px;background:#1e3a5f">Go to Login</a>
<?php endif;?>
<hr class="uk-divider-small">
<form method="POST" class="uk-flex" style="gap:8px"><input type="text" name="password" class="uk-input uk-form-small" value="admin123" style="border-radius:8px"><button class="uk-button uk-button-default uk-button-small" style="border-radius:8px;white-space:nowrap">Reset</button></form>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/uikit@3.21.6/dist/js/uikit.min.js"></script>
</body></html>
