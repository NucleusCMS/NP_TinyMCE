function callFileBrowser (field_name, url, type, win)
{
//	alert("Field_Name: " + field_name + "\\nURL: " + url + "\\nType: " + type + "\\nWin: " + win); //debug code
	var cmsURL = '<%FileBrowserURL%>';
	tinyMCE.activeEditor.windowManager.open
	({
		file : cmsURL,
		width : 500,
		height : 450,
		resizable : 'yes',
		inline : 'yes',
		close_previous : 'no'
	},
	{
		w_n : win,
		f_n : field_name,
		file_path : field_name,
		file_type : type
	});
	return false;
}
