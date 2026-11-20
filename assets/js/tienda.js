const ENVIO_GRATIS_DESDE = 50;
const COSTO_ENVIO = 5;

// usuario del bot sin la @
const BOT_USUARIO = 'NovaShopsjTienda_bot';

const PRODUCTOS_POR_PAGINA = 12;

let CATALOGO = { categorias: {}, productos: [] };


function plata(n) {
  return '$' + Number(n).toFixed(2);
}

function precioFinal(p) {
  return p.oferta ? p.oferta : p.precio;
}

function parametro(nombre) {
  return new URLSearchParams(location.search).get(nombre) || '';
}

function sinAcentos(t) {
  return t.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
}

function escapar(t) {
  return String(t).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
}

// carrito

function leerCarrito() {
  try {
    return JSON.parse(localStorage.getItem('novashop_carrito')) || {};
  } catch (e) {
    return {};
  }
}

function guardarCarrito(carrito) {
  localStorage.setItem('novashop_carrito', JSON.stringify(carrito));
  actualizarContador();
}

function agregarAlCarrito(slug, cantidad) {
  const carrito = leerCarrito();
  carrito[slug] = (carrito[slug] || 0) + cantidad;
  guardarCarrito(carrito);
}

function itemsDelCarrito() {
  const carrito = leerCarrito();
  const items = [];
  for (const slug in carrito) {
    const producto = CATALOGO.productos.find(p => p.slug === slug);
    if (producto && carrito[slug] > 0) {
      items.push({ producto: producto, cantidad: carrito[slug] });
    }
  }
  return items;
}

function totales() {
  let subtotal = 0;
  itemsDelCarrito().forEach(i => { subtotal += precioFinal(i.producto) * i.cantidad; });
  const envio = subtotal === 0 || subtotal >= ENVIO_GRATIS_DESDE ? 0 : COSTO_ENVIO;
  return { subtotal: subtotal, envio: envio, total: subtotal + envio };
}

function actualizarContador() {
  const carrito = leerCarrito();
  let cantidad = 0;
  for (const slug in carrito) cantidad += carrito[slug];
  document.querySelectorAll('.ns-cart-count').forEach(el => {
    if (el.textContent !== String(cantidad)) {
      el.classList.remove('is-nuevo');
      void el.offsetWidth;
      el.classList.add('is-nuevo');
    }
    el.textContent = cantidad;
    el.dataset.count = cantidad;
  });
}


