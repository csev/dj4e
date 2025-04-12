

Errors in Symfony
-----------------

I do not know why but the code in the pictures assignments that needs up upload a picture
does not work with Symfony when talkig to localhost or ngrok.

If you don't add the file form item in the PHP code, Symfony always blows up with:

    Fatal error: Uncaught ValueError: Path cannot be empty in 
    /Users/csev/htdocs/dj4e/tsugi/vendor/symfony/mime/Part/TextPart.php:149

If you do add the file form, Symfony can submit file input tags, to 
www.pythonanywhere.com but not to localhost or ngrok.  It blows up with
a CSRF error in the browser and this in the Django log:

    [12/Apr/2025 14:56:46] "GET /mp/ad/create HTTP/1.1" 200 5653
    Forbidden (CSRF token missing.): /mp/ad/create
    [12/Apr/2025 14:56:46] "POST /mp/ad/create HTTP/1.1" 403 2506
    [12/Apr/2025 14:56:46] code 400, message Bad request syntax ('c')
    [12/Apr/2025 14:56:46] "c" 400 -

That is some scary crap.  I tried to use ngrok's protocol dumping
capabilities and it can't parse the message that is sent from Symfony
to Django except as a binary message.  That points a finger at
Symfony's serialization getting confused outbound when it is talking
to localhost.

But tracing through Symfony's AbstractBrowser and HttpClient is pretty
dense.  I am sure it a a really weird Symfony bug that will never be
fixed.   I advanced the Symfony version to no avail.

The Workaround
--------------

Since it seemed that non-file form posts worked well, perhaps if deep inside
Symfony we could hack it so it never sent files it might be a workaround.
And here it is as a patch

    --- a/vendor/symfony/browser-kit/AbstractBrowser.php
    +++ b/vendor/symfony/browser-kit/AbstractBrowser.php
    @@ -355,6 +355,7 @@ abstract class AbstractBrowser
     
             $server['HTTPS'] = 'https' === parse_url($uri, \PHP_URL_SCHEME);
     
    +        $files = [];
             $this->internalRequest = new Request($uri, $method, $parameters, $files, $this->cookieJar->allValues($uri), $server, $content);
     
             $this->request = $this->filterRequest($this->internalRequest);

Just as a note - I think this could be fixed in `mime/Part/TextPart.php` but it
won't be a simple 1-line fix (I tried).


How to patch
------------

	vi tsugi/vendor/symfony/browser-kit/AbstractBrowser.php

Go to line 358 (approx) and add the code to set the files parameter to an empty array.



-- Chuck Sat Apr 12 11:04:41 EDT 2025

