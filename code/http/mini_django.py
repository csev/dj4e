# https://docs.python.org/3/howto/sockets.html
# https://stackoverflow.com/questions/8627986/how-to-keep-a-socket-open-until-client-closes-it
# https://stackoverflow.com/questions/10091271/how-can-i-implement-a-simple-web-server-using-python-without-using-any-libraries

from socket import *
import traceback

def parseRequest(rd:str) -> dict:
    retval = dict()
    ipos = rd.find("\r\n\r\n")
    if ipos < 1 : 
        print('Incorrectly formatted request')
        print(repr(rd))
        return Null

    # Find the blank line between HEAD and BODY
    head = rd[0:ipos-1]
    lines = head.split("\n")
    headers = dict()

    # GET / HTTP/1.1
    if len(lines) > 0 :
        firstline = lines[0]
        pieces = firstline.split(' ')
        if len(pieces) >= 2 :
            retval['method'] = pieces[0]
            retval['path'] = pieces[1]

    # Accept-Language: en-US,en;q=0.5
    for line in lines:
        line = line.strip()
        pieces = line.split(": ", 1)
        if len(pieces) != 2 : continue
        headers[pieces[0].strip()] = pieces[1].strip()
    retval['headers'] = headers
    return retval

def responseSend(clientsocket, response: dict) :
    clientsocket.sendall("HTTP/1.1 200 OK\r\n".encode())
    for key, value in response['headers'].items():
        clientsocket.sendall(key.encode())
        clientsocket.sendall(": ".encode())
        clientsocket.sendall(value.encode())
        clientsocket.sendall("\r\n".encode())

    clientsocket.sendall("\r\n".encode())
    for line in response['body']:
        clientsocket.sendall(line.replace("\n", "\r\n").encode())
        clientsocket.sendall("\r\n".encode())

def httpServer(handler):
    serversocket = socket(AF_INET, SOCK_STREAM)
    try :
        serversocket.bind(('localhost',9000))
        serversocket.listen(5)
        while(1):
            (clientsocket, address) = serversocket.accept()

            rd = clientsocket.recv(5000).decode()
            print('===================')
            print(rd)
            request = parseRequest(rd)

            response = dict();
            response['headers'] = dict()
            response['body'] = list()

            handler(request, response)

            responseSend(clientsocket, response)

            clientsocket.shutdown(SHUT_WR)

    except KeyboardInterrupt :
        print("\nShutting down...\n")
    except Exception as exc :
        print(exc)
        print(traceback.format_exc())

    print("Closing socket")
    serversocket.close()


