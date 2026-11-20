# Guía rápida — NovaShop en tu WordPress

## Qué incluye el tema

5 plantillas funcionales:

1. **Inicio** (`front-page.php`) — hero + productos destacados  
2. **Tienda / Catálogo** (`woocommerce/archive-product.php`) — filtros, categorías, orden  
3. **Producto** (`woocommerce/single-product.php`) — ficha + agregar al carrito  
4. **Carrito** (`woocommerce/cart/cart.php`)  
5. **Checkout** (`woocommerce/checkout/form-checkout.php`)

---

## Paso 1 — Subir el tema (Bitvise SFTP)

1. En Bitvise abrí **New SFTP window**
2. Subí la carpeta `novashop` completa a:
   ```
   /var/www/html/wp-content/themes/novashop
   ```
3. En la terminal SSH:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/wp-content/themes/novashop
   ```

---

## Paso 2 — Instalar WooCommerce

En WordPress admin (`http://TU_IP/wp-admin`):

1. **Plugins → Añadir nuevo** → buscá **WooCommerce** → Instalar → Activar  
2. Completá el asistente (país, moneda, etc.)  
3. Dejá que cree las páginas: Tienda, Carrito, Checkout, Mi cuenta  

---

## Paso 3 — Activar el tema NovaShop

1. **Apariencia → Temas** → activá **NovaShop**  
2. **Ajustes → Lectura** → “Una página estática” → Inicio = página **Inicio** (o creá una llamada Inicio)  

---

## Paso 4 — Generar catálogo + imágenes + páginas

1. **Apariencia → NovaShop Demo**  
2. Clic en **Generar / actualizar catálogo + imágenes + páginas**  
3. Se crean productos con fotos, menú, **Contacto** y **Quiénes Somos**

Luego visitá:
- `http://TU_IP/`
- `http://TU_IP/tienda/`
- `http://TU_IP/quienes-somos/`
- `http://TU_IP/contacto/`

Los mensajes del formulario aparecen en **Mensajes de contacto** del menú de WordPress.
