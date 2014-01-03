<?php
$extensionPath = t3lib_extMgm::extPath('tq_slideshow');

return array(
	'tx_tqslideshow_conf'					=> $extensionPath . 'lib/class.conf.php',
	'tx_tqslideshow_effectlist'				=> $extensionPath . 'lib/class.effectlist.php',
    'tx_tqslideshow_collectionlist'			=> $extensionPath . 'lib/class.collectionlist.php',
    'tx_tqslideshow_slideshowlist'          => $extensionPath . 'lib/class.slideshowlist.php',
	'tx_tqslideshow_beconf'					=> $extensionPath . 'lib/class.beconf.php',
    'tx_tqslideshow_directory_uploader'		 => $extensionPath . 'lib/class.directory_uploader.php',


);


?>