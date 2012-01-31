<?php

define('_NP_TINYMCE_PLUGIN_MEMBERCUSTOM_DEAULTVALUE',   'contextmenu');
define('_NP_TINYMCE_TOOLBAR_1_MEMBERCUSTOM_DEAULTVALUE',   'undo,redo,|,image,link,|,bold,strikethrough,bullist,numlist,hr,|,code,|,help');
define('_NP_TINYMCE_TOOLBAR_2_MEMBERCUSTOM_DEAULTVALUE',   '');
define('_NP_TINYMCE_TOOLBAR_3_MEMBERCUSTOM_DEAULTVALUE',   '');
define('_NP_TINYMCE_TOOLBAR_4_MEMBERCUSTOM_DEAULTVALUE',   '');

define('_NP_TINYMCE_PLUGIN_DEAULTVALUE',   'autolink,save,advlist,style,fullscreen,advimage,paste,advlink,media,contextmenu,table');
define('_NP_TINYMCE_TOOLBAR_1_DEAULTVALUE',   'save,undo,redo,|,bold,italic,forecolor,backcolor,strikethrough,formatselect,fontsizeselect,pastetext,pasteword,code,|,fullscreen,help');
define('_NP_TINYMCE_TOOLBAR_2_DEAULTVALUE',   'image,media,link,unlink,anchor,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,blockquote,outdent,indent,|,table,hr,|,styleprops,removeformat');

define('_NP_TINYMCE_CUSTOM_CSS_DEFAULTVALUE',   '/* sample */
.justifyleft  {float:left;  margin-right:10px;margin-bottom:5px;}
.justifyright {float:right; margin-left: 10px;margin-bottom:5px;}
');

// create plugin options (member options)
$this->createMemberOption(
	'use_tinymce',
	_NP_TINYMCE02,
	'yesno',
	'yes'
);
$this->createMemberOption(
	'member_custom',
	_NP_TINYMCE03,
	'yesno',
	'no'
);
$this->createMemberOption(
	'member_custom_plugin',
	_NP_TINYMCE_PLUGIN_MEMBERCUSTOM_LABEL,
	'textarea',
	_NP_TINYMCE_PLUGIN_MEMBERCUSTOM_DEAULTVALUE
);
$this->createMemberOption(
	'member_custom_btn1',
	_NP_TINYMCE_TOOLBAR_1_MEMBERCUSTOM_LABEL,
	'textarea',
	_NP_TINYMCE_TOOLBAR_1_MEMBERCUSTOM_DEAULTVALUE
);
$this->createMemberOption(
	'member_custom_btn2',
	_NP_TINYMCE_TOOLBAR_2_MEMBERCUSTOM_LABEL,
	'textarea',
	_NP_TINYMCE_TOOLBAR_2_MEMBERCUSTOM_DEAULTVALUE
);
$this->createMemberOption(
	'member_custom_btn3',
	_NP_TINYMCE_TOOLBAR_3_MEMBERCUSTOM_LABEL,
	'textarea',
	_NP_TINYMCE_TOOLBAR_3_MEMBERCUSTOM_DEAULTVALUE
);
$this->createMemberOption(
	'member_custom_btn4',
	_NP_TINYMCE_TOOLBAR_4_MEMBERCUSTOM_LABEL,
	'textarea',
	_NP_TINYMCE_TOOLBAR_4_MEMBERCUSTOM_DEAULTVALUE
);

// create plugin options (admin)
$this->createOption(
	'textarea_width',
	'_NP_TINYMCE_TEXTAREA_WIDTH',
	'text',
	'100%'
);
$this->createOption(
	'enterkey_mode',
	'_NP_TINYMCE_ENTERKEY_MODE_LABEL',
	'select',
	'p',
	'_NP_TINYMCE_ENTERKEY_MODE_VALUE'
);
$this->createOption(
	'dialog_type',
	'_NP_TINYMCE_DIALOG_TYPE_LABEL',
	'select',
	'dhtml',
	'dhtml|dhtml|modal|modal|window|window'
);
$this->createOption(
	'extended_elements',
	'_NP_TINYMCE04',
	'select',
	'unlimited',
	'_NP_TINYMCE04_VALUE'
);
$this->createOption(
	'use_tgzip',
	'_NP_TINYMCE05',
	'yesno',
	'no'
);
$this->createOption(
	'path_style',
	'_NP_TINYMCE06',
	'select',
	'absolute',
	'_NP_TINYMCE10'
);
$this->createOption(
	'accessibility',
	'_NP_TINYMCE07',
	'yesno',
	'no'
);
$this->createOption(
	'skin_select',
	'_NP_TINYMCE08',
	'select',
	'default',
	'_NP_TINYMCE09'
);
$this->createOption(
	'plugin',
	'_NP_TINYMCE_PLUGIN_LABEL',
	'textarea',
	_NP_TINYMCE_PLUGIN_DEAULTVALUE
);
$this->createOption(
	'btn1',
	'_NP_TINYMCE_TOOLBAR_1_LABEL',
	'textarea',
	_NP_TINYMCE_TOOLBAR_1_DEAULTVALUE
);
$this->createOption(
	'btn2',
	'_NP_TINYMCE_TOOLBAR_2_LABEL',
	'textarea',
	_NP_TINYMCE_TOOLBAR_2_DEAULTVALUE
);
$this->createOption(
	'custom_css',
	'_NP_TINYMCE_CUSTOM_CSS',
	'textarea',
	_NP_TINYMCE_CUSTOM_CSS_DEFAULTVALUE
);
$this->createOption(
	'include_css',
	'_NP_TINYMCE_INCLUDE_CSS',
	'yesno',
	'yes'
);
$this->createOption(
	'style_label',
	'_NP_TINYMCE_THEME_ADVANCED_STYLES',
	'textarea',
	_NP_TINYMCE_THEME_ADVANCED_STYLES_DEFAULTVALUE
);
$custom_init = file_get_contents($inc_dir . 'custom_init.php');
$this->createOption(
	'custom_init',
	'_NP_TINYMCE_INIT',
	'textarea',
	$custom_init
);
$this->createOption(
	'detect_mode',
	'_NP_TINYMCE_DETECT_MODE_LABEL',
	'select',
	'exact',
	'_NP_TINYMCE_DETECT_MODE_VALUE'
);
$this->createOption(
	'trim_ptag',
	'_NP_TINYMCE_TRIM_PTAG',
	'yesno',
	'yes'
);
$this->createOption(
	'def_dir_mode',
	'_NP_TINYMCE_DEF_DIR_MODE',
	'select',
	'dhtml',
	'_NP_TINYMCE_DEF_DIR_MODE_VALUE'
);
$this->createOption(
	'def_dir',
	'_NP_TINYMCE_DEF_DIR_VALUE',
	'text',
	''
);
$this->createItemOption(
	'use_tinymce',
	_NP_TINYMCE02,
	'yesno',
	'yes'
);


// disable the default javascript editbar that comes with nucleus
sql_query(
	"UPDATE "
	. sql_table('config')
	. " SET   value = '1'"
	. " WHERE name  = 'DisableJSTools'"
);
?>