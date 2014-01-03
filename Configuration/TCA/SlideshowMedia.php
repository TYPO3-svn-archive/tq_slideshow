<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_tq_slideshow_media'] = array(
    'ctrl' => $TCA['tx_tq_slideshow_media']['ctrl'],
    'interface' => array(
        'showRecordFieldList' => 'type,title'
    ),
    'types' => array(
        '0'	=> array(
            'showitem' =>  '--div--;EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow_image.tab.standard,title,media_type,video_type,media_video_youtube,media_image_preview,--palette--;LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.palette.type_local;type_local,image,preview,description,link_type,--palette--;LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.palette.type_link;type_link,is_lightbox,effect_forward,effect_backward',
            'canNotCollapse' => '1',
        ),
    ),

    'palettes' => array(
        'standard'  => array(
            'showitem' => 'title,--linebreak--,url,',
            'canNotCollapse' => '1',
        ),

        'type_local'  => array(
            'showitem' => 'media_video_flash,media_video_theora,media_video_h264,',
            'canNotCollapse' => '1',
        ),

        'type_link'  => array(
            'showitem' => 'link_page,link_video,',
            'canNotCollapse' => '1',
        ),

    ),
    'columns' => array(
        'hidden' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array(
                'type' => 'check'
            )
        ),

        'starttime' => array (
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.starttime',
            'config'  => array (
                'type'    => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => 0,
                'default' => 0
            )
        ),
        'endtime' => array (
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.endtime',
            'displayCond' => 'FIELD:media_type:=:1',
            'config'  => array (
                'type'    => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => 0,
                'default' => 0,
                'range' => array (
                    'upper' => 1609369200
                )
            )
        ),

        'media_type' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.media_type',
            'config' => array(
                'type' => 'select',
                'items' => array(
                        array(' --- Bitte wählen --- ',0),
                        array('Image',1),
                        array('Video',2)
                ),
                'size' => 1,
                'maxitems' => 1,
                'eval' => 'required'
            ),
        ),


        #######################################################################
        # Type: IMAGE
        #######################################################################

        'title' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.title',
            'config'  => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            )
        ),

