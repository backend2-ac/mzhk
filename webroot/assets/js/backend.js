// Видео из админки: клик по превью подменяет его плеером YouTube / произвольным iframe.
(() => {
  function embedUrl(url) {
    const id = (url.match(/(?:v=|youtu\.be\/|embed\/|shorts\/)([\w-]{11})/) || [])[1];
    const base = id ? 'https://www.youtube.com/embed/' + id : url;
    return base + (base.includes('?') ? '&' : '?') + 'autoplay=1&rel=0';
  }
  function play(host) {
    if (host.classList.contains('is-playing')) return;
    const frame = document.createElement('iframe');
    frame.src = embedUrl(host.dataset.videoSrc);
    frame.title = host.dataset.videoTitle || 'Видео';
    frame.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
    frame.allowFullscreen = true;
    host.classList.add('is-playing');
    host.querySelectorAll('img, .play, .caption, .gallery-play, .gallery-label').forEach(el => { el.hidden = true; });
    host.appendChild(frame);
  }
  document.querySelectorAll('[data-video-src]').forEach(host => {
    host.addEventListener('click', () => play(host));
    host.addEventListener('keydown', e => {
      if ((e.key === 'Enter' || e.key === ' ') && host.getAttribute('role') === 'button') { e.preventDefault(); play(host); }
    });
  });
})();
