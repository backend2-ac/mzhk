
<?php $editor_id = [3, 11]; ?>

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
					<div class="card-tools">
						<a href="/admin/comps" type="button" class="btn btn-tool">
							<i class="fas fa-arrow-left"></i>
						</a>
					</div>
				</div>
				<div class="card-body">

					<div class="form-group">
						<label for="inputTitle">Название</label>
						<?php if( isset($_GET['lang']) && $_GET['lang'] == 'ru' ): ?>
							<?= $this->Form->text('title', array('id' => 'inputTitle', 'class' => 'form-control', 'required' => 'required')); ?>
						<?php else: ?>
							<?= $this->Form->text('title', array('id' => 'inputTitle', 'class' => 'form-control', 'required' => 'required', 'disabled' => 'disabled')); ?>
						<?php endif; ?>
					</div>

					<?php if( !$data['img'] ): ?>
						<?php if( in_array($data['id'], $editor_id) ): ?>
							<div class="form-group">
								<label for="editor">Описание</label>
								<?= $this->Form->textarea('body', array('class' => 'form-control', 'id' => 'editor')); ?>
							</div>
						<?php else: ?>
							<div class="form-group">
								<label for="inputBody">Описание</label>
								<?= $this->Form->textarea('body', array('class' => 'form-control', 'id' => 'inputBody')); ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>


					<?php if( isset($_GET['lang']) && $_GET['lang'] == 'ru' && $data['img'] ): ?>
						<div class="form-group">
							<label for="reviewimg">Картинка  </label>
							<?php if(!empty($data['img'])): ?>
								<div class="model_info_img">
									<div class="model_item_container">
										<div class="model_item">
											<img src="/img/comps/thumbs/<?=$data['img']?>">
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

					<?php endif; ?>

					<div class="submit_row">
						<?php echo $this->Form->button('Сохранить', array('class' => 'btn btn-success')); ?>
				    </div>
				</div>
				<!-- /.card-body -->
			</div>
			<!-- /.card -->
		</div>
	</div>
</section>
<?php echo $this->Form->end(); ?>


<?php if( in_array($data['id'], $editor_id) ): ?>
	<script type="text/javascript">
	    CKEDITOR.replace( 'editor' );
	</script>
<?php endif; ?>


