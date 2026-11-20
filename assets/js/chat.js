const CHAT_SALUDO = 'Hola! Soy el bot de NovaShop. Preguntame por productos, precios, ofertas, envíos o medios de pago.';
// botones de abajo: texto, comando, icono
const CHAT_ATAJOS = [
  ['Ofertas', '/ofertas', '<path d="M4 12.5V5h7.5l8.5 8.5-7.5 7.5L4 12.5Z"/><circle cx="8.5" cy="9.5" r="1.2"/>'],
  ['Categorías', '/categorias', '<rect x="4" y="4" width="7" height="7" rx="1.5"/><rect x="13" y="4" width="7" height="7" rx="1.5"/><rect x="4" y="13" width="7" height="7" rx="1.5"/><rect x="13" y="13" width="7" height="7" rx="1.5"/>'],
  ['Destacados', '/recomendar', '<path d="M12 4l2.4 5 5.6.7-4.1 3.8 1.1 5.5L12 16.3 7 19l1.1-5.5L4 9.7 9.6 9 12 4Z"/>'],
  ['Envíos', '/envios', '<path d="M3 7h11v9H3zM14 10h4l3 3v3h-7z"/><circle cx="7.5" cy="18" r="1.6"/><circle cx="17.5" cy="18" r="1.6"/>'],
  ['Pagos', '/pagos', '<rect x="3" y="6" width="18" height="12" rx="2"/><path d="M3 10h18M7 15h3"/>'],
  ['Garantía', '/garantia', '<path d="M12 3l8 3v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-3Z"/><path d="M9 12l2 2 4-4"/>']
];

let chatMensajes = [];

function chatGuardar() {
  // asi no se pierde el chat al cambiar de pagina
  try {
    sessionStorage.setItem('novashop_chat', JSON.stringify(chatMensajes.slice(-40)));
  } catch (e) { }
}

function chatLeer() {
  try {
    return JSON.parse(sessionStorage.getItem('novashop_chat')) || [];
  } catch (e) {
    return [];
  }
}

function chatEscapar(t) {
  return String(t).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
}

function chatFormato(texto) {
  let html = chatEscapar(texto);
  html = html.replace(/https?:\/\/[^\s]+/g, url => {
    let destino = url;
    let etiqueta = 'Abrir';
    try {
      const u = new URL(url.replace(/&amp;/g, '&'));
      if (u.pathname.includes('producto')) etiqueta = 'Ver producto';
      else if (u.pathname.includes('tienda')) etiqueta = 'Ver en la tienda';
      else if (u.pathname.includes('contacto')) etiqueta = 'Ir a contacto';
      else if (u.pathname.includes('quienes')) etiqueta = 'Quiénes somos';
      if (u.hostname.includes('novashop')) destino = u.pathname.replace(/^\//, '') + u.search;
    } catch (e) { }
    return `<a class="ns-chat__link" href="${destino}">${etiqueta}</a>`;
  });
  html = html.replace(/(^|\s)(\/[a-z]+)/g, (todo, antes, comando) =>
    `${antes}<button type="button" class="ns-chat__comando" data-chat-enviar="${comando}">${comando}</button>`);
  return html;
}

function chatDibujar() {
  const lista = document.getElementById('chat-mensajes');
  lista.innerHTML = chatMensajes.map(m =>
    `<div class="ns-chat__msg ns-chat__msg--${m.de}">${m.de === 'bot' ? chatFormato(m.texto) : chatEscapar(m.texto)}</div>`
  ).join('');
  lista.scrollTop = lista.scrollHeight;
}

async function chatEnviar(texto) {
  texto = texto.trim();
  if (!texto) return;
  chatMensajes.push({ de: 'yo', texto: texto });
  chatDibujar();

  const lista = document.getElementById('chat-mensajes');
  lista.insertAdjacentHTML('beforeend', '<div class="ns-chat__msg ns-chat__msg--bot ns-chat__escribiendo" id="chat-escribiendo"><span></span><span></span><span></span></div>');
  lista.scrollTop = lista.scrollHeight;

  let respuesta;
  try {
    const r = await fetch('/api/chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ mensaje: texto })
    });
    respuesta = (await r.json()).respuesta;
  } catch (e) {
    respuesta = 'No pude conectarme con el bot. Probá de nuevo en un momento.';
  }

  chatMensajes.push({ de: 'bot', texto: respuesta });
  chatGuardar();
  chatDibujar();
}

