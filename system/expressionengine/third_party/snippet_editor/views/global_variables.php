<ul class="tab_menu" id="tab_menu_tabs">
<li class="content_tab"> <a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=snippets'?>"><?=lang('snippets')?></a>  </li> 
<li class="content_tab current"> <a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=global_variables'?>"><?=lang('global_variables')?></a>  </li> 
<li class="content_tab"> <a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=settings'?>"><?=lang('settings')?></a>  </li> 
</ul> 
<div class="clear_left shun"></div> 


<?php
	$this->table->set_template(array('table_open' => '<table class="mainTable clear_left" cellspacing="0" cellpadding="0">'));
	$this->table->set_heading(
								lang('global_variables'),
								lang('global_variable_syntax'),
								lang('delete')
							);
							
	if ($global_variables_count >= 1)
	{
		
        foreach ($global_variables->result() as $variable)
		{
			$this->table->add_row(
				'<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=edit_global_variable'.AMP.'variable_id='.$variable->variable_id.'">'.$variable->variable_name.'</a>', 
				'{'.$variable->variable_name.'}', 
				'<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=delete_global_variable'.AMP.'variable_id='.$variable->variable_id.'">'.lang('delete').'</a>'
			);
		}
	}
	else
	{
		$this->table->add_row(array('data' => lang('no_global_variables'), 'colspan' => 3));
	}
	
	echo $this->table->generate();
    $this->table->clear();
?>
