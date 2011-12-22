<div id="mainContent"style="width:100%;">
	<div class="contents">
		
		<br /><br />
		<div class="heading"><h2 class="edit"><?=lang('revision_history')?></h2></div>

		<div id="templateEditor" class="formArea">

			<div class="clear_left" id="template_details" style="margin-bottom: 0">
				<p>
				<?=$type?>: <?=$item_name?> (<?=$item_date?>)
			</div>

			<div id="template_create" class="pageContents">
		
			<?=form_textarea(array(
									'name'	=> 'template_data',
					              	'id'	=> 'template_data',
					              	'cols'	=> '100',
					              	'rows'	=> '20',
									'value'	=> $item_data,
									'style' => 'border: 0;',
                                    'readonly'=>'readonly'
							));?>

			</div>
		</div>

	<div align="center"><a href="JavaScript:window.close();"><b><?=lang('close_window')?></b></a></div>

	</div> <!-- contents -->
</div> <!-- mainContent -->