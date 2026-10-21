<div class="login-box">
  <div class="login-logo">
    <div>Панель администратора</div>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Войдите, чтобы начать сеанс</p>

    <?php 
      echo $this->Form->create(null, [
        'url' => [
          'controller' => 'Admin',
          'action' => 'login'
        ]
      ]);
     ?>

        <div class="input-group mb-3">
          <?php echo $this->Form->text('username', array('class' => 'form-control', 'placeholder' => 'Логин')); ?>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <?php echo $this->Form->password('password', array('class' => 'form-control', 'placeholder' => 'Пароль')); ?>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
         
          <!-- /.col -->
          <div class="col-4">
             <?= $this->Form->button('Войти', array('class' => 'btn btn-primary btn-block mb-3'));?>
          
          </div>
          <!-- /.col -->
        </div>
        <?php echo $this->Form->end(); ?>
      <!-- /.social-auth-links -->
      <!-- <p class="mb-1">
        <a href="/admin/forgetpwd">Забыли пароль?</a>
      </p> -->
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
