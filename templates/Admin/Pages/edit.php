<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1>Редактирование</h1>
			</div>
		</div>
	</div>
</section>

 <section>
	<div class="row">
		<div class="col-12 col-sm-12">
			<div class="card card-primary card-tabs">
				<div class="card-header p-0 pt-1">
					<ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="custom-tabs-one-main-tab" data-toggle="pill" href="#custom-tabs-one-main" role="tab" aria-controls="custom-tabs-one-main" aria-selected="true">Данные</a>
						</li>
						<?php if( $data['id'] == 2 ): ?>
							<li class="nav-item">
								<a class="nav-link" id="custom-tabs-one-aboutitems-tab" data-toggle="pill" href="#custom-tabs-one-aboutitems" role="tab" aria-controls="custom-tabs-one-aboutitems" aria-selected="true">Цифры о компании</a>
							</li>
						<?php endif; ?>
						<?php if( $data['id'] == 7 || $data['id'] == 9 ): ?>
							<li class="nav-item">
								<a class="nav-link" id="custom-tabs-one-docs-tab" data-toggle="pill" href="#custom-tabs-one-docs" role="tab" aria-controls="custom-tabs-one-docs" aria-selected="true">Документы</a>
							</li>
						<?php endif; ?>
					</ul>
				</div>

				<div class="card-body">
					<div class="tab-content" id="custom-tabs-one-tabContent">
						<div class="tab-pane fade show active" id="custom-tabs-one-main" role="tabpanel" aria-labelledby="custom-tabs-main-tab">
							<?php echo $this->Form->create($data, ['type' => 'file', 'onsubmit' => 'submitForm()']); ?>
								<div class="card-body">

									<div class="form-group">
										<label for="inputTitle">Название</label>
										<?= $this->Form->text('title', array('id' => 'inputTitle', 'class' => 'form-control', 'required' => 'required')); ?>
									</div>

									<div class="submit_row">
										<?php echo $this->Form->button('Сохранить', array('class' => 'btn btn-success')); ?>
									</div>
								</div>
								<div class="col-md-12">
									<div class="card card-secondary">
										<div class="card-header">
											<h3 class="card-title">SEO</h3>
											<div class="card-tools">
												<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
													<i class="fas fa-minus"></i>
												</button>
											</div>
										</div>
										<div class="card-body">
											<div class="form-group">
												<label for="seoMetaTitle">Мета заголовок</label>
												<?php echo $this->Form->text('meta_title', array('class' => 'form-control', 'id' => 'seoMetaTitle')); ?>
											</div>
											<div class="form-group">
												<label for="seoKeywords">Ключевые слова</label>
												<?php echo $this->Form->text('meta_keywords', array('class' => 'form-control', 'id' => 'seoKeywords')); ?>
											</div>
											<div class="form-group">
												<label for="seoDescription">Описание</label>
												<?php echo $this->Form->textarea('meta_description', array('class' => 'form-control', 'id' => 'seoDescription')); ?>
											</div>
											
											<div class="submit_row">
												<?php echo $this->Form->button('Сохранить', array('class' => 'btn btn-success')); ?>
											</div>
										</div>
									</div>
								</div>
							<?php echo $this->Form->end(); ?>
						</div>

						<?php if( $data['id'] == 2 ): ?>
							<div class="tab-pane fade show" id="custom-tabs-one-aboutitems" role="tabpanel" aria-labelledby="custom-tabs-aboutitems-tab">
								<div class="card-body">
									<?php echo $this->Form->create(null, [
										'url' => ['controller' => 'AboutItems', 'action' => 'add'], 
										'type' => 'file', 'onsubmit' => 'submitForm()',
										]); ?>

										<div class="form-group">
											<label for="inputOrder">Приоритет</label>
											<?= $this->Form->number('item_order', array('id' => 'inputOrder', 'class' => 'form-control', 'value' => 0)); ?>
										</div>
										<div class="form-group">
											<label for="inputNum">Цифра</label>
											<?= $this->Form->number('num', array('id' => 'inputNum', 'class' => 'form-control', 'required')); ?>
										</div>
										<div class="form-group">
											<label for="inputTitle">Название</label>
											<?= $this->Form->text('title', array('id' => 'inputTitle', 'class' => 'form-control', 'required')); ?>
										</div>
										<div class="form-group">
											<label for="inputBody">Описание</label>
											<?= $this->Form->text('body', array('id' => 'inputBody', 'class' => 'form-control')); ?>
										</div>

										<div class="submit_row">
											<?php echo $this->Form->button('Добавить', array('class' => 'btn btn-success')); ?>
										</div>
									<?php echo $this->Form->end(); ?>
								</div>
								<hr>
								<div class="card-body p-0">
									<?php if(!empty($about_items)): ?>
										<table class="table table-striped projects">
											<thead>
												<tr>
													<th style="width: 1%">ID</th>
													<th style="width: 2%">Цифра</th>
													<th style="width: 8%">Название</th>
													<th style="width: 8%">Описание</th>
													<th style="width: 5%">Приоритет</th>
													<th style="width: 8%; text-align: right;">Редактирование</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($about_items as $item): ?>
													<tr>
														<td>
															<b><?=$item['id']?></b>
														</td>
														<td>
															<?=$item['num']?>
														</td>
														<td>
															<?=$item['title']?>
														</td>
														<td>
															<?=$item['body']?>
														</td>
														<td>
															<?= $item['item_order'] ?>
														</td>
														<td class="project-actions text-right">
															<a class="btn btn-info btn-sm" href="/admin/about_items/edit/<?=$item['id']?>">
																<i class="fas fa-pencil-alt"></i>редактировать
															</a>
															<?php  echo $this->Form->postLink('Удалить', "/admin/about_items/delete/{$item['id']}", array('confirm' => 'Удалить Материал?', 'value'=>'465', 'class' => 'btn btn-danger btn-sm')) ?>
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
						<?php endif; ?>
						
						<?php if( $data['id'] == 7 || $data['id'] == 9 ): ?>
							<div class="tab-pane fade show" id="custom-tabs-one-docs" role="tabpanel" aria-labelledby="custom-tabs-docs-tab">
								<div class="card-body">
									<?php echo $this->Form->create(null, [
										'url' => ['controller' => 'Documents', 'action' => 'add'], 
										'type' => 'file', 'onsubmit' => 'submitForm()',
										]); ?>

										<div class="form-group">
											<label for="inputTitle">Название</label>
											<?= $this->Form->text('title', array('id' => 'inputTitle', 'class' => 'form-control', 'required' => 'required')); ?>
										</div>
										<div class="form-group">
											<label for="reviewDoc">Документ</label>
											<div class="input-group">
												<div class="custom-file">
													<?= $this->Form->input('doc', array('class' => 'custom-file-input', 'id' => 'reviewDoc', 'type' => 'file', 'required', 'accept' => 'application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document' )); ?>
													<label class="custom-file-label" for="reviewDoc"></label>
												</div>
											</div>
										</div>

										<?= $this->Form->text('page_id', array('type' => 'hidden', 'value' => $data['id'])); ?>

										<div class="submit_row">
											<?php echo $this->Form->button('Добавить', array('class' => 'btn btn-success')); ?>
										</div>
									<?php echo $this->Form->end(); ?>
								</div>
								<hr>
								<div class="card-body p-0">
									<?php if(!empty($data['documents'])): ?>
										<table class="table table-striped projects">
											<thead>
												<tr>
													<th style="width: 1%">ID</th>
													<th style="width: 15%">Название</th>
													<th style="width: 5%">Файл</th>
													<th style="width: 8%; text-align: right;">Редактирование</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($data['documents'] as $item): ?>
													<tr>
														<td>
															<?=$item['id']?>
														</td>
														<td>
															<?=$item['title']?>
														</td>
														<td>
															<?php if( $item['doc'] ): ?>
																<a class="btn btn-info btn-sm" href="/files/docs/<?= $item['doc'] ?>" target="_blank">Просмотр</a>
															<?php else: ?>
																-
															<?php endif; ?>
														</td>
														<td class="project-actions text-right">
															<a class="btn btn-info btn-sm" href="/admin/documents/edit/<?=$item['id']?>">
																<i class="fas fa-pencil-alt"></i>Редактировать
															</a>
															<?php  echo $this->Form->postLink('Удалить', "/admin/documents/delete/{$item['id']}", array('confirm' => 'Удалить Материал?', 'value'=>'465', 'class' => 'btn btn-danger btn-sm')) ?>
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
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

