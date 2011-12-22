<ul class="tab_menu" id="tab_menu_tabs">
<li class="content_tab current"> <a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=snippets'?>"><?=lang('snippets')?></a>  </li> 
<li class="content_tab"> <a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=global_variables'?>"><?=lang('global_variables')?></a>  </li> 
<li class="content_tab"> <a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=settings'?>"><?=lang('settings')?></a>  </li> 
</ul> 
<div class="clear_left shun"></div> 


<p><?=str_replace('%s', BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=global_variables', lang('snippets_explanation'))?></p>

<?php
	$this->table->set_template(array('table_open' => '<table class="mainTable clear_left" cellspacing="0" cellpadding="0">'));
	$this->table->set_heading(
								lang('snippets'),
								lang('snippet_syntax'),
								lang('delete')
							);
							
	if ($snippets_count >= 1)
	{
		
        foreach ($snippets->result() as $variable)
		{
			$this->table->add_row(
				'<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=edit_snippet'.AMP.'snippet_id='.$variable->snippet_id.'">'.$variable->snippet_name.'</a>', 
				'{'.$variable->snippet_name.'}', 
				'<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=delete_snippet'.AMP.'snippet_id='.$variable->snippet_id.'">'.lang('delete').'</a>'
			);
		}
	}
	else
	{
		$this->table->add_row(array('data' => lang('no_snippets'), 'colspan' => 3));
	}
	
	echo $this->table->generate();
    $this->table->clear();
?>
