<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Слайды</h1>
      </div>
      <div class="col-sm-6">
        
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<?php $langs = array('ru', 'kz', 'en'); ?>
<!-- Main content -->
<section class="content">

  <!-- Default box -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Слайды</h3>
      <div class="card-tools">
        <a href="/admin/slides/add" class="btn btn-success">Добавить новый материал</a>
      </div>
		
    </div>
    <div class="card-body p-0">
    <?php if(!empty($data)): ?>
      <table class="table table-striped projects">
        <thead>
            <tr>
                <th style="width: 1%">ID</th>
                <th style="width: 20%">Картинка</th>
                <th style="width: 20%">Картинка для телефона</th>
                <th style="width: 5%">Приоритет</th>
                <th style="width: 8%; text-align: right;">Редактирование</th>
            </tr>
        </thead>
        <tbody>
         	<?php foreach($data as $item): ?>
        		<tr>
        			<td>
        				<?=$item['id']?>
        			</td>
              <td>
                <img src="/img/slides/thumbs/<?= $item['img'] ?>" width="150" alt="">
        			</td>
              <td>
                <img src="/img/slides/thumbs/<?= $item['images'] ?>" width="150" alt="">
        			</td>
              <td>
        				<?= $item['item_order'] ?>
        			</td>
        			<td class="project-actions text-right">
        				<a class="btn btn-info btn-sm" href="/admin/slides/edit/<?=$item['id']?>?lang=ru">
                  <i class="fas fa-pencil-alt"></i>Редактировать
                </a>
        				<?php echo $this->Form->postLink('Удалить', "/admin/slides/delete/{$item['id']}", array('confirm' => 'Удалить Материал?', 'value'=>'465', 'class' => 'btn btn-danger btn-sm')) ?>
        			</td>
        		</tr>
        	<?php endforeach ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="emty_data">
        К сожалению в данном разделе еще не добавлена информация...
      </div> 
    <?php endif ?>
    </div>
  </div>

</section>
