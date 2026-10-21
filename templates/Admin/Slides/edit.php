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
                    
                    <!-- <div class="form-group">
                        <label for="inputBody">Описание #1</label>
                        <?= $this->Form->textarea('body', array('id' => 'inputBody', 'class' => 'form-control')); ?>
                    </div>
                    <div class="form-group">
                        <label for="inputBody_1">Описание #2</label>
                        <?= $this->Form->textarea('body_1', array('id' => 'inputBody_1', 'class' => 'form-control')); ?>
                    </div> -->
					<div class="form-group">
                        <label for="reviewimg">Баннер для светлой темы (1920×700)</label>
                        <?php if(!empty($data['img'])): ?>
                        <div class="model_info_img">
                            <div class="model_item_container">
                                <div class="model_item">
                                    <img src="/img/slides/thumbs/<?=$data['img']?>">
                                </div>
                            </div>
                        </div>
                        <?php endif ?>
                        <div class="input-group">
                            <div class="custom-file">
                                <?= $this->Form->input('img', array('class' => 'custom-file-input', 'id' => 'reviewimg', 'type' => 'file', 'accept' => 'image/jpeg, image/png, image/jpg, image/gif')); ?>
                                <label class="custom-file-label" for="reviewimg"></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
						<label for="reviewimg">Баннер для тёмной темы (1920×700)</label>
						<?php if(!empty($data['images'])): ?>
						<div class="model_info_img">
							<div class="model_item_container">
								<div class="model_item">
									<img src="/img/slides/thumbs/<?=$data['images']?>">
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
						<label for="input_body">Описание баннера (alt-текст)</label>
						<?= $this->Form->textarea('body', array('id' => 'input_body', 'class' => 'form-control')); ?>
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
    CKEDITOR.replace('inputBody');
</script>
<script type="text/javascript">
    CKEDITOR.replace('inputBody_1');
</script>