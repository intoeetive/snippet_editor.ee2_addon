<?php

/*
=====================================================
 Snippet Editor
-----------------------------------------------------
 http://www.intoeetive.com/
-----------------------------------------------------
 Copyright (c) 2011 Yuri Salimovskiy
=====================================================
 This software is based upon and derived from
 ExpressionEngine software protected under
 copyright dated 2004 - 2011. Please see
 http://expressionengine.com/docs/license.html
=====================================================
 File: mcp.snippet_editor.php
-----------------------------------------------------
 Purpose: Better editing for snippets and global variables
=====================================================
*/

if ( ! defined('BASEPATH'))
{
    exit('Invalid file request');
}



class Snippet_editor_mcp {

    var $version = '1.0';
    
    var $settings = array();
    
    function __construct() { 
        // Make a local reference to the ExpressionEngine super object 
        $this->EE =& get_instance(); 
        $this->EE->lang->loadfile('design');
        $this->EE->lang->loadfile('snippet_editor');
        $this->EE->db->select('settings')
                    ->from('exp_modules')
                    ->where('module_name', 'Snippet_editor')
                    ->limit(1);
        $query = $this->EE->db->get();
        $this->settings = unserialize($query->row('settings'));  
    } 
    
    
    
    function index()
    {
        return $this->snippets();
    }
    
    
    
    function settings()
    {
        $this->EE->load->helper('form');
    	$this->EE->load->library('table');  
        
        $vars = array();
        
        $editors = array(
            'none'  => lang('none'),
            'editarea'  => lang('editarea')
        );
        
        $vars['settings'] = array(	
            'editor'	=> form_dropdown('editor', $editors, $this->settings['editor'])
        );

        $this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor', lang('snippet_editor_module_name'));
       	if (version_compare(APP_VER, '2.6.0', '>='))
        {
        	$this->EE->view->cp_page_title = lang('settings');
        }
        else
        {
        	$this->EE->cp->set_variable('cp_page_title', lang('settings'));
        }
        
    	return $this->EE->load->view('settings', $vars, TRUE);
        
    }

    
    function save_settings()
    {

        $settings['editor'] = $this->EE->input->post('editor');
        
        $this->EE->db->where('module_name', 'Snippet_editor');
        $this->EE->db->update('modules', array('settings' => serialize($settings)));
        
        $this->EE->session->set_flashdata('message_success', $this->EE->lang->line('preferences_updated'));        
        $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor');
        
    }    
    


