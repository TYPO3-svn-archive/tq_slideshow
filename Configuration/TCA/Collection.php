<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');




$TCA['tx_tq_slideshow_collection'] = array(
    'ctrl' => $TCA['tx_tq_slideshow_collection']['ctrl'],
    'interface' => array(
        'showRecordFieldList' => 'title'
    ),
    'types' => array(
        '0'	=> array(
            'showitem' => 'title,mode,slideshow_media',
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
                    array('Bitte wählen Sie aus:', 0),
                    array('LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.mode.manual', '1'),
                //    array('LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.mode.local', '2'),
                ),
                'size' => 1,
                'maxitems' => 1,
                'eval' => 'required'
            ),
        ),

        #######################################################################
        # TAB IMAGES
        #######################################################################
        'slideshow_media' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.article_links',
            'config'  => array(
                'type' => 'inline',
                'foreign_table' => 'tx_tq_slideshow_media',
                'foreign_field' => 'collection_id',
                'foreign_sortby' => 'sorting',
                'foreign_label' => 'title',
                'appearance' => Array(
                    'collapseAll' => 1,
                    'expandSingle' => 1,
                ),
            ),
        ),
    ),
);

#######################################################################
# ADD ON FOR DIRECTORY UPLOADER
#######################################################################
$TCA['tx_tq_slideshow_collection']['columns']['mode']['config']['items'][]  = array('LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.mode.local', '2');

$TCA['tx_tq_slideshow_collection']['columns']['directory_uploader'] = array(
    'exclude'       => 0,
    'label'       => 'LLL:EXT:tq_slideshow/Resources/Private/Language/locallang_tca.xml:slideshow.collection.localDirectory',
    'displayCond' => 'FIELD:mode:=:2',
    'config'  => array(
        'type' => 'user',
        'userFunc' => 'tx_tqslideshow_directory_uploader->field',
    ),
);

$TCA['tx_tq_slideshow_collection']['types']['0']['showitem']   =  'title,mode,directory_uploader,slideshow_media';




?>
