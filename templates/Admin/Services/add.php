<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1>Добавление</h1>
			</div>
		</div>
	</div><!-- /.container-fluid -->
</section>

<?php echo $this->Form->create(null, ['type' => 'file', 'onsubmit' => 'submitForm()']); ?>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="card card-primary">
				<div class="card-header">
					<h3 class="card-title">Данные</h3>
					<div class="card-tools">
						<a href="/admin/slides" type="button" class="btn btn-tool">
							<i class="fas fa-arrow-left"></i>
						</a>
					</div>
				</div>
				<div class="card-body">
					<div class="form-group">
						<label for="inputOrder">Приоритет</label>
						<?= $this->Form->number('item_order', array('id' => 'inputOrder', 'class' => 'form-control', 'value' => 0)); ?>
					</div>
<!-- 
					<div class="form-group">
						<label for="inputTitle">Название</label>
						<?= $this->Form->text('title', array('id' => 'inputTitle', 'class' => 'form-control', 'required')); ?>
					</div>
					<div class="form-group">
						<label for="inputBody">Описание</label>
						<?= $this->Form->textarea('body', array('id' => 'inputBody', 'class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<label for="inputTitle">Название видео</label>
						<?= $this->Form->text('name', array('id' => 'inputTitle', 'class' => 'form-control', 'required')); ?>
					</div>
					<div class="form-group">
						<label for="inputTitle">Ссылка</label>
						<?= $this->Form->text('link', array('id' => 'inputTitle', 'class' => 'form-control', 'required')); ?>
					</div> -->
					<div class="form-group">
						<label for="inputTitle">Условия (текст / HTML)</label>
						<?= $this->Form->textarea('title', array('id' => 'inputTitle', 'class' => 'form-control', 'required')); ?>
					</div>
					<div class="form-group">
						<label for="inputTitle">Цена (например, 290 000)</label>
						<?= $this->Form->text('number', array('id' => 'inputTitle', 'class' => 'form-control', 'required')); ?>
					</div>

					<div class="form-group">
						<label for="reviewimg">Картинка заднего фона</label>
						<div class="input-group">
							<div class="custom-file">
								<?= $this->Form->input('img', array('class' => 'custom-file-input', 'id' => 'reviewimg', 'type' => 'file', 'accept' => 'image/jpeg, image/png, image/jpg, image/gif')); ?>
								<label class="custom-file-label" for="reviewimg"></label>
							</div>
						</div>
					</div>

					<div class="form-group">
                        <label for="reviewimg">Логотип</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <?= $this->Form->input('images', array('class' => 'custom-file-input', 'id' => 'reviewimg', 'type' => 'file', 'accept' => 'image/jpeg, image/png, image/jpg, image/gif')); ?>
                                <label class="custom-file-label" for="reviewimg"></label>
                            </div>
                        </div>
                    </div>

					<div class="form-group">
						<label for="input_name">Название тарифа (например, Рассрочка 50/50)</label>
						<?= $this->Form->text('name', array('id' => 'input_name', 'class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<label for="input_subtitle">Подзаголовок (например, на 3 года)</label>
						<?= $this->Form->text('subtitle', array('id' => 'input_subtitle', 'class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<label for="input_unit">Единица цены (например, ₸/м²)</label>
						<?= $this->Form->text('unit', array('id' => 'input_unit', 'class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<label for="input_motif">Крупная надпись на карточке (например, 50/50)</label>
						<?= $this->Form->text('motif', array('id' => 'input_motif', 'class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<label for="input_tags">Теги (по одному в строке)</label>
						<?= $this->Form->textarea('tags', array('id' => 'input_tags', 'class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<label for="input_topic">Тема заявки при клике на стрелку</label>
						<?= $this->Form->text('topic', array('id' => 'input_topic', 'class' => 'form-control')); ?>
					</div>
					<div class="submit_row">
						<?php echo $this->Form->button('Добавить', array('class' => 'btn btn-success')); ?>
				    </div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php echo $this->Form->end(); ?>

<script type="text/javascript">
    CKEDITOR.replace( 'inputTitle' );
</script> 