	function snippets()
	{
		if ( ! $this->EE->cp->allowed_group('can_access_design', 'can_admin_templates'))
		{
			show_error(lang('unauthorized_access'));
		}

		$this->EE->load->model('template_model');
		$this->EE->load->helper('form');
		$this->EE->load->library('table');

		$this->EE->jquery->tablesorter('.mainTable', '{
			headers: {2: {sorter: false}},
			widgets: ["zebra"]
		}');

		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor', lang('snippet_editor_module_name'));
		if (version_compare(APP_VER, '2.6.0', '>='))
        {
        	$this->EE->view->cp_page_title = lang('snippets');
        }
        else
        {
        	$this->EE->cp->set_variable('cp_page_title', lang('snippets'));
        }

		$vars['snippets'] = $this->EE->template_model->get_snippets();
		$vars['snippets_count'] = $vars['snippets']->num_rows();

		$this->EE->javascript->compile();
		
        $this->EE->cp->set_right_nav(array(
		            'create_new_snippet' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=edit_snippet')
		        );
        
		return $this->EE->load->view('snippets', $vars, true);
	}


	function edit_snippet()
	{
		if ( ! $this->EE->cp->allowed_group('can_access_design', 'can_admin_templates'))
		{
			show_error(lang('unauthorized_access'));
		}
		
		$this->EE->load->model('template_model');
		$this->EE->load->helper('form');

		$vars = array(
						'msm'					=> FALSE,
                        'save_tmpl_revisions'   => FALSE,
						'update'				=> ($this->EE->input->get_post('update') == 1),
						'site_id'				=> $this->EE->config->item('site_id'),
						'all_sites'				=> FALSE,
						'snippet_id'			=> NULL,
						'snippet_name'			=> '',
						'snippet_contents'		=> '',
					);

		if ($this->EE->config->item('multiple_sites_enabled') == 'y')
		{
			$vars['msm'] = TRUE;
		}
        
		if ($this->EE->input->get_post('snippet_id') !== FALSE)
		{
			if (($snippet = $this->EE->template_model->get_snippet($this->EE->input->get_post('snippet_id'))) !== FALSE)
			{
				$vars = array_merge($vars, $snippet);
			}			
            
            if ($this->EE->config->item('save_tmpl_revisions') == 'y')
    		{
                $revision_options[''] = lang('revision_history');
    
        		$this->EE->db->select('tracker_id, item_date, screen_name')
                            ->from('exp_revision_tracker')
                            ->join('exp_members', 'exp_members.member_id = exp_revision_tracker.item_author_id', 'left')
                            ->where('item_table', 'exp_snippets')
                            ->where('item_field', 'snippet_contents')
                            ->where('item_id', $this->EE->input->get_post('snippet_id'))
                            ->order_by('tracker_id', 'desc');
                $query = $this->EE->db->get();
        
        		if ($query->num_rows() > 0)
        		{			 
                    $max_tmpl_revisions = $this->EE->config->item('max_tmpl_revisions');
                    $cnt = 0;
        			if ($max_tmpl_revisions != '' AND is_numeric($max_tmpl_revisions) AND $max_tmpl_revisions > 0)
        			{  
                        $delete = array();
                    }
                    foreach ($query->result_array() as $row)
        			{
        				if (isset($delete) && $cnt > $max_tmpl_revisions)
                        {
                            $delete[] = $row['tracker_id'];
                        }
                        else
                        {
                            $revision_options[$row['tracker_id']] = $this->EE->localize->human_time($row['item_date']).' ('.$row['screen_name'].')';
                        }
                        $cnt++;
        			}  
        		}
                
                if (!empty($delete))
                {
                    $this->EE->db->where_in('tracker_id', $delete);
                    $this->EE->db->delete('exp_revision_tracker');
                }
                
                $vars['revisions_dropdown'] = form_dropdown('tracker_id', $revision_options, '');
                $vars['save_tmpl_revisions'] = true;
            }
		}

		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor', lang('snippet_editor_module_name'));
        $this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=snippets', lang('snippets'));
        $cp_page_title = ($vars['snippet_name']!='')?str_replace('%s', $vars['snippet_name'], lang('snippet_edit')):lang('snippet_create');
        if (version_compare(APP_VER, '2.6.0', '>='))
        {
        	$this->EE->view->cp_page_title = $cp_page_title;
        }
        else
        {
        	$this->EE->cp->set_variable('cp_page_title', $cp_page_title);
        }
        
		
        $theme_folder_url = trim($this->EE->config->item('theme_folder_url'), '/').'/third_party/snippet_editor/';
        switch ($this->settings['editor'])
        {
            case 'editarea':
                $this->EE->cp->add_to_head('<script src="'.$theme_folder_url.'edit_area/edit_area_full.js" type="text/javascript" charset="utf-8"></script>');
                $js = "editAreaLoader.init({
	id : \"snippet_editor\",
    syntax: \"html\",
	start_highlight: true
});";
                $this->EE->javascript->output($js);
                break;
        }
 
 		$this->EE->javascript->compile();
        
