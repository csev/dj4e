import mini_django
import urls

print('Access http://localhost:9000')
mini_django.httpServer(urls.router)

