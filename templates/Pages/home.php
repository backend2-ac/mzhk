<?php
/**
 * Главная страница (верстка Pangaea), все тексты и карточки — из админки.
 * Данные приходят из PagesController::home() и AppController ($comps — блоки «Компоненты»);
 * ресурсы — /assets/*, layout — site.
 *
 * Блоки «Компоненты» (comps) берутся по alias, например $t('about_title').
 *
 * @var \App\View\AppView $this
 */

// Компоненты по alias; значение — HTML из админки (как есть).
$byAlias = [];
foreach( ($comps ?? []) as $comp ){
    if( !empty($comp['alias']) ){
        $byAlias[$comp['alias']] = $comp;
    }
}
$t     = fn(string $alias): string => trim((string)($byAlias[$alias]['body'] ?? ''));
$plain = fn(string $alias): string => trim(strip_tags($t($alias)));
$cimg  = fn(string $alias): string => trim((string)($byAlias[$alias]['img'] ?? ''));

$videoUrl  = $plain('about_video');
$phone     = $plain('phone');
$email     = $plain('email');
$instagram = $plain('instagram') ?: 'https://www.instagram.com/mzhk.astana/';
$whatsapp  = $plain('whatsapp_link') ?: 'https://wa.me/';
$handle    = $plain('social_handle') ?: 'mzhk.astana';
$brand     = $plain('brand_name') ?: 'Pangaea';
$mapUrl    = $t('map_url');

$slides          = $slides ?? [];
$houses          = $houses ?? [];
$houses_2        = $houses_2 ?? [];
$services        = $services ?? [];
$galleries       = $galleries ?? [];
$characteristics = $characteristics ?? [];

// Иконки интерфейса (постоянные элементы верстки).
$svgBase  = 'aria-hidden="true" class="svg-icon%s" width="1.1em" height="1.1em" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" viewBox="0 0 24 24"';
$icArrowUp = '<svg ' . sprintf($svgBase, ' action-arrow') . '><path d="M7 17 17 7M7 7h10v10"/></svg>';
$icArrowR  = '<svg ' . sprintf($svgBase, ' action-arrow') . '><path d="M4 12h16m-6-6 6 6-6 6"/></svg>';
$icPhone   = '<svg ' . sprintf($svgBase, '') . '><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.4 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/></svg>';
$icLogo    = '<svg aria-hidden="true" class="logo" viewBox="0 0 40 40"><path d="M0 0h40v40H0z" fill="#FFB342"></path><path d="M0 8h32v32H0z" fill="#F58220"></path><path d="M0 16h24v24H0z" fill="#EF5A18"></path><path d="M0 24h16v16H0z" fill="#D73716"></path><path d="M0 32h8v8H0z" fill="#B92317"></path></svg>';

// Иконка карточки: inline-SVG из админки (поле «SVG-иконка»), иначе загруженная картинка.
$cardIcon = function($item, string $dir, string $fallback = '') {
    if( !empty($item['icon']) ){
        return (string)$item['icon'];
    }
    if( !empty($item['img']) ){
        return '<img alt="" loading="lazy" src="/img/' . $dir . '/' . h($item['img']) . '"/>';
    }
    return $fallback;
};

$planLabel = [1 => '1-комнатная квартира', 2 => '2-комнатная квартира'];
$planAlt   = [1 => 'однокомнатной', 2 => 'двухкомнатной'];

// Карточка планировки; площадь хранится в поле «Название» (например, 27,4).
$planCard = function($item, int $room) use ($planLabel, $planAlt, $icArrowUp): string {
    $area = trim((string)($item['title'] ?? ''));
    $img  = !empty($item['img'])
        ? '<img alt="Цветная планировка ' . $planAlt[$room] . ' квартиры ' . h($area) . ' м²" height="400" loading="lazy" src="/img/houses/' . h($item['img']) . '" width="400"/>'
        : '';
    $size = $area !== '' ? '<strong>' . h($area) . ' <small>м²</small></strong>' : '';
    return '<article class="plan-card"><div class="plan-image">' . $img . '</div>'
        . '<div class="plan-meta"><span>' . $planLabel[$room] . '</span>' . $size . '</div>'
        . '<button class="plan-select" data-plan="' . h($area) . '" type="button">Подобрать квартиру <span aria-hidden="true">' . $icArrowUp . '</span></button></article>' . "\n";
};

