import json
import os
import unicodedata

# mismo catalogo que la web
RUTA_CATALOGO = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'data', 'productos.json')

with open(RUTA_CATALOGO, encoding='utf-8') as archivo:
    CATALOGO = json.load(archivo)

PRODUCTOS = CATALOGO['productos']
CATEGORIAS = CATALOGO['categorias']

SITIO = os.environ.get('SITE_URL', 'https://novashop-bot.vercel.app').rstrip('/')

ENVIO_GRATIS_DESDE = 50
COSTO_ENVIO = 5


def normalizar(texto):
    texto = unicodedata.normalize('NFD', texto.lower())
    return ''.join(c for c in texto if unicodedata.category(c) != 'Mn')


def precio_final(producto):
    return producto['oferta'] if producto['oferta'] else producto['precio']


def formato_precio(producto):
    if producto['oferta']:
        descuento = round((producto['precio'] - producto['oferta']) / producto['precio'] * 100)
        return f"${producto['oferta']:.2f} (antes ${producto['precio']:.2f}, -{descuento}%)"
    return f"${producto['precio']:.2f}"


def link_producto(producto):
    return f"{SITIO}/producto.html?p={producto['slug']}"


def renglon(producto):
    return f"• {producto['nombre']} - {formato_precio(producto)}"


def ficha(producto):
    return (f"{producto['nombre']}\n"
            f"Categoría: {CATEGORIAS[producto['categoria']]}\n"
            f"Precio: {formato_precio(producto)}\n"
            f"{producto['descripcion']}\n\n"
            f"Comprar: {link_producto(producto)}")


# sinonimos
SINONIMOS = {
    'reloj': 'smartwatch', 'relojes': 'smartwatch',
    'celular': 'smartphone', 'celulares': 'smartphone', 'celu': 'smartphone', 'telefonos': 'smartphone', 'movil': 'smartphone',
    'computadora': 'notebook', 'computadoras': 'notebook', 'compu': 'notebook', 'laptop': 'notebook', 'laptops': 'notebook',
    'portatil': 'notebook',
    'audifonos': 'auriculares', 'cascos': 'auriculares',
    'camara': 'webcam', 'camaras': 'webcam',
    'bocina': 'parlante', 'bocinas': 'parlante', 'altavoz': 'parlante', 'altavoces': 'parlante',
    'raton': 'mouse',
}


def palabras_de(texto):
    limpio = ''.join(c if c.isalnum() else ' ' for c in normalizar(texto))
    return limpio.split()


def buscar_productos(texto):
    buscadas = [SINONIMOS.get(p, p) for p in palabras_de(texto)]
    mejores = []
    mejor_puntaje = 0
    for producto in PRODUCTOS:
        del_nombre = palabras_de(producto['nombre'] + ' ' + producto['slug'])
        largas = 0
        cortas = 0
        for b in set(buscadas):
            if len(b) > 3:
                if any(n.startswith(b) or (len(n) > 3 and b.startswith(n)) for n in del_nombre):
                    largas += 1
            elif b in del_nombre:
                cortas += 1
        # las cortas (pro, 13, se) solo cuentan si ya hubo una larga
        puntaje = largas + cortas if largas else 0
        if puntaje > mejor_puntaje:
            mejor_puntaje = puntaje
            mejores = [producto]
        elif puntaje == mejor_puntaje and puntaje > 0:
            mejores.append(producto)
    return mejores


def lista_encontrados(encontrados):
    encontrados = sorted(encontrados, key=precio_final)
    lineas = [f'Encontré {len(encontrados)} productos:\n'] + [renglon(p) for p in encontrados[:10]]
    if len(encontrados) > 10:
        lineas.append(f'... y {len(encontrados) - 10} más en {SITIO}/tienda.html')
    lineas.append('\nEscribime el nombre completo de uno para ver el detalle.')
    return '\n'.join(lineas)


