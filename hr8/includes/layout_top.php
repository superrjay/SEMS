<?php
/**
 * HR8 Master Layout — UIKit 3 Modern Minimal
 * Usage: $pageTitle = "Title"; include 'layout_top.php'; ... include 'layout_bottom.php';
 * Or:    require layout.php with $pageTitle and $pageContent
 */
declare(strict_types=1);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= $pageTitle ?? 'HR8' ?> — HR8 System</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.21.6/dist/css/uikit.min.css">
<style>
:root{--c1:#1e3a5f;--c2:#2d5986;--bg:#f4f6f9;--card:#fff;--border:#e8e8e8}
*{box-sizing:border-box}body{background:var(--bg);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;margin:0;color:#333}

/* SIDEBAR */
.sb{width:250px;min-height:100vh;background:var(--card);border-right:1px solid var(--border);position:fixed;top:0;left:0;z-index:980;overflow-y:auto;transition:transform .3s}
.sb-logo{font-size:1.4rem;font-weight:800;color:var(--c1);letter-spacing:-.5px;padding:24px 24px 4px}.sb-logo b{color:var(--c2)}
.sb .uk-nav-header{font-size:.65rem!important;text-transform:uppercase;letter-spacing:1.5px;color:#aaa;margin:18px 0 4px;padding:0 24px!important}
.sb .uk-nav>li>a{display:flex;align-items:center;gap:10px;padding:9px 18px;margin:1px 8px;color:#666;font-size:.84rem;border-radius:8px;text-decoration:none;transition:.15s}
.sb .uk-nav>li>a:hover{background:#f0f2f5;color:var(--c1)}.sb .uk-nav>li.uk-active>a{background:var(--c1);color:#fff!important}
.sb .uk-nav-sub{padding:2px 0 2px 48px;margin:0}.sb .uk-nav-sub a{font-size:.78rem;padding:5px 12px;color:#888;border-radius:6px;display:block;text-decoration:none}
.sb .uk-nav-sub a:hover,.sb .uk-nav-sub .uk-active a{color:var(--c1);background:rgba(30,58,95,.06)}

/* TOPBAR */
.tb{height:60px;background:var(--card);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 28px;position:sticky;top:0;z-index:970}
.av{width:34px;height:34px;border-radius:50%;background:var(--c1);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:.7rem}

/* MAIN */
.mn{margin-left:250px;min-height:100vh}.mn-inner{padding:28px}

/* CARDS */
.uk-card{border-radius:12px;border:1px solid var(--border);overflow:hidden}.uk-card-header{border-bottom:1px solid #f0f0f0;padding:16px 24px}
.uk-card-body{padding:24px}.uk-card-body.np{padding:0}

/* TABLE */
.uk-table th{font-size:.7rem;text-transform:uppercase;letter-spacing:.8px;color:#999;font-weight:600;white-space:nowrap}
.uk-table td{font-size:.85rem;vertical-align:middle}

/* BADGES */
.bd{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.68rem;font-weight:600;letter-spacing:.2px;white-space:nowrap}
.bd-primary{background:#dbeafe;color:#1e40af}.bd-success{background:#dcfce7;color:#166534}.bd-warning{background:#fef3c7;color:#92400e}
.bd-danger{background:#fee2e2;color:#991b1b}.bd-info{background:#e0f2fe;color:#075985}.bd-secondary{background:#f3f4f6;color:#6b7280}.bd-dark{background:#1f2937;color:#f9fafb}

/* STAT */
.st{background:var(--card);border-radius:12px;padding:22px;border:1px solid var(--border);transition:.2s}.st:hover{box-shadow:0 4px 20px rgba(0,0,0,.05)}
.st-v{font-size:1.8rem;font-weight:700;line-height:1}.st-l{font-size:.78rem;color:#999;margin-top:2px}
.st-i{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}

/* FORMS */
.uk-form-label{font-size:.78rem;font-weight:600;color:#555;margin-bottom:3px}
.uk-input,.uk-select,.uk-textarea{border-radius:8px!important;border-color:var(--border)!important}
.uk-input:focus,.uk-select:focus,.uk-textarea:focus{border-color:var(--c1)!important}
.uk-button{border-radius:8px;font-weight:500;font-size:.84rem}
.uk-button-primary{background:var(--c1)}.uk-button-primary:hover{background:var(--c2)}

/* MISC */
code{background:#f1f5f9;padding:2px 8px;border-radius:4px;font-size:.78rem;color:var(--c1)}
.filter-bar{background:var(--card);border-radius:12px;border:1px solid var(--border);padding:14px 20px}
.qa{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:20px;text-align:center;text-decoration:none!important;color:#666;transition:.2s;display:block}
.qa:hover{border-color:var(--c1);color:var(--c1);box-shadow:0 4px 15px rgba(0,0,0,.05)}
.page-hd h3{font-weight:700;font-size:1.4rem;margin:0}.page-hd p{color:#999;font-size:.85rem;margin:2px 0 0}
@media(max-width:960px){.sb{transform:translateX(-100%)}.sb.open{transform:translateX(0)}.mn{margin-left:0}}
.uk-alert{border-radius:8px}
</style>
</head>
<body>
<?php include __DIR__ . '/sidebar.php'; ?>
<div class="mn">
<?php include __DIR__ . '/navbar.php'; ?>
<div class="mn-inner">
<?php if($__msg=flash('success')):?><div class="uk-alert-success" uk-alert><a class="uk-alert-close" uk-close></a><?=sanitize_output($__msg)?></div><?php endif;?>
<?php if($__msg=flash('error')):?><div class="uk-alert-danger" uk-alert><a class="uk-alert-close" uk-close></a><?=sanitize_output($__msg)?></div><?php endif;?>
