<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Добро пожаловать!</h1>
      </div><!-- /.col -->
      
     <!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>

<div class="container-fluid">
	<div class="row">
	  <!-- Left col -->
	  <section class="col-lg-5 connectedSortable">
	    <!-- Custom tabs (Charts with tabs)-->
	    <div class="card">
	      <div class="card-header ui-sortable-handle">
	        <h3 class="card-title" class="col-sm-5">
	          <i class="fas fa-chart-pie mr-1"></i>
	          Статус системы
	        </h3>
	       	<div id="datetime" class="card-tools"></div>
	      </div><!-- /.card-header -->
	      <div class="card-body">
	        <div class="alert alert-success alert-dismissible">
              <h5><i class="icon fas fa-check"></i> Используется последняя версия</h5>
            </div>
            <div class="alert alert-success alert-dismissible">
              <h5><i class="icon fas fa-check"></i> Ошибок нет</h5>
            </div>
            <div class="alert alert-info  alert-dismissible">
              <h5><i class="icon fas fa-info"></i> Сборка</h5>
            </div>
            <div class="alert alert-info  alert-dismissible">
              <h5><i class="icon fas fa-info"></i> Лог событий</h5>
            </div>
            <div class="alert alert-info  alert-dismissible">
              <h5><i class="icon fas fa-info"></i> Лог запросов</h5>
            </div>

	      </div><!-- /.card-body -->
	    </div>
	    
	  </section>
	  <!-- /.Left col -->
	  <!-- right col (We are only adding the ID to make the widgets sortable)-->
	  <section class="col-lg-7 connectedSortable">

	    <!-- Map card -->
	    <div class="card ">
	      <div class="card-header ">
	        <h3 class="card-title">
	          <i class="fas fa-map-marker-alt mr-1"></i>
	          Веб сайт
	        </h3>
	       
	      </div>
	      <div class="card-body">
	        <!-- <img src="/img/admin_img/site_img.jpg" width="100%" alt=""> -->
	      </div>
	     
	    </div>
	    <!-- /.card -->

	   
	  </section>
	  <!-- right col -->
	</div>
</div>

<script>
var dt = new Date();
document.getElementById("datetime").innerHTML = (("0"+dt.getDate()).slice(-2)) +"."+ (("0"+(dt.getMonth()+1)).slice(-2)) +"."+ (dt.getFullYear()) +", "+ (("0"+dt.getHours()).slice(-2)) +":"+ (("0"+dt.getMinutes()).slice(-2));
</script>