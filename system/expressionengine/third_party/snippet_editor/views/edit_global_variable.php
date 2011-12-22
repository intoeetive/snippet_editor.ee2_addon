<ul class="tab_menu" id="tab_menu_tabs">
<li class="content_tab"> <a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=snippets'?>"><?=lang('snippets')?></a>  </li> 
<li class="content_tab current"> <a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=global_variables'?>"><?=lang('global_variables')?></a>  </li> 
<li class="content_tab"> <a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=settings'?>"><?=lang('settings')?></a>  </li> 
</ul> 
<div class="clear_left shun"></div> 

<?php if ($save_tmpl_revisions):?>
<div class="rightNav">
	<div style="float: left; width: 100%;">
        <span class="button" style="margin-top:-6px">
			<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=view_revision'.AMP.'type=global_variables', array('id' => 'revisions', 'name' => 'revisions', 'target' => 'Revisions')).
			$revisions_dropdown.
            form_submit('submit', lang('view'), 'class="submit" id="revision_button"')?>
			<?=form_close()?>
			</span>
	</div>
	<div class="clear_left"></div>
</div>
<?php endif;?>


<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=save_global_variable')?>

	<?php if ($variable_id) echo form_hidden('variable_id', $variable_id)?>

	<p>
	<label for="variable_name"><?=lang('variable_name')?></label><br />
	<?=lang('template_group_instructions') . ' ' . lang('undersores_allowed')?><br />
	<?=form_input(array('id'=>'variable_name','name'=>'variable_name','size'=>70,'class'=>'field','value'=>$variable_name))?>				
	</p>
    
	<p>
	<label for="snippet_editor"><?=lang('variable_data')?></label><br />
	<?=form_textarea(array('id'=>'snippet_editor','name'=>'variable_data','cols'=>70,'rows'=>10,'class'=>'fullfield','value'=>$variable_data))?>    
	</p>    
    
    <?php if ($save_tmpl_revisions): ?>
	<p><?=form_checkbox('save_revision', 'y', true, 'id="save_variable_revision"')?> &nbsp;
	<?=form_label(lang('save_revision'), 'save_variable_revision')?></p>
	<?php endif; ?>
	
	<p><?=form_submit('update', lang('update'), 'class="submit"')?> <?=form_submit('update_and_return', lang('update_and_return'), 'class="submit"')?></p>
<?=form_close()?>
