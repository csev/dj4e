
import mini_django
import json

# This is similar to Django's views.py
def view_root(request: dict, response: dict) :
    response['headers']['Content-Type'] = 'text/plain; charset=utf-8'
    response['body'].append("This is the page at the root path, try another path")
    response['body'].append("Try /dj4e /js4e or /dump")

def view_dj4e(request: dict, response: dict) :
    response['headers']['Content-Type'] = 'text/html; charset=utf-8'
    response['body'].append("<html><body><h1>Django is fun</h1></body>")

def view_js4e(request: dict, response: dict) :
    response['headers']['Content-Type'] = 'text/html; charset=utf-8'
    response['body'].append("<html><body><h1>JavaScript is Getting Much Better!</h1></body>")

def view_default(request: dict, response: dict) :
    path = request['path']

    response['headers']['Content-Type'] = 'text/html; charset=utf-8'

    response['body'].append("<html><body><h1>Path not found: ")
    response['body'].append(path)
    response['body'].append("</h1><pre>")
    response['body'].append("Request data:")
    response['body'].append(json.dumps(request, indent=4))
    response['body'].append("</pre></body></html>")

# This is similar to Django's urls.py
def url_router(request: dict, response: dict):
    path = request['path']
    if path == '/' : 
        view_root(request, response)
    elif path == '/dj4e' : 
        view_dj4e(request, response)
    elif path == '/js4e' : 
        view_js4e(request, response)
    else :
        view_default(request, response)
 
print('Access http://localhost:9000')

mini_django.httpServer(url_router)

