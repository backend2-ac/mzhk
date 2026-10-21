//img parallax
var image = document.querySelector('.entry__img');
new simpleParallax(image, {
  delay: 0.5,
  orientation: 'down',
  scale: 0.9,
  transition: 'linear'
});


//progress   
$(function () {
  var progress = $('.progress');
  var Status = true;
  $(window).scroll(function() {
      var scrollEvent = ($(window).scrollTop() > (progress.position().top - $(window).height()));
      if (scrollEvent && Status) { 
          Status = false;
          $(".progress__animated span").each(function () {
            $(this).animate(
              {
                width: $(this).attr("data-progress") + "%",
              },
              1000
            );
            //$('.progress__result').text($(this).attr("data-progress") + "%");
            var numb_start = $(".progress__result").text(); 
            var numb_end = $(this).attr("data-progress");
            $({numb_start}).animate({numb_end}, {
          
              duration: 1500,  
              easing: "linear",
              
              step: function(val) {
              
                $(".progress__result").html(Math.ceil(val) + "%");
                
              }
              
            });
          });
          
      }
      
  });
});


var scene = new ScrollMagic.Scene({triggerElement: ".trigger"})
.setClassToggle(".card", "leaving__card")
.addTo(controller);