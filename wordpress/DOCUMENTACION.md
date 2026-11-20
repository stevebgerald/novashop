# Documentación del Proyecto — NovaShop

**Tienda online con WordPress + WooCommerce sobre servidor Linux (LAMP)**

| Dato | Detalle |
|------|---------|
| Proyecto | NovaShop — E-commerce de tecnología |
| Plataforma | WordPress + WooCommerce |
| Tema | NovaShop (tema personalizado) |
| Servidor | Ubuntu (IBM Cloud / instancia VPS) |
| Stack | Apache2 · MySQL · PHP · WordPress |
| Acceso ejemplo | `http://168.129.176.214/` |
| Versión del tema | 1.1.0 |
| Usuario SSH / sistema | `admin` *(o el usuario de la instancia, p. ej. ubuntu)* |
| Usuario WordPress | `admin` |
| Alumno | Steve Gerald Brusa |
| Materia | Metodología de Diseño de Software |
| Instancia | Examen final |

---

## 1. Objetivo del proyecto

Implementar un sitio web de comercio electrónico funcional denominado **NovaShop**, desplegado en una instancia Linux, con:

- Servidor web Apache y base de datos MySQL.
- CMS WordPress y plugin WooCommerce.
- Tema personalizado con diseño propio (navy / azul / naranja).
- Catálogo de productos con imágenes, carrito y checkout.
- Secciones institucionales: **Quiénes Somos** y **Contacto** (formulario funcional).
- Al menos **5 plantillas (templates) funcionales**.

---

## 2. Alcance y funcionalidades

### 2.1 Funcionalidades implementadas

| Módulo | Descripción |
|--------|-------------|
| Inicio | Hero, beneficios (envío, pago, garantía) y productos destacados |
| Catálogo / Tienda | Listado de productos, categorías, filtro por precio y ordenamiento |
| Ficha de producto | Detalle, precio, imagen y botón “Agregar al carrito” |
| Carrito | Cantidades, subtotal, actualización y enlace a checkout |
| Checkout | Formulario de datos del cliente y resumen del pedido |
| Quiénes Somos | Historia, propósito y pilares de valor de la marca |
| Contacto | Formulario funcional (guarda mensajes en el admin y envía email si el servidor lo permite) |
| Responsive | Adaptación a escritorio y móvil |

### 2.2 Plantillas funcionales del tema (≥ 5)

| # | Plantilla | Archivo | Uso |
|---|-----------|---------|-----|
| 1 | Inicio | `front-page.php` | Página principal |
| 2 | Tienda / Catálogo | `woocommerce/archive-product.php` | Listado y filtros |
| 3 | Producto | `woocommerce/single-product.php` | Detalle de producto |
| 4 | Carrito | `woocommerce/cart/cart.php` | Carrito de compras |
| 5 | Checkout | `woocommerce/checkout/form-checkout.php` | Finalizar compra |
| 6 | Contacto | `page-contacto.php` | Formulario de contacto |
| 7 | Quiénes Somos | `page-quienes-somos.php` | Página institucional |

---

## 3. Arquitectura del sistema

```
[Cliente / Navegador]
        │  HTTP :80
        ▼
[Apache2]  ──►  PHP  ──►  WordPress + Tema NovaShop + WooCommerce
        │
        └──►  MySQL  (base: wordpress / usuario de aplicación)
```

### 3.1 Componentes

- **Sistema operativo:** Ubuntu Server.
- **Servidor web:** Apache 2.4.
- **Lenguaje:** PHP (con extensiones `php-mysql`, `php-gd`, `php-xml`, `php-mbstring`, `php-curl`, etc.).
- **Base de datos:** MySQL Server.
- **CMS:** WordPress.
- **E-commerce:** WooCommerce.
- **Presentación:** Tema hijo/personalizado **NovaShop** (HTML/CSS/JS + plantillas PHP).
- **Acceso remoto:** SSH / SFTP (Bitvise).

### 3.2 Flujo de compra

1. El usuario navega el **Inicio** o la **Tienda**.
2. Selecciona un producto y lo agrega al **carrito**.
3. Revisa cantidades en **Carrito**.
4. Completa datos en **Checkout**.
5. WooCommerce registra el pedido en la base de datos.

---

## 4. Estructura del tema NovaShop

```
novashop/
├── style.css                 # Encabezado del tema (metadatos)
├── functions.php             # Carga de módulos del tema
├── header.php / footer.php   # Cabecera y pie globales
├── front-page.php            # Plantilla de inicio
├── page.php / index.php      # Páginas genéricas / fallback
├── page-contacto.php         # Contacto
├── page-quienes-somos.php    # Quiénes Somos
├── assets/
│   ├── css/main.css          # Estilos principales
│   ├── js/main.js            # Menú móvil / categorías
│   └── img/                  # Imágenes de productos y hero
├── inc/
│   ├── setup.php             # Menús, assets, soporte WooCommerce
│   ├── woocommerce.php       # Cards, badges, fragmentos del carrito
│   ├── contact.php           # Formulario y CPT de mensajes
│   └── demo-products.php     # Generador de catálogo demo
└── woocommerce/
    ├── archive-product.php
    ├── single-product.php
    ├── cart/cart.php
    └── checkout/form-checkout.php
```

