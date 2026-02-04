<?php

// Service worker registration and notifications web component are now loaded
// in Output.php footerStart() method

$foot = '
<p style="font-size: 75%; margin-top: 5em;">
Copyright Creative Commons Attribution 3.0 - Charles R. Severance
</p>';

$OUTPUT->setAppFooter($foot);

$OUTPUT->footer();