// Для калькулятора: площади планировок (1-комн., затем 2-комн.) и цены из компонентов.
$calcAreas = [];
foreach( array_merge($houses, $houses_2) as $item ){
    $area = trim((string)($item['title'] ?? ''));
    if( $area !== '' ){
        $calcAreas[] = $area;
    }
}
$calcNum = fn(string $alias): string => preg_replace('/[^0-9.]/', '', $plain($alias));
$planNames = \App\Service\PaymentPlans::names($services);

// Бегущая строка: по строке на пункт, «~» в начале — контурный.
$marquee = array_values(array_filter(array_map('trim', preg_split('/\R/u', strip_tags($t('marquee'))))));
?>
  <section class="hero" id="home">
   <header class="site-header">
    <div class="container header-inner">
     <a aria-label="<?= h($brand) ?> — главная" class="brand" href="#home">
      <?= $icLogo ?>
      <span><?= h($brand) ?></span>
     </a>
     <nav aria-label="Основное меню" class="navigation">
      <a href="#about"><?= $t('nav_about') ?></a>
      <a href="#advantages"><?= $t('nav_advantages') ?></a>
      <a href="#payment"><?= $t('nav_payment') ?></a>
      <a href="#specifications"><?= $t('nav_specs') ?></a>
     </nav>
     <div class="header-actions">
      <!-- Переключатель светлой / тёмной темы (assets/js/theme.js) -->
      <button aria-label="Включить тёмную тему" class="theme-toggle" data-theme-toggle="" type="button">
       <svg aria-hidden="true" class="theme-icon theme-icon-sun" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="4.2"></circle>
        <path d="M12 2.5v2.2M12 19.3v2.2M4.6 4.6l1.6 1.6M17.8 17.8l1.6 1.6M2.5 12h2.2M19.3 12h2.2M4.6 19.4l1.6-1.6M17.8 6.2l1.6-1.6"></path>
       </svg>
       <svg aria-hidden="true" class="theme-icon theme-icon-moon" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24">
        <path d="M20.5 14.2A8.5 8.5 0 0 1 9.8 3.5a8.5 8.5 0 1 0 10.7 10.7Z"></path>
       </svg>
      </button>
      <a class="button button-small" data-modal-open="callback" href="#contact-form">
       <?= $t('btn_callback') ?>
       <?= $icPhone ?>
      </a>
     </div>
    </div>
   </header>
   <div aria-label="Предложения МЖК Астана" aria-roledescription="карусель" class="hero-slider" role="region">
    <div class="hero-slides">
     <!-- Слайды: админка → Слайды. Размер баннера — 1920 × 700 px; «Картинка заголовок» — светлая тема, «Картинка преимуществах» — тёмная. -->
<?php $first = true; foreach( $slides as $i => $item ): if( empty($item['img']) ) continue;
    $light = '/img/slides/' . $item['img'];
    $dark  = !empty($item['images']) ? '/img/slides/' . $item['images'] : $light;
    $alt   = trim(strip_tags((string)($item['body'] ?? ''))) ?: 'МЖК Астана — предложение ' . ($i + 1);
?>
     <img alt="<?= h($alt) ?>" class="hero-slide" data-src-dark="<?= h($dark) ?>" data-src-light="<?= h($light) ?>" decoding="async" height="700" width="1920" <?= $first ? 'fetchpriority="high"' : 'loading="lazy"' ?> src="<?= h($light) ?>"/>
<?php $first = false; endforeach; ?>
    </div>
    <div class="container slider-controls">
     <div class="slider-nav">
      <div aria-label="Выбор слайда" class="slider-dots">
      </div>
      <button aria-label="Остановить автопрокрутку" class="slider-pause" data-slide-pause="" type="button">
      </button>
     </div>
     <div class="slider-arrows">
      <button aria-label="Предыдущий слайд" data-slide-prev="" type="button">
       <svg aria-hidden="true" class="svg-icon" width="1.1em" height="1.1em" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
      </button>
      <button aria-label="Следующий слайд" data-slide-next="" type="button">
       <svg aria-hidden="true" class="svg-icon" width="1.1em" height="1.1em" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" viewBox="0 0 24 24"><path d="m9 6 6 6-6 6"/></svg>
      </button>
     </div>
    </div>
   </div>
  </section>
  <section class="section" id="about">
   <div class="container">
    <div class="heading">
     <div>
      <p class="eyebrow"><?= $t('about_eyebrow') ?></p>
      <h2 class="section-title"><?= $t('about_title') ?></h2>
     </div>
     <p class="intro"><?= $t('about_intro') ?></p>
    </div>
    <div class="main">
     <div class="video"<?php if( $videoUrl ): ?> data-video-src="<?= h($videoUrl) ?>" data-video-title="<?= h($plain('about_video_caption')) ?>" role="button" tabindex="0"<?php endif; ?>>
      <img alt="Дома МЖК Астана" src="/assets/images/complex.webp"/>
      <div class="play">
      </div>
      <div class="caption">
       <span><?= $t('about_video_caption') ?></span>
       <span><?= $t('about_video_duration') ?></span>
      </div>
     </div>
     <div class="features" id="about-features">