Ruta en el servidor:

```text
/var/www/html/wp-content/themes/novashop/
```

---

## 5. Instalación del servidor (LAMP + WordPress)

### 5.1 Actualización e instalación de Apache

```bash
sudo apt-get update
sudo apt install apache2 -y
sudo systemctl status apache2
```

### 5.2 Firewall (HTTP)

```bash
sudo apt install firewalld -y
sudo firewall-cmd --permanent --add-service=http --zone=public
sudo firewall-cmd --reload
sudo firewall-cmd --list-all
```

> En IBM Cloud también debe estar abierto el **puerto 80** en el Security Group / reglas de red.

### 5.3 MySQL

```bash
sudo apt install mysql-server mysql-client -y
mysql --version
sudo mysql_secure_installation
```

Configuración típica de base y usuario:

```sql
CREATE DATABASE wordpress;
CREATE USER 'wordpress'@'%' IDENTIFIED BY 'TuPasswordSeguro';
GRANT ALL PRIVILEGES ON wordpress.* TO 'wordpress'@'%';
FLUSH PRIVILEGES;
```

### 5.4 WordPress y PHP

```bash
cd ~
wget https://wordpress.org/latest.tar.gz
tar xzvf latest.tar.gz
sudo apt install php libapache2-mod-php php-mysql php-cgi php-cli php-gd php-mbstring php-xml php-curl -y
sudo cp -r ~/wordpress/* /var/www/html/
sudo rm -f /var/www/html/index.html
sudo chown -R www-data:www-data /var/www/
sudo systemctl restart apache2
```

Configurar `wp-config.php` con `DB_NAME`, `DB_USER`, `DB_PASSWORD` y `DB_HOST`.

Completar el instalador web de WordPress en el navegador (`http://IP_PUBLICA`).

### 5.5 Permalinks (importante)

Para que funcionen URLs como `/tienda/`, `/contacto/`, `/quienes-somos/`:

1. Habilitar `mod_rewrite` y `AllowOverride All` en Apache.
2. Contar con `.htaccess` de WordPress en `/var/www/html/`.
3. En WP: **Ajustes → Enlaces permanentes → Guardar**.

---

## 6. Instalación del tema y WooCommerce

### 6.1 WooCommerce

1. **Plugins → Añadir nuevo → WooCommerce → Instalar y activar**.
2. Completar el asistente (país, moneda, páginas Tienda / Carrito / Checkout / Mi cuenta).
3. Desactivar modo “Próximamente” / Coming Soon para publicar la tienda.

### 6.2 Tema NovaShop

1. Subir la carpeta `novashop` a `/var/www/html/wp-content/themes/novashop` (SFTP).
2. Asignar permisos:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/wp-content/themes/novashop
   ```
3. **Apariencia → Temas → Activar NovaShop**.
4. **Ajustes → Lectura:** página de inicio estática = **Inicio**.  
   Página de entradas: dejar vacío (no asignar Quiénes Somos ni Contacto).
5. **Apariencia → NovaShop Demo → Generar / actualizar catálogo + imágenes + páginas**.

Esto crea categorías, productos con imágenes, menú, y las páginas Contacto / Quiénes Somos.

### 6.3 URLs principales del sitio

| Sección | URL (ejemplo) |
|---------|----------------|
| Inicio | `http://168.129.176.214/` |
| Tienda | `http://168.129.176.214/tienda/` |
| Quiénes Somos | `http://168.129.176.214/quienes-somos/` |
| Contacto | `http://168.129.176.214/contacto/` |
| Carrito | `http://168.129.176.214/carrito/` |
| Checkout | `http://168.129.176.214/finalizar-compra/` |
| Admin | `http://168.129.176.214/wp-admin/` |

> Si una URL amigable falla, probar `?page_id=ID` y luego volver a guardar Enlaces permanentes.

---

## 7. Descripción de secciones clave

### 7.1 Inicio

- Barra superior (envío / ayuda).
- Logo, buscador y acceso a cuenta/carrito.
- Hero: “Tecnología que conecta tu mundo”.
- Trust badges: envío, pago seguro, garantía.
- Grilla de productos destacados con CTA “Agregar al carrito”.

### 7.2 Tienda (catálogo)

- Breadcrumb Inicio / Tienda.
- Sidebar: categorías, filtro por precio, ordenar por.
- Grilla de productos con badge de descuento, precio y botón de compra.

### 7.3 Quiénes Somos

Contenido institucional:

