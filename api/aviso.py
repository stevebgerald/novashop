from http.server import BaseHTTPRequestHandler
import json
import os
import urllib.request

TOKEN = os.environ.get('TELEGRAM_TOKEN', '')
ADMIN_CHAT_ID = os.environ.get('ADMIN_CHAT_ID', '')


def armar_texto(datos):
    if datos.get('tipo') == 'pedido':
        lineas = [f"NUEVO PEDIDO {datos.get('numero', '')}",
                  f"Cliente: {datos.get('nombre', '')}",
                  f"Email: {datos.get('email', '')}",
                  f"Teléfono: {datos.get('telefono', '')}",
                  f"Dirección: {datos.get('direccion', '')}",
                  f"Pago: {datos.get('pago', '')}",
                  '']
        for item in datos.get('items', [])[:30]:
            lineas.append(f"• {item.get('cantidad')} x {item.get('nombre')} (${item.get('precio')})")
        lineas.append('')
        lineas.append(f"Envío: ${datos.get('envio', 0)}")
        lineas.append(f"TOTAL: ${datos.get('total', 0)}")
        return '\n'.join(lineas)

    return (f"NUEVA CONSULTA\n"
            f"Nombre: {datos.get('nombre', '')}\n"
            f"Email: {datos.get('email', '')}\n"
            f"Teléfono: {datos.get('telefono', '')}\n"
            f"Asunto: {datos.get('asunto', '')}\n\n"
            f"{datos.get('mensaje', '')}")


class handler(BaseHTTPRequestHandler):
    def do_POST(self):
        largo = int(self.headers.get('Content-Length', 0))
        enviado = False
        try:
            datos = json.loads(self.rfile.read(min(largo, 20000)) or b'{}')
            if TOKEN and ADMIN_CHAT_ID and datos.get('nombre'):
                cuerpo = json.dumps({'chat_id': ADMIN_CHAT_ID, 'text': armar_texto(datos)[:3500]}).encode('utf-8')
                pedido = urllib.request.Request(
                    f'https://api.telegram.org/bot{TOKEN}/sendMessage',
                    data=cuerpo,
                    headers={'Content-Type': 'application/json'},
                )
                urllib.request.urlopen(pedido, timeout=8)
                enviado = True
        except Exception as e:
            print('error en aviso:', e)

        self.send_response(200)
        self.send_header('Content-Type', 'application/json')
        self.end_headers()
        self.wfile.write(json.dumps({'ok': True, 'enviado': enviado}).encode('utf-8'))