<?php foreach( ($advans ?? []) as $item ): ?>
      <article class="card">
       <div class="icon"><?= $cardIcon($item, 'advans') ?></div>
       <div>
        <h2><?= h($item['title']) ?></h2>
<?php if( trim((string)($item['body'] ?? '')) !== '' ): ?>
        <p><?= nl2br(h($item['body'])) ?></p>
<?php endif; ?>
       </div>
      </article>
<?php endforeach; ?>
     </div>
    </div>
    <div class="infrastructure" id="infrastructure">
     <p class="eyebrow"><?= $t('infra_eyebrow') ?></p>
     <div class="infra-grid">
<?php foreach( ($infrastructures ?? []) as $item ): ?>
      <article class="infra-card">
       <div class="icon"><?= $cardIcon($item, 'infrastructures', '<svg aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 34 34"><path d="M17 31s10-9 10-17a10 10 0 0 0-20 0c0 8 10 17 10 17ZM17 17.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0-7Z"/></svg>') ?></div>
       <div>
        <h2><?= h($item['title']) ?></h2>
       </div>
      </article>
<?php endforeach; ?>
     </div>
    </div>
   </div>
  </section>
<?php if( $marquee ): ?>
  <div aria-hidden="true" class="marquee">
   <div class="marquee-track">
    <?php foreach( $marquee as $word ): $outline = $word !== '' && $word[0] === '~'; ?><span<?= $outline ? ' class="is-outline"' : '' ?>><?= h(ltrim($word, '~')) ?></span><i>✦</i><?php endforeach; ?>

   </div>
  </div>
<?php endif; ?>
  <section class="section payment-section" id="payment">
   <div class="container">
    <div class="payment-heading">
     <div>
      <p class="eyebrow"><?= $t('payment_eyebrow') ?></p>
      <h2 class="section-title"><?= $t('payment_title') ?></h2>
     </div>
     <p><?= $t('payment_lead') ?></p>
    </div>
    <div class="payment-grid">
<?php foreach( $services as $i => $item ):
    $name  = trim((string)($item['name'] ?? '')) ?: 'Условия приобретения';
    $topic = trim((string)($item['topic'] ?? '')) ?: $name;
    $terms = (string)($item['title'] ?? '');
    if( $terms !== '' && strpos($terms, '<') === false ){ $terms = '<p>' . nl2br(h($terms)) . '</p>'; }
    $tags  = array_values(array_filter(array_map('trim', preg_split('/\R/u', (string)($item['tags'] ?? '')))));
?>
     <article class="payment-card <?= $i === 0 ? 'payment-featured' : 'payment-compact' ?>">
      <div class="payment-summary">
       <h3><?= h($name) ?></h3>
<?php if( trim((string)($item['subtitle'] ?? '')) !== '' ): ?>
       <p class="payment-subtitle"><?= h($item['subtitle']) ?></p>
<?php endif; ?>
       <p class="payment-price">
        <?= h($item['number'] ?? '') ?>
<?php if( trim((string)($item['unit'] ?? '')) !== '' ): ?>
        <span><?= h($item['unit']) ?></span>
<?php endif; ?>
       </p>
      </div>
      <div class="payment-terms">
       <?= $terms ?>
<?php if( $tags ): ?>
       <div class="payment-tags">
<?php foreach( $tags as $tag ): ?>
        <span><?= h($tag) ?></span>
<?php endforeach; ?>
       </div>
<?php endif; ?>
      </div>