- Título: *Innovación y tecnología al alcance de tu mano*.
- Historia / propósito de NovaShop.
- Pilares: Envío rápido · Pago 100% seguro · Garantía y soporte personalizado.
- Llamados a acción hacia Tienda y Contacto.

### 7.4 Contacto

Formulario con: nombre, email, teléfono, asunto y mensaje.

- Validación de campos obligatorios.
- Protección con nonce de WordPress.
- Persistencia: mensajes visibles en el admin (**Mensajes de contacto**).
- Intento de envío por `wp_mail` al email del administrador.

### 7.5 Carrito y Checkout

Gestionados por WooCommerce con plantillas override del tema NovaShop, manteniendo la identidad visual del sitio.

---

## 8. Base de datos (resumen)

| Elemento | Uso |
|----------|-----|
| Base `wordpress` | Datos del CMS y de WooCommerce |
| Tablas `wp_*` | Posts, páginas, opciones, usuarios |
| Productos | Custom post type `product` (WooCommerce) |
| Pedidos | Pedidos / órdenes WooCommerce |
| Mensajes | Custom post type `ns_contact` (tema) |

---

## 9. Pruebas realizadas (checklist)

| Prueba | Resultado esperado |
|--------|--------------------|
| Apache activo (`systemctl status apache2`) | `active (running)` |
| Acceso HTTP a la IP pública | Carga el sitio |
| Inicio con hero y productos | OK |
| Tienda con filtros y productos | OK |
| Agregar al carrito | Contador del carrito se actualiza |
| Carrito / Checkout | Formularios visibles |
| Quiénes Somos | Contenido institucional completo |
| Contacto | Mensaje guardado / confirmación |
| Responsive | Menú y grillas se adaptan en móvil |
| Permalinks | URLs amigables responden 200 |

---

## 10. Problemas frecuentes y soluciones

| Problema | Causa habitual | Solución |
|----------|----------------|----------|
| No carga el sitio por IP | Puerto 80 cerrado (SG / firewall) | Abrir HTTP en Security Group y `firewalld` |
| Sitio sin estilos (CSS) | URL del sitio incorrecta o CSS no subido | **Ajustes → Generales** = `http://IP` y verificar `assets/css/main.css` |
| 404 en `/contacto/` o `/quienes-somos/` | Rewrite / `.htaccess` | `AllowOverride All`, `a2enmod rewrite`, guardar permalinks |
| Página muestra “Hola, mundo!” | Página de entradas mal asignada | **Ajustes → Lectura**: página de entradas vacía; asignar plantilla correcta |
| Fallo al subir por SFTP | Permisos `www-data` | Subir a `/home/ubuntu` y `sudo cp`, luego `chown www-data` |
| Productos sin imagen | Catálogo demo no regenerado | **Apariencia → NovaShop Demo** |

---

## 11. Criterios de evaluación cubiertos

- Instalación y configuración de servidor Linux (Apache, MySQL, PHP, firewall).
- Despliegue de WordPress y conexión a base de datos.
- Integración WooCommerce (catálogo, carrito, checkout).
- Tema personalizado con identidad visual propia.
- Mínimo 5 templates funcionales.
- Secciones Quiénes Somos y Contacto operativas.
- Documentación de instalación, arquitectura y pruebas.

---

## 12. Conclusiones

El proyecto **NovaShop** demuestra el ciclo completo de puesta en marcha de una tienda online sobre infraestructura propia: desde el aprovisionamiento del servidor LAMP hasta un sitio WordPress/WooCommerce con tema a medida, catálogo con imágenes, flujo de compra y páginas institucionales.

Queda como base escalable para agregar pasarelas de pago reales, SSL (HTTPS), dominios personalizados y más contenido de catálogo.

---

## 13. Anexos

### Anexo A — Comandos útiles

```bash
sudo systemctl status apache2
sudo systemctl restart apache2
sudo firewall-cmd --list-all
ip a
curl -I http://127.0.0.1
ls /var/www/html/wp-content/themes/novashop
```

### Anexo B — Credenciales (completar para la entrega)

> **No versionar contraseñas reales en repositorios públicos.** Completar solo en el informe impreso/PDF privado.

| Recurso | Valor |
|---------|--------|
| IP / URL | `http://168.129.176.214/` |
| Usuario SSH / sistema | `admin` |
| Usuario WP admin | `admin` |
| Base de datos | `wordpress` |
| Usuario MySQL | `wordpress` |
| Alumno | Steve Gerald Brusa |
| Materia | Metodología de Diseño de Software — Tecnicatura en Software |
| Evaluación | Examen final |

### Anexo C — Capturas sugeridas para el informe

1. Apache / página de inicio de NovaShop.  
2. Tienda con productos e imágenes.  
3. Ficha de producto + carrito.  
4. Checkout.  
5. Quiénes Somos.  
6. Contacto (formulario + mensaje de éxito).  
7. Panel de administración WordPress / WooCommerce.

---

**Fin del documento**
