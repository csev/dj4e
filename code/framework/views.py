
from mini_django import HttpRequest, HttpResponse

# This is similar to Django's views.py

def root(req: HttpRequest) -> HttpResponse:
    res = HttpResponse()
    res.headers['Content-Type'] = 'text/html; charset=utf-8'
    res.println("<html><head></head><body>")
    res.println("This is the page at the root path, try another path")
    res.println("Try /dj4e /js4e /ca4e or /broken")
    res.println("</body></html>")
    return res

def dj4e(req: HttpRequest) -> HttpResponse:
    res = HttpResponse()
    res.headers['Content-Type'] = 'text/plain; charset=utf-8'
    res.println("Django is fun")
    return res

def js4e(req: HttpRequest) -> HttpResponse:
    res = HttpResponse()
    res.code = "302"    # Lets do a temporary redirect...
    res.headers['Location'] = '/dj4e'
    res.headers['Content-Type'] = 'text/plain; charset=utf-8'
    res.println("You will only see this in the debugger!")
    return res

def broken(req: HttpRequest):
    return "I am a broken view, returning a string by mistake"

