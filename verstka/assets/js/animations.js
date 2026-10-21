// Анимации появления, шапка, прогресс прокрутки и плавный переход к якорям.
// Без библиотек. Работает поверх main.js и не меняет его логику.
(() => {
  const root = document.documentElement;
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- 1. Появление элементов при прокрутке ---------- */
  // [селектор, вариант]. Порядок внутри одной партии задаёт каскад задержек.
  const groups = [
    ['.eyebrow', 'soft'],
    ['.section-title, .layouts-heading h2', ''],
    ['.heading .intro, .payment-heading > p, .layouts-heading p:not(.eyebrow), .social-profile', 'soft'],
    ['#about .video, .contact-map', 'media'],
    ['.card, .infra-card, .payment-card, .social-card, .plan-card, .video-card, .spec-card, .advantage-item', ''],
    ['.plan-tabs, .selection-form, .payment-action, .company-facts', ''],
    ['.company-facts article', 'soft'],
    ['.company-story > *, .contact-info > *', 'soft'],
    ['.footer-bottom > *', 'soft'],
  ];

  if (root.classList.contains('anim') && 'IntersectionObserver' in window) {
    const targets = new Set();
    groups.forEach(([selector, variant]) => {
      document.querySelectorAll(selector).forEach(el => {
        if (el.closest('.hero, dialog') || el.hasAttribute('data-reveal')) return;
        el.setAttribute('data-reveal', variant);
        targets.add(el);
      });
    });

    const STEP = 90;      // мс между соседними элементами
    const MAX_DELAY = 540;
    const revealObserver = new IntersectionObserver(entries => {
      // Всё, что вошло в экран одновременно, появляется каскадом в порядке документа.
      const batch = entries.filter(e => e.isIntersecting).map(e => e.target)
        .sort((a, b) => (a.compareDocumentPosition(b) & Node.DOCUMENT_POSITION_FOLLOWING) ? -1 : 1);
      batch.forEach((el, i) => {
        el.style.setProperty('--reveal-delay', Math.min(i * STEP, MAX_DELAY) + 'ms');
        el.classList.add('is-visible');
        revealObserver.unobserve(el);
        // Убираем задержку, чтобы ховер-эффекты реагировали сразу.
        setTimeout(() => el.style.removeProperty('--reveal-delay'), 1600 + i * STEP);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
    targets.forEach(el => revealObserver.observe(el));

    // У самого низа страницы последние элементы не доходят до зоны срабатывания — показываем их.
    const revealRest = () => {
      if (window.innerHeight + window.scrollY < document.documentElement.scrollHeight - 4) return;
      let n = 0;
      document.querySelectorAll('[data-reveal]:not(.is-visible)').forEach(el => {
        if (el.getBoundingClientRect().top > window.innerHeight) return;
        el.style.setProperty('--reveal-delay', Math.min(n++ * STEP, MAX_DELAY) + 'ms');
        el.classList.add('is-visible');
        revealObserver.unobserve(el);
      });
    };
    window.addEventListener('scroll', revealRest, { passive: true });

    // Прорисовка разделительных линий
    const lineObserver = new IntersectionObserver(entries => entries.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add('is-lined'); lineObserver.unobserve(e.target); }
    }), { threshold: 0.1 });
    document.querySelectorAll('.infrastructure, .footer-bottom').forEach(el => lineObserver.observe(el));

    // Каскад при переключении вкладок планировок
    document.querySelectorAll('[data-plan-tab]').forEach(tab => tab.addEventListener('click', () => {
      const panel = document.getElementById('plans-' + tab.dataset.planTab);
      if (!panel) return;
      const items = panel.classList.contains('plans-grid') ? [...panel.children] : [panel];
      items.forEach((item, i) => {
        item.classList.remove('tab-in');
        item.classList.add('is-visible');
        void item.offsetWidth; // перезапуск анимации
        item.style.setProperty('--tab-delay', i * 70 + 'ms');
        item.classList.add('tab-in');
      });
    }));
  }
  root.classList.add('anim-ready');

  /* ---------- 2. Шапка: фиксация, стекло, скрытие при прокрутке вниз ---------- */
  const header = document.querySelector('.hero .site-header');
  // Высоту для отступа первого экрана берём только у развёрнутой шапки (вверху страницы),
  // иначе при сворачивании шапки контент под ней «прыгал» бы.
  const setHeaderHeight = () => {
    if (header.classList.contains('is-scrolled')) return;
    root.style.setProperty('--header-h', header.offsetHeight + 'px');
  };
  if (header) {
    setHeaderHeight();
    root.classList.add('header-fixed');
    if ('ResizeObserver' in window) new ResizeObserver(setHeaderHeight).observe(header);
  }

  /* ---------- 3. Полоса прогресса ---------- */
  const progress = document.createElement('div');
  progress.className = 'scroll-progress';
  progress.setAttribute('aria-hidden', 'true');
  document.body.prepend(progress);

  let lastY = window.scrollY;
  let ticking = false;
  const onScroll = () => {
    const y = window.scrollY;
    const max = document.documentElement.scrollHeight - window.innerHeight;
    progress.style.transform = `scaleX(${max > 0 ? Math.min(y / max, 1) : 0})`;

    // Шапка всегда на месте; после прокрутки — «стекло», на телефоне сворачивается меню.
    if (header) header.classList.toggle('is-scrolled', y > 8);
    lastY = y;
    ticking = false;
  };
  window.addEventListener('scroll', () => {
    if (!ticking) { ticking = true; requestAnimationFrame(onScroll); }
  }, { passive: true });
  window.addEventListener('resize', onScroll);
  onScroll();

  /* ---------- 4. Плавный переход к якорям с учётом шапки ---------- */
  document.addEventListener('click', event => {
    const link = event.target.closest('a[href^="#"]');
    if (!link || event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey) return;
    const hash = link.getAttribute('href');
    const target = hash === '#' || hash === '#home' ? document.body : document.querySelector(hash);
    if (!target) return;
    event.preventDefault();

    const rect = target.getBoundingClientRect();
    // Отступ = высота шапки в свёрнутом виде (такой она будет после прокрутки).
    let offset = 0;
    if (header) {
      const wasScrolled = header.classList.contains('is-scrolled');
      header.classList.add('is-measuring', 'is-scrolled');
      offset = header.offsetHeight + 16;
      header.classList.toggle('is-scrolled', wasScrolled);
      void header.offsetHeight;
      header.classList.remove('is-measuring');
    }
    const top = target === document.body ? 0 : Math.max(0, window.scrollY + rect.top - offset);
    window.scrollTo({ top, behavior: reduceMotion ? 'auto' : 'smooth' });

    if (hash.length > 1) history.replaceState(null, '', hash);
    if (target.matches('form')) {
      const input = target.querySelector('input');
      if (input) setTimeout(() => input.focus({ preventScroll: true }), 700);
    }
  });

  /* ---------- 5. Подсветка активного пункта меню ---------- */
  const navLinks = [...document.querySelectorAll('.navigation a[href^="#"]')];
  const sections = navLinks.map(a => document.querySelector(a.getAttribute('href'))).filter(Boolean);
  if ('IntersectionObserver' in window && sections.length) {
    const navObserver = new IntersectionObserver(entries => entries.forEach(e => {
      if (!e.isIntersecting) return;
      navLinks.forEach(a => a.classList.toggle('is-active', a.getAttribute('href') === '#' + e.target.id));
    }), { rootMargin: '-45% 0px -50% 0px' });
    sections.forEach(s => navObserver.observe(s));
  }
})();

/* ---------- SVG-иконки и логотип ---------- */
(() => {
  const root = document.documentElement;
  if (!root.classList.contains('anim') || !('IntersectionObserver' in window)) return;
  const SVG_NS = 'http://www.w3.org/2000/svg';

  // 1. Иконки в карточках: каждая линия контура рисуется по очереди.
  const icons = document.querySelectorAll('.card .icon svg, .infra-card .icon svg, .spec-card > svg, .advantage-item > svg');
  icons.forEach(svg => {
    // Разбиваем составной контур (несколько «M…») на отдельные линии.
    [...svg.querySelectorAll('path')].forEach(path => {
      const parts = path.getAttribute('d').trim().split(/(?=M)/).map(p => p.trim()).filter(Boolean);
      if (parts.length < 2) return;
      parts.forEach(d => {
        const piece = document.createElementNS(SVG_NS, 'path');
        piece.setAttribute('d', d);
        path.before(piece);
      });
      path.remove();
    });
    let delay = 0;
    [...svg.querySelectorAll('path')].forEach((path, i) => {
      const len = Math.ceil(path.getTotalLength()) + 1;
      path.style.setProperty('--len', len);
      path.style.setProperty('--pd', delay + 'ms');
      path.style.setProperty('--ph', i * 60 + 'ms');
      delay += 160;
    });
    svg.classList.add('icon-draw');
  });

  const iconObserver = new IntersectionObserver(entries => {
    entries.filter(e => e.isIntersecting).forEach((e, i) => {
      // Небольшая пауза, чтобы иконка рисовалась, когда карточка уже проявилась.
      setTimeout(() => e.target.classList.add('is-drawn'), 250 + i * 120);
      iconObserver.unobserve(e.target);
    });
  }, { rootMargin: '0px 0px -10% 0px', threshold: 0.4 });
  icons.forEach(svg => iconObserver.observe(svg));

  // 2. Логотип: сборка при загрузке (шапка), при появлении (подвал) и при наведении.
  const playLogo = logo => {
    logo.classList.remove('logo-play');
    void logo.getBoundingClientRect();
    logo.classList.add('logo-play');
  };
  document.querySelectorAll('.brand').forEach(brand => {
    const logo = brand.querySelector('.logo');
    if (!logo) return;
    if (brand.closest('.site-header')) setTimeout(() => playLogo(logo), 150);
    else {
      const logoObserver = new IntersectionObserver(entries => entries.forEach(e => {
        if (e.isIntersecting) { playLogo(logo); logoObserver.disconnect(); }
      }), { threshold: 0.6 });
      logoObserver.observe(logo);
    }
    let lock = false;
    brand.addEventListener('mouseenter', () => {
      if (lock) return;
      lock = true;
      playLogo(logo);
      setTimeout(() => { lock = false; }, 1100);
    });
  });
})();

/* ---------- Счётчики цифр: год, площади, цены ---------- */
(() => {
  const root = document.documentElement;
  if (!root.classList.contains('anim') || !('IntersectionObserver' in window)) return;

  // Элемент → откуда начинать счёт. Год считаем от 1990, чтобы не «пролетать» тысячи лет.
  const targets = [
    ['.company-facts article:first-child strong', v => v - 25],
    ['.plan-meta strong', () => 0],
    ['.payment-price', () => 0],
  ];
  const easeOutExpo = t => (t === 1 ? 1 : 1 - Math.pow(2, -10 * t));
  const counters = [];

  targets.forEach(([selector, startFrom]) => {
    document.querySelectorAll(selector).forEach(el => {
      // Берём первый текстовый узел с цифрами («290 000», «27,0», «2015»).
      const node = [...el.childNodes].find(n => n.nodeType === 3 && /\d/.test(n.textContent));
      if (!node) return;
      const raw = node.textContent;
      const match = raw.match(/\d[\d\s ]*(?:,\d+)?/);
      if (!match) return;
      const text = match[0].trim();
      const decimals = text.includes(',') ? text.split(',')[1].length : 0;
      const sep = (text.match(/\d([\s ])\d/) || [])[1] || '';
      const value = parseFloat(text.replace(/[\s ]/g, '').replace(',', '.'));
      if (isNaN(value)) return;

      // Оборачиваем число в span, остальное (₸/м², м²) остаётся как было.
      const span = document.createElement('span');
      span.className = 'count-num';
      span.textContent = text;
      const before = raw.slice(0, match.index);
      const after = raw.slice(match.index + text.length);
      node.replaceWith(document.createTextNode(before), span, document.createTextNode(after));
      el.setAttribute('aria-label', el.textContent.replace(/\s+/g, ' ').trim());

      const format = n => {
        let [int, frac] = n.toFixed(decimals).split('.');
        if (sep) int = int.replace(/\B(?=(\d{3})+(?!\d))/g, sep);
        return frac ? int + ',' + frac : int;
      };
      // Фиксируем ширину по итоговому значению, чтобы строка не «прыгала».
      span.style.minWidth = span.getBoundingClientRect().width + 'px';
      const from = startFrom(value);
      span.textContent = format(from);
      counters.push({ el, span, from, value, format });
    });
  });

  // После загрузки шрифтов пересчитываем ширину — Lora шире системного шрифта.
  const remeasure = () => counters.forEach(c => {
    const current = c.span.textContent;
    c.span.style.minWidth = '';
    c.span.textContent = c.format(c.value);
    c.span.style.minWidth = c.span.getBoundingClientRect().width + 'px';
    c.span.textContent = current;
  });
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(remeasure);
  window.addEventListener('resize', remeasure);

  const run = (c, delay) => {
    const duration = c.value >= 1000 && c.from === 0 ? 1800 : 1400;
    setTimeout(() => {
      const start = performance.now();
      const tick = now => {
        const t = Math.min((now - start) / duration, 1);
        c.span.textContent = c.format(c.from + (c.value - c.from) * easeOutExpo(t));
        if (t < 1) requestAnimationFrame(tick);
        else c.span.classList.add('count-done');
      };
      requestAnimationFrame(tick);
    }, delay);
  };

  const observer = new IntersectionObserver(entries => {
    entries.filter(e => e.isIntersecting).forEach((e, i) => {
      const c = counters.find(x => x.el === e.target);
      if (c) run(c, 200 + i * 120);
      observer.unobserve(e.target);
    });
  }, { rootMargin: '0px 0px -10% 0px', threshold: 0.6 });
  counters.forEach(c => observer.observe(c.el));
})();
