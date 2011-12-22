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
 File: acc.snippet_editor.php
-----------------------------------------------------
 Purpose: Better editing for snippets and global variables
=====================================================
*/

if ( ! defined('BASEPATH'))
{
    exit('Invalid file request');
}

class Snippet_editor_acc
{
	var $name					= 'Snippet Editor';
	var $id						= 'snippet_editor_acc';
	var $version				= '1.0';
	var $description			= 'Better editing for snippets and global variables';
	var $sections				= array();


    function __construct() { 
        // Make a local reference to the ExpressionEngine super object 
        $this->EE =& get_instance(); 
    } 
    
    
	function set_sections()
	{
		$this->EE->javascript->output('		
		$("#snippet_editor_acc.accessory").remove();
		$("a.snippet_editor_acc").parent("li").remove();
		');	
        
        if ($this->EE->input->get('C')=="design" && $this->EE->input->get('M')=="snippets")
		{
            $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=snippets');
		}
        else if ($this->EE->input->get('C')=="design" && $this->EE->input->get('M')=="global_variables")
		{
            $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=snippet_editor'.AMP.'method=global_variables');
		}

	}

}
