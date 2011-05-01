<?php

/**
  * Plugin for Nucleus CMS (http://plugins.nucleuscms.org/)
  * Copyright (C) 2009 The Nucleus Plugins Project
  *
  * This program is free software; you can redistribute it and/or
  * modify it under the terms of the GNU General Public License
  * as published by the Free Software Foundation; either version 2
  * of the License, or (at your option) any later version.
  *
  * see license.txt for the full license
  * or visit http://www.gnu.org/copyleft/gpl.html
  */

class NP_TinyMCE extends NucleusPlugin
{

var $memory_bconvertbreaks;

	function getName()              {return 'NP_TinyMCE';}
	function getURL()               {return 'http://plugins.nucleuscms.org/';}
	function getVersion()           {return '3.4.2';}
	function getMinNucleusVersion() {return 300;}
	function getDescription()       {return _NP_TINYMCE01;	}
	function supportsFeature($w)    { return ($w == 'SqlTablePrefix') ? 1 : 0; }

	function getAuthor()
	{
		return 'karma | roel | eph | kg | nakahara21 | dcw-niwa | shizuki | Cacher | ftruscot | yama.kyms';
	}

	function getEventList()
	{
		return array
		(
			'AdminPrePageHead', 	// include javascript on admin add/edit pages
			'BookmarkletExtraHead',	// include javascript on bookmarklet pages
			'PreSendContentType', 	// we need to force text/html instead of application/xhtml+xml
			'PreAddItem',
			'PreUpdateItem',
			'PostAddItem',
			'PostUpdateItem',
			'PrepareItemForEdit',
			'PrePluginOptionsEdit',
			'PreSkinParse'
		);
	}

	function install()
	{
		$inc_dir  = $this->getDirectory() . 'inc/';
		require($inc_dir . 'install.php');
	}

	function unInstall()
	{
		// restore to standard settings
		sql_query('UPDATE ' . sql_table('config') . " SET value = '2' WHERE name = 'DisableJSTools'");
	}

	function init()
	{
		// include language file for this plugin
		global $CONF;
		$adminurl   = parse_url($CONF['AdminURL']);
		$currenturl = getenv('SCRIPT_NAME');
		if(strpos($currenturl, $adminurl['path'])!==0) {return;}
		$language = preg_replace('@\\|/@', '', getLanguageName());
		$langDir  = $this->getDirectory() . 'language/';
		if (! @include_once($langDir . $language . '.php')) {include_once($langDir . 'english.php');}
	}
	
	function event_PreSkinParse(&$data)
	{
		if($this->getOption('include_css')!=='yes') return;
		$contents = &$data['contents'];
		$css  = '<style type="text/css">' . PHP_EOL;
		$css .= '<!--' . PHP_EOL;
		$css .= $this->getOption('custom_css') . PHP_EOL;
		$css .= '-->' . PHP_EOL;
		$css .= '</style>' . PHP_EOL;
		$contents = str_replace('</head>', $css . '</head>', $contents);
	}
	
	/**
	  * Check NP_Mediatocu
	  */
	function _checkMediatocu()
	{
		global $manager;
		if (!$manager->pluginInstalled('NP_Mediatocu'))
		{
			return false;
		}
		$mediaTocu =& $manager->getPlugin('NP_Mediatocu');
		if (intval($mediaTocu->getVersion()) > 0) return $mediaTocu;
		return false;
	}

