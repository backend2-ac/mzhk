<!DOCTYPE html>
<html lang="ru">
 <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, follow">
  <meta name="theme-color" content="#F7F3EC">
  <title>Страница не найдена — Pangaea</title>
  <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon.png?v=20261022-pangaea">
  <link rel="icon" type="image/svg+xml" sizes="any" href="/images/favicon.svg?v=20261022-pangaea">
  <link rel="stylesheet" href="/assets/css/styles.css?v=20261016-1">
  <link rel="stylesheet" href="/assets/css/theme-light.css">
  <link rel="stylesheet" href="/assets/css/theme-dark.css?v=20261013-4">
  <link rel="stylesheet" href="/assets/css/not-found.css?v=20261023">
  <script>
   (function(d){try{var t=localStorage.getItem('site-theme');if(t==='dark'||t==='light')d.setAttribute('data-theme',t);}catch(e){}if(!d.getAttribute('data-theme'))d.setAttribute('data-theme','light');})(document.documentElement);
  </script>
 </head>
 <body class="not-found-page">
  <a class="not-found-skip" href="#not-found-content">Перейти к содержимому</a>
  <header class="not-found-header">
   <a class="not-found-brand" href="/" aria-label="Pangaea — главная">
    <img src="/images/favicon.svg?v=20261022-pangaea" width="44" height="44" alt="">
    <span>Pangaea</span>
   </a>
   <div class="not-found-header-actions">
    <a class="not-found-header-link" href="/#layouts">Планировки</a>
    <button class="theme-toggle" data-theme-toggle type="button" aria-label="Включить тёмную тему" title="Тёмная тема">
     <svg class="theme-icon theme-icon-moon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M20.9 13A9 9 0 0 1 11 3.1 9 9 0 1 0 20.9 13Z"/></svg>
     <svg class="theme-icon theme-icon-sun" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
    </button>
   </div>
  </header>
  <?= $this->fetch('content') ?>
  <footer class="not-found-footer">
   <span>© <?= date('Y') ?> Pangaea</span>
   <nav aria-label="Полезные ссылки">
    <a href="/#about">О комплексе</a>
    <a href="/#contacts">Контакты</a>
   </nav>
   <a href="https://astanacreative.kz/" target="_blank" rel="noopener noreferrer">Создание сайтов — Astana Creative</a>
  </footer>
  <script defer src="/assets/js/theme.js?v=20261013-2"></script>
 </body>
</html>
