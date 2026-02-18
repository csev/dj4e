<?php
/**
 * Table of contents + introduction. Markdown is read from en-php/ (self-contained).
 */
if ( ! defined('COOKIE_SESSION') ) define('COOKIE_SESSION', true);
require_once __DIR__ . '/../../tsugi/config.php';
require_once __DIR__ . '/inc/functions.php';

$md_dir = __DIR__;
$chapters = get_chapters();
$intro_body = render_markdown('README.md', $md_dir);
$current_slug = '';  // Introduction is at root

require_once __DIR__ . '/../../top.php';
require_once __DIR__ . '/../../nav.php';
?>
<link rel="stylesheet" href="style.css">
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
  <?php echo render_toc_dropdown('', $current_slug); ?>
</select>
</div>
<div class="django-girls-content content">
<?php echo $intro_body; ?>
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
