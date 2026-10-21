<h1>Редактирование Админа</h1>

<?php 

echo $this->Form->create($data);

echo $this->Form->input('username', array('placeholder' => 'Логин'));
echo $this->Form->password('password', array('placeholder' => 'Пароль'));

echo $this->Form->submit('Редактировать');

echo $this->Form->end();
?>