function armarCabecera() {
  const cats = Object.keys(CATALOGO.categorias).map(clave => {
    const cantidad = CATALOGO.productos.filter(p => p.categoria === clave).length;
    return `<li><a href="tienda.html?cat=${clave}" data-cat="${clave}">${CATALOGO.categorias[clave]} <span>${cantidad}</span></a></li>`;
  }).join('');

  document.getElementById('cabecera').innerHTML = `
<div class="ns-topbar">
  <div class="ns-container ns-topbar__inner">
    <p class="ns-topbar__promo">Envío gratis en compras +$50 · Entrega 24–48 hs</p>
    <nav class="ns-topbar__links" aria-label="Ayuda">
      <a href="contacto.html">Ayuda</a>
      <a href="bot.html">Bot de Telegram</a>
    </nav>
  </div>
</div>
<header class="ns-header">
  <div class="ns-container ns-header__row">
    <a class="ns-logo" href="index.html">
      <span class="ns-logo__icon" aria-hidden="true">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M6 7h12l-1.2 9.6a2 2 0 0 1-2 1.7H9.2a2 2 0 0 1-2-1.7L6 7Z" stroke="currentColor" stroke-width="1.8"/><path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7" stroke="currentColor" stroke-width="1.8"/><circle cx="9.5" cy="20" r="1.2" fill="currentColor"/><circle cx="15.5" cy="20" r="1.2" fill="currentColor"/></svg>
      </span>
      <span class="ns-logo__text">NovaShop</span>
    </a>
    <form class="ns-search" role="search" method="get" action="tienda.html">
      <input type="search" name="s" placeholder="Buscar productos…" value="${escapar(parametro('s'))}" aria-label="Buscar">
      <button type="submit" aria-label="Buscar">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
    </form>
    <div class="ns-header__actions">
      <a class="ns-icon-btn ns-icon-btn--cart" href="carrito.html" aria-label="Carrito">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 6h2.2l1.3 9.2a1.5 1.5 0 0 0 1.5 1.3h7.6a1.5 1.5 0 0 0 1.5-1.2L19.5 8H7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="10" cy="20" r="1.2" fill="currentColor"/><circle cx="16.5" cy="20" r="1.2" fill="currentColor"/></svg>
        <span class="ns-cart-count" data-count="0">0</span>
      </a>
      <button class="ns-nav-toggle" type="button" aria-label="Menú" aria-expanded="false" id="boton-menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
  <div class="ns-nav-bar">
    <div class="ns-container ns-nav-bar__inner">
      <div class="ns-cats">
        <button class="ns-cats__btn" type="button" id="boton-categorias">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          Categorías
        </button>
        <div class="ns-cats__menu" hidden id="menu-categorias"><ul>${cats}</ul></div>
      </div>
      <nav class="ns-nav" id="menu-principal" aria-label="Principal">
        <ul class="ns-nav__list">
          <li><a href="index.html">Inicio</a></li>
          <li><a href="tienda.html">Tienda</a></li>
          <li><a href="tienda.html?ofertas=1">Ofertas</a></li>
          <li><a href="quienes-somos.html">Quiénes Somos</a></li>
          <li><a href="contacto.html">Contacto</a></li>
        </ul>
      </nav>
    </div>
  </div>
</header>`;

  const actual = location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.ns-nav__list a').forEach(a => {
    const esOfertas = a.getAttribute('href').includes('ofertas=1');
    const enOfertas = parametro('ofertas') === '1';
    if (a.getAttribute('href').split('?')[0] === actual && esOfertas === enOfertas) a.classList.add('is-actual');
  });

  const cabecera = document.querySelector('.ns-header');
  window.addEventListener('scroll', () => {
    cabecera.classList.toggle('is-scrolled', window.scrollY > 10);
  });

  const botonMenu = document.getElementById('boton-menu');
  const menu = document.getElementById('menu-principal');
  botonMenu.addEventListener('click', () => {
    const abierto = menu.classList.toggle('is-open');
    botonMenu.setAttribute('aria-expanded', abierto ? 'true' : 'false');
  });

  const botonCats = document.getElementById('boton-categorias');
  const menuCats = document.getElementById('menu-categorias');
  botonCats.addEventListener('click', () => { menuCats.hidden = !menuCats.hidden; });
  document.addEventListener('click', (e) => {
    if (!menuCats.hidden && !botonCats.contains(e.target) && !menuCats.contains(e.target)) {
      menuCats.hidden = true;
    }
  });
}

function armarPie() {
  document.getElementById('pie').innerHTML = `
<footer class="ns-footer">
  <div class="ns-container ns-footer__grid">
    <div>
      <div class="ns-logo ns-logo--footer">
        <span class="ns-logo__icon" aria-hidden="true">🛍</span>
        <span class="ns-logo__text">NovaShop</span>
      </div>
      <p class="ns-footer__tag">Tecnología que conecta tu mundo. Compra segura, envíos rápidos y soporte personalizado.</p>
    </div>
    <div>
      <h4>Tienda</h4>
      <ul>
        <li><a href="tienda.html">Catálogo</a></li>
        <li><a href="carrito.html">Carrito</a></li>
        <li><a href="checkout.html">Checkout</a></li>
      </ul>
    </div>
    <div>
      <h4>Ayuda</h4>
      <ul>
        <li><a href="quienes-somos.html">Quiénes Somos</a></li>
        <li><a href="contacto.html">Contacto</a></li>
        <li><a href="bot.html">Bot de Telegram</a></li>
      </ul>
    </div>
    <div>
      <h4>Beneficios</h4>
      <ul class="ns-footer__perks">
        <li>Diseño 100% responsive</li>
        <li>Filtros y ordenamiento</li>
        <li>Compra segura</li>
        <li>Envíos a todo el país</li>
      </ul>
    </div>
  </div>
  <div class="ns-footer__bottom">
    <div class="ns-container">
      <p>&copy; ${new Date().getFullYear()} NovaShop. Tienda online de tecnología.</p>
    </div>
  </div>
</footer>`;

  if (BOT_USUARIO) {
    document.querySelectorAll('[data-bot]').forEach(a => {
      a.href = 'https://t.me/' + BOT_USUARIO;
      a.target = '_blank';
      a.rel = 'noopener';
    });
    document.querySelectorAll('[data-bot-usuario]').forEach(e => { e.textContent = '@' + BOT_USUARIO; });
  }
}