/*
        'image' => array (
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.image',
            'displayCond' => 'FIELD:media_type:=:1',
            'config' => Array (
                'eval' => 'required',
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => 'gif,png,jpeg,jpg',
                'show_thumbs' => 1,
                'max_size' => 1500,
                'uploadfolder' => 'fileadmin/user_upload/tq_slideshow/',
                'show_thumbs' => 1,
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),
*/
        'image' => array(
            'exclude' => 0,
            'displayCond' => 'FIELD:media_type:=:1',
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.image',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('image', array(
                    'foreign_types' => array(),
                    'maxitems' => 1,
                ), $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ),


        'thumbnail_alt' => array (
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.thumbnail_alt',
            'displayCond' => 'FIELD:media_type:=:1',
            'config' => Array (
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => 'gif,png,jpeg,jpg',
                'show_thumbs' => 1,
                'max_size' => 500,
                'uploadfolder' => 'fileadmin/user_upload/tq_slideshow/',
                'show_thumbs' => 1,
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),

        'link_type' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.link_type',
            'displayCond' => 'FIELD:media_type:=:1',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array(' --- Bitte wählen --- ',0),
                    array('LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.page',1),
                    array('LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.image',2),
                    array('LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.youtube',3),
                    array('LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.description',4)
                ),
                'size' => 1,
                'maxitems' => 1,
                'eval' => 'required'
            ),
        ),

        'link_page' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.link_page',
            'displayCond' => 'FIELD:link_type:=:1',
            'config'  => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'wizards' => array(
                    '_PADDING' => 2,
                    'link' => array(
                        'type' => 'popup',
                        'title' => 'Link',
                        'icon' => 'link_popup.gif',
                        'script' => 'browse_links.php?mode=wizard',
                        'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
                        'params' => array(
                            'blindLinkOptions' => 'file,mail,spec,folder',
                            'allowedExtensions' => 'mp3,ogg',
                        ),
                    ),
                ),
            ),
        ),

        'link_video' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.link_youtube',
            'displayCond' => 'FIELD:link_type:=:3',
            'config'  => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            )
        ),

        'is_lightbox' => array(
            'exclude' => 0,
            'displayCond' => 'FIELD:media_type:=:1',
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.is_lightbox',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),


        'preview' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.preview',
            'defaultExtras' => 'richtext[*]',
            'config' => array(
                'type' => 'text',
                'eval' => '',
                'rows' => '5',
                'cols' => 48,
                'wizards' => array(
                    '_PADDING' => 4,
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'LLL:EXT:cms/locallang_ttc.php:bodytext.W.RTE',
                        'icon' => 'wizard_rte2.gif',
                        'script' => 'wizard_rte.php'
                    )
                )
            )
        ),


        'description' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.description',
            'displayCond' => 'FIELD:media_type:=:1',
            'defaultExtras' => 'richtext[*]',
            'config' => array(
                'type' => 'text',
                'eval' => '',
                'rows' => '5',
                'cols' => 48,
                'wizards' => array(
                    '_PADDING' => 4,
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'LLL:EXT:cms/locallang_ttc.php:bodytext.W.RTE',
                        'icon' => 'wizard_rte2.gif',
                        'script' => 'wizard_rte.php'
                    )
                )
            )
        ),


        #######################################################################
        # Type: VIDEO
        #######################################################################

        'video_type' => array(
            'exclude' => 0,
            'displayCond' => 'FIELD:media_type:=:2',
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.video_type',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array(
                        'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.video_type.youtube',
                        'youtube'
                    ),
                    array(
                        'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.local',
                        'local'
                    ),
                ),
                'size' => 1,
                'maxitems' => 1,
                'eval' => 'required'
            ),
        ),

        #######################################################################
        # Type: File
        #######################################################################


        #######################################################################
        # Type: YouTube
        #######################################################################

        'media_video_youtube' => array(
            'exclude' => 0,
            'displayCond' => 'FIELD:media_type:=:2',
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.media_video_youtube',
            'config'  => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),


        #######################################################################
        # Type: File
        #######################################################################

        'media_image_preview' => array(
            'exclude' => 0,
            'displayCond' => 'FIELD:media_type:=:2',
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.media_image_preview',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('media_image_preview', array(
                    'foreign_types' => array(),
                    'maxitems' => 1,
                ), $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ),

        'media_video_flash' => array(
            'exclude' => 0,
            'displayCond' => 'FIELD:media_type:=:2',
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.media_video_flash',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('media_video_flash', array(
                    'foreign_types' => array(),
                    'maxitems' => 1,
                ), 'flv'
            ),
        ),

        'media_video_theora' => array(
            'exclude' => 0,
            'displayCond' => 'FIELD:media_type:=:2',
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.media_video_theora',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('media_video_theora', array(
                    'maxitems' => 1,
                ), 'ogg,ogv'
            ),
        ),

        'media_video_h264' => array(
            'exclude' => 0,
            'displayCond' => 'FIELD:media_type:=:2',
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.media_video_h264',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('media_video_h264', array(
                    'maxitems' => 1,
                ), 'mp4,m4v'
            ),
        ),



        #######################################################################
        # Type: BOTH
        #######################################################################


        'effect_forward' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.effect_forward',
            'config'  => array(
                'type' => 'select',
                'itemsProcFunc' => 'tx_tqslideshow_effectlist->user_effect',
            )
        ),

        'effect_backward' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.effect_backward',
            'config'  => array(
                'type' => 'select',
                'itemsProcFunc' => 'tx_tqslideshow_effectlist->user_effect',
            )
        ),


        ## more fields here ##
    ),
);




###############################################################################
# EXTENSION TQ_FILEUPLOADER FROM DIRECTORY
###############################################################################

?>
