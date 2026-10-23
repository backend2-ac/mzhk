<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Редактирование</h1>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<?php echo $this->Form->create($data, ['type' => 'file', 'onsubmit' => 'submitForm()']); ?>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Данные</h3>

                </div>

                <div class="card-body">
                <div class="form-group">
						<label for="inputOrder">Приоритет</label>
						<?= $this->Form->number('item_order', array('id' => 'inputOrder', 'class' => 'form-control')); ?>
					</div>
                    <div class="form-group">
						<label for="inputTitle">Условия (текст / HTML)</label>
						<?= $this->Form->textarea('title', array('id' => 'inputTitle', 'class' => 'form-control', 'required')); ?>
					</div>
					<div class="form-group">
						<label for="inputTitle">Цена (например, 290 000)</label>
						<?= $this->Form->text('number', array('id' => 'inputTitle', 'class' => 'form-control', 'required')); ?>
					</div>
                    <div class="form-group">
                        <label for="reviewimg">Картинка</label>
                        <div class="model_info_img">
                            <div class="model_item_container">
                                <div class="model_item">
                                    <?= $this->element('admin/media_preview', ['item' => $data, 'directory' => 'services']) ?>
                                </div>
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="custom-file">
                                <?= $this->Form->input('img', array('class' => 'custom-file-input', 'id' => 'reviewimg', 'type' => 'file', 'accept' => 'image/jpeg, image/png, image/jpg, image/gif')); ?>
                                <label class="custom-file-label" for="reviewimg"></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
						<label for="reviewimg">Логотип</label>
						<?php if(!empty($data['images'])): ?>
						<div class="model_info_img">
							<div class="model_item_container">
								<div class="model_item">
									<img src="/img/services/thumbs/<?=$data['images']?>">
								</div>
							</div>
						</div>
						<?php endif ?>
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
                        <?php echo $this->Form->button('Сохранить', array('class' => 'btn btn-success')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php echo $this->Form->end(); ?>

<script type="text/javascript">
    CKEDITOR.replace('inputTitle');

</script>
<script type="text/javascript">
    CKEDITOR.replace('inputBody_1');

</script>