function htmlPrecio(p) {
  if (p.oferta) {
    return `<del>${plata(p.precio)}</del> <ins>${plata(p.oferta)}</ins>`;
  }
  return plata(p.precio);
}

function htmlDescuento(p) {
  if (!p.oferta) return '';
  const porcentaje = Math.round((p.precio - p.oferta) / p.precio * 100);
  return `<span class="ns-badge">-${porcentaje}%</span>`;
}

function tarjetas(lista) {
  return lista.map((p, i) => tarjeta(p, i)).join('');
}

function tarjeta(p, i = 0) {
  return `
<article class="ns-card" style="--i:${i}">
  <a class="ns-card__media" href="producto.html?p=${p.slug}">
    <img class="ns-card__img" src="assets/img/${p.imagen}" alt="${escapar(p.nombre)}" loading="lazy">
    ${htmlDescuento(p)}
  </a>
  <div class="ns-card__body">
    <h3 class="ns-card__title"><a href="producto.html?p=${p.slug}">${escapar(p.nombre)}</a></h3>
    <div class="ns-card__price">${htmlPrecio(p)}</div>
    <div class="ns-card__actions">
      <div class="ns-cant">
        <button type="button" data-menos aria-label="Menos">−</button>
        <input type="number" value="1" min="1" max="20" aria-label="Cantidad">
        <button type="button" data-mas aria-label="Más">+</button>
      </div>
      <button type="button" class="button" data-agregar="${p.slug}">Agregar</button>
    </div>
  </div>
</article>`;
}

document.addEventListener('click', (e) => {
  const paso = e.target.closest('[data-mas], [data-menos]');
  if (paso) {
    const campo = paso.parentElement.querySelector('input');
    const valor = (parseInt(campo.value) || 1) + (paso.hasAttribute('data-mas') ? 1 : -1);
    campo.value = Math.min(20, Math.max(1, valor));
    return;
  }

  const boton = e.target.closest('[data-agregar]');
  if (!boton) return;
  const campo = boton.parentElement.querySelector('.ns-cant input');
  const cantidad = Math.min(20, Math.max(1, parseInt(campo.value) || 1));
  agregarAlCarrito(boton.dataset.agregar, cantidad);
  campo.value = 1;
  boton.textContent = cantidad > 1 ? 'Agregados ✓' : 'Agregado ✓';
  boton.classList.add('is-ok');
  setTimeout(() => {
    boton.textContent = 'Agregar';
    boton.classList.remove('is-ok');
  }, 1200);
});


const ICONOS_CATEGORIA = {
  tecnologia: '<path d="M4 6.5A1.5 1.5 0 0 1 5.5 5h13A1.5 1.5 0 0 1 20 6.5V15H4V6.5Z"/><path d="M2.5 18.5h19"/>',
  audio: '<path d="M4 15v-3a8 8 0 0 1 16 0v3"/><rect x="3" y="14" width="4" height="6" rx="1.5"/><rect x="17" y="14" width="4" height="6" rx="1.5"/>',
  smartphones: '<rect x="7" y="2.5" width="10" height="19" rx="2.5"/><path d="M11 18.5h2"/>',
  wearables: '<rect x="7" y="7" width="10" height="10" rx="3"/><path d="M9 7l.7-4h4.6l.7 4M9 17l.7 4h4.6l.7-4M12 10.5v2l1.2 1"/>',
  accesorios: '<rect x="7" y="3" width="10" height="18" rx="5"/><path d="M12 7v3"/>'
};

