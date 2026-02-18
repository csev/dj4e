<?php
/**
 * Shared functions for en-php: Markdown processing and link rewriting.
 * Reads from en-php/ (self-contained; run setup_en_php.sh to copy from en/).
 */

function process_template_syntax($text) {
    // {% filename %}path{% endfilename %} -> styled label
    $text = preg_replace_callback(
        '/\{%\s*filename\s*%\}(.*?)\{%\s*endfilename\s*%\}/s',
        function ($m) {
            $label = trim($m[1]);
            $label = preg_replace('/\{\{\s*warning_icon\s*\}\}/', '⚠ ', $label);
            // blog/... file paths -> ~/djangogirls/blog/...
            $label = preg_replace('#(?<![/\w])blog/#', '~/djangogirls/blog/', $label);
            return '<div class="filename">' . htmlspecialchars($label) . '</div>' . "\n";
        },
        $text
    );
    // {% raw %}...{% endraw %} -> keep content, remove tags
    $text = preg_replace('/\{%\s*raw\s*%\}(.*?)\{%\s*endraw\s*%\}/s', '$1', $text);
    // Strip remaining Honkit syntax
    $text = preg_replace('/\{%\s*set[^%]+%\}/', '', $text);
    $text = preg_replace('/\{%\s*if[^%]+%\}.*?\{%\s*endif\s*%\}/s', '', $text);
    $text = preg_replace('/\{%\s*include[^%]+%\}/', '', $text);
    // Keep only Linux variants in <!--sec--> sections; strip Windows/macOS-only
    $text = preg_replace_callback(
        '/<!--sec\s+([^>]*?)-->\s*(.*?)\s*<!--endsec-->/s',
        function ($m) {
            $attrs = $m[1];
            $content = $m[2];
            if (preg_match('/data-title="([^"]*)"/', $attrs, $titleMatch)) {
                $title = $titleMatch[1];
                // Keep: Linux variants only (PythonAnywhere/Linux target)
                $keep = preg_match(
                    '/Linux|Debian|Ubuntu|Fedora|openSUSE|macOS and Linux|Linux and macOS|macOS or Linux/i',
                    $title
                );
                if (!$keep) {
                    return '';  // Strip Windows/macOS-only sections
                }
            }
            return $content;
        },
        $text
    );
    return $text;
}

function rewrite_links($text, $base_folder) {
    $text = preg_replace_callback(
        '/\[([^\]]+)\]\(([^\)]+)\)/',
        function ($m) {
            $url = trim($m[2]);
            if ($url === '' || $url[0] === '#' || strpos($url, 'http') === 0 || strpos($url, 'mailto:') === 0) {
                return $m[0];
            }
            $url = preg_replace('/README\.md\s*(#.*)?$/i', 'index.php$1', $url);
            $url = preg_replace('/^\.\//', '', $url);
            return '[' . $m[1] . '](' . $url . ')';
        },
        $text
    );
    return $text;
}

function get_chapters() {
    return [
        ['README.md', 'Introduction'],
        ['django/README.md', 'What is Django?'],
        ['django_start_project/README.md', 'Your first Django project!'],
        ['django_models/README.md', 'Django models'],
        ['django_admin/README.md', 'Django admin'],
        ['deploy/README.md', 'Deploy!'],
        ['django_urls/README.md', 'Django URLs'],
        ['django_views/README.md', 'Django views – time to create!'],
        ['html/README.md', 'Introduction to HTML'],
        ['django_orm/README.md', 'Django ORM (Querysets)'],
        ['dynamic_data_in_templates/README.md', 'Dynamic data in templates'],
        ['django_templates/README.md', 'Django templates'],
        ['css/README.md', 'CSS – make it pretty'],
        ['template_extending/README.md', 'Template extending'],
        ['extend_your_application/README.md', 'Extend your application'],
        ['django_forms/README.md', 'Django Forms'],
        ['whats_next/README.md', "What's next?"],
    ];
}

function md_path_to_slug($md_path) {
    $dir = dirname($md_path);
    return ($dir === '.') ? '' : $dir;
}

/**
 * Attribution footer for Django Girls tutorial content.
 */
function render_django_girls_footer() {
    return '<footer class="django-girls-attribution" style="margin-top:2rem;padding-top:1rem;border-top:1px solid #ddd;font-size:0.8em;color:#666;">' .
        'This material is adapted from the <a href="https://tutorial.djangogirls.org/">Django Girls tutorial</a> for use on PythonAnywhere, ' .
        'and used under the <a href="https://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a>. ' .
        'To view a copy of this license, visit <a href="https://creativecommons.org/licenses/by-sa/4.0/">https://creativecommons.org/licenses/by-sa/4.0/</a>.' .
        '</footer>';
}

/**
 * Render a table-of-contents dropdown (like mdn). Base path is '' for root, '../' for chapters.
 */
function render_toc_dropdown($base_path, $current_slug) {
    $chapters = get_chapters();
    $options = [];
    foreach ($chapters as $ch) {
        list($md_path, $title) = $ch;
        $slug = md_path_to_slug($md_path);
        $href = ($slug === '') ? $base_path . 'index.php' : $base_path . $slug . '/index.php';
        $selected = ($slug === $current_slug) ? ' selected' : '';
        $options[] = '<option value="' . htmlspecialchars($href) . '"' . $selected . '>' . htmlspecialchars($title) . '</option>';
    }
    $options[] = '<option value="' . htmlspecialchars($base_path . 'clear_account.php') . '">Change PythonAnywhere Account</option>';
    return implode("\n  ", $options);
}

function render_markdown($md_path, $md_dir) {
    $full_path = $md_dir . '/' . $md_path;
    if (!file_exists($full_path)) {
        return '<p><em>Content not found.</em></p>';
    }
    $raw = file_get_contents($full_path);
    $raw = process_template_syntax($raw);
    $raw = str_replace('myvenv', '.ve52', $raw);  // PythonAnywhere venv name
    $account = (!empty($_COOKIE['pythonanywhere_account']) && preg_match('/^[a-zA-Z0-9]+$/', $_COOKIE['pythonanywhere_account']))
        ? $_COOKIE['pythonanywhere_account'] : 'account';
    $raw = str_replace('http://127.0.0.1:8000', 'https://' . $account . '.pythonanywhere.com', $raw);
    // Full path for mysite/ file references (PythonAnywhere structure)
    $raw = preg_replace('#(?<!djangogirls/)mysite/#', '~/djangogirls/mysite/', $raw);
    $base = md_path_to_slug($md_path);
    $raw = rewrite_links($raw, $base);
    require_once __DIR__ . '/../Parsedown.php';
    $parsedown = new Parsedown();
    return $parsedown->text($raw);
}
