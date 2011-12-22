<ul class="tab_menu" id="tab_menu_tabs">
<li class="content_tab"> <a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=snippets'?>"><?=lang('snippets')?></a>  </li> 
<li class="content_tab current"> <a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=global_variables'?>"><?=lang('global_variables')?></a>  </li> 
<li class="content_tab"> <a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=settings'?>"><?=lang('settings')?></a>  </li> 
</ul> 
<div class="clear_left shun"></div> 


<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=delete_global_variable')?>
	<?=form_hidden('delete_confirm', TRUE)?>
	<?=form_hidden('variable_id', $variable_id)?>
	<p><?=lang('delete_this_variable')?> <strong><?=$variable_name?></strong></p>
	<p><strong class="notice"><?=lang('action_can_not_be_undone')?></strong></p>
	<p><?=form_submit('template', lang('yes'), 'class="submit"')?></p>
<?=form_close()?>