function paginaInicio() {
  let htmlCats = '';
  for (const clave in CATALOGO.categorias) {
    const cantidad = CATALOGO.productos.filter(p => p.categoria === clave).length;
    htmlCats += `
<a class="ns-categoria" href="tienda.html?cat=${clave}">
  <span class="ns-categoria__icono"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${ICONOS_CATEGORIA[clave] || ''}</svg></span>
  <strong>${CATALOGO.categorias[clave]}</strong>
  <span>${cantidad} productos</span>
</a>`;
  }
  document.getElementById('categorias-inicio').innerHTML = htmlCats;

  const destacados = CATALOGO.productos.filter(p => p.destacado).slice(0, 4);
  document.getElementById('destacados').innerHTML = tarjetas(destacados);

  const ofertas = CATALOGO.productos
    .filter(p => p.oferta && !destacados.includes(p))
    .sort((a, b) => (b.precio - b.oferta) / b.precio - (a.precio - a.oferta) / a.precio)
    .slice(0, 4);
  document.getElementById('ofertas-inicio').innerHTML = tarjetas(ofertas);
}


function cambiarFiltros(cambios) {
  const filtros = new URLSearchParams(location.search);
  // vuelve a la pagina 1
  if (!('pag' in cambios)) filtros.delete('pag');
  for (const nombre in cambios) {
    if (cambios[nombre]) filtros.set(nombre, cambios[nombre]);
    else filtros.delete(nombre);
  }
  const consulta = filtros.toString();
  history.pushState(null, '', 'tienda.html' + (consulta ? '?' + consulta : ''));
  dibujarTienda();
}

function paginaTienda() {
  // filtros en el celu
  document.getElementById('boton-filtros').addEventListener('click', () => {
    document.getElementById('filtros').classList.toggle('is-open');
  });

  document.addEventListener('click', (e) => {
    const link = e.target.closest('a[data-cat]');
    if (!link) return;
    e.preventDefault();
    cambiarFiltros({ cat: link.dataset.cat, s: '' });
    document.getElementById('menu-categorias').hidden = true;
  });

  document.querySelector('.ns-search').addEventListener('submit', (e) => {
    e.preventDefault();
    cambiarFiltros({ s: e.target.s.value.trim(), cat: '' });
  });

  document.getElementById('form-precio').addEventListener('submit', (e) => {
    e.preventDefault();
    cambiarFiltros({ min: e.target.min.value, max: e.target.max.value });
  });

  document.querySelectorAll('input[name="orden"]').forEach(radio => {
    radio.addEventListener('change', () => cambiarFiltros({ orden: radio.value === 'destacados' ? '' : radio.value }));
  });
  document.getElementById('solo-ofertas').addEventListener('change', (e) => {
    cambiarFiltros({ ofertas: e.target.checked ? '1' : '' });
  });

  document.getElementById('limpiar-filtros').addEventListener('click', (e) => {
    e.preventDefault();
    history.pushState(null, '', 'tienda.html');
    dibujarTienda();
  });

  document.getElementById('paginas-tienda').addEventListener('click', (e) => {
    const link = e.target.closest('a[data-pag]');
    if (!link) return;
    e.preventDefault();
    cambiarFiltros({ pag: link.dataset.pag === '1' ? '' : link.dataset.pag });
    document.getElementById('titulo-tienda').scrollIntoView();
  });

  // para que ande el boton atras
  window.addEventListener('popstate', dibujarTienda);

  dibujarTienda();
}