		return $this->EE->load->view('edit_snippet', $vars, true);
	}


	function save_snippet()
	{
		if ( ! $this->EE->cp->allowed_group('can_access_design', 'can_admin_templates'))
		{
			show_error(lang('unauthorized_access'));
		}

		$this->EE->load->model('template_model');
		$this->EE->load->library('api');
		
		foreach (array('snippet_id', 'site_id', 'snippet_name', 'snippet_contents') as $var)
		{
			${$var} = $this->EE->input->get_post($var);
		}
		
		$update = FALSE;

		if ($snippet_id !== FALSE && ($snippet = $this->EE->template_model->get_snippet($snippet_id)) !== FALSE)
		{
			$update = TRUE;
		}
		
		if ($snippet_name == '' OR $snippet_contents == '' OR $site_id === FALSE)
		{
			show_error(lang('all_fields_required'));
		}
		elseif ($this->EE->api->is_url_safe($snippet_name) === FALSE)
		{
			show_error(lang('illegal_characters'));
		}
		elseif (in_array($snippet_name, $this->EE->cp->invalid_custom_field_names()))
		{
			show_error(lang('reserved_name'));
		}
		
		if ($site_id != $this->EE->config->item('site_id') AND $site_id != 0)
		{
			$site_id = $this->EE->config->item('site_id');
		}

		$data = array(
						'snippet_name'		=> $snippet_name,
						'snippet_contents'	=> $snippet_contents,
						'site_id'			=> $site_id
					);
			
		if ($update === TRUE)
		{
			if ($snippet['snippet_name'] != $data['snippet_name'] && $this->EE->template_model->unique_snippet_name($data['snippet_name']) !== TRUE)
			{
				show_error(lang('duplicate_snippet_name'));				
			}       
            
            $this->EE->db->update('snippets', $data, array('snippet_id' => $snippet_id));     

            $this->EE->session->set_flashdata('message_success', lang('snippet_updated'));
		}
		else
		{
			if ($this->EE->template_model->unique_snippet_name($data['snippet_name']) !== TRUE)
			{
				show_error(lang('duplicate_snippet_name'));
			}

			$this->EE->db->insert('snippets', $data);
            $snippet_id = $this->EE->db->insert_id();

            $this->EE->session->set_flashdata('message_success', lang('snippet_created'));
		}
        
        if ($this->EE->config->item('save_tmpl_revisions') == 'y' && ($update !== TRUE || $this->EE->input->get_post('save_revision')=='y'))
        {
            $revision = array(
                'item_id'       => $snippet_id,
                'item_table'    => 'exp_snippets',
                'item_field'    => 'snippet_contents',
               	'item_date'     => $this->EE->localize->now,
                'item_author_id'=> $this->EE->session->userdata('member_id'),
                'item_data'     => $snippet_contents
            );
                        
            $this->EE->db->insert('exp_revision_tracker', $revision);
        }

		$this->EE->functions->clear_caching('all');

		if ($this->EE->input->get_post('update_and_return') !== FALSE)
		{
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=snippets');
		}
		else
		{
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=edit_snippet'.AMP.'snippet_id='.$snippet_id);
		}
	}


	function delete_snippet()
	{
		if ( ! $this->EE->cp->allowed_group('can_access_design', 'can_admin_templates'))
		{
			show_error(lang('unauthorized_access'));
		}

		$this->EE->load->model('template_model');
		$this->EE->load->helper('form');
	
		if (($snippet_id = $this->EE->input->get_post('snippet_id')) === FALSE)
		{
			show_error(lang('unauthorized_access'));
		}

		if (($snippet = $this->EE->template_model->get_snippet($snippet_id)) === FALSE)
		{
			show_error(lang('unauthorized_access'));
		}

		if ($this->EE->input->post('delete_confirm') == TRUE)
		{
			$this->EE->template_model->delete_snippet($snippet_id);
            //and delete all revisions
            $this->EE->db->where('item_id', $snippet_id)
                        ->where('item_table', 'exp_snippets');
            $this->EE->db->delete('exp_revision_tracker');
			$this->EE->session->set_flashdata('message_success', lang('snippet_deleted'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=snippets');
		}
		else
		{
			$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor', lang('snippet_editor_module_name'));
            $this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=snippets', lang('snippets'));
            if (version_compare(APP_VER, '2.6.0', '>='))
	        {
	        	$this->EE->view->cp_page_title = lang('delete_snippet');
	        }
	        else
	        {
	        	$this->EE->cp->set_variable('cp_page_title', lang('delete_snippet'));
	        }
            			
			return $this->EE->load->view('delete_snippet', $snippet, true);
		}
	}
    
    
    
    
    
    function view_revision()
    {
		if ($this->EE->config->item('save_tmpl_revisions') == 'n')
		{
			show_error(lang('tmpl_revisions_not_enabled'));
		}
	
		if ( ! $this->EE->cp->allowed_group('can_access_design', 'can_admin_templates'))
		{
			show_error(lang('unauthorized_access'));
		}
		 
		if ( $this->EE->input->get_post('tracker_id')===false)
        {
            show_error(lang('unauthorized_access'));
        }
        
        if ( !in_array($this->EE->input->get_post('type'), array('snippets', 'global_variables')))
        {
            show_error(lang('unauthorized_access'));
        }

		$this->EE->load->helper('form');

		$vars = array();
		
		$this->EE->javascript->output('$(window).focus();');

		$this->EE->javascript->compile();
        
        if (version_compare(APP_VER, '2.6.0', '>='))
        {
        	$this->EE->view->cp_page_title = lang('revision_history');
        }
        else
        {
        	$this->EE->cp->set_variable('cp_page_title', lang('revision_history'));
        }
                
		$this->EE->db->select('item_id, item_data, item_date')
                    ->from('exp_revision_tracker')
                    ->where('tracker_id', $this->EE->input->get_post('tracker_id'))
                    ->where('item_table', 'exp_'.$this->EE->input->get_post('type'));
        switch ($this->EE->input->get_post('type'))
        {
            case 'snippets':
                $this->EE->db->where('item_field', 'snippet_contents');
                break;
            case 'global_variables':
                $this->EE->db->where('item_field', 'variable_data');
                break;
        }
				
		$query = $this->EE->db->get();

    	if ($query->num_rows() == 0)
    	{
    		show_error(lang('unauthorized_access'));
    	}
        
        $vars['item_data'] = $query->row('item_data');

		$date_fmt = ($this->EE->session->userdata('time_format') != '') ? $this->EE->session->userdata('time_format') : $this->EE->config->item('time_format');
		if ($date_fmt == 'us')
		{
			$datestr = '%m/%d/%y %h:%i %a';
		}
		else
		{
			$datestr = '%Y-%m-%d %H:%i';
		}

		$vars['item_date'] = $this->EE->localize->decode_date($datestr, $query->row('item_date'), TRUE);			

		$this->EE->db->select('snippet_name')
                    ->where('snippet_id', $query->row('item_id'))
                    ->where('(site_id=0 OR site_id='.$this->EE->config->item('site_id').')');
		$query = $this->EE->db->get('snippets');
        if ($query->num_rows() == 0)
		{
			show_error(lang('id_not_found'));
		}

		$vars['item_name']	 = $query->row('snippet_name');
        $vars['type']	 = lang('snippets');
        
        $out = '';
        
        $this->EE->load->add_package_path(APPPATH);
        $out .= $this->EE->load->view('_shared/header', array(), true);
        $this->EE->load->remove_package_path(APPPATH);
        
		$out .= $this->EE->load->view('revision', $vars, true);
        
        $this->EE->load->add_package_path(APPPATH);
        $out .= $this->EE->load->view('_shared/footer', array(), true);
        $this->EE->load->remove_package_path(APPPATH);
        
        exit($out);
    }
    
    
    
    
    
    
    
    


	function global_variables()
	{
		if ( ! $this->EE->cp->allowed_group('can_access_design', 'can_admin_templates'))
		{
			show_error(lang('unauthorized_access'));
		}

		$this->EE->load->model('template_model');
		$this->EE->load->helper('form');
		$this->EE->load->library('table');
		
		$this->EE->jquery->tablesorter('.mainTable', '{
			headers: {2: {sorter: false}},
			widgets: ["zebra"]
		}');
		
        $this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor', lang('snippet_editor_module_name'));
        if (version_compare(APP_VER, '2.6.0', '>='))
        {
        	$this->EE->view->cp_page_title = lang('global_variables');
        }
        else
        {
        	$this->EE->cp->set_variable('cp_page_title', lang('global_variables'));
        }
                        
		$vars['global_variables']		= $this->EE->template_model->get_global_variables();
		$vars['global_variables_count']	= $vars['global_variables']->num_rows();

		$this->EE->javascript->compile();
		
        $this->EE->cp->set_right_nav(array(
		            'create_new_global_variable' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=edit_global_variable')
		        );
        
		return $this->EE->load->view('global_variables', $vars, true);

	}





	function save_global_variable()
	{
		if ( ! $this->EE->cp->allowed_group('can_access_design', 'can_admin_templates'))
		{
			show_error(lang('unauthorized_access'));
		}
		
        $this->EE->load->library('api');
		$this->EE->load->model('template_model');
		
        
        $reserved_vars	= array(
							'lang',
							'charset',
							'homepage',
							'debug_mode',
							'gzip_mode',
							'version',
							'elapsed_time',
							'hits',
							'total_queries',
							'XID_HASH'
		);
        
        foreach (array('variable_id', 'variable_name', 'variable_data') as $var)
		{
			${$var} = $this->EE->input->get_post($var);
		}
		
		$update = FALSE;

		if ($variable_id !== FALSE && ($global_variable = $this->EE->template_model->get_global_variable($variable_id)) !== FALSE)
		{
			$update = TRUE;
		}
        
		
		if ($variable_name == '' || $variable_data == '')
		{
			show_error(lang('all_fields_required'));
		}
		else if ( ! preg_match("#^[a-zA-Z0-9_\-/]+$#i",$variable_name))
		{
			show_error(lang('illegal_characters'));
		}
		else if (in_array($variable_name, $reserved_vars))
		{
			show_error(lang('reserved_name'));
		}
        
        if ($update === TRUE)
		{
			if ($global_variable->num_rows() < 1)
			{
				show_error(lang('variable_does_not_exist')); 
			}
            
            $global_variable_info = $global_variable->row(); 
            
            if ($variable_name != $global_variable_info->variable_name && $this->EE->template_model->check_duplicate_global_variable_name($variable_name) !== TRUE)
			{
				show_error(lang('duplicate_var_name'));				
			}       
            
            $this->EE->template_model->update_global_variable($variable_id, $variable_name, $variable_data);  

            $this->EE->session->set_flashdata('message_success', lang('global_var_updated'));
		}
		else
		{
			if ($this->EE->template_model->check_duplicate_global_variable_name($variable_name) === FALSE)
			{
				show_error(lang('duplicate_var_name'));		
			}

			$variable_id = $this->EE->template_model->create_global_variable($variable_name, $variable_data);

            $this->EE->session->set_flashdata('message_success', lang('global_var_created'));
		}
        
        if ($this->EE->config->item('save_tmpl_revisions') == 'y' && ($update !== TRUE || $this->EE->input->get_post('save_revision')=='y'))
        {
            $revision = array(
                'item_id'       => $variable_id,
                'item_table'    => 'exp_global_variables',
                'item_field'    => 'variable_data',
               	'item_date'     => $this->EE->localize->now,
                'item_author_id'=> $this->EE->session->userdata('member_id'),
                'item_data'     => $variable_data
            );
                        
            $this->EE->db->insert('exp_revision_tracker', $revision);
        }

		$this->EE->functions->clear_caching('all');

		if ($this->EE->input->get_post('update_and_return') !== FALSE)
		{
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=global_variables');
		}
		else
		{
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=edit_global_variable'.AMP.'variable_id='.$variable_id);
		}


	}
	
    
    
    
	function edit_global_variable()
	{
		if ( ! $this->EE->cp->allowed_group('can_access_design', 'can_admin_templates'))
		{
			show_error(lang('unauthorized_access'));
		}
		
		$this->EE->load->model('template_model');
		$this->EE->load->helper('form');
        $this->EE->load->library('table');

		$vars = array(
						'save_tmpl_revisions'   => false,
                        'variable_id'			=> NULL,
						'variable_name'			=> '',
						'variable_data'	      	=> '',
					);
        
		if ($this->EE->input->get_post('variable_id') !== FALSE)
		{
			$global_variable = $this->EE->template_model->get_global_variable($this->EE->input->get_post('variable_id'));

			if ($global_variable->num_rows() < 1)
			{
				show_error(lang('variable_does_not_exist')); 
			}
	
			$global_variable_info = $global_variable->row(); 
			
			$vars['variable_id'] = $global_variable_info->variable_id;
			$vars['variable_name'] = $global_variable_info->variable_name;		
			$vars['variable_data'] = $global_variable_info->variable_data;		
            
            if ($this->EE->config->item('save_tmpl_revisions') == 'y')
    		{
                $revision_options[''] = lang('revision_history');
    
        		$this->EE->db->select('tracker_id, item_date, screen_name')
                            ->from('exp_revision_tracker')
                            ->join('exp_members', 'exp_members.member_id = exp_revision_tracker.item_author_id', 'left')
                            ->where('item_table', 'exp_global_variables')
                            ->where('item_field', 'variable_data')
                            ->where('item_id', $this->EE->input->get_post('variable_id'))
                            ->order_by('tracker_id', 'desc');
                $query = $this->EE->db->get();
        
        		if ($query->num_rows() > 0)
        		{			 
                    $max_tmpl_revisions = $this->EE->config->item('max_tmpl_revisions');
                    $cnt = 0;
        			if ($max_tmpl_revisions != '' AND is_numeric($max_tmpl_revisions) AND $max_tmpl_revisions > 0)
        			{  
                        $delete = array();
                    }
                    foreach ($query->result_array() as $row)
        			{
        				if (isset($delete) && $cnt > $max_tmpl_revisions)
                        {
                            $delete[] = $row['tracker_id'];
                        }
                        else
                        {
                            $revision_options[$row['tracker_id']] = $this->EE->localize->human_time($row['item_date']).' ('.$row['screen_name'].')';
                        }
                        $cnt++;
        			}  
        		}
                
                if (!empty($delete))
                {
                    $this->EE->db->where_in('tracker_id', $delete);
                    $this->EE->db->delete('exp_revision_tracker');
                }
                
                $vars['revisions_dropdown'] = form_dropdown('tracker_id', $revision_options, '');
                $vars['save_tmpl_revisions'] = true;
            }
		}

		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor', lang('snippet_editor_module_name'));
        $this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=global_variables', lang('global_variables'));
        $cp_page_title = ($vars['variable_name']!='')?lang('global_var_update'):lang('create_new_global_variable');
        if (version_compare(APP_VER, '2.6.0', '>='))
        {
        	$this->EE->view->cp_page_title = $cp_page_title;
        }
        else
        {
        	$this->EE->cp->set_variable('cp_page_title', $cp_page_title);
        }
        
        $theme_folder_url = trim($this->EE->config->item('theme_folder_url'), '/').'/third_party/snippet_editor/';
        switch ($this->settings['editor'])
        {
            case 'editarea':
                $this->EE->cp->add_to_head('<script src="'.$theme_folder_url.'edit_area/edit_area_full.js" type="text/javascript" charset="utf-8"></script>');
                $js = "editAreaLoader.init({
	id : \"snippet_editor\",
    syntax: \"html\",
	start_highlight: true
});";
                $this->EE->javascript->output($js);
                break;
        }
 
 		$this->EE->javascript->compile();
        
		return $this->EE->load->view('edit_global_variable', $vars, true);
	}    
    
    
    
    
    
    

	function delete_global_variable()
	{
		if ( ! $this->EE->cp->allowed_group('can_access_design', 'can_admin_templates'))
		{
			show_error(lang('unauthorized_access'));
		}

		$this->EE->load->helper('form');
	
		$variable_id = $this->EE->input->get_post('variable_id');

		if ($variable_id == '')
		{
			show_error(lang('variable_does_not_exist'));
		}

		$global_variable = $this->EE->template_model->get_global_variable($variable_id);
		
		if ($global_variable->num_rows() < 1)
		{
			show_error(lang('variable_does_not_exist')); 
		}

		if ($this->EE->input->get_post('delete_confirm') == TRUE)
		{
			$this->EE->template_model->delete_global_variable($variable_id);
	
			//and delete all revisions
            $this->EE->db->where('item_id', $variable_id)
                        ->where('item_table', 'exp_global_variables');
            $this->EE->db->delete('exp_revision_tracker');
			$this->EE->session->set_flashdata('message_success', lang('variable_deleted'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=global_variables');
		}
		else
		{
			$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor', lang('snippet_editor_module_name'));
            $this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=global_variables', lang('global_variables'));
            if (version_compare(APP_VER, '2.6.0', '>='))
	        {
	        	$this->EE->view->cp_page_title = lang('delete_global_variable');
	        }
	        else
	        {
	        	$this->EE->cp->set_variable('cp_page_title', lang('delete_global_variable'));
	        }

			$global_variable_info = $global_variable->row(); 
			
			$vars['variable_id'] = $global_variable_info->variable_id;
			$vars['variable_name'] = $global_variable_info->variable_name;		
			
			return $this->EE->load->view('delete_global_variable', $vars, true);
		}
	}

}
/* END */
?>