<?php
/**
 * Clears the pythonanywhere_account cookie and redirects to the intro (login will appear).
 */
if ( ! defined('COOKIE_SESSION') ) define('COOKIE_SESSION', true);
require_once __DIR__ . '/../../tsugi/config.php';

setcookie('pythonanywhere_account', '', time() - 3600, '/');
header('Location: index.php');
exit;
