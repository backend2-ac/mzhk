
<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo $this->fetch('title'); ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/css/admin/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/css/admin/ionicons.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="/css/admin/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/css/admin/adminlte.min.css?v=1.13">
    <link rel="stylesheet" href="/js/admin/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <?php
        echo $this->Html->meta('icon');
        if(isset($login) && !empty($login)){
            // echo $this->Html->css(array('cake.generic'));
        }

        echo $this->Html->script('ckeditor/ckeditor.js');
        echo $this->fetch('meta');
    ?>
</head>
<?php if(isset($login) && !empty($login)): ?>
    <body class="hold-transition sidebar-mini layout-fixed">
        <!-- Site wrapper -->
        <div class="wrapper">
          <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="javascript:;" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="/" class="nav-link">Перейти на сайт</a>
                    </li>
                </ul>
            </nav>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4" >
               
                <a href="/" class="brand-link">
                  <img src="/img/admin_img/admin_logo.svg" alt="AdminLTE Logo" class="brand-image">
                </a>

                <!-- Sidebar -->
                <div class="sidebar">
                  <!-- Sidebar user (optional) -->
                  <?php $cur_admin = $this->request->getSession()->read('Auth.User.role') ?>
                  <?php $cur_admin_name = $this->request->getSession()->read('Auth.User.username') ?>

                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            <img src="/img/admin_img/technical-support.svg" class="img-circle " alt="User Image">
                        </div>
                        <div class="info">
                            <div class="d-block" style="color:#fff"><?php echo $cur_admin_name ?></div>
                        </div>
                    </div>

                    <!-- Sidebar Menu -->
                    <?php if( $cur_admin == 'admin' ): ?>
                        <nav class="mt-2">
                            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                                <!-- Add icons to the links using the .nav-icon class
                                with font-awesome or any other icon font library -->

                                <li class="nav-item">
                                    <a href="/admin" class="nav-link">
                                        <i class="nav-icon fas fa-address-card"></i>
                                        <p>Панель администратора</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="/admin/requests?type=request" class="nav-link">
                                        <i class="nav-icon fa fa-clipboard-list"></i>
                                        <p>Заявки</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/admin/houses" class="nav-link">
                                        <i class="nav-icon fa fa-home"></i>
                                        <p>Планировки</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="/admin/slides" class="nav-link">
                                        <i class="nav-icon fa fa-th"></i>
                                        <p>Слайды</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/admin/advans" class="nav-link">
                                        <i class="nav-icon fa fa-th"></i>
                                        <p>О комплексе</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/admin/infrastructures" class="nav-link">
                                        <i class="nav-icon fa fa-th"></i>
                                        <p>Инфраструктура</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="/admin/galleries" class="nav-link">
                                        <i class="nav-icon fa fa-th"></i>
                                        <p>Галерея</p>
                                    </a>
                                </li>
                             
                                <li class="nav-item">
                                    <a href="/admin/characteristics" class="nav-link">
                                        <i class="nav-icon fa fa-th"></i>
                                        <p>Харатеристики</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="/admin/preferences" class="nav-link">
                                        <i class="nav-icon fa fa-th"></i>
                                        <p>Преимущество</p>
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a href="/admin/services" class="nav-link">
                                        <i class="nav-icon fa fa-th"></i>
                                        <p>Условия приобритения жилья</p>
                                    </a>
                                </li>


                                <li class="nav-item">
                                    <a href="/admin/comps" class="nav-link">
                                        <i class="nav-icon fa fa-cog"></i>
                                        <p>Элементы</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/admin/logout" class="nav-link">
                                        <i class="nav-icon fas fa-sign-out-alt"></i>
                                        <p>Выход</p>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                    <!-- /.sidebar-menu -->
                </div>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <!-- Main content -->
                <section class="content">
                    <?php echo $this->Flash->render(); ?>
                    <?php echo $this->fetch('content'); ?>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
            <footer class="main-footer">
                <strong>Разработка сайтов в   <a href="https://astanacreative.kz/">AstanaCreative.kz</a>.</strong> 
            </footer>
        </div>
        <!-- ./wrapper -->

        <div class="submit_preloader" id="form_submit">
            <div class="loader_block">
                <div class="loader_gif"></div>
                <div class="loader_text">Идет загрузка</div>
            </div>
        </div>
        
        <script src="/js/admin/jquery/main.js"></script>
        <!-- jQuery -->
        <script src="/js/admin/jquery/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="/js/admin/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- overlayScrollbars -->
        <script src="/js/admin/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
        <!-- AdminLTE App -->
        <script src="/js/admin/adminlte.min.js"></script>
        <script src="/js/admin/bs-custom-file-input/bs-custom-file-input.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="/js/admin/inputmask/min/jquery.inputmask.bundle.min.js"></script>
        <!-- date-range-picker -->
        <script src="/js/admin/moment/moment-with-locales.min.js"></script>
        <script src="/js/admin/daterangepicker/daterangepicker.js"></script>
        <script src="/js/admin/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
        <script src="/js/admin/demo.js"></script>
        <script type="text/javascript">
            function submitForm(){
                $('#form_submit').show();
            }

            $(document).ready(function () {
              bsCustomFileInput.init();
            });
            $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
            //Datemask2 mm/dd/yyyy
            $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
            //Money Euro
            $('[data-mask]').inputmask()

            //Date range picker
            $('#reservationdate').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'ru'
            });
            $('#reservationdate_end').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'ru'
            });
            $('#vek_begin_date').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'ru'
            });
            $('#vek_end_date').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'ru'
            });
            $('#meeting_date').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'ru'
            });
            $('#reservationdate_prog').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'ru'
            });
            $('#reservationdate_end_prog').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'ru'
            });
            $('#vek_begin_date_prog').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'ru'
            });
            $('#vek_end_date_prog').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'ru'
            });
            
        </script>
    </body>

<?php else: ?>

    <div class="login-page">
        <div class="form">
            <?php echo $this->Flash->render(); ?>
            <?php echo $this->fetch('content'); ?>
        </div>
    </div>
<?php endif ?>

</html>
