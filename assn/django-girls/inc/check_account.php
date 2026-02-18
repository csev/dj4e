<?php
/**
 * PythonAnywhere account gate: require pythonanywhere_account cookie for django-girls pages.
 * If not set, show login form. On valid POST (alphanumeric only), set cookie and redirect.
 * Must be included after config.php.
 */
$cookie_name = 'pythonanywhere_account';
$redirect_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';

// Handle POST: validate account, set cookie, redirect
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST[$cookie_name])) {
    $account = trim($_POST[$cookie_name]);
    if (preg_match('/^[a-zA-Z0-9]+$/', $account)) {
        setcookie($cookie_name, $account, time() + (86400 * 365), '/');  // 1 year
        header('Location: ' . $redirect_url);
        exit;
    }
    $account_error = 'Account name must contain only letters and numbers.';
}

// If cookie is set, we're done
if (!empty($_COOKIE[$cookie_name]) && preg_match('/^[a-zA-Z0-9]+$/', $_COOKIE[$cookie_name])) {
    return;  // Continue to main content
}

// No valid cookie: show login page
require_once __DIR__ . '/../../../top.php';
require_once __DIR__ . '/../../../nav.php';
?>
<?php $paw_base = isset($paw_account_base) ? $paw_account_base : ''; ?>
<link rel="stylesheet" href="<?php echo htmlspecialchars($paw_base); ?>style.css">
<style>
/* Override style.css body - we're inside Tsugi layout */
body { max-width: none !important; margin: 0 !important; }
.django-girls-content { max-width: 800px; margin: 0; padding: 0 2rem; }
.paw-login-box { max-width: 400px; margin: 2rem auto; padding: 2rem; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; }
.paw-login-box h2 { margin-top: 0; }
.paw-login-box .form-group { margin-bottom: 1rem; }
.paw-login-box label { display: block; margin-bottom: 0.25rem; font-weight: 500; }
.paw-login-box input { width: 100%; padding: 0.5rem; font-size: 1rem; border: 1px solid #ccc; border-radius: 4px; }
.paw-login-box .error { color: #c00; font-size: 0.9em; margin-top: 0.25rem; }
.paw-login-box button { padding: 0.5rem 1.5rem; font-size: 1rem; cursor: pointer; }
</style>
<div class="django-girls-content content">
<div class="paw-login-box">
    <h2>PythonAnywhere Account</h2>
    <p>To personalize the tutorial links for your PythonAnywhere site, enter your account name:</p>
    <form method="post" action="">
        <div class="form-group">
            <label for="paw-account">Account name</label>
            <input type="text" id="paw-account" name="<?php echo htmlspecialchars($cookie_name); ?>" 
                   placeholder="e.g. johndoe" autocomplete="username" 
                   value="<?php echo isset($account) ? htmlspecialchars($account) : ''; ?>">
            <?php if (!empty($account_error)): ?>
            <div class="error"><?php echo htmlspecialchars($account_error); ?></div>
            <?php endif; ?>
        </div>
        <button type="submit">Continue</button>
    </form>
</div>
</div>
<?php
$OUTPUT->footerStart();
$OUTPUT->footerEnd();
exit;
