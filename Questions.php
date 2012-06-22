<?php

class Cms_Questions extends Cms_AbstractPlural {

    protected $_dbClass = 'Question';

    /**
     * @author: Mayank Gupta 20110705$.
     * @desc: This function is a generalized function to retrieve all data related to category topics.
     * @param type $categoryId
     * @param type $level
     * @param type $publicationId
     * @param type $count
     * @param type $pageNumber
     * @return type 
     */
    
    
function getPaging($currentPageNumber = 1, $pagePath = 'page/') {
        $tempPaging = array();
        if ($this->getTotalPages() > 1) {
            $p_start = 1;

            if ($currentPageNumber > 5) {
                $p_start = $currentPageNumber - 5;
            }

            $p_end = $p_start + 9;

            if ($p_end > $this->getTotalPages()) {
                $p_end = $this->getTotalPages();
            }

            $i = 0;
            if ($currentPageNumber > 1) {
                $temp_paging_link = HOME_PATH . $pagePath . ( $currentPageNumber - 1 ) . "/";
                if ( 2 == $currentPageNumber and $pagePath == 'page/' ) {
                    $temp_paging_link = HOME_PATH;
                }
                $temp_paging[$i]['numbers'] = "Prev";
                $temp_paging[$i]['link'] = $temp_paging_link;
                $temp_paging[$i]['isActive'] = false;
                $i++;
            }

            for ($x = $p_start; $x <= $p_end; $x++) {
                $temp_paging_link = HOME_PATH . $pagePath . $x . "/";
                if (1 == $x and $pagePath == 'page/' ) {
                    $temp_paging_link = HOME_PATH;
                }

                if ($currentPageNumber != $x) {
                    $temp_paging[$i]['numbers'] = $x;
                    $temp_paging[$i]['link'] = $temp_paging_link;
                    $temp_paging[$i]['isActive'] = false;
                } else {
                    $temp_paging[$i]['numbers'] = $x;
                    $temp_paging[$i]['link'] = "";
                    $temp_paging[$i]['isActive'] = true;
                }
                $i++;
            }

            if ($currentPageNumber < $p_end) {
                $temp_paging_link = HOME_PATH . $pagePath . ( $currentPageNumber + 1 ) . "/";
                $temp_paging[$i]['numbers'] = "Next";
                $temp_paging[$i]['link'] = $temp_paging_link;
                $temp_paging[$i]['isActive'] = false;
            }
        }
        return $temp_paging;
    }
    
    
    public function getCategoryTopic($categoryId, $level, $publicationId, $count='all', $pageNumber=false) {
        $categoryLevel = $level . 'CategoryId';
        if ($count == 'all') {
            $dbTopicObj = new Cms_Db_Question();
            $dbTopicObj->set("$categoryLevel||$categoryId", "count||Y", "order||N", "publicationId||$publicationId","questionStatus||publish");
            return $dbTopicObj->getTotalCount();
        }
        if ($pageNumber) //according to page number.
            parent::__construct("$categoryLevel||$categoryId", 'pageNumber||' . intval($pageNumber), "publicationId||$publicationId","questionStatus||publish");
//        elseif($pageNumber && $count!='all') //according to page number with count.
//             parent::__construct("$categoryLevel||$categoryId", "quantity||$count", "publicationId||$publicationId", 'pageNumber||' . intval($pageNumber));
        else //for count only.
            parent::__construct("$categoryLevel||$categoryId", "quantity||$count", "publicationId||$publicationId","questionStatus||publish");
        return $this->_matchedRecords;
    }

    //un answer question by category id
    public function getUnAnswerTopic($categoryId) {
        parent::__construct("categoryId||$categoryId", "totalAnswer||0","questionStatus||publish");
        return $this->_matchedRecords;
    }

    public function getTotalTopic($publicationId) {
        $templateObj = new Cms_Db_Question();
        $templateObj->set("order||N", "count||Y", "publicationId||$publicationId","questionStatus||publish");
        return $totalCount = $templateObj->getTotalCount();
    }

// return text
    public function getTopicByUser($userId, $count) {
        parent::__construct("createdBy||$userId", "quantity||$count");
        return $this->_matchedRecords;
    }

    public function getTopicByslug($topicSlug, $publicationId) {

        parent::__construct("topicSlug||$topicSlug", "publicationId||$publicationId");
        return $this->_matchedRecords;
    }

    public function getTopicByPublicationId($publicationId, $count) {

        parent::__construct("publicationId||$publicationId", "quantity||$count","questionStatus||publish");
        return $this->_matchedRecords;
    }

    public function getRecentTopics($pageNumber, $publicationId, $quantity) {

        parent::__construct('pageNumber||' . intval($pageNumber), "publicationId||$publicationId", "quantity||$quantity","questionStatus||publish");
        return $this->_matchedRecords;
    }

}