<?php if( trim((string)($item['motif'] ?? '')) !== '' ): ?>
      <span aria-hidden="true" class="payment-motif"><?= h($item['motif']) ?></span>
<?php endif; ?>
      <a aria-label="Узнать условия: <?= h($name) ?>" class="circle-link" data-modal-open="callback" data-topic="<?= h($topic) ?>" href="#consultation">
       <?= $icArrowR ?>
      </a>
     </article>
<?php endforeach; ?>
    </div>
    <?php if( $t('payment_note') !== '' ): ?>
    <p class="calc-note payment-note"><?= $t('payment_note') ?></p>
    <?php endif; ?>
    <!-- Калькулятор рассрочки. Цены — компоненты calc_price_* / calc_monthly_3070, площади — из «Планировок»; логика — assets/js/extras.js. -->
    <div class="calc" id="calculator" data-price-5050="<?= h($calcNum('calc_price_5050')) ?>" data-price-3070="<?= h($calcNum('calc_price_3070')) ?>" data-price-full="<?= h($calcNum('calc_price_full')) ?>" data-monthly-3070="<?= h($calcNum('calc_monthly_3070')) ?>">
     <div class="calc-controls">
      <p class="eyebrow calc-eyebrow"><?= $t('calc_eyebrow') ?></p>
      <h3 class="calc-title"><?= $t('calc_title') ?></h3>
      <div class="calc-group">
       <span class="calc-label" id="calc-plan-label">
        Вариант оплаты
       </span>
       <div aria-labelledby="calc-plan-label" class="calc-options" role="radiogroup">
        <button aria-checked="true" data-calc-plan="5050" role="radio" type="button"><?= h($planNames['5050']) ?></button>
        <button aria-checked="false" data-calc-plan="3070" role="radio" type="button"><?= h($planNames['3070']) ?></button>
        <button aria-checked="false" data-calc-plan="full" role="radio" type="button"><?= h($planNames['full']) ?></button>
       </div>
      </div>
      <div class="calc-group">
       <div class="calc-label-row">
        <label class="calc-label" for="calc-area">
         Площадь квартиры
        </label>
        <output class="calc-area-out" for="calc-area" id="calc-area-out">
         32,1 м²
        </output>
       </div>
       <input class="calc-range" id="calc-area" max="70" min="25" step="0.1" type="range" value="32.1"/>
       <div aria-label="Планировки" class="calc-chips">
<?php foreach( $calcAreas as $area ): ?>
        <button data-calc-area="<?= h(str_replace(',', '.', $area)) ?>" type="button"><?= h($area) ?> м²</button>
<?php endforeach; ?>
       </div>
      </div>
     </div>
     <div class="calc-result">
      <span class="calc-result-label">
       Стоимость квартиры
      </span>
      <strong class="calc-total" data-calc="total">
       9 309 000 ₸
      </strong>
      <span class="calc-rate" data-calc="rate">
       290 000 ₸/м²
      </span>
      <div aria-hidden="true" class="calc-bar">
       <span class="calc-bar-fill">
       </span>
      </div>
      <dl class="calc-rows">
       <div>
        <dt data-calc="l1">
         Первоначальный взнос 50%
        </dt>
        <dd data-calc="v1">
         4 654 500 ₸
        </dd>
       </div>
       <div>
        <dt data-calc="l2">
         Ежемесячно, 36 мес. без %
        </dt>
        <dd data-calc="v2">
         129 292 ₸
        </dd>
       </div>
      </dl>
      <button class="button calc-cta" data-modal-open="callback" data-topic="Расчёт из калькулятора" type="button">
       Зафиксировать условия
       <span aria-hidden="true"><?= $icArrowR ?></span>
      </button>
      <p class="calc-note"><?= $t('calc_note') ?></p>
     </div>
    </div>
    <div class="payment-action" id="consultation">
     <button class="button" data-modal-open="callback" data-topic="Консультация по условиям оплаты" type="button">
      <?= $t('payment_cta') ?>
      <span aria-hidden="true"><?= $icArrowR ?></span>
     </button>
    </div>
   </div>
  </section>
  <section aria-labelledby="layouts-title" class="section" id="layouts">
   <div class="container">
    <div class="layouts-heading">
     <div>
      <p class="eyebrow"><?= $t('layouts_eyebrow') ?></p>
      <h2 id="layouts-title"><?= $t('layouts_title') ?></h2>
     </div>
     <p><?= $t('layouts_lead') ?></p>
    </div>
    <?php if( $houses || $houses_2 ): ?>
    <div aria-label="Количество комнат" class="plan-tabs" role="tablist">
     <button aria-controls="plans-all" aria-selected="true" data-plan-tab="all" id="tab-all" role="tab" type="button">Все</button>