function dibujarTienda() {
  const cat = CATALOGO.categorias[parametro('cat')] ? parametro('cat') : '';
  const busqueda = parametro('s');
  const soloOfertas = parametro('ofertas') === '1';
  const min = parseFloat(parametro('min')) || 0;
  const max = parseFloat(parametro('max')) || 0;
  const orden = parametro('orden') || 'destacados';

  let htmlCats = `<li class="${cat ? '' : 'is-active'}"><a href="tienda.html" data-cat="">Todos <span>${CATALOGO.productos.length}</span></a></li>`;
  for (const clave in CATALOGO.categorias) {
    const cantidad = CATALOGO.productos.filter(p => p.categoria === clave).length;
    htmlCats += `<li class="${cat === clave ? 'is-active' : ''}"><a href="tienda.html?cat=${clave}" data-cat="${clave}">${CATALOGO.categorias[clave]} <span>${cantidad}</span></a></li>`;
  }
  document.getElementById('filtro-categorias').innerHTML = htmlCats;

  document.querySelector('input[name="min"]').value = parametro('min');
  document.querySelector('input[name="max"]').value = parametro('max');
  const radio = document.querySelector(`input[name="orden"][value="${orden}"]`);
  if (radio) radio.checked = true;
  document.getElementById('solo-ofertas').checked = soloOfertas;
  document.querySelector('.ns-search input').value = busqueda;
  document.getElementById('limpiar-filtros').hidden = !location.search;

  let lista = CATALOGO.productos.slice();
  if (cat) lista = lista.filter(p => p.categoria === cat);
  if (soloOfertas) lista = lista.filter(p => p.oferta);
  if (busqueda) {
    const palabras = sinAcentos(busqueda).split(' ').filter(x => x);
    lista = lista.filter(p => {
      const texto = sinAcentos(p.nombre + ' ' + p.descripcion + ' ' + CATALOGO.categorias[p.categoria]);
      return palabras.every(x => texto.includes(x));
    });
  }
  if (min) lista = lista.filter(p => precioFinal(p) >= min);
  if (max) lista = lista.filter(p => precioFinal(p) <= max);

  if (orden === 'menor') lista.sort((a, b) => precioFinal(a) - precioFinal(b));
  if (orden === 'mayor') lista.sort((a, b) => precioFinal(b) - precioFinal(a));
  if (orden === 'nombre') lista.sort((a, b) => a.nombre.localeCompare(b.nombre));
  if (orden === 'destacados') lista.sort((a, b) => (b.destacado ? 1 : 0) - (a.destacado ? 1 : 0));

  let titulo = cat ? CATALOGO.categorias[cat] : 'Tienda';
  if (soloOfertas) titulo = cat ? 'Ofertas en ' + CATALOGO.categorias[cat] : 'Ofertas';
  if (busqueda) titulo = 'Resultados para "' + busqueda + '"';
  document.getElementById('titulo-tienda').textContent = titulo;
  document.getElementById('miga-tienda').textContent = cat ? CATALOGO.categorias[cat] : 'Tienda';
  document.title = titulo + ' - NovaShop';

  const totalPaginas = Math.max(1, Math.ceil(lista.length / PRODUCTOS_POR_PAGINA));
  const pagina = Math.min(totalPaginas, Math.max(1, parseInt(parametro('pag')) || 1));
  const desde = (pagina - 1) * PRODUCTOS_POR_PAGINA;
  const visibles = lista.slice(desde, desde + PRODUCTOS_POR_PAGINA);

  document.getElementById('cantidad-tienda').textContent =
    lista.length ? `Mostrando ${desde + 1}–${desde + visibles.length} de ${lista.length} productos` : '';

  document.getElementById('grilla-tienda').innerHTML = lista.length
    ? tarjetas(visibles)
    : '<p class="ns-notice">No se encontraron productos con esos filtros.</p>';

  let htmlPaginas = '';
  if (totalPaginas > 1) {
    if (pagina > 1) htmlPaginas += `<a class="page-numbers" href="#" data-pag="${pagina - 1}" aria-label="Anterior">←</a>`;
    for (let n = 1; n <= totalPaginas; n++) {
      htmlPaginas += n === pagina
        ? `<span class="page-numbers current">${n}</span>`
        : `<a class="page-numbers" href="#" data-pag="${n}">${n}</a>`;
    }
    if (pagina < totalPaginas) htmlPaginas += `<a class="page-numbers" href="#" data-pag="${pagina + 1}" aria-label="Siguiente">→</a>`;
  }
  document.getElementById('paginas-tienda').innerHTML = htmlPaginas;
}


