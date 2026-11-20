import json
import os
import sys
import urllib.request

ruta_env = os.path.join(os.path.dirname(os.path.abspath(__file__)), '.env')
if os.path.exists(ruta_env):
    for linea in open(ruta_env, encoding='utf-8'):
        if '=' in linea and not linea.startswith('#'):
            clave, valor = linea.strip().split('=', 1)
            os.environ.setdefault(clave, valor)

token = os.environ.get('TELEGRAM_TOKEN', '')
if not token or len(sys.argv) < 2:
    raise SystemExit('Uso: python set_webhook.py https://mi-proyecto.vercel.app  (y TELEGRAM_TOKEN en .env)')

sitio = sys.argv[1].rstrip('/')
if sitio == 'borrar':
    url = f'https://api.telegram.org/bot{token}/deleteWebhook'
else:
    url = f'https://api.telegram.org/bot{token}/setWebhook?url={sitio}/api/webhook'

respuesta = json.loads(urllib.request.urlopen(url, timeout=15).read())
print(respuesta.get('description', respuesta))
