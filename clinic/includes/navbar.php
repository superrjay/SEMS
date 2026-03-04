<?php use Clinic\Config\Auth; ?>
<div class="tb">
    <div class="uk-flex uk-flex-middle" style="gap:12px">
        <a class="mob-toggle uk-hidden@m" uk-icon="icon:menu"></a>
    </div>
    <div class="uk-flex uk-flex-middle uk-margin-auto-left" style="gap:14px">
        <span class="uk-text-small uk-text-muted uk-visible@m"><?=date('D, M d Y')?></span>
        <?php if(Auth::isLoggedIn()):?>
        <div class="uk-inline">
            <a class="uk-flex uk-flex-middle" style="gap:8px;text-decoration:none;color:#333;cursor:pointer">
                <span class="av"><?=strtoupper(substr($_SESSION['user_data']['first_name']??'U',0,1))?></span>
                <span class="uk-visible@s uk-text-small uk-text-bold"><?=sanitize_output($_SESSION['user_data']['first_name']??'')?></span>
                <span uk-icon="icon:chevron-down;ratio:.6"></span>
            </a>
            <div uk-dropdown="mode:click;pos:bottom-right;offset:8" style="min-width:180px">
                <ul class="uk-nav uk-dropdown-nav">
                    <li class="uk-nav-header" style="font-size:.7rem"><?=Auth::getUserRole()?></li>
                    <li><a href="<?=base_url()?>/dashboard.php"><span uk-icon="icon:home;ratio:.8" class="uk-margin-small-right"></span>Dashboard</a></li>
                    <li class="uk-nav-divider"></li>
                    <li><a href="<?=base_url()?>/logout.php" style="color:#dc2626"><span uk-icon="icon:sign-out;ratio:.8" class="uk-margin-small-right"></span>Logout</a></li>
                </ul>
            </div>
        </div>
        <?php endif;?>
    </div>
</div>