function paginaProducto() {
  const p = CATALOGO.productos.find(x => x.slug === parametro('p'));
  const caja = document.getElementById('producto');
  if (!p) {
    caja.innerHTML = '<p class="ns-notice">El producto no existe. <a class="ns-link" href="tienda.html">Volver a la tienda</a></p>';
    return;
  }
  document.title = p.nombre + ' - NovaShop';

  const relacionados = CATALOGO.productos.filter(x => x.categoria === p.categoria && x.slug !== p.slug).slice(0, 4);

  caja.innerHTML = `
<nav class="ns-breadcrumbs" aria-label="Breadcrumb">
  <a href="index.html">Inicio</a><span>/</span>
  <a href="tienda.html">Tienda</a><span>/</span>
  <a href="tienda.html?cat=${p.categoria}">${CATALOGO.categorias[p.categoria]}</a><span>/</span>
  <span>${escapar(p.nombre)}</span>
</nav>
<div class="ns-product__layout">
  <div class="ns-product__gallery">
    ${htmlDescuento(p)}
    <img src="assets/img/${p.imagen}" alt="${escapar(p.nombre)}">
  </div>
  <div class="ns-product__summary">
    <h1>${escapar(p.nombre)}</h1>
    <div class="ns-product__price">${htmlPrecio(p)}</div>
    <div class="ns-product__excerpt">${escapar(p.descripcion)}</div>
    <div class="ns-product__cart">
      <form id="form-agregar">
        <div class="quantity"><input type="number" id="cantidad" value="1" min="1" max="20" aria-label="Cantidad"></div>
        <button type="submit" class="single_add_to_cart_button">Agregar al carrito</button>
      </form>
      <p class="ns-aviso-carrito" id="aviso-carrito" hidden>Producto agregado. <a class="ns-link" href="carrito.html">Ver carrito</a></p>
    </div>
    <ul class="ns-product__meta-list">
      <li>Envío rápido a todo el país</li>
      <li>Pago seguro y protegido</li>
      <li>Garantía oficial</li>
    </ul>
  </div>
</div>
<section class="ns-product__tabs ns-section">
  <h2>Descripción</h2>
  <div class="ns-page__body"><p>${escapar(p.descripcion)}</p></div>
</section>
${relacionados.length ? `
<section class="ns-section">
  <div class="ns-section__head"><h2>También te puede interesar</h2></div>
  <div class="ns-grid ns-grid--4">${tarjetas(relacionados)}</div>
</section>` : ''}`;

  document.getElementById('form-agregar').addEventListener('submit', (e) => {
    e.preventDefault();
    const cantidad = parseInt(document.getElementById('cantidad').value) || 1;
    agregarAlCarrito(p.slug, cantidad);
    document.getElementById('aviso-carrito').hidden = false;
  });
}


function paginaCarrito() {
  const items = itemsDelCarrito();
  const caja = document.getElementById('carrito');

  if (!items.length) {
    caja.innerHTML = '<p class="ns-notice">Tu carrito está vacío.</p><p><a class="ns-btn ns-btn--navy" href="tienda.html">Ir a la tienda</a></p>';
    return;
  }

  const filas = items.map(i => `
<tr>
  <td class="product-remove"><button type="button" class="ns-quitar" data-quitar="${i.producto.slug}" aria-label="Quitar">&times;</button></td>
  <td class="product-thumbnail"><a href="producto.html?p=${i.producto.slug}"><img src="assets/img/${i.producto.imagen}" alt=""></a></td>
  <td class="product-name" data-title="Producto"><a href="producto.html?p=${i.producto.slug}">${escapar(i.producto.nombre)}</a></td>
  <td class="product-price" data-title="Precio">${plata(precioFinal(i.producto))}</td>
  <td class="product-quantity" data-title="Cantidad"><input type="number" class="ns-cantidad" data-cantidad="${i.producto.slug}" value="${i.cantidad}" min="1" max="20"></td>
  <td class="product-subtotal" data-title="Subtotal">${plata(precioFinal(i.producto) * i.cantidad)}</td>
</tr>`).join('');

  const t = totales();
  caja.innerHTML = `
<table class="ns-cart-table">
  <thead>
    <tr><th></th><th></th><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr>
  </thead>
  <tbody>${filas}</tbody>
</table>
<div class="ns-cart-collaterals">
  <div class="cart_totals">
    <h2>Total del carrito</h2>
    <p class="ns-total-fila"><span>Subtotal</span><span>${plata(t.subtotal)}</span></p>
    <p class="ns-total-fila"><span>Envío</span><span>${t.envio ? plata(t.envio) : 'Gratis'}</span></p>
    <p class="ns-total-fila ns-total-fila--final"><span>Total</span><span>${plata(t.total)}</span></p>
  </div>
  <a class="ns-btn ns-btn--accent" href="checkout.html">Finalizar compra →</a>
  <a class="ns-link" href="tienda.html">Seguir comprando</a>
</div>`;

  caja.querySelectorAll('[data-quitar]').forEach(boton => {
    boton.addEventListener('click', () => {
      const carrito = leerCarrito();
      delete carrito[boton.dataset.quitar];
      guardarCarrito(carrito);
      paginaCarrito();
    });
  });
  caja.querySelectorAll('[data-cantidad]').forEach(campo => {
    campo.addEventListener('change', () => {
      const carrito = leerCarrito();
      carrito[campo.dataset.cantidad] = Math.max(1, parseInt(campo.value) || 1);
      guardarCarrito(carrito);
      paginaCarrito();
    });
  });
}