def start():
    return ('Hola! Soy el bot de NovaShop, la tienda de tecnología.\n'
            'Te puedo ayudar con esto:\n\n'
            '/productos - ver todo el catálogo\n'
            '/categorias - ver las categorías\n'
            '/ofertas - productos con descuento\n'
            '/destacados - los más elegidos\n'
            '/buscar - buscar un producto (ej: /buscar teclado)\n'
            '/hasta - productos hasta un precio (ej: /hasta 100)\n'
            '/recomendar - te recomiendo productos\n'
            '/envios - costos y tiempos de envío\n'
            '/pagos - medios de pago\n'
            '/garantia - garantía y devoluciones\n'
            '/stock - disponibilidad\n'
            '/seguimiento - dónde está mi pedido\n'
            '/contacto - horarios y contacto\n\n'
            'También podés escribirme el nombre de un producto y te paso el precio.')


def ayuda():
    return ('Escribime lo que estás buscando, por ejemplo "auriculares" o "precio del smartwatch".\n'
            'También me podés preguntar por envíos, pagos, garantía u horarios.\n'
            'Con /start ves todos los comandos.')


def productos():
    # no entran los 62 en un mensaje
    lineas = [f'Tenemos {len(PRODUCTOS)} productos:\n']
    for clave, nombre in CATEGORIAS.items():
        de_la_categoria = [p for p in PRODUCTOS if p['categoria'] == clave]
        if de_la_categoria:
            mas_barato = min(precio_final(p) for p in de_la_categoria)
            lineas.append(f'• {nombre}: {len(de_la_categoria)} productos desde ${mas_barato:.2f}')
    lineas.append('\nEscribime una categoría (por ejemplo "audio") para ver sus productos, '
                  'o el nombre de un producto para ver el precio.')
    lineas.append(f'Tienda: {SITIO}/tienda.html')
    return '\n'.join(lineas)


def categorias():
    lineas = ['Categorías:\n']
    for clave, nombre in CATEGORIAS.items():
        cantidad = len([p for p in PRODUCTOS if p['categoria'] == clave])
        lineas.append(f'• {nombre} ({cantidad})')
    lineas.append('\nEscribime el nombre de una categoría y te muestro sus productos.')
    return '\n'.join(lineas)


def categoria(clave):
    de_la_categoria = sorted([p for p in PRODUCTOS if p['categoria'] == clave], key=precio_final)
    lineas = [f'{CATEGORIAS[clave]} ({len(de_la_categoria)} productos):\n']
    lineas += [renglon(p) for p in de_la_categoria]
    lineas.append(f'\nVer en la tienda: {SITIO}/tienda.html?cat={clave}')
    return '\n'.join(lineas)


def ofertas():
    en_oferta = [p for p in PRODUCTOS if p['oferta']]
    en_oferta.sort(key=lambda p: (p['precio'] - p['oferta']) / p['precio'], reverse=True)
    lineas = [f'Hay {len(en_oferta)} productos en oferta. Los 10 con más descuento:\n']
    lineas += [renglon(p) for p in en_oferta[:10]]
    lineas.append(f'\nTodas las ofertas: {SITIO}/tienda.html?ofertas=1')
    lineas.append(f'Envío gratis en compras de más de ${ENVIO_GRATIS_DESDE}.')
    return '\n'.join(lineas)


def destacados():
    lineas = ['Productos destacados:\n'] + [renglon(p) for p in PRODUCTOS if p['destacado']]
    return '\n'.join(lineas)


def buscar(texto):
    if not texto.strip():
        return 'Decime qué producto buscás. Por ejemplo: /buscar teclado'
    encontrados = buscar_productos(texto)
    if not encontrados:
        return f'No encontré productos con "{texto}". Probá con /productos para ver todo el catálogo.'
    if len(encontrados) == 1:
        return ficha(encontrados[0])
    return lista_encontrados(encontrados)


def envios():
    return (f'Hacemos envíos a todo el país.\n'
            f'• Compras de más de ${ENVIO_GRATIS_DESDE}: envío gratis\n'
            f'• Compras menores: ${COSTO_ENVIO}\n'
            f'• Entrega en 24 a 48 hs hábiles')