<?php if( $houses ): ?>
     <button aria-controls="plans-one" aria-selected="false" data-plan-tab="one" id="tab-one" role="tab" tabindex="-1" type="button">1-комнатные</button>
<?php endif; ?>
<?php if( $houses_2 ): ?>
     <button aria-controls="plans-two" aria-selected="false" data-plan-tab="two" id="tab-two" role="tab" tabindex="-1" type="button">2-комнатные</button>
<?php endif; ?>
    </div>
    <div aria-labelledby="tab-all" class="plans-grid" id="plans-all" role="tabpanel">
<?php foreach( $houses as $item ){ echo $planCard($item, 1); } ?>
<?php foreach( $houses_2 as $item ){ echo $planCard($item, 2); } ?>
    </div>
<?php if( $houses ): ?>
    <div aria-labelledby="tab-one" class="plans-grid" hidden id="plans-one" role="tabpanel">
<?php foreach( $houses as $item ){ echo $planCard($item, 1); } ?>
    </div>
<?php endif; ?>
<?php if( $houses_2 ): ?>
    <div aria-labelledby="tab-two" class="plans-grid" hidden id="plans-two" role="tabpanel">
<?php foreach( $houses_2 as $item ){ echo $planCard($item, 2); } ?>
    </div>
<?php endif; ?>
<?php endif; ?>
    <form class="selection-form" data-lead-form="selection" data-topic="Подбор квартиры" id="selection-form" novalidate="">
     <div>
      <h3><?= $t('selection_title') ?></h3>
      <p><?= $t('form_lead') ?></p>
     </div>
     <div class="selection-fields">
      <label>
       Ваше имя
       <input autocomplete="given-name" name="name" placeholder="Как к вам обращаться" required=""/>
      </label>
      <label>
       Телефон
       <input autocomplete="tel" name="phone" inputmode="tel" placeholder="+7 (___) ___-__-__" required="" type="tel"/>
      </label>
      <button type="submit">
       Подобрать квартиру <?= $icArrowUp ?>
      </button>
     </div>
     <p hidden="" id="selection-result" role="status">
     </p>
    </form>
   </div>
  </section>
  <section aria-labelledby="video-gallery-title" class="section" id="video-gallery">
   <div class="container">
    <div class="layouts-heading">
     <div>
      <p class="eyebrow"><?= $t('video_eyebrow') ?></p>
      <h2 id="video-gallery-title"><?= $t('video_title') ?></h2>
     </div>
     <p><?= $t('video_lead') ?></p>
    </div>
    <div class="video-grid">
<?php $noLink = false; foreach( $galleries as $i => $item ):
    $link = trim((string)($item['link'] ?? ''));
    if( $link === '' ){ $noLink = true; }
?>
     <article class="video-card<?= $i === 0 ? ' video-featured' : '' ?>">
      <button aria-label="Открыть видео: <?= h($item['title']) ?>" class="gallery-preview"<?= $link !== '' ? ' data-video-src="' . h($link) . '" data-video-title="' . h($item['title']) . '"' : ' data-gallery-video=""' ?> type="button">
       <?php if( !empty($item['img']) ): ?><img alt="<?= h($item['title']) ?>" loading="lazy" src="/img/galleries/<?= h($item['img']) ?>"/><?php endif; ?>

       <span aria-hidden="true" class="gallery-play">
       </span>
       <span class="gallery-label">ВИДЕО</span>
      </button>
      <div class="gallery-copy">
       <div>
        <h3><?= h($item['title']) ?></h3>
       </div>
       <span aria-hidden="true"><?= $icArrowUp ?></span>
      </div>
     </article>
<?php endforeach; ?>
    </div>
<?php if( $noLink && $t('video_status') !== '' ): ?>
    <p class="gallery-status" hidden="" role="status"><?= $t('video_status') ?></p>
