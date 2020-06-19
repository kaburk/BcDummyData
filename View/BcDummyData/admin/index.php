<?php echo $this->BcForm->create('BcDummyData', ['type' => 'file']) ?>

	<section class="bca-section">
		<table id="FormTable" class="form-table bca-form-table">
			<tbody>
				<tr>
					<th class="col-head bca-form-table__label" width="25%"><?php echo $this->BcForm->label('BcDummyData.blog_content_id', 'ブログの指定') ?></th>
					<td class="col-input bca-form-table__input">
						<?php echo $this->BcForm->input('BcDummyData.blog_content_id', ['type' => 'select', 'options' => $blogContents]) ?>
						<?php echo $this->BcForm->error('BcDummyData.blog_content_id') ?>
						<br /><small>ブログ記事を作成するブログを指定できます</small>
					</td>
				</tr>
				<tr>
					<th class="col-head bca-form-table__label" width="25%"><?php echo $this->BcForm->label('BcDummyData.clear_data', 'ブログ記事の初期化') ?></th>
					<td class="col-input bca-form-table__input">
						<?php echo $this->BcForm->input('BcDummyData.clear_data', ['type' => 'checkbox', 'label' => '作成前にブログ記事を初期化する']) ?>
						<?php echo $this->BcForm->error('BcDummyData.clear_data') ?>
						<br /><small>ブログ記事を初期化する場合はチェックを入れてください。</small>
						<br /><small>初期化を指定すると、選択したブログの記事を削除した上で、AUTO_INCREMENTの値を、次のデータが最大値になるように調整します。</small>
					</td>
				</tr>
				<tr>
					<th class="col-head bca-form-table__label" width="25%"><?php echo $this->BcForm->label('BcDummyData.num', '作成する記事の数') ?></th>
					<td class="col-input bca-form-table__input">
						<?php echo $this->BcForm->input('BcDummyData.num', ['type' => 'input', 'default' => 100]) ?>
						<?php echo $this->BcForm->error('BcDummyData.num') ?>
						<br /><small>作成するブログ記事の数を指定できます</small>
					</td>
				</tr>
			</tbody>
		</table>
	</section>

	<section class="bca-actions">
		<div class="bca-actions__main">
			<?php echo $this->BcForm->submit('ダミーデータの作成', [
				'id' => 'BtnSave',
				'div' => false,
				'class' => 'button bca-btn bca-actions__item',
				'data-bca-btn-type' => 'save',
				'data-bca-btn-size' => 'lg',
				'data-bca-btn-width' => 'lg',
			]) ?>
		</div>
	</section>

<?php echo $this->BcForm->end() ?>
