<?php
use HR8\Config\Auth;
if(!Auth::isLoggedIn())return;
$uri=$_SERVER['REQUEST_URI']??'';
$isHR=Auth::hasAnyRole(['Admin','HR Manager','HR Staff']);$isAdmin=Auth::hasAnyRole(['Admin','HR Manager']);
function _a($u,$m){return str_contains($u,$m)?' uk-active':'';}
function _o($u,$m){return str_contains($u,$m)?' uk-open':'';}
?>
<aside class="sb">
<div class="sb-logo">HR<b>8</b></div>
<div style="padding:0 24px 16px"><span class="uk-text-meta" style="font-size:.68rem">Human Resource Management</span></div>
<ul class="uk-nav uk-nav-default" uk-nav="multiple:true">
    <li class="<?=_a($uri,'dashboard')?>"><a href="<?=base_url()?>/dashboard.php"><span uk-icon="icon:home;ratio:.85"></span>Dashboard</a></li>
    <?php if($isHR):?>
    <li class="uk-nav-header">Recruitment</li>
    <li class="uk-parent<?=_o($uri,'pre_employment')?>"><a href="#"><span uk-icon="icon:user;ratio:.85"></span>Pre-Employment<span uk-nav-parent-icon></span></a>
        <ul class="uk-nav-sub"><li class="<?=_a($uri,'pre_employment/index')?>"><a href="<?=get_module_path('pre_employment')?>/index.php">Applicants</a></li><li class="<?=_a($uri,'pre_employment/positions')?>"><a href="<?=get_module_path('pre_employment')?>/positions.php">Positions</a></li></ul></li>
    <li class="uk-parent<?=_o($uri,'/recruitment/')?>"><a href="#"><span uk-icon="icon:search;ratio:.85"></span>Recruitment<span uk-nav-parent-icon></span></a>
        <ul class="uk-nav-sub"><li class="<?=_a($uri,'recruitment/interviews')?>"><a href="<?=get_module_path('recruitment')?>/interviews.php">Interviews</a></li><li class="<?=_a($uri,'recruitment/offers')?>"><a href="<?=get_module_path('recruitment')?>/offers.php">Job Offers</a></li></ul></li>
    <li class="uk-nav-header">Employee</li>
    <li class="uk-parent<?=_o($uri,'employee_records')?>"><a href="#"><span uk-icon="icon:users;ratio:.85"></span>Records<span uk-nav-parent-icon></span></a>
        <ul class="uk-nav-sub"><li class="<?=_a($uri,'employee_records/index')?>"><a href="<?=get_module_path('employee_records')?>/index.php">List</a></li><li class="<?=_a($uri,'employee_records/create')?>"><a href="<?=get_module_path('employee_records')?>/create.php">Add New</a></li></ul></li>
    <li class="uk-parent<?=_o($uri,'performance')?>"><a href="#"><span uk-icon="icon:star;ratio:.85"></span>Performance<span uk-nav-parent-icon></span></a>
        <ul class="uk-nav-sub"><li class="<?=_a($uri,'performance/index')?>"><a href="<?=get_module_path('performance')?>/index.php">Evaluations</a></li><li class="<?=_a($uri,'performance/disciplinary')?>"><a href="<?=get_module_path('performance')?>/disciplinary.php">Service Records</a></li></ul></li>
    <li class="uk-nav-header">Separation</li>
    <li class="<?=_a($uri,'post_employment')?>"><a href="<?=get_module_path('post_employment')?>/index.php"><span uk-icon="icon:sign-out;ratio:.85"></span>Clearance</a></li>
    <?php endif;?>
    <?php if($isAdmin):?>
    <li class="uk-nav-header">Admin</li>
    <li class="uk-parent<?=_o($uri,'user_management')?>"><a href="#"><span uk-icon="icon:cog;ratio:.85"></span>System<span uk-nav-parent-icon></span></a>
        <ul class="uk-nav-sub"><li class="<?=_a($uri,'user_management/index')?>"><a href="<?=get_module_path('user_management')?>/index.php">Users</a></li><li class="<?=_a($uri,'user_management/audit')?>"><a href="<?=get_module_path('user_management')?>/audit.php">Audit Trail</a></li></ul></li>
    <?php endif;?>
</ul>
</aside>
