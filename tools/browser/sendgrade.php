<?php
require_once "../config.php";

use \Tsugi\Core\LTIX;

$LAUNCH = LTIX::requireData();
if ( $LAUNCH->user && $LAUNCH->user->instructor ) {
   $retval = LTIX::gradeSend(1.0, false);
   $_SESSION['success'] = __('Grade sent');
} else {
    die('Must be instructor');
}
header("Location: ".addSession('index.php'));
