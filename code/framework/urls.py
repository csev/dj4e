
from mini_django import HttpRequest, HttpResponse
import views

# This is similar to Django's urls.py
def router(request: HttpRequest) -> HttpResponse:
    print('==== Routing to path:', request.path);
    if request.path == '/' : 
        return views.root(request)
    elif request.path.startswith('/dj4e') : 
        return views.dj4e(request)
    elif request.path == '/js4e' : 
        return views.js4e(request)
    else :
        return views.default(request)

