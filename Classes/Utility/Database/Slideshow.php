<?php
namespace TQ\TqSlideshow\Utility\Database;

class Slideshow extends \TQ\TqSlideshow\Utility\DatabaseUtility {


    public function getSlideshow($list){
        global $TSFE;
        $pageId = $TSFE->page['uid'];

        if(!is_array($list)){
            $list = array($list);
        }

        $paramList  = null;
        if(!empty($list)) {
            $paramList  = 'AND '.implode(' AND ', $list);
        }

        $query  = 'SELECT *
                     FROM tx_tq_slideshow
                    WHERE deleted = 0
                      AND hidden = 0 '.$paramList;


        $row    = self::getAll($query);

        if(count($row) > 1 ) {
            foreach($row as $r ) {
                $idList = explode(',',$r['pageSelector']);
                foreach($idList as $id) {
                    if($id == $pageId) {
                        $row    = array($r);
                        break 2;
                    }
                }
            }
        }

       return $row[0];


    }




}