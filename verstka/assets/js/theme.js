// Переключатель светлой / тёмной темы. Выбор запоминается в браузере посетителя.
(() => {
  const root = document.documentElement;
  const KEY = 'site-theme';
  const meta = document.querySelector('meta[name="theme-color"]');
  const buttons = [...document.querySelectorAll('[data-theme-toggle]')];
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const current = () => root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
  // Баннеры слайдера: у каждого есть светлая и тёмная версия (data-src-light / data-src-dark)
  const themedImages = [...document.querySelectorAll('img[data-src-dark][data-src-light]')];
  const srcFor = (img, theme) => img.getAttribute(theme === 'dark' ? 'data-src-dark' : 'data-src-light');
  function swapImages(theme) {
    themedImages.forEach(img => {
      const src = srcFor(img, theme);
      if (img.getAttribute('src') !== src) img.setAttribute('src', src);
    });
  }
  // Заранее подгружаем вторую версию баннеров, чтобы переключение было мгновенным
  const preload = theme => themedImages.map(img => {
    const pre = new Image(); pre.src = srcFor(img, theme);
    return pre.decode ? pre.decode().catch(() => {}) : Promise.resolve();
  });
  window.addEventListener('load', () => preload(current() === 'dark' ? 'light' : 'dark'));

  function apply(theme) {
    root.setAttribute('data-theme', theme);
    swapImages(theme);
    try { localStorage.setItem(KEY, theme); } catch (e) { /* приватный режим — просто не запоминаем */ }
    if (meta) meta.setAttribute('content', theme === 'dark' ? '#091421' : '#F7F3EC');
    buttons.forEach(b => {
      b.setAttribute('aria-label', theme === 'dark' ? 'Включить светлую тему' : 'Включить тёмную тему');
      b.setAttribute('title', theme === 'dark' ? 'Светлая тема' : 'Тёмная тема');
    });
  }
  apply(current());

  buttons.forEach(button => button.addEventListener('click', () => {
    const next = current() === 'dark' ? 'light' : 'dark';
    // Без поддержки View Transitions или при «уменьшить движение» — мгновенная смена
    if (reduceMotion || !document.startViewTransition) { apply(next); return; }
    const r = button.getBoundingClientRect();
    const x = r.left + r.width / 2, y = r.top + r.height / 2;
    const radius = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));
    const transition = document.startViewTransition(async () => {
      apply(next);
      // ждём, пока новые баннеры будут готовы, чтобы в «круге» сразу была нужная картинка
      await Promise.race([Promise.all(themedImages.map(img => img.decode().catch(() => {}))), new Promise(r => setTimeout(r, 400))]);
    });
    transition.ready.then(() => {
      root.animate(
        { clipPath: [`circle(0px at ${x}px ${y}px)`, `circle(${radius}px at ${x}px ${y}px)`] },
        { duration: 700, easing: 'cubic-bezier(.4, 0, .2, 1)', pseudoElement: '::view-transition-new(root)' }
      );
    }).catch(() => {});
  }));
})();