<?php endif; ?>
   </div>
  </section>
  <section aria-labelledby="specifications-title" class="section" id="specifications">
   <div class="container">
    <div class="layouts-heading">
     <div>
      <p class="eyebrow"><?= $t('spec_eyebrow') ?></p>
      <h2 id="specifications-title"><?= $t('spec_title') ?></h2>
     </div>
    </div>
    <div class="spec-grid">
<?php foreach( $characteristics as $item ): ?>
     <article class="spec-card">
      <?= $cardIcon($item, 'characteristics') ?>

      <h3><?= h($item['title']) ?></h3>
      <p><?= $item['body'] ?></p>
     </article>
<?php endforeach; ?>
    </div>
   </div>
  </section>
  <section aria-labelledby="advantages-title" class="section" id="advantages">
   <div class="container">
    <div class="layouts-heading">
     <div>
      <p class="eyebrow"><?= $t('adv_eyebrow') ?></p>
      <h2 id="advantages-title"><?= $t('adv_title') ?></h2>
     </div>
    </div>
    <div class="advantages-grid">
<?php foreach( ($preferences ?? []) as $item ): ?>
     <article class="advantage-item">
      <?= $cardIcon($item, 'preferences') ?>

      <h3><?= h(preg_replace('/\s*\R\s*/u', ' ', (string)$item['title'])) ?></h3>
     </article>
<?php endforeach; ?>
    </div>
    <form class="selection-form" data-lead-form="contact" data-topic="Заказать звонок" id="contact-form" novalidate="">
     <div>
      <h3><?= $t('btn_callback') ?></h3>
      <p><?= $t('form_lead') ?></p>
     </div>
     <div class="selection-fields">
      <label>
       Ваше имя
       <input autocomplete="given-name" name="name" placeholder="Как к вам обращаться" required=""/>
      </label>
      <label>
       Телефон
       <input autocomplete="tel" name="phone" inputmode="tel" placeholder="+7 (___) ___-__-__" required="" type="tel"/>
      </label>
      <button type="submit">
       <?= $t('btn_callback') ?>
       <?= $icPhone ?>
      </button>
     </div>
     <p hidden="" id="contact-result" role="status">
     </p>
    </form>
   </div>
  </section>
  <section aria-labelledby="company-title" class="section" id="company">
   <div class="container">
    <div class="layouts-heading">
     <div>
      <p class="eyebrow"><?= $t('company_eyebrow') ?></p>
      <h2 id="company-title"><?= $t('company_heading') ?></h2>
     </div>
    </div>
    <div class="company-grid">
     <div class="company-story">
      <h3><?= $t('company_title') ?></h3>
      <?= $t('company_text') ?>

     </div>
     <div class="company-facts">
<?php for( $n = 1; $n <= 3; $n++ ): ?>
      <article>
       <strong><?= $t('company_fact_' . $n . '_value') ?></strong>
       <span><?= $t('company_fact_' . $n . '_label') ?></span>
      </article>
<?php endfor; ?>
     </div>
    </div>
   </div>
  </section>
  <section aria-labelledby="social-title" class="section social-section" id="instagram">
   <div class="container">
    <div class="social-heading">
     <div>
      <p class="eyebrow"><?= $t('social_eyebrow') ?></p>
      <h2 class="section-title" id="social-title"><?= $t('social_title') ?></h2>
     </div>
     <a class="social-profile" href="<?= h($instagram) ?>" rel="noopener noreferrer" target="_blank">
      <?= $t('social_link') ?> <?= $icArrowUp ?>
     </a>
    </div>
    <div class="social-grid">
<?php $hasInstagramEmbed = false;
for( $n = 1; $n <= 4; $n++ ):
    $postImg = $cimg('social_post_' . $n);
    $post = \App\Service\InstagramPost::forSlot('social_post_' . $n, $t('social_post_' . $n));
    $caption = trim(strip_tags($post['caption']));
    if ($post['url'] !== ''):
        $hasInstagramEmbed = true;
?>
     <article class="social-embed" aria-label="Публикация Instagram <?= $n ?>">
      <blockquote class="instagram-media" data-instgrm-permalink="<?= h($post['url']) ?>" data-instgrm-version="14">
       <a href="<?= h($post['url']) ?>" target="_blank" rel="noopener noreferrer">Смотреть публикацию <?= h($handle) ?> в Instagram</a>
      </blockquote>
     </article>
<?php continue; endif;
    if( $postImg === '' ) continue;
