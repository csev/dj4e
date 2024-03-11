<?php
require_once "../config.php";

use \Tsugi\Core\Settings;
use \Tsugi\UI\SettingsForm;
use \Tsugi\Core\LTIX;

$LAUNCH = LTIX::requireData();
$p = $CFG->dbprefix;

if ( SettingsForm::handleSettingsPost() ) {
    header( 'Location: '.addSession('index.php') ) ;
    return;
}
Here is some documentation on the software used to build these unit tests:
<ul>
<li><a href="https://symfony.com/doc/current/components/dom_crawler.html" target="_new">
https://symfony.com/doc/current/components/dom_crawler.html
</a></li>
<li><a href="https://api.symfony.com/2.3/Symfony/Component/BrowserKit.html" target="_new">
https://api.symfony.com/2.3/Symfony/Component/BrowserKit.html
</a></li>
<li><a href="https://api.symfony.com/2.3/Symfony/Component/DomCrawler/Crawler.html" target="_new">
https://api.symfony.com/2.3/Symfony/Component/DomCrawler/Crawler.html
</a></li>
</ul>
</p>
