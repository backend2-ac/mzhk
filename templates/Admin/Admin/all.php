<h1>Список Админов</h1>

<a class="btn btn--add" href="/admin/add">Добавить Админа</a>
<br><br>

<?php if($admins): ?>
	<table>
		<thead>
			<tr>
				<th>ID</th>
				<th>Логин</th>
				<th>Роль</th>
				<th>Редактирование</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $admins as $item ): ?>
			<tr>
				<td>
					<?php echo $item['id'] ?>
				</td>
				<td>
					<?= $item['username'] ?>
				</td>
				<td>
					<?= $item['role'] ?>
				</td>
				<td>
					<a href="/admin/edit/<?php echo $item['id'] ?>">Редактировать</a> |
					<?php  echo $this->Form->postLink('Удалить', "/admin/delete/{$item['id']}", array('confirm' => 'Удалить Админа?')) ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>