function paginaCheckout() {
  const items = itemsDelCarrito();
  const caja = document.getElementById('checkout');
  if (!items.length) {
    caja.innerHTML = '<p class="ns-notice">No hay productos en el carrito.</p><p><a class="ns-btn ns-btn--navy" href="tienda.html">Ir a la tienda</a></p>';
    return;
  }

  const t = totales();
  document.getElementById('resumen').innerHTML =
    items.map(i => `<p class="ns-total-fila"><span>${i.cantidad} x ${escapar(i.producto.nombre)}</span><span>${plata(precioFinal(i.producto) * i.cantidad)}</span></p>`).join('') +
    `<p class="ns-total-fila"><span>Envío</span><span>${t.envio ? plata(t.envio) : 'Gratis'}</span></p>
     <p class="ns-total-fila ns-total-fila--final"><span>Total</span><span>${plata(t.total)}</span></p>`;

  document.getElementById('form-checkout').addEventListener('submit', async (e) => {
    e.preventDefault();
    const f = e.target;
    const boton = f.querySelector('button[type="submit"]');
    boton.disabled = true;
    boton.textContent = 'Enviando...';

    const numero = 'NS-' + Date.now().toString().slice(-6);
    const pedido = {
      tipo: 'pedido',
      numero: numero,
      nombre: f.nombre.value + ' ' + f.apellido.value,
      email: f.email.value,
      telefono: f.telefono.value,
      direccion: f.direccion.value + ', ' + f.ciudad.value + ', ' + f.provincia.value + ' (CP ' + f.cp.value + ')',
      pago: f.pago.value,
      items: items.map(i => ({ nombre: i.producto.nombre, cantidad: i.cantidad, precio: precioFinal(i.producto).toFixed(2) })),
      envio: t.envio.toFixed(2),
      total: t.total.toFixed(2)
    };

    // le avisa al bot
    try {
      await fetch('/api/aviso', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(pedido) });
    } catch (error) {
      console.log('no se pudo avisar el pedido', error);
    }

    guardarCarrito({});
    caja.innerHTML = `
<div class="ns-alert ns-alert--success">¡Gracias por tu compra, ${escapar(f.nombre.value)}! Tu pedido <strong>${numero}</strong> fue registrado por un total de ${plata(t.total)}.</div>
<p>Te vamos a escribir a ${escapar(f.email.value)} para coordinar el pago y el envío.</p>
<p><a class="ns-btn ns-btn--navy" href="tienda.html">Seguir comprando</a></p>`;
    window.scrollTo(0, 0);
  });
}


function paginaContacto() {
  document.getElementById('form-contacto').addEventListener('submit', async (e) => {
    e.preventDefault();
    const f = e.target;
    const aviso = document.getElementById('aviso-contacto');
    const consulta = {
      tipo: 'contacto',
      nombre: f.nombre.value,
      email: f.email.value,
      telefono: f.telefono.value,
      asunto: f.asunto.value || 'Consulta NovaShop',
      mensaje: f.mensaje.value
    };
    try {
      await fetch('/api/aviso', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(consulta) });
    } catch (error) {
      console.log('no se pudo enviar la consulta', error);
    }
    aviso.className = 'ns-alert ns-alert--success';
    aviso.textContent = '¡Gracias! Recibimos tu consulta. Te responderemos pronto.';
    aviso.hidden = false;
    f.reset();
  });
}


async function iniciar() {
  const respuesta = await fetch('data/productos.json');
  CATALOGO = await respuesta.json();

  armarCabecera();
  armarPie();
  actualizarContador();

  const pagina = document.body.dataset.pagina;
  if (pagina === 'inicio') paginaInicio();
  if (pagina === 'tienda') paginaTienda();
  if (pagina === 'producto') paginaProducto();
  if (pagina === 'carrito') paginaCarrito();
  if (pagina === 'checkout') paginaCheckout();
  if (pagina === 'contacto') paginaContacto();
}

iniciar();
