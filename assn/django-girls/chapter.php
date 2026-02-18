<?php
/**
 * Shared chapter renderer. Include from each chapter's index.php.
 * Expects $chapter_dir (e.g. 'django') to be set.
 */
if (!isset($chapter_dir)) {
    die('$chapter_dir not set');
}
if ( ! defined('COOKIE_SESSION') ) define('COOKIE_SESSION', true);
require_once __DIR__ . '/../../tsugi/config.php';
$paw_account_base = '../';  // for check_account login page styles when in a chapter
require_once __DIR__ . '/inc/check_account.php';
$md_dir = __DIR__;  // django-girls/ when chapter.php is in django-girls/
$md_path = $chapter_dir . '/README.md';
require_once $md_dir . '/inc/functions.php';

$chapters = get_chapters();
$title = 'Unknown';
foreach ($chapters as $ch) {
    if (dirname($ch[0]) === $chapter_dir || (dirname($ch[0]) === '.' && $chapter_dir === '')) {
        $title = $ch[1];
        break;
    }
}

$body = render_markdown($md_path, $md_dir);
$current_slug = $chapter_dir;
$base_path = '../';

require_once __DIR__ . '/../../top.php';
require_once __DIR__ . '/../../nav.php';
?>
<link rel="stylesheet" href="../style.css">
<style>
/* Override style.css body - we're inside Tsugi layout */
body { max-width: none !important; margin: 0 !important; }
#chapters { margin-left: 1rem; }
@media print { #chapters { display: none; } }
.django-girls-content { max-width: 800px; margin: 0; padding: 0 2rem; }
a[target="_blank"]:after { content: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAAQElEQVR42qXKwQkAIAxDUUdxtO6/RBQkQZvSi8I/pL4BoGw/XPkh4XigPmsUgh0626AjRsgxHTkUThsG2T/sIlzdTsp52kSS1wAAAABJRU5ErkJggg==); margin: 0 3px 0 5px; }
</style>
<script>
function onTocSelect() { window.location = document.getElementById('chapters').value; }
</script>
<div style="float:right">
<select id="chapters" onchange="onTocSelect();">
  <?php echo render_toc_dropdown($base_path, $current_slug); ?>
</select>
</div>
<div class="django-girls-content content">
<?php echo $body; ?>
<?php echo render_django_girls_footer(); ?>
</div>
<?php
$OUTPUT->footerStart();
?>
<script>
$(document).ready(function() {
    $('a[href^="http"]').attr('target', function() {
        if (this.host === location.host) return '_self';
        return '_blank';
    });
});
</script>
<?php
$OUTPUT->footerEnd();
