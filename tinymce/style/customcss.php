<?php
	$strRel = '../../../../';
	include($strRel . 'config.php');
	if (!$member->isLoggedIn())
	{
		doError('You\'re not logged in.');
	} else {
		$tinymce	= $manager->getPlugin('NP_TinyMCE');
		$customcss = $tinymce->getOption('custom_css');
		header("Expires: Thu, 01 Dec 1994 16:00:00 GMT");
		header("Last-Modified: ". gmdate("D, d M Y H:i:s"). " GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		echo $customcss;
	}
?>
