// Формы заявок: маска телефона, проверка полей, модальное окно «Заказать звонок».
(() => {
  /* ======================================================================
     1. ОТПРАВКА ЗАЯВКИ — единственное место для подключения CRM / Telegram / почты.
     Сейчас заявка никуда не уходит (демо-режим): данные выводятся в консоль
     браузера, а посетитель видит сообщение об успехе.
     Пример подключения:
       return fetch('https://ваш-сервер/lead', {
         method: 'POST',
         headers: { 'Content-Type': 'application/json' },
         body: JSON.stringify(lead),
       }).then(r => { if (!r.ok) throw new Error('Ошибка отправки'); });
     ====================================================================== */
  function submitLead(lead) {
    console.info('[Заявка — демо, не отправлена]', lead);
    return new Promise(resolve => setTimeout(resolve, 700));
  }

  /* ---------- 2. Маска телефона +7 (___) ___-__-__ ---------- */
  const PREFIX = '+7 (';
  const digitsOf = value => {
    let d;
    if (value.trim().startsWith('+7')) d = value.trim().slice(2).replace(/\D/g, '');  // уже с кодом страны
    else {
      d = value.replace(/\D/g, '');
      if (d.length > 10 && /^[78]/.test(d)) d = d.slice(1);                         // вставили 8 707… или 7 707…
    }
    if (d.length > 10 && /^[78]/.test(d)) d = d.slice(1);  // вставили 8707… в поле с «+7 (»
    if (d.startsWith('8')) d = d.slice(1);                   // привычная «8» в начале (казахстанские номера с 8 не начинаются)
    return d.slice(0, 10);
  };
  const formatPhone = d => {
    if (!d) return '';
    let out = PREFIX + d.slice(0, 3);
    if (d.length >= 3) out += ') ' + d.slice(3, 6);
    if (d.length >= 6) out += '-' + d.slice(6, 8);
    if (d.length >= 8) out += '-' + d.slice(8, 10);
    return out;
  };
  const phoneComplete = input => digitsOf(input.value).length === 10;

  function attachMask(input) {
    input.setAttribute('maxlength', '18');
    input.addEventListener('focus', () => {
      if (!input.value) input.value = PREFIX;
      requestAnimationFrame(() => input.setSelectionRange(input.value.length, input.value.length));
    });
    input.addEventListener('blur', () => { if (!digitsOf(input.value)) input.value = ''; });
    input.addEventListener('keydown', event => {
      // Backspace сразу за скобкой/дефисом удаляет цифру, а не только символ маски
      if (event.key !== 'Backspace') return;
      const pos = input.selectionStart;
      if (pos !== input.selectionEnd || pos !== input.value.length) return;
      if (/[\s()\-]$/.test(input.value.slice(0, pos))) {
        event.preventDefault();
        const d = digitsOf(input.value).slice(0, -1);
        input.value = d ? formatPhone(d) : PREFIX;
        input.dispatchEvent(new Event('input', { bubbles: true }));
      }
    });
    input.addEventListener('input', () => {
      const atEnd = input.selectionStart >= input.value.length - 1;
      const d = digitsOf(input.value);
      input.value = d ? formatPhone(d) : (document.activeElement === input ? PREFIX : '');
      if (atEnd) input.setSelectionRange(input.value.length, input.value.length);
      if (phoneComplete(input)) setInvalid(input, false);
    });
  }

  /* ---------- 3. Проверка полей ---------- */
  function errorFor(input) {
    let error = input.parentElement.querySelector('.field-error');
    if (!error) {
      error = document.createElement('small');
      error.className = 'field-error';
      error.textContent = input.type === 'tel' ? 'Введите номер полностью' : 'Введите имя';
      input.after(error);
    }
    return error;
  }
  function setInvalid(input, invalid) {
    input.classList.toggle('is-invalid', invalid);
    input.setAttribute('aria-invalid', invalid);
    errorFor(input).classList.toggle('is-shown', invalid);
  }
  function validate(form) {
    let firstBad = null;
    form.querySelectorAll('input[name="name"], input[type="tel"]').forEach(input => {
      const bad = input.type === 'tel' ? !phoneComplete(input) : input.value.trim().length < 2;
      setInvalid(input, bad);
      if (bad && !firstBad) firstBad = input;
    });
    if (firstBad) firstBad.focus();
    return !firstBad;
  }

  /* ---------- 4. Общая обработка форм ---------- */
  function collect(form) {
    const data = Object.fromEntries(new FormData(form));
    return {
      name: (data.name || '').trim(),
      phone: '+7' + digitsOf(data.phone || ''),
      topic: data.topic || form.dataset.topic || 'Обратный звонок',
      form: form.dataset.leadForm,
      page: location.href,
      sentAt: new Date().toISOString(),
    };
  }

  document.querySelectorAll('input[type="tel"]').forEach(attachMask);
  document.querySelectorAll('[data-lead-form] input[name="name"]').forEach(input =>
    input.addEventListener('input', () => { if (input.value.trim().length >= 2) setInvalid(input, false); }));

  document.querySelectorAll('[data-lead-form]').forEach(form => {
    form.addEventListener('submit', event => {
      event.preventDefault();
      if (!validate(form)) return;
      const button = form.querySelector('[type="submit"]');
      button.disabled = true;
      button.classList.add('is-loading');
      submitLead(collect(form))
        .then(() => {
          if (form.dataset.leadForm === 'callback') showModalSuccess(form);
          else showInlineSuccess(form);
          form.reset();
        })
        .catch(() => alertError(form))
        .finally(() => { button.disabled = false; button.classList.remove('is-loading'); });
    });
  });

  function showInlineSuccess(form) {
    const result = form.querySelector('[role="status"]');
    if (!result) return;
    result.hidden = false;
    result.className = 'form-result is-success';
    result.textContent = 'Спасибо! Заявка принята — менеджер перезвонит вам в ближайшее рабочее время.';
  }
  function alertError(form) {
    const result = form.querySelector('[role="status"]') || form.querySelector('.modal-consent');
    if (result) {
      result.hidden = false;
      result.classList.add('is-error');
      result.textContent = 'Не удалось отправить заявку. Позвоните нам: +7 771 832 03 03.';
    }
  }

  /* ---------- 5. Модальное окно ---------- */
  const modal = document.getElementById('callback-modal');
  if (!modal || typeof modal.showModal !== 'function') return;
  const modalForm = modal.querySelector('.modal-form');
  const success = modal.querySelector('.modal-success');
  const topicLine = modal.querySelector('.modal-topic');
  const topicInput = modalForm.querySelector('input[name="topic"]');
  let closeTimer;

  function openModal(topic) {
    clearTimeout(closeTimer);
    modalForm.hidden = false;
    success.hidden = true;
    modalForm.querySelectorAll('input:not([type="hidden"])').forEach(i => setInvalid(i, false));
    topicInput.value = topic || 'Обратный звонок';
    topicLine.hidden = !topic;
    topicLine.textContent = topic || '';
    const scrollbar = window.innerWidth - document.documentElement.clientWidth;
    document.documentElement.style.setProperty('--scrollbar', scrollbar + 'px');
    document.documentElement.classList.add('modal-open');
    modal.showModal();
    requestAnimationFrame(() => modal.classList.add('is-open'));
    setTimeout(() => modalForm.querySelector('input[name="name"]').focus(), 60);
  }
  function closeModal() {
    if (!modal.open) return;
    modal.classList.remove('is-open');
    closeTimer = setTimeout(() => {
      modal.close();
      document.documentElement.classList.remove('modal-open');
    }, 280);
  }
  function showModalSuccess() {
    modalForm.hidden = true;
    success.hidden = false;
    success.classList.remove('is-drawn');
    void success.offsetWidth;
    success.classList.add('is-drawn');
    success.querySelector('.modal-done').focus();
  }

  document.querySelectorAll('[data-modal-open="callback"]').forEach(trigger => {
    trigger.addEventListener('click', event => {
      event.preventDefault();
      openModal(trigger.dataset.topic);
    });
  });
  modal.querySelectorAll('[data-modal-close]').forEach(b => b.addEventListener('click', closeModal));
  modal.addEventListener('cancel', event => { event.preventDefault(); closeModal(); }); // Esc
  modal.addEventListener('click', event => { if (event.target === modal) closeModal(); }); // клик по фону
})();
