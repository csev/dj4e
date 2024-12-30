
import json
from mini_django import HttpRequest, HttpResponse

# This is similar to Django's views.py

def root(req: HttpRequest) -> HttpResponse:
    res = HttpResponse()
    res.headers['Content-Type'] = 'text/plain; charset=utf-8'
    res.println("This is the page at the root path, try another path")
    res.println("Try /dj4e /js4e or /ca4e")
    return res

def dj4e(req: HttpRequest) -> HttpResponse:
    res = HttpResponse()
    res.headers['Content-Type'] = 'text/html; charset=utf-8'
    res.println("<html><body><h1>Django is fun</h1></body>")

def js4e(req: HttpRequest) -> HttpResponse:
    res = HttpResponse()
    res.headers['Content-Type'] = 'text/html; charset=utf-8'
    res.println("<html><body><h1>JavaScript is Getting Much Better!</h1></body>")

def default(req: HttpRequest) -> HttpResponse:
    res = HttpResponse()

    res.headers['Content-Type'] = 'text/html; charset=utf-8'

    res.println('<html><body><div style="background-color: rgb(255, 255, 204);"><b>Page not found (404)</b>')
    res.println('<div><b>Request Method:</b> '+request.method+"</div>");
    res.println('<div><b>Request URL:</b> '+request.path+'</div></div>')
    res.println("</div><pre>")
    res.println("Valid paths: /dj4e /js4e or /404")
    res.println("\nRequest data:")
    res.println(json.dumps(request, indent=4))
    res.body.println("</pre></body></html>")