def pagos():
    return ('Medios de pago:\n'
            '• Tarjeta de crédito o débito\n'
            '• Transferencia bancaria\n'
            'El pago se hace en el checkout de la tienda y es seguro.')


def garantia():
    return ('Todos los productos tienen garantía oficial.\n'
            'Si el producto llega con una falla escribinos por /contacto y lo cambiamos.')


def contacto():
    return ('Atención al cliente:\n'
            '• Lunes a viernes de 9:00 a 18:00\n'
            '• Respondemos en menos de 24 hs hábiles\n'
            f'• Formulario: {SITIO}/contacto.html')


def conjunto_del_mensaje(mensaje):
    encontrados = buscar_productos(mensaje)
    if encontrados:
        return encontrados, 'entre los que buscás'
    for clave, nombre in CATEGORIAS.items():
        if clave in mensaje or normalizar(nombre) in mensaje:
            return [p for p in PRODUCTOS if p['categoria'] == clave], 'de ' + nombre
    return PRODUCTOS, 'de la tienda'


def presupuesto(monto, mensaje=''):
    conjunto, de_donde = conjunto_del_mensaje(mensaje)
    alcanzan = sorted([p for p in conjunto if precio_final(p) <= monto], key=precio_final, reverse=True)
    if not alcanzan:
        mas_barato = min(conjunto, key=precio_final)
        return (f'Con ${monto:.0f} no llegás a ningún producto {de_donde}.\n'
                f'El más económico es:\n{renglon(mas_barato)}')
    lineas = [f'Con ${monto:.0f} te alcanza para {len(alcanzan)} productos {de_donde}:\n']
    lineas += [renglon(p) for p in alcanzan[:8]]
    if len(alcanzan) > 8:
        lineas.append(f'... y {len(alcanzan) - 8} más en {SITIO}/tienda.html?max={monto:.0f}')
    return '\n'.join(lineas)


def extremo(mensaje, mas_barato=True):
    conjunto, de_donde = conjunto_del_mensaje(mensaje)
    elegido = min(conjunto, key=precio_final) if mas_barato else max(conjunto, key=precio_final)
    titulo = 'El más económico' if mas_barato else 'El más caro'
    return f'{titulo} {de_donde}:\n\n{ficha(elegido)}'


def stock():
    return ('Todos los productos que ves en el catálogo tienen stock.\n'
            'Se despachan en 24 a 48 hs hábiles después de confirmar el pedido.')


def seguimiento():
    return ('Cuando despachamos el pedido te mandamos un email con el número de seguimiento.\n'
            'Si ya pasaron 48 hs hábiles y no lo recibiste, escribinos con tu número de pedido '
            f'(empieza con NS-) en {SITIO}/contacto.html')


def ubicacion():
    return ('NovaShop es una tienda 100% online, no tenemos local a la calle.\n'
            'Hacemos envíos a todo el país en 24 a 48 hs hábiles.')


def asesor():
    return ('Si preferís hablar con una persona, dejanos tu consulta en el formulario y un asesor '
            f'te responde en menos de 24 hs hábiles:\n{SITIO}/contacto.html\n'
            'Atendemos de lunes a viernes de 9:00 a 18:00.')


def recomendar():
    lineas = ['Te recomiendo los más elegidos por nuestros clientes:\n']
    lineas += [renglon(p) for p in PRODUCTOS if p['destacado']]
    lineas.append('\nSi me decís cuánto querés gastar (por ejemplo "hasta 100") te muestro opciones.')
    return '\n'.join(lineas)


def quienes_somos():
    return ('NovaShop es una tienda online de tecnología: auriculares, notebooks, smartphones, '
            'relojes y accesorios con garantía oficial.\n'
            f'Más sobre nosotros: {SITIO}/quienes-somos.html')


def numero_del_mensaje(mensaje):
    for palabra in mensaje.replace('$', ' ').replace(',', '.').split():
        try:
            return float(palabra.strip('.?!'))
        except ValueError:
            pass
    return None