	/**
	  * Current member using TinyMCE Editor ?
	  */
	function _memberCheck($aid)
	{
		if ($this->getMemberOption($aid, 'use_tinymce') == 'yes')
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	  * Add extra header
	  */
	function _addExtraHead(&$extrahead)
	{
		global $CONF, $itemid, $member;
		
		if ( ($this->_memberCheck($member->id) == true) and ($this->getItemOption($itemid, 'use_tinymce')=='yes'))
		{
			$CONF['DisableJsTools'] = 1; // overrule simple global settings
			$extrahead .= $this->renderCSS();
			$extrahead .= $this->renderBootStrap();
		}
		else
		{
			$CONF['DisableJsTools'] = 2;
		}
	}
	
	/**
	  * Hook into the <head> section of bookmarkler area pages.
	  * Insert extra script/css includes there.
	  */
	function event_BookmarkletExtraHead(&$data)
	{
		$this->_addExtraHead($data['extrahead']);
	}

	function event_AdminPrePageHead(&$data) 
	{
		global $member, $blogid, $manager;
		$action = $data['action'];
		
		$useEditor = false;
		if (strpos($action,'plugin') !== false)
		{
			$info['editor']    = 'TinyMCE';
			$info['action']    = $action;
			$info['useEditor'] = &$useEditor;
			$manager->notify('EditorAdminPrePageHead', $info);
		} 

		//if (($action != 'createitem') && ($action != 'itemedit') && !($action == 'plugin_MultiLanguage' && requestVar('showlist') == 'edititem')) {
		if (($action != 'createitem') && ($action != 'itemedit') && !$useEditor) return;
		
		$this->_addExtraHead($data['extrahead']);
	}

	/**
	  * Nucleus sends its admin area pages as application/xhtml+xml to browsers that can handle this.
	  *
	  * Unfortunately, this causes javascripts that alter page contents through non-DOM methods
	  * to stop working correctly. As the jscalendar and htmlarea both need this, we're forcing
	  * the content-type to text/html for add/edit item forms.
	  */
	function event_PreSendContentType(&$data)
	{
		$pageType = $data['pageType'];
		switch($pageType) {
			case 'bookmarklet-add':
			case 'bookmarklet-edit':
			case 'admin-createitem':
			case 'admin-itemedit':
				if ($data['contentType'] == 'application/xhtml+xml')
				{
					$data['contentType'] = 'text/html';
				}
		}
	}

	function _suspendConvertBreaks(&$data)
	{
		global $manager;
		$blogid = intval($data['blog']->blogid);
		$b = & $manager->getBlog($blogid);
		$this->memory_bconvertbreaks = false;
		if ($b->getSetting('bconvertbreaks') == true)
		{
			$this->memory_bconvertbreaks = true;
			$data['body'] = removeBreaks($data['body']);
			$data['more'] = removeBreaks($data['more']);
			$b->setConvertBreaks(false);
			$b->writeSettings();
		}
	}

	function _restoreConvertBreaks(&$data)
	{
		global $manager;
		$itemid = intval($data['itemid']);
		$blogid = intval(getBlogIDFromItemID($itemid));
		$b = & $manager->getBlog($blogid);
		if ($this->memory_bconvertbreaks == true)
		{
			$b->setConvertBreaks(true);
			$b->writeSettings();
		}
	}

	function contributeTinymceContent(&$data)
	{
		global $member;
		if ( (cookieVar($CONF['CookiePrefix'] . 'loginkey') )
		  && ($this->_memberCheck($member->getID()) == true) )
		{
			$this->_suspendConvertBreaks($data);
			$this->_recoverTags($data);
			if ($this->getOption('trim_ptag') == 'yes')
			{
				return $this->_delP($data);
			}
		}
	}
	
	function event_PreAddItem(&$data)     { $this->contributeTinymceContent($data);}
	function event_PreUpdateItem(&$data)  { $this->contributeTinymceContent($data);}
	function event_PostAddItem(&$data)    { $this->_restoreConvertBreaks($data); }
	function event_PostUpdateItem(&$data) { $this->_restoreConvertBreaks($data);}

	function _delP (&$item)
	{
			$pattern = "@<p>&nbsp;</p>@";
			$item['body'] = preg_replace(preg_quote($pattern), '', $item['body']);
			$item['more'] = preg_replace(preg_quote($pattern), '', $item['more']);
	}

	function event_PrepareItemForEdit($data)
	{
		$src  = array('<%',     '%>',    '<!%',     '%!>');
		$dist = array('@&lt;%', '%&gt;', '@&lt;!%', '%!&gt;');
		$data['item']['body'] = str_replace($src, $dist, $data['item']['body']);
		$data['item']['more'] = str_replace($src, $dist, $data['item']['more']);
	}

	function _recoverTags (&$item)
	{
		$item['body'] = preg_replace('/@&lt;%(.+?)%&gt;/', '<%$1%>', $item['body']);
		$item['body'] = preg_replace('/@&lt;\!%(.+?)%!&gt;/', '<!%$1%!>', $item['body']);
		$item['body'] = preg_replace('@<br />(.+?)@', "<br />\n$1", $item['body']);
		$item['more'] = preg_replace('/@&lt;%(.+?)%&gt;/', '<%$1%>', $item['more']);
		$item['more'] = preg_replace('/@&lt;\!%(.+?)%!&gt;/', '<!%$1%!>', $item['more']);
		$item['more'] = preg_replace('@<br />(.+?)@', "<br />\n$1", $item['more']);
	}
	
	function event_PrePluginOptionsEdit($data)
	{
		if ($data['plugid'] === $this->getID())
		{
			foreach($data['options'] as $key => $value)
			{
				if (defined($value['description']))
				{
					$data['options'][$key]['description'] = constant($value['description']);
				}
				if (!strcmp($value['type'], 'select') && defined($value['typeinfo']))
				{
					$data['options'][$key]['typeinfo'] = constant($value['typeinfo']);
				}
			}
		}
	}
	
	function conf_mceskin($option_skin_select)
	{
		switch ($option_skin_select)
		{
		case "o2k7":
		case "o2k7_silver":
		case "o2k7_black":
			$result = 'o2k7';
			break;
		default:
			$result = 'default';
		}
		return $result;
	}
	
	function conf_mceskin_variant($option_skin_select)
	{
		switch ($option_skin_select)
		{
		case "o2k7_silver":
			$result = 'silver';
			break;
		case "o2k7_black":
			$result = 'black';
			break;
		default:
			$result = '';
		}
		return $result;
	}
	
	function renderCSS()
	{
		$str[] = '	<style type=text/css>';
		$str[] = '		div.contextMenu table,';
		$str[] = '		div.mceMenu table {';
		$str[] = '			width : auto;';
		$str[] = '			margin : 0;';
		$str[] = '		}';
		$str[] = '	</style>';
		return join(PHP_EOL, $str) . PHP_EOL;
	}
	
	function renderBootStrap($option_use_tgzip)
	{
		global $CONF;
		$mce_url = $this->getAdminURL();
		$filename = ($this->getOption('use_tgzip') == 'yes') ? 'tiny_mce_gzip.js': 'tiny_mce.js';
		$str  = '	<script type="text/javascript" src="' . $mce_url . 'mce_core/tiny_mce/' . $filename . '"></script>' . PHP_EOL;
		$str .= '	<script type="text/javascript" src="' . $mce_url . 'parse.php?file=file_browser_callback.js"></script>' . PHP_EOL;
		if ($this->getOption('use_tgzip') == 'yes')
		{
			$str .= '	<script type="text/javascript" src="' . $mce_url . 'parse.php?file=mce_gz_init.js"></script>' . PHP_EOL;
		}
		$str .= '	<script type="text/javascript" src="' . $mce_url . 'parse.php?file=mce_init.js"></script>' . PHP_EOL;
		return $str;
	}
	
	function read_init($init)
	{
		$init_array = array();
		$init_array = explode("\n", $init);
		$result = '';
		foreach($init_array as $str)
		{
			$str =  trim($str);
			$str = rtrim($str);
			if (preg_match('@^[/#\*]@', $str)) continue;
			$result .= $str . PHP_EOL;
		}
		return $result;
	}
	
	function parse($contents)
	{
		global $CONF;
		
		$mce_url = $this->getAdminURL();
		$enterkey_mode = $this->getOption('enterkey_mode');
		
		$param['mode'] = $this->getOption('detect_mode');
		$param['elements'] = ($param['mode'] == 'exact') ? 'inputbody,inputmore' : 'textareas';
		$param['language'] = _NP_TINYMCE_LANG_CODE;
		
		$param['theme'] = 'advanced';
		$param['skin'] = $this->conf_mceskin($this->getOption('skin_select'));
		$param['skin_variant'] = $this->conf_mceskin_variant($this->getOption('skin_select'));
		
		$param['width'] = $this->getOption('textarea_width');
		$param['content_css'] = $mce_url . 'style/style.css';
		$param['dialog_type'] = $this->getOption('dialog_type');
		$param['file_browser_callback'] = 'callFileBrowser';
		
		$param['force_p_newlines'] =  ($enterkey_mode== 'p') ? 'true' : 'false';
		$param['force_br_newlines'] = ($enterkey_mode== 'br') ? 'true' : 'false';
		$param['forced_root_block'] = ($enterkey_mode== 'p') ? 'p' : '';
		
		$param['popup_css_add'] = $mce_url . 'style/popup_add.css';
		
		$param['document_base_url'] = $CONF['IndexURL'];
		switch($this->getOption('path_style'))
		{
			case 'relative':
				$param['relative_urls']      = 'true';
				$param['remove_script_host'] = 'true';
				$param['convert_urls']       = 'true';
				break;
			case 'absolute':
				$param['relative_urls']      = 'false';
				$param['remove_script_host'] = 'true';
				$param['convert_urls']       = 'true';
				break;
			case 'url':
				$param['relative_urls']      = 'false';
				$param['remove_script_host'] = 'false';
				$param['convert_urls']       = 'true';
				break;
			default:
				$param['relative_urls']      = 'true';
				$param['remove_script_host'] = 'true';
				$param['convert_urls']       = 'false';
		}
		$mce_option = $this->get_mce_option();
		$param['plugins'] = $mce_option['plugins'];
		$param['theme_advanced_buttons1'] = $mce_option['toolbar1'];
		$param['theme_advanced_buttons2'] = $mce_option['toolbar2'];
		$param['theme_advanced_buttons3'] = $mce_option['toolbar3'];
		$param['theme_advanced_buttons4'] = $mce_option['toolbar4'];
		
		$param['theme_advanced_styles'] = $this->getOption('style_label');
		
		$param['extended_valid_elements'] = ($this->getOption('extended_elements') !== 'unlimited') ? $this->getOption('extended_elements') : '*[*]';
		$param['accessibility_warnings'] = ($this->getOption('accessibility') == 'yes') ? 'true':'false';
		
		$param['custom_init'] = $this->read_init($this->getOption('custom_init'));
		
		
		if ($mediatocu = $this->_checkMediatocu())
		{
			$param['FileBrowserURL'] = $mediatocu->getAdminURL() . 'media.php';
		}
		else
		{
			$param['FileBrowserURL'] = $this->getAdminURL() . 'filemanager/mediaphp/media.php';
		}
		$delim = '(<%|%>)';
		$pieces = preg_split('/'.$delim.'/',$contents);

		$maxidx = sizeof($pieces);
		for ($idx = 0; $idx < $maxidx; $idx++)
		{
			echo $pieces[$idx];
			$idx++;
			if ($idx < $maxidx)
			{
				$name = $pieces[$idx];
				echo $param[$name];
			}
		}
	}
	
	function get_mce_option()
	{
		global $member;
		if ($this->getMemberOption($member->id, 'member_custom')=='yes')
		{
			$plugins  = $this->getMemberOption($member->id, 'member_custom_plugin');
			$toolBar1 = $this->getMemberOption($member->id, 'member_custom_btn1');
			$toolBar2 = $this->getMemberOption($member->id, 'member_custom_btn2');
			$toolBar3 = $this->getMemberOption($member->id, 'member_custom_btn3');
			$toolBar4 = $this->getMemberOption($member->id, 'member_custom_btn4');
		}
		else
		{
			$plugins  = $this->getOption('plugin');
			$toolBar1 = $this->getOption('btn1');
			$toolBar2 = $this->getOption('btn2');
			$toolBar3 = "";
			$toolBar4 = "";
		}
		if ($this->getOption('dialog_type') === 'dhtml')
			{$plugins	.= ',inlinepopups';}
		
		return array('plugins'=>$plugins,
					'toolbar1'=>$toolBar1,
					'toolbar2'=>$toolBar2,
					'toolbar3'=>$toolBar3,
					'toolbar4'=>$toolBar4);
	}
}