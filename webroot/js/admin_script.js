$( window ).on("load", function() {

  $('.mob-start').on('click', function () {
      console.log("click");
          if($('.mob-start').hasClass('mob-start--active')){
              $('.mob-start').removeClass('mob-start--active');
              $('.sidebar').removeClass('sidebar--active');
          }else{
              $('.mob-start').addClass('mob-start--active');
              $('.sidebar').addClass('sidebar--active');
          }                           
    });

  $('#marks_admin').on('change', function() {
      mark_id = this.value;
      $('#table').html("<tr><td>Загрузка данных</td></tr>");
      $.ajax({
          type: 'POST',
          url: '/extracts/get_model2/' + mark_id,
          data: 'id=' + mark_id,
          success: function(data){
            console.log(data);
           
            
            $('#table').html(data);
            $('.pagi').hide();

          }
      })

    });
  
});

// Admin Tabs

$('.admin_tab').on("click", function(){
  var index = $(this).index();

  if( !$(this).hasClass('active') ){
    $('.admin_tab.active').removeClass('active');
    $(this).addClass('active');
    $('.admin_tab_content.active').removeClass('active');
    $('.admin_tab_content').eq(index).addClass('active');
  }
});

// Admin Tabs END
 
  