<?php
declare(strict_types=1);
require_once __DIR__.'/config/db.php';require_once __DIR__.'/config/auth.php';require_once __DIR__.'/includes/helpers.php';
use Clinic\Config\Auth;use Clinic\Config\Database;
if(Auth::isLoggedIn()){try{log_audit(Database::getConnection(),'System','Logout','user',Auth::getUserId());}catch(\Exception $e){}}
Auth::logout();flash('success','You have been logged out.');redirect('/clinic/login.php');
