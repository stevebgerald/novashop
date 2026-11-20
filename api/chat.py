from http.server import BaseHTTPRequestHandler
import json
import os
import sys

# responses.py esta en ../bot
sys.path.insert(0, os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'bot'))
import responses


def responder(texto):
    texto = texto.strip()[:300]
    if not texto:
        return 'Escribime tu consulta.'
    if texto.startswith('/'):
        partes = texto.split(maxsplit=1)
        comando = partes[0][1:].split('@')[0].lower()
        argumentos = partes[1] if len(partes) > 1 else ''
        if comando == 'miid':
            return 'Ese comando solo funciona desde Telegram.'
        return responses.get_command(comando, argumentos)
    return responses.get_responses(texto)


class handler(BaseHTTPRequestHandler):
    def do_POST(self):
        largo = int(self.headers.get('Content-Length', 0))
        try:
            datos = json.loads(self.rfile.read(min(largo, 5000)) or b'{}')
            respuesta = responder(str(datos.get('mensaje', '')))
        except Exception as e:
            print('error en el chat:', e)
            respuesta = 'Tuve un problema para responder. Probá de nuevo en un momento.'

        self.send_response(200)
        self.send_header('Content-Type', 'application/json; charset=utf-8')
        self.end_headers()
        self.wfile.write(json.dumps({'respuesta': respuesta}, ensure_ascii=False).encode('utf-8'))
