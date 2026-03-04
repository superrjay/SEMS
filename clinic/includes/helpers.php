<?php
declare(strict_types=1);
function sanitize_output(string $d): string { return htmlspecialchars($d, ENT_QUOTES, 'UTF-8'); }
function redirect(string $u): void { header("Location: $u"); exit; }
function base_url(): string { $p=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http'; return "{$p}://".($_SERVER['HTTP_HOST']??'localhost')."/clinic"; }
function get_module_path(string $m): string { return base_url()."/modules/{$m}"; }
function format_date(?string $d, string $f='M d, Y'): string { if(!$d)return "\xe2\x80\x94"; $t=strtotime($d); return $t?date($f,$t):''; }
function format_currency(float $a): string { return "\xe2\x82\xb1".number_format($a,2); }
function generate_csrf_token(): string { if(empty($_SESSION['csrf_token'])) $_SESSION['csrf_token']=bin2hex(random_bytes(32)); return $_SESSION['csrf_token']; }
function validate_csrf_token(string $t): bool { return isset($_SESSION['csrf_token'])&&hash_equals($_SESSION['csrf_token'],$t); }
function flash(string $k='', $v=null) { if(!isset($_SESSION['flash_messages'])) $_SESSION['flash_messages']=[]; if($v!==null){$_SESSION['flash_messages'][$k]=$v;return null;} $m=$_SESSION['flash_messages'][$k]??null; unset($_SESSION['flash_messages'][$k]); return $m; }
function get_status_badge(string $s): string {
    $m=['Active'=>'success','Inactive'=>'secondary','Graduated'=>'info','Pending'=>'warning','Cleared'=>'success','Not Cleared'=>'danger','Conditional'=>'warning',
        'Open'=>'danger','Under Review'=>'warning','Resolved'=>'success','Closed'=>'secondary',
        'Ongoing'=>'info','Completed'=>'success','Follow-up'=>'warning','Referred'=>'primary',
        'Available'=>'success','Low Stock'=>'warning','Out of Stock'=>'danger','Expired'=>'dark',
        'Minor'=>'info','Moderate'=>'warning','Severe'=>'danger','Critical'=>'dark',
        'Physical Exam'=>'primary','Lab Result'=>'info','Vaccination'=>'success','Dental'=>'warning','Vision'=>'info','Xray'=>'secondary','Medical History'=>'dark','Other'=>'secondary',
        'Injury'=>'danger','Illness'=>'warning','Allergic Reaction'=>'danger','Fainting'=>'warning','Seizure'=>'danger','Mental Health'=>'info','Accident'=>'danger',
        'Analgesic'=>'primary','Antibiotic'=>'danger','Antiviral'=>'warning','Antiseptic'=>'info','Vitamins'=>'success','First Aid'=>'primary','Antacid'=>'secondary','Antihistamine'=>'warning','Antifungal'=>'info',
        'Tablet'=>'primary','Capsule'=>'info','Syrup'=>'warning','Cream'=>'secondary','Ointment'=>'secondary',
        'Enrollment'=>'primary','OJT'=>'info','Sports'=>'success','Graduation'=>'dark','Employment'=>'warning','Field Trip'=>'info',
        'Archived'=>'secondary','Draft'=>'secondary','Male'=>'primary','Female'=>'info','Unknown'=>'secondary',
        'A+'=>'danger','A-'=>'danger','B+'=>'info','B-'=>'info','AB+'=>'warning','AB-'=>'warning','O+'=>'success','O-'=>'success'];
    $c=$m[$s]??'secondary';
    return '<span class="bd bd-'.$c.'">'.sanitize_output($s).'</span>';
}
function log_audit(PDO $db, string $mod, string $act, ?string $rt=null, ?int $rid=null, $old=null, $new=null): void {
    $db->prepare("INSERT INTO audit_logs (user_id,module,action,record_type,record_id,old_data,new_data,ip_address,user_agent) VALUES (?,?,?,?,?,?,?,?,?)")->execute([$_SESSION['user_id']??null,$mod,$act,$rt,$rid,$old?json_encode($old):null,$new?json_encode($new):null,$_SERVER['REMOTE_ADDR']??null,substr($_SERVER['HTTP_USER_AGENT']??'',0,500)]);
}
