<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/inc/bootstrap.php';
ru_admin_logout();
ru_redirect('/admin/login.php');
