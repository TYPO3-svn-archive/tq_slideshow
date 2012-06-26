<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi1',
	array(
		'Slideshow'					=> 'show',
	),
	array(
		'Slideshow'					=> 'show',
	)
);

############ GENERATE lightbox Content URLS for rzColorbox #####################
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_slideshow']['lightboxLink'][] = 'EXT:tq_slideshow/hooks/class.colorbox.php:tx_tq_slideshow_colorbox';


############### FRONTED OUTPUT JAVASCRIPT CODE ################
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_slideshow']['javascript'][] = 'EXT:tq_slideshow/hooks/class.javascript.php:tx_tq_slideshow_javascript';

############# BACKEND EVEN NUMBER VALIDATION ###########
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals']['tx_tqslideshow_uneven'] = 'EXT:tq_slideshow/lib/class.tx_tqslideshow_uneven.php';



?>