?>
     <article class="social-card">
      <div class="instagram-header">
       <a aria-label="Профиль <?= h($handle) ?>" class="instagram-avatar" href="<?= h($instagram) ?>" rel="noopener noreferrer" target="_blank">
        <?= h(mb_strtoupper(mb_substr($handle, 0, 1))) ?>
       </a>
       <a class="instagram-account" href="<?= h($instagram) ?>" rel="noopener noreferrer" target="_blank">
        <?= h($handle) ?>
        <span>
         Instagram
        </span>
       </a>
       <a class="instagram-follow" href="<?= h($instagram) ?>" rel="noopener noreferrer" target="_blank">
        Профиль <?= $icArrowUp ?>
       </a>
      </div>
      <a aria-label="Открыть профиль <?= h($handle) ?> в Instagram" class="social-photo" href="<?= h($instagram) ?>" rel="noopener noreferrer" target="_blank">
       <img alt="<?= h($caption) ?>" height="900" loading="lazy" src="/img/comps/<?= h($postImg) ?>" width="720"/>
       <span aria-hidden="true" class="instagram-play">
       </span>
      </a>
      <div class="social-copy">
       <a class="instagram-more" href="<?= h($instagram) ?>" rel="noopener noreferrer" target="_blank">
        Ещё больше в Instagram <?= $icArrowUp ?>
       </a>
       <div aria-hidden="true" class="instagram-actions">
        <span style="display:flex;gap:18px">
         <svg fill="none" height="22" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" width="22">
          <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z">
          </path>
         </svg>
         <svg fill="none" height="22" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" width="22">
          <path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5 9 9 0 0 1-4-.9L3 21l1.9-5.5a9 9 0 0 1-.9-4A8.5 8.5 0 0 1 12.5 3H13a8.5 8.5 0 0 1 8 8v.5Z">
          </path>
         </svg>
         <svg fill="none" height="22" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" width="22">
          <path d="m22 2-7 20-4-9-9-4 20-7Z M22 2 11 13">
          </path>
         </svg>
        </span>
        <svg fill="none" height="22" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" width="22">
         <path d="M6 3h12v18l-6-4-6 4V3Z">
         </path>
        </svg>
       </div>
       <p class="instagram-caption">
        <strong>
         <?= h($handle) ?>
        </strong>
        <?= h($caption) ?>
       </p>
      </div>
     </article>
<?php endfor; ?>
    </div>
<?php if ($hasInstagramEmbed): ?>
    <script async src="https://www.instagram.com/embed.js"></script>
<?php endif; ?>
   </div>
  </section>
  <footer class="section site-footer" id="contacts">
   <div class="container">
    <div class="layouts-heading">
     <div>
      <p class="eyebrow"><?= $t('contacts_eyebrow') ?></p>
      <h2><?= $t('contacts_title') ?></h2>
     </div>
    </div>
    <div class="contacts-grid">
     <div class="contact-info">
      <p class="contact-label">
       ТЕЛЕФОН
      </p>
      <a class="contact-phone" href="tel:<?= h(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?= h($phone) ?></a>
      <p class="contact-label">
       ЭЛЕКТРОННАЯ ПОЧТА
      </p>
      <a class="contact-email" href="mailto:<?= h($email) ?>"><?= h($email) ?></a>
      <p class="contact-label">
       МЫ В СОЦИАЛЬНЫХ СЕТЯХ
      </p>
      <div class="contact-socials">
       <a href="<?= h($instagram) ?>" rel="noopener noreferrer" target="_blank">
        <svg aria-hidden="true" class="svg-icon" width="1.1em" height="1.1em" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".9" fill="currentColor" stroke="none"/></svg>
        Instagram
       </a>
       <a href="<?= h($whatsapp) ?>" rel="noopener noreferrer" target="_blank">
        <svg aria-hidden="true" class="svg-icon" width="1.1em" height="1.1em" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" viewBox="0 0 24 24"><path d="M20.5 3.5A11 11 0 0 0 3.2 16.7L1.7 22.3l5.7-1.5A11 11 0 0 0 20.5 3.5Z"/><path d="M8.2 6.8c-.3-.7-.6-.7-.9-.7h-.8c-.3 0-.7.1-1 .5-.4.4-1.3 1.3-1.3 3.1s1.3 3.5 1.5 3.7c.2.3 2.6 4 6.4 5.5.9.3 1.6.5 2.2.7.9.2 1.7.2 2.4.1.7-.1 2.2-.9 2.5-1.8.3-.9.3-1.6.2-1.8-.1-.2-.4-.3-.8-.5l-2.5-1.2c-.4-.2-.7-.3-1 .2l-1.1 1.3c-.2.3-.4.3-.8.1-1-.5-1.9-1-2.6-1.7-.8-.7-1.4-1.5-1.9-2.4-.2-.3 0-.5.2-.7l.6-.7.4-.7c.1-.2.1-.4 0-.6Z" transform="translate(2 1) scale(.8)" fill="currentColor" stroke="none"/></svg>
        WhatsApp
       </a>
      </div>
      <a class="button footer-call" data-modal-open="callback" href="#contact-form">
       <?= $t('btn_callback') ?>
       <?= $icPhone ?>
      </a>
     </div>
     <div class="contact-map">