COMANDOS = {
    'start': start,
    'help': ayuda,
    'ayuda': ayuda,
    'productos': productos,
    'categorias': categorias,
    'ofertas': ofertas,
    'destacados': destacados,
    'envios': envios,
    'pagos': pagos,
    'garantia': garantia,
    'contacto': contacto,
    'stock': stock,
    'seguimiento': seguimiento,
    'recomendar': recomendar,
}


def get_command(comando, argumentos=''):
    if comando == 'buscar':
        return buscar(argumentos)
    if comando == 'hasta':
        monto = numero_del_mensaje(argumentos)
        if monto is None:
            return 'Decime cuánto querés gastar. Por ejemplo: /hasta 100'
        return presupuesto(monto)
    if comando in COMANDOS:
        return COMANDOS[comando]()
    return 'No conozco ese comando. Escribí /start para ver los que hay.'


def get_responses(texto):
    mensaje = normalizar(texto)

    if any(p in mensaje for p in ('hasta', 'menos de', 'tengo', 'presupuesto', 'gastar', 'por debajo')):
        monto = numero_del_mensaje(mensaje)
        if monto is not None:
            return presupuesto(monto, mensaje)

    if any(p in mensaje for p in ('mas barat', 'mas economic', 'mas accesible')):
        return extremo(mensaje, mas_barato=True)
    if any(p in mensaje for p in ('mas car', 'mas costos')):
        return extremo(mensaje, mas_barato=False)

    encontrados = buscar_productos(mensaje)
    if len(encontrados) == 1:
        return ficha(encontrados[0])

    for clave, nombre in CATEGORIAS.items():
        if clave in mensaje or normalizar(nombre) in mensaje:
            return categoria(clave)

    if len(encontrados) > 1:
        return lista_encontrados(encontrados)

    if any(p in mensaje for p in ('seguimiento', 'mi pedido', 'mi compra', 'no me llego', 'rastrear')):
        return seguimiento()

    if any(p in mensaje for p in ('envio', 'envian', 'demora', 'tarda', 'llega')):
        return envios()

    if any(p in mensaje for p in ('stock', 'disponible', 'disponibilidad', 'queda')):
        return stock()

    if any(p in mensaje for p in ('local', 'sucursal', 'direccion', 'ubicacion', 'donde estan', 'donde queda')):
        return ubicacion()

    if any(p in mensaje for p in ('persona', 'asesor', 'humano', 'vendedor', 'operador')):
        return asesor()

    if any(p in mensaje for p in ('recomend', 'regalo', 'regalar', 'sugerencia', 'aconseja')):
        return recomendar()

    if any(p in mensaje for p in ('quienes son', 'quien sos', 'que es novashop', 'que sos')):
        return quienes_somos()

    if any(p in mensaje for p in ('pago', 'pagar', 'tarjeta', 'transferencia', 'cuotas')):
        return pagos()

    if any(p in mensaje for p in ('garantia', 'devolucion', 'devolver', 'cambio', 'falla')):
        return garantia()

    if any(p in mensaje for p in ('horario', 'contacto', 'telefono', 'atienden', 'atencion')):
        return contacto()

    if any(p in mensaje for p in ('oferta', 'descuento', 'promo', 'barato')):
        return ofertas()

    if any(p in mensaje for p in ('producto', 'catalogo', 'venden', 'precio', 'lista')):
        return productos()

    if any(p in mensaje for p in ('comprar', 'compra', 'pedido', 'carrito')):
        return (f'Para comprar entrá a la tienda, agregá los productos al carrito y completá el checkout:\n'
                f'{SITIO}/tienda.html')

    if any(p in palabras_de(mensaje) for p in ('hola', 'buenas', 'buen', 'hey')):
        return 'Hola! Bienvenido a NovaShop. ¿Qué estás buscando? Con /start te muestro todo lo que puedo hacer.'

    if 'gracias' in mensaje:
        return 'De nada! Cualquier otra consulta escribime.'

    if any(p in mensaje for p in ('chau', 'adios', 'hasta luego')):
        return 'Hasta luego! Gracias por visitar NovaShop.'

    return ('No te entendí. Probá escribiendo el nombre de un producto (por ejemplo "teclado") '
            'o usá /start para ver los comandos.')
