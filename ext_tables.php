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


?>
