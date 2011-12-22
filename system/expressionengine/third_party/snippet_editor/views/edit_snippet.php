<ul class="tab_menu" id="tab_menu_tabs">
<li class="content_tab current"> <a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=snippets'?>"><?=lang('snippets')?></a>  </li> 
<li class="content_tab"> <a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=global_variables'?>"><?=lang('global_variables')?></a>  </li> 
<li class="content_tab"> <a href="<?=BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=settings'?>"><?=lang('settings')?></a>  </li> 
</ul> 
<div class="clear_left shun"></div> 

<?php if ($save_tmpl_revisions):?>
<div class="rightNav">
	<div style="float: left; width: 100%;">
        <span class="button" style="margin-top:-6px">
			<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=view_revision'.AMP.'type=snippets', array('id' => 'revisions', 'name' => 'revisions', 'target' => 'Revisions')).
			$revisions_dropdown.
            form_submit('submit', lang('view'), 'class="submit" id="revision_button"')?>
			<?=form_close()?>
			</span>
	</div>
	<div class="clear_left"></div>
</div>
<?php endif;?>


<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=save_snippet')?>

	<?php if ($snippet_id) echo form_hidden('snippet_id', $snippet_id)?>

	<p>
	<label for="snippet_name"><?=lang('snippet_name')?></label><br />
	<?=lang('variable_name_instructions')?><br />
	<?=form_input(array('id'=>'snippet_name','name'=>'snippet_name','size'=>70,'class'=>'field','value'=>$snippet_name))?>				
	</p>
	
	<p>
	<label for="snippet_editor"><?=lang('variable_data')?></label><br />
	<?=form_textarea(array('id'=>'snippet_editor','name'=>'snippet_contents','cols'=>70,'rows'=>10,'class'=>'fullfield','value'=>$snippet_contents))?>    
	</p>
	
	<?php if ($msm):?>
		<p>
		<label for="snippet_name"><?=lang('available_to_sites')?></label><br />
		<label><?=form_radio('site_id', 0, $all_sites).NBS.lang('all')?></label>&nbsp;&nbsp;
		<label><?=form_radio('site_id', $site_id, ( ! $all_sites)).NBS.lang('this_site_only')?></label>
		</p>
	<?php else:?>
		<div><?=form_hidden('site_id', $site_id)?></div>
	<?php endif;?>
    
    <?php if ($save_tmpl_revisions): ?>
	<p><?=form_checkbox('save_revision', 'y', true, 'id="save_snippet_revision"')?> &nbsp;
	<?=form_label(lang('save_revision'), 'save_snippet_revision')?></p>
	<?php endif; ?>
	
	<p><?=form_submit('update', lang('update'), 'class="submit"')?> <?=form_submit('update_and_return', lang('update_and_return'), 'class="submit"')?></p>
<?=form_close()?>
