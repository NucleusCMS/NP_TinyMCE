<?php

// if your 'plugin' directory is not in the default location,
// edit this variable to point to your site directory
// (where config.php is)
$strRel = '../../../';

include($strRel . 'config.php');
if (!$member->isLoggedIn())
doError('You\'re not logged in.');

include($DIR_LIBS . 'PLUGINADMIN.php');

// create the admin area page
$oPluginAdmin = new PluginAdmin('TinyMCE');

if(strpos($_GET['file'], '/') !== false) exit;
$file = file_get_contents('inc/' . $_GET['file']);

global $manager;
$mce = $manager->getPlugin('NP_TinyMCE');
$mce->parse($file);
