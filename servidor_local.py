import importlib.util
import os
from http.server import SimpleHTTPRequestHandler, ThreadingHTTPServer

PUERTO = int(os.environ.get('PUERTO', 8000))
CARPETA = os.path.dirname(os.path.abspath(__file__))
os.chdir(CARPETA)


def cargar(nombre):
    ruta = os.path.join(CARPETA, 'api', nombre + '.py')
    spec = importlib.util.spec_from_file_location(nombre, ruta)
    modulo = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(modulo)
    return modulo


FUNCIONES = {'/api/chat': cargar('chat'), '/api/aviso': cargar('aviso'), '/api/webhook': cargar('webhook')}


class Servidor(SimpleHTTPRequestHandler):
    def do_POST(self):
        funcion = FUNCIONES.get(self.path.split('?')[0])
        if funcion:
            funcion.handler.do_POST(self)
        else:
            self.send_error(404)


if __name__ == '__main__':
    print('NovaShop en http://localhost:' + str(PUERTO) + '  (Ctrl+C para cortar)')
    ThreadingHTTPServer(('127.0.0.1', PUERTO), Servidor).serve_forever()
