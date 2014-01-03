<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_tq_slideshow'] = array(
    'ctrl' => $TCA['tx_tq_slideshow']['ctrl'],
    'interface' => array(
        'showRecordFieldList' => 'title'
    ),
    'types' => array(
        '0'	=> array(
            'showitem' =>
                '--div--;LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.tab.general,title,mode,changeTime,pageSelector,'
                .'--div--;LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.tab.media,media_mode,slideshow_media,collection_id,'
                .'--div--;LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.tab.thumbnails,'
            ##    .'--div--;LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.tab.toolbar,showToolbar,'
            ##    .'--div--;LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.tab.thumbnails,showThumbnails,'
                .'--div--;LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.tab.settings,showPaging,showThumbnails,showToolbar,keyEvents',
            'canNotCollapse' => '1',
        ),
    ),

    'palettes' => array(
        'standard'  => array(
            'showitem' => 'type,--linebreak--,title',
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

        #######################################################################
        # TAB GENERAL
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

        'mode' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.mode',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array(
                        'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.mode.normal',
                        'normal'
                    ),
                    array(
                        'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.mode.random',
                        'random'
                    ),
                ),
                'size' => 1,
                'maxitems' => 1,
                'eval' => 'required'
            ),
        ),

        'changeTime' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.changeTime',
            'config'  => array(
                'type' => 'input',
                'size' => 5,
                'eval' => 'trim,int'
            )
        ),

        'pageSelector'  => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.pageSelector',
            'config'  => array(
                'type' => 'select',
                'foreign_table' => 'pages',
                'foreign_table_where' => ' AND doktype = 1 OR doktype = 4',
                'size' => 10,
                "minitems" => 0,
                "maxitems" => 10,
                'renderMode' => 'tree',
                'treeConfig' => array(
                    'expandAll' => true,
                    'parentField' => 'pid',
                    'appearance' => array(
                        'showHeader' => TRUE,
                    ),
                ),
            ),
        ),

        #######################################################################
        # TAB MEDIA
        #######################################################################

        'media_mode' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.mode',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('Bitte wählen Sie aus:', 0),
                    array('LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.mode.manual', '1'),
                    array('LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.mode.collection', '2'),
                ),
                'size' => 1,
                'maxitems' => 1,
                'eval' => 'required'
            ),
        ),


        'slideshow_media' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.article_links',
            'displayCond' => 'FIELD:media_mode:=:1',
            'config'  => array(
                'type' => 'inline',
                'foreign_table' => 'tx_tq_slideshow_media',
                'foreign_field' => 'slideshow_id',
                'foreign_sortby' => 'sorting',
                'foreign_label' => 'title',
                'appearance' => Array(
                    'collapseAll' => 1,
                    'expandSingle' => 1,
                ),
            ),
        ),

        'collection_id' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.collection_id',
            'displayCond' => 'FIELD:media_mode:=:2',
            'config'  => array(
                'type' => 'select',
                'itemsProcFunc' => 'tx_tqslideshow_collectionlist->user_collection',
            )
        ),


        #######################################################################
        # TAB SETTINGS
        #######################################################################

        'templateFile' => array (
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.templateFile',
            'config' => Array (
                'type' => 'group',
                'internal_type' => 'file_reference',
                'allowed' => 'html,htm,tmpl',
                'show_thumbs' => 1,
                'max_size' => 500,
                'uploadfolder' => 'fileadmin/user_upload/tq_slideshow/',
                'show_thumbs' => 1,
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),

        'keyEvents' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.keyEvents',
            'config' => array(
                'type' => 'check',
                'default' => '1'
            ),
        ),



        #######################################################################
        # TAB TOOLBAR
        #######################################################################
        'showToolbar' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.showToolbar',
            'config' => array(
                'type' => 'check',
                'default' => '1'
            ),
        ),


        #######################################################################
        # TAB PAGING
        #######################################################################
        'showPaging' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.showPaging',
            'config' => array(
                'type' => 'check',
                'default' => '1'
            ),
        ),



        #######################################################################
        # TAB THUMBNAILS
        #######################################################################
        'showThumbnails' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.showThumbnails',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),

    ),
);


?>
