
import mini_django
import views

# This is similar to Django's urls.py
def router(request: dict, response: dict):
    path = request['path']
    print('==== Routing to path:', path);
    if path == '/' : 
        views.root(request, response)
    elif path == '/dj4e' : 
        views.dj4e(request, response)
    elif path == '/js4e' : 
        views.js4e(request, response)
    else :
        views.default(request, response)

