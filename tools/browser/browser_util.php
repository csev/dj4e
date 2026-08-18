<?php

/**
 * Build a stable, readable secret for a student launch.
 *
 * Pattern: adjective + Noun + two digits, e.g. calmMaple42
 */
function browser_readable_secret($seed) {
    $adjectives = array(
        'amber', 'brave', 'calm', 'crisp', 'eager', 'fancy', 'gentle',
        'happy', 'jolly', 'kind', 'lively', 'merry', 'noble', 'proud',
        'quick', 'quiet', 'sunny', 'swift', 'vivid', 'zesty'
    );
    $nouns = array(
        'Apple', 'Birch', 'Cedar', 'Daisy', 'Eagle', 'Falcon', 'Ginger',
        'Hazel', 'Ivory', 'Jade', 'Kite', 'Lemon', 'Maple', 'Nova',
        'Olive', 'Pearl', 'Quill', 'River', 'Stone', 'Tulip'
    );

    $h = hexdec(substr(md5((string)$seed), 0, 8));
    $adj = $adjectives[$h % count($adjectives)];
    $noun = $nouns[($h >> 8) % count($nouns)];
    $num = 10 + (($h >> 16) % 90);
    return $adj.$noun.$num;
}
