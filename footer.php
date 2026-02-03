<?php

// Read service worker registration template
$swTemplate = file_get_contents(__DIR__ . '/tsugi/lib/src/Controllers/static/ServiceWorker/service-worker-register.php');
// Extract content after the PHP closing tag
$swScript = substr($swTemplate, strpos($swTemplate, '?>') + 2);

$foot = '
<p style="font-size: 75%; margin-top: 5em;">
Copyright Creative Commons Attribution 3.0 - Charles R. Severance
</p><script type="module" src="' . htmlspecialchars(\Tsugi\Controllers\StaticFiles::url('Notifications', 'tsugi-notifications.js')) .'"></script>
' . $swScript;

$OUTPUT->setAppFooter($foot);

$OUTPUT->footer();
