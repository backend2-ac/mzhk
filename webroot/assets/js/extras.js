// WOW-эффекты: калькулятор рассрочки, свечение за курсором, бегущая строка, проявление текста.
(() => {
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const fmt = n => Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' ₸';
  const fmtArea = a => a.toFixed(1).replace('.', ',') + ' м²';

  /* =================== 1. Калькулятор рассрочки ===================
     Условия взяты из блока «Ваш дом — на ваших условиях». При изменении цен поправьте здесь. */
  const PLANS = {
    '5050': { name: 'Рассрочка 50/50', price: 290000, down: 0.5, months: 36 },
    '3070': { name: 'Рассрочка 30/70', price: 320000, down: 0.3, monthly: 150000 },
    'full': { name: '100% оплата', price: 250000, down: 1 },
  };
  const calc = document.getElementById('calculator');
  if (calc) {
    // Цены из админки (компоненты calc_price_*, calc_monthly_3070) перекрывают значения по умолчанию.
    const num = v => { const n = parseFloat(v); return n > 0 ? n : null; };
    PLANS['5050'].price = num(calc.dataset.price5050) || PLANS['5050'].price;
    PLANS['3070'].price = num(calc.dataset.price3070) || PLANS['3070'].price;
    PLANS['3070'].monthly = num(calc.dataset.monthly3070) || PLANS['3070'].monthly;
    PLANS.full.price = num(calc.dataset.priceFull) || PLANS.full.price;
    const range = calc.querySelector('#calc-area');
    const out = calc.querySelector('#calc-area-out');
    const planButtons = [...calc.querySelectorAll('[data-calc-plan]')];
    const chips = [...calc.querySelectorAll('[data-calc-area]')];
    const el = key => calc.querySelector(`[data-calc="${key}"]`);
    const bar = calc.querySelector('.calc-bar-fill');
    const cta = calc.querySelector('.calc-cta');
    let plan = '5050';
    const shown = {}; // текущие отображаемые значения для плавной анимации

    function tween(node, key, to) {
      const from = shown[key] ?? to;
      shown[key] = to;
      if (reduceMotion || from === to) { node.textContent = fmt(to); return; }
      node.classList.remove('is-bump'); void node.offsetWidth; node.classList.add('is-bump');
      const start = performance.now(), dur = 500;
      const step = now => {
        const t = Math.min((now - start) / dur, 1), e = 1 - Math.pow(1 - t, 3);
        node.textContent = fmt(from + (to - from) * e);
        if (t < 1 && shown[key] === to) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
    }

    function update() {
      const area = parseFloat(range.value);
      const p = PLANS[plan];
      const total = area * p.price;
      out.textContent = fmtArea(area);
      range.style.setProperty('--p', ((area - range.min) / (range.max - range.min) * 100) + '%');
      chips.forEach(c => c.classList.toggle('is-active', Math.abs(parseFloat(c.dataset.calcArea) - area) < 0.05));
      tween(el('total'), 'total', total);
      el('rate').textContent = fmt(p.price).replace('₸', '₸/м²');
      bar.style.width = (p.down * 100) + '%';

      const l1 = el('l1'), l2 = el('l2'), v1 = el('v1'), v2 = el('v2');
      v2.classList.remove('is-accent');
      let summary;
      if (plan === '5050') {
        const monthly = total * (1 - p.down) / p.months;
        l1.textContent = 'Первоначальный взнос 50%';
        l2.textContent = 'Ежемесячно, 36 мес. без %';
        tween(v1, 'v1', total * p.down); tween(v2, 'v2', monthly);
        summary = `взнос ${fmt(total * p.down)}, ${fmt(monthly)}/мес`;
      } else if (plan === '3070') {
        l1.textContent = 'Первоначальный взнос 30%';
        l2.textContent = 'Ежемесячно до выдачи ключей';
        tween(v1, 'v1', total * p.down); tween(v2, 'v2', p.monthly);
        summary = `взнос ${fmt(total * p.down)}, ${fmt(p.monthly)}/мес`;
      } else {
        const saving = area * (PLANS['5050'].price - p.price);
        l1.textContent = 'Оплата сразу';
        l2.textContent = 'Выгода по сравнению с 50/50';
        tween(v1, 'v1', total); tween(v2, 'v2', saving);
        v2.classList.add('is-accent');
        summary = `выгода ${fmt(saving)}`;
      }
      cta.dataset.topic = `Расчёт: ${fmtArea(area)}, ${p.name} — ${fmt(total)} (${summary})`.replace(/ /g, ' ');
    }

    planButtons.forEach(btn => btn.addEventListener('click', () => {
      plan = btn.dataset.calcPlan;
      planButtons.forEach(b => b.setAttribute('aria-checked', b === btn));
      update();
    }));
    // Стрелки клавиатуры в группе вариантов
    calc.querySelector('.calc-options').addEventListener('keydown', e => {
      if (!['ArrowLeft', 'ArrowRight'].includes(e.key)) return;
      e.preventDefault();
      const i = planButtons.findIndex(b => b.dataset.calcPlan === plan);
      const next = planButtons[(i + (e.key === 'ArrowRight' ? 1 : -1) + planButtons.length) % planButtons.length];
      next.focus(); next.click();
    });
    range.addEventListener('input', update);
    chips.forEach(chip => chip.addEventListener('click', () => { range.value = chip.dataset.calcArea; update(); }));
    update();
  }

  /* =================== 2. Свечение за курсором =================== */
  if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    const spots = [...document.querySelectorAll(
      '.card, .infra-card, .payment-card, .plan-card, .video-card, .spec-card, .advantage-item, .company-facts, .selection-form, .calc, .contact-map'
    )];
    spots.forEach(s => s.classList.add('spot'));
    let px = 0, py = 0, frame = 0;
    const paint = () => {
      frame = 0;
      spots.forEach(s => {
        const r = s.getBoundingClientRect();
        if (r.bottom < -300 || r.top > innerHeight + 300) return;
        s.style.setProperty('--mx', (px - r.left) + 'px');
        s.style.setProperty('--my', (py - r.top) + 'px');
      });
    };
    document.addEventListener('pointermove', e => {
      px = e.clientX; py = e.clientY;
      document.documentElement.classList.add('spot-on');
      if (!frame) frame = requestAnimationFrame(paint);
    }, { passive: true });
    window.addEventListener('scroll', () => { if (!frame) frame = requestAnimationFrame(paint); }, { passive: true });
    document.documentElement.addEventListener('pointerleave', () => document.documentElement.classList.remove('spot-on'));
  }

  /* =================== 3. Бегущая строка =================== */
  document.querySelectorAll('.marquee').forEach(marquee => {
    const track = marquee.querySelector('.marquee-track');
    const original = track.innerHTML;
    // Дублируем содержимое, чтобы лента шла бесшовно
    let copies = 2;
    track.innerHTML = original + original;
    while (track.scrollWidth < innerWidth * 3 && copies < 12) { track.innerHTML += original; copies++; }
    if (reduceMotion) return;
    let x = 0, boost = 0, dir = 1, lastY = scrollY, visible = true, setWidth = 0;
    const measure = () => { setWidth = track.scrollWidth / copies; };
    measure();
    addEventListener('resize', measure);
    if ('IntersectionObserver' in window) new IntersectionObserver(([e]) => { visible = e.isIntersecting; }).observe(marquee);
    addEventListener('scroll', () => {
      const dy = scrollY - lastY; lastY = scrollY;
      if (Math.abs(dy) > 1) dir = dy > 0 ? 1 : -1;      // направление следует за прокруткой
      boost = Math.min(boost + Math.abs(dy) * 0.15, 14); // прокрутка разгоняет ленту
    }, { passive: true });
    const tick = () => {
      if (visible) {
        x -= dir * (0.6 + boost);
        boost *= 0.92;
        if (x <= -setWidth) x += setWidth;
        if (x > 0) x -= setWidth;
        track.style.transform = `translate3d(${x}px,0,0)`;
      }
      requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  });

  /* =================== 4. Проявление текста по словам =================== */
  document.querySelectorAll('[data-word-reveal]').forEach(block => {
    const accent = ['малоэтажные', 'Үркер', 'современные'].map(w => w.toLowerCase());
    const words = block.textContent.trim().split(/\s+/);
    block.innerHTML = words.map(w =>
      `<span class="w${accent.some(a => w.toLowerCase().startsWith(a)) ? ' is-accent' : ''}">${w}</span>`).join(' ');
    block.classList.add('word-reveal');
    const spans = [...block.querySelectorAll('.w')];
    if (reduceMotion) { spans.forEach(s => s.classList.add('is-lit')); return; }
    let ticking = false, revealed = 0;
    const update = () => {
      ticking = false;
      const r = block.getBoundingClientRect();
      // Завершаем проявление, когда весь абзац вошёл в экран; длинный — у верхнего края.
      const start = innerHeight * 0.9, end = Math.max(innerHeight * 0.1, start - r.height);
      const atBottom = scrollY + innerHeight >= document.documentElement.scrollHeight - 4;
      const progress = atBottom ? 1 : Math.min(Math.max((start - r.top) / Math.max(start - end, 1), 0), 1);
      revealed = Math.max(revealed, Math.ceil(progress * spans.length));
      spans.forEach((s, i) => s.classList.toggle('is-lit', i < revealed));
    };
    const schedule = () => { if (!ticking) { ticking = true; requestAnimationFrame(update); } };
    addEventListener('scroll', schedule, { passive: true });
    addEventListener('resize', schedule);
    if (document.fonts) document.fonts.ready.then(schedule);
    update();
  });
})();
