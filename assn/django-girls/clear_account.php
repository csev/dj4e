<?php
/**
 * Clears the pythonanywhere_account cookie and redirects to the intro (login will appear).
 * Passes the former value as ?former= so the form can pre-fill it.
 */
if ( ! defined('COOKIE_SESSION') ) define('COOKIE_SESSION', true);
require_once __DIR__ . '/../../tsugi/config.php';

$former = '';
if ( !empty($_COOKIE['pythonanywhere_account']) && preg_match('/^[a-zA-Z0-9]+$/', $_COOKIE['pythonanywhere_account']) ) {
    $former = $_COOKIE['pythonanywhere_account'];
}
setcookie('pythonanywhere_account', '', time() - 3600, '/');
$redirect = 'index.php' . ( $former !== '' ? '?former=' . urlencode($former) : '' );
header('Location: ' . $redirect);
exit;
