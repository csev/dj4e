
import framework
import json

def handleRequest(request: dict, response: dict):
    path = request['path']
    if path == '/' : 
        response['headers']['Content-Type'] = 'text/plain; charset=utf-8'
        response['body'].append("This is the page at the root path, try another path")
    else :
        response['headers']['Content-Type'] = 'text/html; charset=utf-8'

        response['body'].append("<html><body><h1>Path not found: ")
        response['body'].append(path)
        response['body'].append("</h1><pre>")
        response['body'].append("Request data:")
        response['body'].append(json.dumps(request, indent=4))
        response['body'].append("</pre></body></html>")
    
print('Access http://localhost:9000')

framework.httpServer(handleRequest)

