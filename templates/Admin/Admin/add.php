<h1>Добавление Админа</h1>

 <?php 

echo $this->Form->create(null);

echo $this->Form->input('username', array('placeholder' => 'Логин'));
echo $this->Form->password('password', array('placeholder' => 'Пароль'));

echo $this->Form->submit('Добавить');

echo $this->Form->end();

 ?>