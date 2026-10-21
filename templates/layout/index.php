<?php

use Cake\Core\Configure;

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <?php if( isset($meta) && $meta['title'] ): ?>
    <title><?= $meta['title'] ?></title>
    <meta name="description" content="<?= $meta['desc'] ?>" />
    <meta name="keywords" content="<?= $meta['keys'] ?>" />
    <?php else: ?>
    <title>ТАУНХАУСЫ И МЖК АСТАНА<</title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <?php endif; ?>
    <link rel="stylesheet" href="/cssassel/font-awesome.min.css">
    <link rel="stylesheet" href="/cssassel/slick.min.css">
    <link rel="shortcut icon" href="/images/favicon.png">
    <link rel="stylesheet" href="/css/style.css?v=8.67876780">
    
    <!-- <meta name="facebook-domain-verification" content="peetgr6qapbfhz6vqzhmo13qhb6vos"> -->
    <meta name="facebook-domain-verification" content="49v9wnsw1ori6k05pubrarfmju25xg" />

    <!-- Meta Pixel Code -->
    <script async="" src="https://mc.yandex.ru/metrika/tag.js"></script>
    <script src="https://connect.facebook.net/signals/config/768462314168241?v=2.9.79&amp;r=stable" async=""></script>
    <script src="https://connect.facebook.net/signals/config/375571394414709?v=2.9.79&amp;r=stable" async=""></script>
    <script async="" src="https://connect.facebook.net/en_US/fbevents.js"></script>
    <script>
        ! function (f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function () {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '375571394414709');
        fbq('track', 'PageView');
    </script>
    <!-- Meta Pixel Code -->
    <script>
        ! function (f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function () {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '768462314168241');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=768462314168241&ev=PageView&noscript=1" /></noscript>
    <!-- End Meta Pixel Code -->
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=375571394414709&ev=PageView&noscript=1" /></noscript>
    <!-- End Meta Pixel Code -->

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function (m, e, t, r, i, k, a) {
            m[i] = m[i] || function () {
                (m[i].a = m[i].a || []).push(arguments)
            };
            var z = null;
            m[i].l = 1 * new Date();
            for (var j = 0; j < document.scripts.length; j++) {
                if (document.scripts[j].src === r) {
                    return;
                }
            }
            k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode
                .insertBefore(k, a)
        })
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(90049308, "init", {
            clickmap: true,
            trackLinks: true,
            accurateTrackBounce: true,
            webvisor: true
        });
    </script>
    <noscript>
        <div><img src="https://mc.yandex.ru/watch/90049308" style="position:absolute; left:-9999px;" alt="" /></div>
    </noscript>
    <!-- /Yandex.Metrika counter -->
    
    <link rel="stylesheet" href="/cssassel/font-awesome.min.css">
    <link rel="stylesheet" href="/cssassel/slick.min.css">
    <link rel="stylesheet" href="/cssassel/style.css?v=8.73">
    <link rel="stylesheet" href="/cssassel/main.css?v=8.63">
    <link rel="stylesheet" href="/cssassel/animate.css">
    <link rel="stylesheet" href="/cssassel/fancybox.css">
    <link rel="stylesheet" href="/cssassel/swiper-bundle.min.css">
    <script src="/jsassel/wow.min.js"></script>
    <script>
        new WOW().init();
    </script>
</head>

<body>

<?php
   $session = $this->request->getSession();
   $msg = $session->read('Flash.flash');
   $msg_type = '';
   if( $msg ){
       $msg_type = mb_substr($msg[0]['element'], 6);

       if( $msg_type == 'success' ){
           $msg_type = 'alert--active';
       } elseif( $msg_type == 'warning' ){
           $msg_type = 'alert--warning';
       } elseif( $msg_type == 'error' ){
           $msg_type = 'alert--error';
       }
   }
   ?>

   <div class="alert--fixed box-size <?= ( isset($msg) && $msg[0]['message'] && $msg_type) ? $msg_type : '' ?>">
       <div class="alert--content alert--width alert--block box-size">
           <div class="alert--img">
               <div class="alert--img__item active box-size">
                   <sv g version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                        viewBox="0 0 329.954 329.954" style="enable-background:new 0 0 329.954 329.954;" xml:space="preserve">
                       <path id="XMLID_89_" d="M99.85,299.045c2.813,2.813,6.629,4.393,10.607,4.393c3.979,0,7.794-1.581,10.606-4.393L325.56,94.548
                           c2.814-2.813,4.394-6.628,4.394-10.606c0-3.979-1.58-7.794-4.394-10.607l-42.427-42.426c-5.857-5.857-15.355-5.858-21.213,0
                           L110.461,182.37l-42.428-42.428c-2.813-2.813-6.629-4.394-10.607-4.394s-7.793,1.581-10.606,4.394L4.393,182.369
                           c-5.857,5.858-5.857,15.355,0,21.213L99.85,299.045z"/>
                   </svg>
               </div>
               <div class="alert--img__item warning box-size">
                   <sv g height="512pt" viewBox="0 -18 512.00043 512" width="512pt" xmlns="http://www.w3.org/2000/svg"><path d="m241 116h30v165h-30zm0 0"/>
                       <path d="m256 371c-8.277344 0-15 6.722656-15 15s6.722656 15 15 15 15-6.722656 15-15-6.722656-15-15-15zm0 0"/>
                       <path d="m502.53125 364.714844-180.890625-326.011719c-13.40625-24.230469-37.941406-38.703125-65.640625-38.703125s-52.234375 14.472656-65.640625 38.714844l-180.890625 325.988281c-12.976562 23.480469-12.597656 51.28125 1.027344 74.398437 13.621094 23.101563 37.777344 36.898438 64.597656 36.898438h361.8125c26.820312 0 50.976562-13.796875 64.597656-36.898438 13.625-23.117187 14.003906-50.917968 1.027344-74.386718zm-246.53125 66.285156c-24.8125 0-45-20.1875-45-45s20.1875-45 45-45 45 20.1875 45 45-20.1875 45-45 45zm45-120h-90v-225h90zm0 0"/>
                   </svg>
               </div>
               <div class="alert--img__item error box-size">

                   <sv g class="box-size" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                        viewBox="0 0 455.111 455.111" style="enable-background:new 0 0 455.111 455.111;" xml:space="preserve">
                       <circle style="fill:#E24C4B;" cx="227.556" cy="227.556" r="227.556"/>
                       <path style="fill:#D1403F;" d="M455.111,227.556c0,125.156-102.4,227.556-227.556,227.556c-72.533,0-136.533-32.711-177.778-85.333
                       c38.4,31.289,88.178,49.778,142.222,49.778c125.156,0,227.556-102.4,227.556-227.556c0-54.044-18.489-103.822-49.778-142.222
                       C422.4,91.022,455.111,155.022,455.111,227.556z"/>
                       <path style="fill:#FFFFFF;" d="M331.378,331.378c-8.533,8.533-22.756,8.533-31.289,0l-72.533-72.533l-72.533,72.533
                       c-8.533,8.533-22.756,8.533-31.289,0c-8.533-8.533-8.533-22.756,0-31.289l72.533-72.533l-72.533-72.533
                       c-8.533-8.533-8.533-22.756,0-31.289c8.533-8.533,22.756-8.533,31.289,0l72.533,72.533l72.533-72.533
                       c8.533-8.533,22.756-8.533,31.289,0c8.533,8.533,8.533,22.756,0,31.289l-72.533,72.533l72.533,72.533
                       C339.911,308.622,339.911,322.844,331.378,331.378z"/>
                   </svg>
               </div>
           </div>
           <div class="alert-text box-size">
               <div class="alert--title box-size">
                   <?= $msg[0]['message'] ?>
               </div>
               <!-- <div class="alert--subtitle box-size">
                   Subtitle of alert
               </div> -->
           </div>
           <div class="alert--x alert--close box-size">
               <sv g class="box-size" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                    viewBox="0 0 284.559 284.559" style="enable-background:new 0 0 284.559 284.559;" xml:space="preserve">
                   <path id="XMLID_90_" d="M4.394,237.739l42.427,42.427c2.812,2.813,6.629,4.394,10.606,4.394c3.978,0,7.794-1.581,10.606-4.394
                       l74.246-74.246l74.246,74.246c2.813,2.813,6.629,4.394,10.606,4.394c3.978,0,7.794-1.581,10.607-4.394l42.427-42.427
                       c5.858-5.858,5.858-15.355,0-21.213L205.92,142.28l74.245-74.247c2.814-2.813,4.394-6.628,4.394-10.606
                       c0-3.979-1.58-7.794-4.394-10.607L237.739,4.393c-5.857-5.858-15.356-5.858-21.213,0.001L142.28,78.639L68.033,4.394
                       c-5.857-5.858-15.356-5.858-21.213,0L4.394,46.82C1.58,49.633,0,53.448,0,57.426c0,3.978,1.58,7.793,4.394,10.606l74.245,74.247
                       L4.394,216.526C-1.465,222.384-1.465,231.881,4.394,237.739z"/>
               </svg>
           </div>
       </div>
       <div class="alert--bg alert--close box-size"></div>
       <?php if( isset($msg) && $msg[0]['message'] && $msg_type ){ $session->delete('Flash'); } ?>
   </div>

    <?= $this->element('header') ?>

    <?= $this->fetch('content') ?>

    <?= $this->element('footer') ?>
    

    <div class="newpopup">
        <p>наш менеджер</p>
        <p>Свяжется с вами</p>
        <?= $this->Form->create(null, array('action' => '/requests/send', 'onSubmit' => 'submitForm()')) ?>
            <input type="text" name="name"  placeholder="Введите имя" required>
            <input type="tel" id="phone-mask" name="phone" placeholder="Введите номер" required>
			<!--<div class="g-recaptcha"
				 data-sitekey="<?php echo Configure::read('Recaptcha.SiteKey'); ?>">
			</div>
			<?php echo $this->Html->script('https://www.google.com/recaptcha/api.js'); ?>-->
            <button type="submit">Оставить заявку 
                <svg width="17" height="9" viewBox="0 0 17 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 4C0.723858 4 0.5 4.22386 0.5 4.5C0.5 4.77614 0.723858 5 1 5V4ZM16.3536 4.85355C16.5488 4.65829 16.5488 4.34171 16.3536 4.14645L13.1716 0.964466C12.9763 0.769204 12.6597 0.769204 12.4645 0.964466C12.2692 1.15973 12.2692 1.47631 12.4645 1.67157L15.2929 4.5L12.4645 7.32843C12.2692 7.52369 12.2692 7.84027 12.4645 8.03553C12.6597 8.2308 12.9763 8.2308 13.1716 8.03553L16.3536 4.85355ZM1 5H16V4H1V5Z" fill="white"/>
                </svg>
            </button>
            <div style="margin-top: 30px; max-width: 300px;">
              <p>оставляя зяавку вы соглашаетесь на <a href="/privacy-policy" target="_blank" style="text-decoration: underline;">политику конфиденциальности и обработку данных</a></p>  
            </div>

        <?= $this->Form->end(); ?>
    </div>
    <div class="overlay"></div>


    <!--  -->

    <a href="tel:<?= $comps[10]['body'] ?>" class="newclassPhone1 newclassPhone11">
        <svg width="70" height="70" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="35" cy="35" r="35" fill="url(#paint0_linear_46_176)"></circle>
            <circle cx="35" cy="35" r="30" fill="##ff5d5d"></circle>
            <rect width="20" height="20" transform="translate(25 25)" fill="##ff5d5d"></rect>
            <path d="M30.5208 33.9958C31.7208 36.3542 33.65 38.2833 36.0125 39.4833L37.8458 37.6458C38.075 37.4167 38.4042 37.35 38.6917 37.4417C39.625 37.75 40.6292 37.9167 41.6667 37.9167C42.1292 37.9167 42.5 38.2875 42.5 38.75V41.6667C42.5 42.1292 42.1292 42.5 41.6667 42.5C33.8417 42.5 27.5 36.1583 27.5 28.3333C27.5 27.8708 27.875 27.5 28.3333 27.5H31.25C31.7125 27.5 32.0833 27.8708 32.0833 28.3333C32.0833 29.3708 32.25 30.375 32.5583 31.3083C32.65 31.5958 32.5833 31.925 32.3542 32.1542L30.5208 33.9958V33.9958Z" fill="#fff"></path>
            <defs>
                <linearGradient id="paint0_linear_46_176" x1="20" y1="1.5" x2="62" y2="79" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#fff"></stop>
                    <stop offset="1" stop-color="#064CD6" stop-opacity="0"></stop>
                </linearGradient>
            </defs>
        </svg>
    </a>
    


    <a href="<?= $comps[9]['body'] ?>" class="newclassPhone1 newclassPhone2" target="_blank">
       <img src="/img/unnamed.png" alt="">
    </a>




    <div class="popup form_loader" id="form_loader">
            <div class="form_loader_block">
                <div class="form_loader_animate"></div>
                <div class="form_loader_text" style="color: #000;">Пожалуйста, подождите</div>
            </div>
    </div>

    <script src="/jsassel/jquery-3.6.0.min.js"></script>
    <script src="/jsassel/slick.min.js"></script>
    <script src="/jsassel/instafeed.min.js"></script>
    <script src="/js/imask.js"></script>
    <script src="/jsassel/scripta.js?v=8.69"></script>
    <script src="/js/lazyload.min.js"></script>
    <script>
        var lazyLoadInstance = new LazyLoad({
            elements_selector: ".lazy"
        });
    </script>
    <script src="/js/jquery.maskedinput.min.js"></script>
    <script src="/js/script.js?v=2.129"></script>
    <script src="/js/jquery.fancybox.min.js"></script>
    <!-- <script src="/js/framework.js"></script> -->
    <!-- <script src="/js/framework.extras.js"></script> -->
    <!-- <link rel="stylesheet" property="stylesheet" href="/css/framework.extras.css"> -->

    <div class="alert" id="main_alert">
        <div class="container">
            <div id="goodMessage" class="message">Сохранено</div>
            <div class="my-alert__close"></div>
        </div>
    </div>
    <div class="stripe-loading-indicator loaded">
        <div class="stripe"></div>
        <div class="stripe-loaded"></div>
    </div>

</html>
