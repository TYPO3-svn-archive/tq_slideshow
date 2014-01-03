<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi1',
	array(
		'Content'					=> 'show',
	),
	array(
		'Content'					=> 'show',
	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
    $_EXTKEY,
    'Pi2',
    array(
        'Header'					=> 'show',
    ),
    array(
        'Header'					=> 'show',
    )
);


############### FRONTED OUTPUT JAVASCRIPT CODE ################
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_slideshow']['javascript'][] = 'EXT:tq_slideshow/hooks/class.javascript.php:tx_tq_slideshow_javascript';


############### FRONTED OUTPUT JAVASCRIPT CODE ################
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_slideshow']['addOn'][] = 'EXT:tq_slideshow/hooks/class.fileuploader.php:DirectoryUploader';




############# BACKEND EVEN NUMBER VALIDATION ###########
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals']['tx_tqslideshow_uneven'] = 'EXT:tq_slideshow/lib/class.tx_tqslideshow_uneven.php';



$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_slideshow']['extensions'][] = array(
    'text'     => 'Uploader from Filesystem',
    'handler'   => 'function() {
                       TQ.windowOpen("mod.php?M=web_TqSlideshowBackend&id=1&action=listDirectoryUploader");
                    }'
);


if (TYPO3_MODE=='BE')    {
    //AJAX
    $TYPO3_CONF_VARS['BE']['AJAX']['tqslideshow::slideshow']             = 'TQ\TqSlideshow\Backend\Ajax\SlideshowController->main';
    $TYPO3_CONF_VARS['BE']['AJAX']['tqslideshow::collection']            = 'TQ\TqSlideshow\Backend\Ajax\CollectionController->main';
}



?>