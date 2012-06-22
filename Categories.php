<?php

class Cms_Categories extends Cms_AbstractPlural {

    protected $_dbClass = 'Category';

    public function getAllCategoriesXML($publicationId) {
        try {

            $str = "";

            $record1 = array();

            $record1[0]['categoryId'] = 2365;
            $record1[0]['categoryName'] = "Accessories";

            $record1[1]['categoryId'] = 2352;
            $record1[1]['categoryName'] = "Art and Antiques";

            $record1[2]['categoryId'] = 2347;
            $record1[2]['categoryName'] = "Auctions";

            $record1[3]['categoryId'] = 2348;
            $record1[3]['categoryName'] = "Autos";

            $record1[4]['categoryId'] = 2353;
            $record1[4]['categoryName'] = "Aviation";

            $record1[5]['categoryId'] = 2349;
            $record1[5]['categoryName'] = "Boats and Yachts";

            $record1[6]['categoryId'] = 2358;
            $record1[6]['categoryName'] = "Collectibles";

            $record1[7]['categoryId'] = 2366;
            $record1[7]['categoryName'] = "Design";

            $record1[8]['categoryId'] = 2344;
            $record1[8]['categoryName'] = "Estates";

            $record1[9]['categoryId'] = 2355;
            $record1[9]['categoryName'] = "Gadgets";

            $record1[10]['categoryId'] = 2345;
            $record1[10]['categoryName'] = "Handbags";

            $record1[11]['categoryId'] = 2354;
            $record1[11]['categoryName'] = "Hotels and Resorts";

            $record1[12]['categoryId'] = 2346;
            $record1[12]['categoryName'] = "Jewelry";

            $record1[13]['categoryId'] = 2351;
            $record1[13]['categoryName'] = "Memorabilia";

            $record1[14]['categoryId'] = 2359;
            $record1[14]['categoryName'] = "Most Expensive";

            $record1[15]['categoryId'] = 2343;
            $record1[15]['categoryName'] = "Watches";

            $record1[16]['categoryId'] = 2357;
            $record1[16]['categoryName'] = "Wealth";

            $record1[17]['categoryId'] = 2426;
            $record1[17]['categoryName'] = "Wines";



            $str = "<channel>";
            if ($record1 != null) {
                $str = $str . "<lastbuilddate>";
                $obj = new Cms_Db_Category();
                $obj->set("quantity||1");
                $str = $str . $obj->get("createdTime", 0) . "</lastbuilddate>";
                foreach ($record1 as $val) {
                    $str = $str . "<Item>";
//                    $str = $str . "<categoryId>" . $val->getCategoryId() . "</categoryId>";
                    $str = $str . "<categoryId>" . $val['categoryId'] . "</categoryId>";
//$str=$str.'<guid isPermaLink="true">'."</guid>";
//                    $str = $str . "<categoryName>" . $val->getCategoryName() . "</categoryName>";
                    $str = $str . "<categoryName>" . $val['categoryName'] . "</categoryName>";
                    $str = $str . "</Item>";
                }
            } else {
                $str = $str . "<error>" . "Error!No Record" . "</error>";
            }

            $str = $str . "</channel>";
        } catch (Exception $e) {

            $str = $str . "<Success>False</Success>";
            $str = $str . "<Message>" . $e->getMessage() . "</Message>";
        }
        return $str;
    }

    public function getAllCategories($publicationId) {
        parent::__construct("publicationId||$publicationId");
        return $this->_matchedRecords;
    }

    public function getCategoriesByLevel($level, $publicationId) {
        parent::__construct("level||$level", "publicationId||$publicationId");
        $totalCount = $this->getTotalCount();
        parent::__construct("level||$level", "publicationId||$publicationId", "quantity||$totalCount", "sortColumn||storiesCount");
        return $this->_matchedRecords;
    }
    
 	public function getSubCategoriesByLevel($parentId, $level, $quantity) {
        return $subCategoriesObj =  parent::__construct("level||$level", 'publicationId||' . PUBLICATION_ID, "quantity||$quantity", "parentId||$parentId");
    }

    public function getSubCategories($parentId, $publicationId) {
        parent::__construct("parentId||$parentId", "publicationId||$publicationId");
        $totalCount = $this->getTotalCount();
        parent::__construct("parentId||$parentId", "publicationId||$publicationId", "quantity||$totalCount");
        $totalCount = $this->getTotalCount();
        return $this->_matchedRecords;
    }

    public function getCategoryBySlug($categorySlug, $publicationId) {

        parent::__construct("categorySlug||$categorySlug", "publicationId||$publicationId");

        return $this->_matchedRecords;
    }

    public function getCategoriesByPublication($parentId, $publicationId) {
        parent::__construct("publicationId||$publicationId", "parentId||$parentId", "sortColumn||categorySlug", "order||Y", "sortOrder||asc");
        return $this->_matchedRecords;
    }

    public function getSubCategoriesByPageNumber($parentId, $pageNumber) {
        parent::__construct("parentId||$parentId", "pageNumber||$pageNumber", "quantity||2");

        return $this->_matchedRecords;
    }

    public function getTotalTopic($publicationId) {
        $topic = new Cms_Db_Question();
        $topic->set("order||N", "count||Y", "publicationId||$publicationId");

        $totalquestion = $topic->getTotalCount();
        return $totalquestion;
    }

    public function getTotalAnswer($publicationId) {
        $tp = new Cms_Db_Answer();
        $tp->set("order||N", "count||Y", "publicationId||$publicationId");

        $totalanswer = $tp->gettotalCount();
        return $totalAnswer;
    }

}