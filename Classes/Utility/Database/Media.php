<?php
namespace TQ\TqSlideshow\Utility\Database;

class Media extends \TQ\TqSlideshow\Utility\DatabaseUtility {


    public function getMediaList($list){
        if(!is_array($list)){
            $list = array($list);
        }

        $paramList  = null;
        if(!empty($list)) {
            $paramList  = 'AND '.implode(' AND ', $list);
        }

        $query  = 'SELECT *
                      FROM tx_tq_slideshow_media
                     WHERE deleted = 0
                     AND hidden = 0
                     '.$paramList.'
                     ORDER BY sorting ASC';

        return self::getAll($query);


    }




}