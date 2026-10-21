// Формы, маска телефона и модальное окно — в assets/js/forms.js.

const planTabs = [...document.querySelectorAll('[data-plan-tab]')];
function activatePlan(tab) {
 planTabs.forEach(t => { const active=t===tab;t.setAttribute('aria-selected',active);t.tabIndex=active?0:-1;document.getElementById('plans-'+t.dataset.planTab).hidden=!active; });
}
planTabs.forEach((tab,index) => {
 tab.addEventListener('click',()=>activatePlan(tab));
 tab.addEventListener('keydown',event=> { if(['ArrowLeft','ArrowRight','Home','End'].includes(event.key)){event.preventDefault();const next=event.key==='Home'?0:event.key==='End'?1:1-index;activatePlan(planTabs[next]);planTabs[next].focus();} });
});
document.querySelectorAll('[data-plan]').forEach(button=>button.addEventListener('click',()=>{document.getElementById('selection-form').scrollIntoView({behavior:'smooth',block:'center'});document.querySelector('#selection-form input').focus({preventScroll:true});}));

document.querySelectorAll('[data-gallery-video]').forEach(button=>button.addEventListener('click',()=>{document.querySelector('.gallery-status').hidden=false;}));


// Слайдер первого экрана: автопрокрутка, стрелки, индикаторы, свайп, клавиатура.
// Чтобы добавить слайд, вставьте ещё один <img class="hero-slide"> внутрь .hero-slides.
(() => {
 const slider=document.querySelector('.hero-slider');
 const slides=[...slider.querySelectorAll('.hero-slide')];
 const prev=slider.querySelector('[data-slide-prev]');
 const next=slider.querySelector('[data-slide-next]');
 const dotsBox=slider.querySelector('.slider-dots');
 const pauseBtn=slider.querySelector('[data-slide-pause]');
 const INTERVAL=6000;
 const reduceMotion=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
 let current=0;
 let userPaused=reduceMotion;   // при «уменьшить движение» сами не листаем
 slider.style.setProperty('--slide-interval',INTERVAL+'ms');

 const dots=slides.map((slide,i)=>{
  slide.setAttribute('aria-roledescription','слайд');
  slide.setAttribute('aria-label',(i+1)+' из '+slides.length);
  const dot=document.createElement('button');
  dot.type='button';
  dot.className='slider-dot';
  dot.setAttribute('aria-label','Слайд '+(i+1));
  dot.innerHTML='<span></span>';
  dot.addEventListener('click',()=>show(i));
  // Конец заполнения полоски = переход к следующему слайду
  dot.firstChild.addEventListener('animationend',()=>{ if(i===current) show(current+1); });
  dotsBox.appendChild(dot);
  return dot;
 });

 function show(index){
  current=(index+slides.length)%slides.length;
  slides.forEach((slide,i)=>{
   const active=i===current;
   slide.classList.toggle('is-active',active);
   slide.setAttribute('aria-hidden',!active);
  });
  dots.forEach((dot,i)=>{
   dot.classList.remove('is-active');
   dot.setAttribute('aria-current',i===current?'true':'false');
  });
  void dotsBox.offsetWidth; // перезапуск анимации полоски
  dots[current].classList.add('is-active');
 }

 function setPaused(value){
  userPaused=value;
  slider.classList.toggle('is-paused',value);
  pauseBtn.setAttribute('aria-label',value?'Запустить автопрокрутку':'Остановить автопрокрутку');
  pauseBtn.classList.toggle('is-play',value);
 }

 const single=slides.length<2;
 prev.disabled=next.disabled=single;
 slider.classList.toggle('is-single',single);
 prev.addEventListener('click',()=>show(current-1));
 next.addEventListener('click',()=>show(current+1));
 pauseBtn.addEventListener('click',()=>setPaused(!userPaused));
 slider.addEventListener('keydown',event=>{
  if(event.key==='ArrowLeft'||event.key==='ArrowRight'){event.preventDefault();show(current+(event.key==='ArrowRight'?1:-1));}
 });

 // Свайп пальцем или мышью
 let startX=null,startY=0;
 const area=slider.querySelector('.hero-slides');
 area.addEventListener('pointerdown',e=>{startX=e.clientX;startY=e.clientY;});
 area.addEventListener('pointerup',e=>{
  if(startX===null)return;
  const dx=e.clientX-startX,dy=e.clientY-startY;
  if(Math.abs(dx)>50&&Math.abs(dx)>Math.abs(dy)) show(current+(dx<0?1:-1));
  startX=null;
 });
 area.addEventListener('pointercancel',()=>{startX=null;});
 area.addEventListener('dragstart',e=>e.preventDefault());

 // Не листаем, пока первый экран не виден
 if('IntersectionObserver' in window){
  new IntersectionObserver(([entry])=>slider.classList.toggle('is-offscreen',!entry.isIntersecting),{threshold:.25}).observe(slider);
 }

 setPaused(userPaused);
 show(0);
})();
