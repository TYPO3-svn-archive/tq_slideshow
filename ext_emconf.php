<?php

########################################################################
# Extension Manager/Repository config file for ext "tq_slideshow".
#
# Auto generated 04-05-2012 12:29
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'TEQneers image slideshow',
	'description' => 'Flexible jQuery slideshow for images and content elements - uses jQuery cycle',
	'category' => 'TEQneers',
	'author' => 'Nico Korthals',
	'author_email' => 'nico@teqneers.de',
	'author_company' => 'TEQneers GmbH & Co KG',
	'shy' => '',
	'dependencies' => 'cms,extbase,fluid,jQuery,pt_extlist',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'version' => '0.9.3',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'extbase' => '',
			'fluid' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			't3jquery' => '',
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:36:{s:9:"ChangeLog";s:4:"d41d";s:10:"README.txt";s:4:"67fa";s:4:"TODO";s:4:"d41d";s:16:"ext_autoload.php";s:4:"67d2";s:21:"ext_conf_template.txt";s:4:"f1d0";s:12:"ext_icon.gif";s:4:"5d8e";s:17:"ext_localconf.php";s:4:"43b4";s:14:"ext_tables.php";s:4:"d703";s:14:"ext_tables.sql";s:4:"6ed3";s:28:"ext_typoscript_constants.txt";s:4:"f866";s:24:"ext_typoscript_setup.txt";s:4:"a6ba";s:42:"Classes/Controller/SlideshowController.php";s:4:"217a";s:28:"Classes/Domain/Slideshow.php";s:4:"02a8";s:42:"Classes/Repository/SlideshowRepository.php";s:4:"8db0";s:39:"Configuration/Flexform/flexform_pi1.xml";s:4:"99d2";s:31:"Configuration/TCA/Slideshow.php";s:4:"d52f";s:40:"Resources/Private/Language/locallang.xml";s:4:"4f97";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"6f9c";s:54:"Resources/Private/Templates/Slideshow/Empty-image.html";s:4:"c57d";s:47:"Resources/Private/Templates/Slideshow/Show.html";s:4:"7ba9";s:55:"Resources/Private/Templates/Slideshow/Show_content.html";s:4:"47aa";s:42:"Resources/Public/Icons/next-horizontal.png";s:4:"f457";s:40:"Resources/Public/Icons/next-vertical.png";s:4:"75f3";s:42:"Resources/Public/Icons/prev-horizontal.png";s:4:"09bd";s:40:"Resources/Public/Icons/prev-vertical.png";s:4:"17bc";s:36:"Resources/Public/Icons/slideshow.gif";s:4:"e922";s:29:"Resources/Public/css/skin.css";s:4:"8935";s:47:"Resources/Public/javascript/jquery.cycle.all.js";s:4:"95cb";s:51:"Resources/Public/javascript/jquery.jcarousel.min.js";s:4:"e21f";s:42:"Resources/Public/javascript/tqslideshow.js";s:4:"148c";s:14:"doc/manual.sxw";s:4:"ba23";s:24:"hooks/class.colorbox.php";s:4:"669d";s:26:"hooks/class.javascript.php";s:4:"07c5";s:18:"lib/class.conf.php";s:4:"8d31";s:24:"lib/class.effectlist.php";s:4:"9523";s:20:"lib/class_beconf.php";s:4:"de3e";}',
);

?>