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
						<a href="/admin/advans" type="button" class="btn btn-tool">
							<i class="fas fa-arrow-left"></i>
						</a>
					</div>
				</div>
				<div class="card-body">
					<div class="form-group">
						<label for="inputOrder">Приоритет</label>
						<?= $this->Form->number('item_order', array('id' => 'inputOrder', 'class' => 'form-control', 'value' => 0)); ?>
					</div>

					<div class="form-group">
						<label for="inputTitle">Название</label>
						<?= $this->Form->textarea('title', array('id' => 'inputTitle', 'class' => 'form-control', 'required')); ?>
					</div>
					<!-- <div class="form-group">
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
						<label for="reviewimg">Картинка</label>
						<div class="input-group">
							<div class="custom-file">
								<?= $this->Form->input('img', array('class' => 'custom-file-input', 'id' => 'reviewimg', 'type' => 'file', 'accept' => 'image/jpeg, image/png, image/jpg, image/gif')); ?>
								<label class="custom-file-label" for="reviewimg"></label>
							</div>
						</div>
					</div>

					<!-- <div class="form-group">
                        <label for="reviewimg">Обложка видео</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <?= $this->Form->input('images', array('class' => 'custom-file-input', 'id' => 'reviewimg', 'type' => 'file', 'accept' => 'image/jpeg, image/png, image/jpg, image/gif')); ?>
                                <label class="custom-file-label" for="reviewimg"></label>
                            </div>
                        </div>
                    </div> -->

					<div class="form-group">
						<label for="input_body">Описание</label>
						<?= $this->Form->textarea('body', array('id' => 'input_body', 'class' => 'form-control')); ?>
					</div>
					<div class="form-group">
						<label for="input_icon">SVG-иконка</label>
						<?= $this->Form->textarea('icon', array('id' => 'input_icon', 'class' => 'form-control', 'rows' => 6)); ?>
						<small class="form-text text-muted">Вставьте готовый &lt;svg …&gt;…&lt;/svg&gt; (stroke="currentColor"). Если пусто — используется загруженная картинка.</small>
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

<!-- <script type="text/javascript">
    CKEDITOR.replace( 'inputBody' );
</script> -->
