<?php
use Clinic\Config\Auth;
if(!Auth::isLoggedIn())return;
$uri=$_SERVER['REQUEST_URI']??'';
$isClinical=Auth::hasAnyRole(['Admin','Doctor','Nurse']);
$isAdmin=Auth::hasAnyRole(['Admin']);
$isAll=Auth::hasAnyRole(['Admin','Doctor','Nurse','Staff']);
function _a($u,$m){return str_contains($u,$m)?' uk-active':'';}
function _o($u,$m){return str_contains($u,$m)?' uk-open':'';}
?>
<aside class="sb">
<div class="sb-logo">Clinic<b>+</b></div>
<div style="padding:0 24px 16px"><span class="uk-text-meta" style="font-size:.68rem">Medical Services System</span></div>
<ul class="uk-nav uk-nav-default" uk-nav="multiple:true">
    <li class="<?=_a($uri,'dashboard')?>"><a href="<?=base_url()?>/dashboard.php"><span uk-icon="icon:home;ratio:.85"></span>Dashboard</a></li>

    <?php if($isAll):?>
    <li class="uk-nav-header">Patient Care</li>
    <li class="uk-parent<?=_o($uri,'medical_records')?>"><a href="#"><span uk-icon="icon:file-text;ratio:.85"></span>Medical Records<span uk-nav-parent-icon></span></a>
        <ul class="uk-nav-sub">
            <li class="<?=_a($uri,'medical_records/index')?>"><a href="<?=get_module_path('medical_records')?>/index.php">Patient List</a></li>
            <li class="<?=str_contains($uri,'action=register')?' uk-active':''?>"><a href="<?=get_module_path('medical_records')?>/index.php?action=register">Register Patient</a></li>
        </ul>
    </li>
    <?php endif;?>

    <?php if($isClinical):?>
    <li class="<?=_a($uri,'consultations')?>"><a href="<?=get_module_path('consultations')?>/index.php"><span uk-icon="icon:bolt;ratio:.85"></span>Consultations</a></li>

    <li class="uk-nav-header">Pharmacy</li>
    <li class="uk-parent<?=_o($uri,'medicine_inventory')?>"><a href="#"><span uk-icon="icon:grid;ratio:.85"></span>Medicine<span uk-nav-parent-icon></span></a>
        <ul class="uk-nav-sub">
            <li class="<?=_a($uri,'medicine_inventory/index')?>"><a href="<?=get_module_path('medicine_inventory')?>/index.php">Inventory</a></li>
            <li class="<?=_a($uri,'medicine_inventory/dispensing')?>"><a href="<?=get_module_path('medicine_inventory')?>/dispensing.php">Dispensing</a></li>
        </ul>
    </li>

    <li class="uk-nav-header">Clearance &amp; Safety</li>
    <li class="<?=_a($uri,'medical_clearance')?>"><a href="<?=get_module_path('medical_clearance')?>/index.php"><span uk-icon="icon:check;ratio:.85"></span>Medical Clearance</a></li>
    <li class="<?=_a($uri,'health_incidents')?>"><a href="<?=get_module_path('health_incidents')?>/index.php"><span uk-icon="icon:warning;ratio:.85"></span>Health Incidents</a></li>
    <?php endif;?>

    <?php if($isAdmin):?>
    <li class="uk-nav-header">Admin</li>
    <li class="uk-parent<?=_o($uri,'user_management')?>"><a href="#"><span uk-icon="icon:cog;ratio:.85"></span>System<span uk-nav-parent-icon></span></a>
        <ul class="uk-nav-sub">
            <li class="<?=_a($uri,'user_management/index')?>"><a href="<?=get_module_path('user_management')?>/index.php">Users</a></li>
            <li class="<?=_a($uri,'user_management/audit')?>"><a href="<?=get_module_path('user_management')?>/audit.php">Audit Trail</a></li>
        </ul>
    </li>
    <?php endif;?>
</ul>
</aside>
