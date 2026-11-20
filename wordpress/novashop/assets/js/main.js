(() => {
  const navToggle = document.querySelector('[data-ns-nav-toggle]');
  const nav = document.querySelector('[data-ns-nav]');
  if (navToggle && nav) {
    navToggle.addEventListener('click', () => {
      const open = nav.classList.toggle('is-open');
      navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  const catsBtn = document.querySelector('[data-ns-cats-toggle]');
  const catsMenu = document.querySelector('[data-ns-cats-menu]');
  if (catsBtn && catsMenu) {
    catsBtn.addEventListener('click', () => {
      const hidden = catsMenu.hasAttribute('hidden');
      if (hidden) catsMenu.removeAttribute('hidden');
      else catsMenu.setAttribute('hidden', '');
    });
    document.addEventListener('click', (e) => {
      if (!catsMenu.hasAttribute('hidden') && !catsBtn.contains(e.target) && !catsMenu.contains(e.target)) {
        catsMenu.setAttribute('hidden', '');
      }
    });
  }
})();
