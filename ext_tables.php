<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');


###############################################################################
# PI1
###############################################################################

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Pi1',
	'TQ Slideshow'
);

$extensionName = t3lib_div::underscoredToUpperCamelCase($_EXTKEY);
$pluginSignaturePi1 = strtolower($extensionName) . '_pi1';

$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignaturePi1] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($pluginSignaturePi1, 'FILE:EXT:'.$_EXTKEY.'/Configuration/Flexform/flexform_pi1.xml');


$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['Tx_TqSlideshow_Utility_WizzardIcon'] = t3lib_extMgm::extPath($_EXTKEY). 'Configuration/Utility/WizzardIcon.php';


###############################################################################
# PI2
###############################################################################
Tx_Extbase_Utility_Extension::registerPlugin(
    $_EXTKEY,
    'Pi2',
    'TQ Slideshow Header'
);


########################
# TCA: tq_slideshow
########################

TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_tq_slideshow', 'EXT:tx_tq_slideshow/Resources/Private/Language/locallang_tca.xml');
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_tq_slideshow');
$TCA['tx_tq_slideshow'] = array (
    'ctrl' => array (
        'title'             => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow',
        'label' 			=> 'title',
        'tstamp' 			=> 'tstamp',
        'crdate' 			=> 'crdate',
        //'origUid' 			=> 't3_origuid',
        // Delete flag is handled via hook, do not enable here!!
        //'delete' 			=> 'deleted',
        'enablecolumns' 	=> array(
            'disabled' => 'hidden'
        ),
        'dividers2tabs'		=> true,
        'requestUpdate' => 'media_mode',
        'dynamicConfigFile' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Slideshow.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif',
    )
);



########################
# TCA: tq_slideshow MEDIA
########################
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_tq_slideshow_media', 'EXT:tx_tq_slideshow/Resources/Private/Language/locallang_tca.xml');
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_tq_slideshow_media');
$TCA['tx_tq_slideshow_media'] = array (
    'ctrl' => array (
        'title'              => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.image',
        'label' 			=> 'title',
        'tstamp' 			=> 'tstamp',
        'crdate' 			=> 'crdate',
        //'origUid' 			=> 't3_origuid',
        'delete' 			=> 'deleted',
        'sortby'            => 'sorting',
        'enablecolumns' 	=> array(
            'disabled' => 'hidden'
        ),
        'dividers2tabs'		=> true,
        'requestUpdate' => 'media_type,link_type',
        'dynamicConfigFile' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/SlideshowMedia.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif',
    )
);

########################
# TCA: tq_slideshow COLLECTION
########################
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_tq_slideshow_collection', 'EXT:tx_tq_slideshow/Resources/Private/Language/locallang_tca.xml');
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_tq_slideshow_collection');
$TCA['tx_tq_slideshow_collection'] = array (
    'ctrl' => array (
        'title'              => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.image',
        'label' 			=> 'title',
        'tstamp' 			=> 'tstamp',
        'crdate' 			=> 'crdate',
        //'origUid' 			=> 't3_origuid',
        'delete' 			=> 'deleted',
        'sortby'            => 'sorting',
        'enablecolumns' 	=> array(
            'disabled' => 'hidden'
        ),
        'requestUpdate' => 'mode',
        'dynamicConfigFile' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Collection.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif',
    )
);


###############################################################################
# Backend
###############################################################################

if (TYPO3_MODE == 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'TQ.'.$_EXTKEY,							# Extension - Key
        'web',										# Category
        'backend',									# Modulname
        'before:info',								# Position
        array( 'Backend'        => 'main',
        ),			# Controller array
        array(
            'access' => 'user,group',
            'icon'   => 'EXT:'.$_EXTKEY.'/Resources/Public/Icons/moduleicon.gif',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_backend.xml',
            //'navigationComponentId' => 'typo3-pagetree',
        )
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'TQ.'.$_EXTKEY,				# Extension - Key
        'web',										# Category
        'collection',									# Modulname
        'before:info',								# Position
        array( 'Collection'        => 'main',
        ),			# Controller array
        array(
            'access' => 'user,group',
            'icon'   => 'EXT:'.$_EXTKEY.'/Resources/Public/Icons/moduleicon.gif',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_collection.xml',
            //'navigationComponentId' => 'typo3-pagetree',
        )
    );


}





?>