<?php if( $mapUrl !== '' ): ?>
      <iframe src="<?= h(html_entity_decode($mapUrl)) ?>" title="МЖК Астана на карте" loading="lazy"></iframe>
<?php else: ?>
      <img alt="Местоположение МЖК Астана на карте" loading="lazy" width="488" height="392" src="/assets/images/location.webp"/>
<?php endif; ?>
     </div>
    </div>
    <div class="footer-bottom">
     <a aria-label="<?= h($brand) ?> — наверх" class="brand" href="#">
      <?= $icLogo ?>
      <span><?= h($brand) ?></span>
     </a>
     <p><?= $t('copyright') ?></p>
     <a class="footer-credit" href="https://astanacreative.kz/" target="_blank" rel="noopener noreferrer">Создание сайтов — <span>Astana Creative</span></a>
     <a href="#">
      Наверх <svg aria-hidden="true" class="svg-icon action-arrow" width="1.1em" height="1.1em" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" viewBox="0 0 24 24"><path d="M12 20V4m-6 6 6-6 6 6"/></svg>
     </a>
    </div>
   </div>
  </footer>
  <!-- Модальное окно «Заказать звонок». Открывается кнопками с атрибутом data-modal-open="callback";
       data-topic="…" — тема заявки, показывается в окне и уходит менеджеру. -->
  <dialog aria-labelledby="callback-title" class="modal" id="callback-modal">
   <div class="modal-card">
    <button aria-label="Закрыть" class="modal-close" data-modal-close="" type="button">
    </button>
    <form class="modal-form" data-lead-form="callback" novalidate="">
     <h2 class="modal-title" id="callback-title">
      <span>
       Бесплатная
      </span>
      консультация
     </h2>
     <p class="modal-lead">
      Ответим на вопросы, поможем выбрать квартиру и способ оплаты
     </p>
     <p class="modal-topic" hidden="">
     </p>
     <input name="topic" type="hidden"/>
     <label class="modal-field">
      <span>
       Ваше имя
      </span>
      <input autocomplete="given-name" minlength="2" name="name" placeholder="Как к вам обращаться" required=""/>
      <small class="field-error">
       Введите имя
      </small>
     </label>
     <label class="modal-field">
      <span>
       Телефон
      </span>
      <input autocomplete="tel" inputmode="tel" name="phone" placeholder="+7 (___) ___-__-__" required="" type="tel"/>
      <small class="field-error">
       Введите номер полностью
      </small>
     </label>
     <button class="button modal-submit" type="submit">
      Оставить заявку
      <span aria-hidden="true"><?= $icArrowR ?></span>
     </button>
     <p class="modal-consent">
      Оставляя заявку, вы соглашаетесь с
      <a href="/privacy-policy" target="_blank">
       политикой конфиденциальности и обработки данных
      </a>
     </p>
    </form>
    <div aria-live="polite" class="modal-success" hidden="">
     <svg aria-hidden="true" class="modal-success-icon" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 64 64">
      <circle cx="32" cy="32" r="29">
      </circle>
      <path d="M20 33l8 8 16-17">
      </path>
     </svg>
     <h2 class="modal-title">
      Спасибо!
     </h2>
     <p class="modal-success-text">
      Заявка принята. Менеджер перезвонит вам в ближайшее рабочее время.
     </p>
     <button class="button modal-done" data-modal-close="" type="button">
      Хорошо
     </button>
    </div>
   </div>
  </dialog>