function chatAbrir(abrir) {
  const panel = document.getElementById('chat-panel');
  panel.classList.toggle('is-open', abrir);
  document.getElementById('chat-boton').setAttribute('aria-expanded', abrir ? 'true' : 'false');
  try { sessionStorage.setItem('novashop_chat_abierto', abrir ? '1' : ''); } catch (e) { }
  if (abrir) {
    chatDibujar();
    if (window.innerWidth > 720) document.getElementById('chat-texto').focus();
  }
}

function armarChat() {
  const telegram = typeof BOT_USUARIO !== 'undefined' && BOT_USUARIO ? 'https://t.me/' + BOT_USUARIO : 'bot.html';

  document.body.insertAdjacentHTML('beforeend', `
<button type="button" class="ns-bot-flotante" id="chat-boton" aria-label="Abrir el chat con el bot" aria-expanded="false">
  <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3h11A2.5 2.5 0 0 1 20 5.5v8a2.5 2.5 0 0 1-2.5 2.5H11l-4.5 4v-4h0A2.5 2.5 0 0 1 4 13.5v-8Z"/><path d="M8.5 8.5h7M8.5 11.5h4.5"/></svg>
  <span>Consultanos</span>
</button>

<section class="ns-chat" id="chat-panel" aria-label="Chat con el bot de NovaShop">
  <header class="ns-chat__cabecera">
    <span class="ns-chat__avatar" aria-hidden="true">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="8" width="14" height="11" rx="3"/><path d="M12 8V4.5M9.5 13v1.5M14.5 13v1.5"/><circle cx="12" cy="3.5" r="1"/></svg>
    </span>
    <div class="ns-chat__titulo">
      <strong>Bot de NovaShop</strong>
      <small>Responde al instante</small>
    </div>
    <a class="ns-chat__telegram" href="${telegram}" target="_blank" rel="noopener" title="Abrir en Telegram" aria-label="Abrir en Telegram">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.9 4.3 18.7 19.4c-.2 1-.9 1.3-1.7.8l-4.8-3.6-2.3 2.2c-.3.3-.5.5-1 .5l.3-4.9 8.9-8c.4-.3-.1-.5-.6-.2L6.5 13.1l-4.7-1.5c-1-.3-1-1 .2-1.5L20.4 3c.9-.3 1.6.2 1.5 1.3Z"/></svg>
    </a>
    <button type="button" class="ns-chat__cerrar" id="chat-cerrar" aria-label="Cerrar el chat">&times;</button>
  </header>
  <div class="ns-chat__mensajes" id="chat-mensajes" aria-live="polite"></div>
  <div class="ns-chat__atajos">
    ${CHAT_ATAJOS.map(a => `<button type="button" data-chat-enviar="${a[1]}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${a[2]}</svg>${a[0]}</button>`).join('')}
  </div>
  <form class="ns-chat__form" id="chat-form">
    <input type="text" id="chat-texto" placeholder="Escribí tu consulta…" autocomplete="off" maxlength="300" aria-label="Tu consulta">
    <button type="submit" aria-label="Enviar">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </button>
  </form>
</section>`);

  chatMensajes = chatLeer();
  if (!chatMensajes.length) chatMensajes = [{ de: 'bot', texto: CHAT_SALUDO }];

  document.getElementById('chat-boton').addEventListener('click', () => {
    chatAbrir(!document.getElementById('chat-panel').classList.contains('is-open'));
  });
  document.getElementById('chat-cerrar').addEventListener('click', () => chatAbrir(false));
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') chatAbrir(false);
  });

  document.getElementById('chat-form').addEventListener('submit', (e) => {
    e.preventDefault();
    const campo = document.getElementById('chat-texto');
    chatEnviar(campo.value);
    campo.value = '';
  });

  document.getElementById('chat-panel').addEventListener('click', (e) => {
    const boton = e.target.closest('[data-chat-enviar]');
    if (boton) chatEnviar(boton.dataset.chatEnviar);
  });

  document.addEventListener('click', (e) => {
    if (e.target.closest('[data-abrir-chat]')) {
      e.preventDefault();
      chatAbrir(true);
    }
  });

  let estabaAbierto = '';
  try { estabaAbierto = sessionStorage.getItem('novashop_chat_abierto'); } catch (e) { }
  if (estabaAbierto) chatAbrir(true);
}

armarChat();
