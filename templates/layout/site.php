<?php
/**
 * Layout главной страницы (новая верстка Pangaea).
 *
 * @var \App\View\AppView $this
 */
?>
<!DOCTYPE html>
<html lang="ru">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <meta content="#F7F3EC" name="theme-color"/>
  <meta name="csrf-token" content="<?= h($this->request->getAttribute('csrfToken')) ?>"/>
<?php if( !empty($meta['title']) ): ?>
  <title><?= h($meta['title']) ?></title>
  <meta name="description" content="<?= h($meta['desc'] ?? '') ?>"/>
  <meta name="keywords" content="<?= h($meta['keys'] ?? '') ?>"/>
<?php else: ?>
  <title>Pangaea — МЖК «Астана»</title>
<?php endif; ?>
  <link rel="shortcut icon" href="/images/favicon.png"/>
  <link href="/assets/css/styles.css?v=20261016-1" rel="stylesheet"/>
  <link href="/assets/css/animations.css?v=20261013-4" rel="stylesheet"/>
  <link href="/assets/css/forms.css?v=20261013-4" rel="stylesheet"/>
  <link href="/assets/css/extras.css?v=20261013-4" rel="stylesheet"/>
  <link href="/assets/css/theme-light.css" rel="stylesheet"/>
  <link href="/assets/css/theme-dark.css?v=20261013-4" rel="stylesheet"/>
  <link href="/assets/css/backend.css?v=1" rel="stylesheet"/>
  <script>
   // Тема применяется до отрисовки, чтобы не было «вспышки» не той темы.
   (function(d){try{var t=localStorage.getItem('site-theme');if(t==='dark'||t==='light')d.setAttribute('data-theme',t);}catch(e){}if(!d.getAttribute('data-theme'))d.setAttribute('data-theme','light');})(document.documentElement);
  </script>
  <script>
   // Включаем анимации появления до первой отрисовки, чтобы блоки не мигали.
   (function(d){if(!window.matchMedia('(prefers-reduced-motion: reduce)').matches&&'IntersectionObserver' in window){d.classList.add('anim');setTimeout(function(){if(!d.classList.contains('anim-ready'))d.classList.remove('anim');},3000);}})(document.documentElement);
  </script>
<?= $this->element('analytics') ?>
 </head>
 <body>
<?= $this->fetch('content') ?>
  <script defer src="/assets/js/main.js?v=20261016-1"></script>
  <script defer src="/assets/js/animations.js"></script>
  <script defer src="/assets/js/forms.js?v=2"></script>
  <script defer src="/assets/js/extras.js?v=20261013"></script>
  <script defer src="/assets/js/theme.js?v=20261013-2"></script>
  <script defer src="/assets/js/backend.js?v=1"></script>
 </body>
</html>
