from http.server import BaseHTTPRequestHandler
import json
import os
import sys
import urllib.request

# responses.py esta en ../bot
sys.path.insert(0, os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'bot'))
import responses

TOKEN = os.environ.get('TELEGRAM_TOKEN', '')


def enviar_mensaje(chat_id, texto):
    datos = json.dumps({'chat_id': chat_id, 'text': texto}).encode('utf-8')
    pedido = urllib.request.Request(
        f'https://api.telegram.org/bot{TOKEN}/sendMessage',
        data=datos,
        headers={'Content-Type': 'application/json'},
    )
    urllib.request.urlopen(pedido, timeout=8)


def responder(texto, chat_id):
    if texto.startswith('/'):
        partes = texto.split(maxsplit=1)
        comando = partes[0][1:].split('@')[0].lower()
        argumentos = partes[1] if len(partes) > 1 else ''
        if comando == 'miid':
            return f'Tu id de chat es: {chat_id}'
        return responses.get_command(comando, argumentos)
    return responses.get_responses(texto)


class handler(BaseHTTPRequestHandler):
    # telegram manda los mensajes aca
    def do_POST(self):
        largo = int(self.headers.get('Content-Length', 0))
        try:
            update = json.loads(self.rfile.read(largo) or b'{}')
            mensaje = update.get('message') or {}
            texto = mensaje.get('text')
            if texto and TOKEN:
                enviar_mensaje(mensaje['chat']['id'], responder(texto, mensaje['chat']['id']))
        except Exception as e:
            print('error en el webhook:', e)

        self.send_response(200)
        self.send_header('Content-Type', 'text/plain; charset=utf-8')
        self.end_headers()
        self.wfile.write(b'ok')

    def do_GET(self):
        self.send_response(200)
        self.send_header('Content-Type', 'text/plain; charset=utf-8')
        self.end_headers()
        self.wfile.write('Bot de NovaShop funcionando'.encode('utf-8'))
