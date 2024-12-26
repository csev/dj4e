
import json

# This is similar to Django's views.py

def root(request: dict, response: dict) :
    response['headers']['Content-Type'] = 'text/plain; charset=utf-8'
    response['body'].append("This is the page at the root path, try another path")
    response['body'].append("Try /dj4e /js4e or /ca4e")

def dj4e(request: dict, response: dict) :
    response['headers']['Content-Type'] = 'text/html; charset=utf-8'
    response['body'].append("<html><body><h1>Django is fun</h1></body>")

def js4e(request: dict, response: dict) :
    response['headers']['Content-Type'] = 'text/html; charset=utf-8'
    response['body'].append("<html><body><h1>JavaScript is Getting Much Better!</h1></body>")

def default(request: dict, response: dict) :
    response['headers']['Content-Type'] = 'text/html; charset=utf-8'

    response['body'].append('<html><body><div style="background-color: rgb(255, 255, 204);"><b>Page not found (404)</b>')
    response['body'].append('<div><b>Request Method:</b> '+request['method']+"</div>");
    response['body'].append('<div><b>Request URL:</b> '+request['path']+'</div></div>')
    response['body'].append("</div><pre>")
    response['body'].append("Valid paths: /dj4e /js4e or /404")
    response['body'].append("\nRequest data:")
    response['body'].append(json.dumps(request, indent=4))
    response['body'].append("</pre></body></html>")
