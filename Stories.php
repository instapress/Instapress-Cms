<?php

class Cms_Stories extends Cms_AbstractPlural {

    protected $_dbClass = 'Story';

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
    
	function getSmallPaging($currentPageNumber = 1, $pagePath = 'page/') {
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
                if (2 == $currentPageNumber) {
                    $temp_paging_link = HOME_PATH;
                }
                $temp_paging[$i]['numbers'] = "Prev";
                $temp_paging[$i]['link'] = $temp_paging_link;
                $temp_paging[$i]['isActive'] = false;
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

    public function getArticlesXML($publicationId, $pageNo) {
        // checking the validity of page number
        $obj = new Cms_Db_Story();
        $obj->set("publicationId||$publicationId", "count||Y", "storyPublished||Y");
        $totalpage = $obj->getTotalPages();
        $totalcount = $obj->getTotalCount();
        if ($totalcount > 0) {
//        $Totalpage=ceil($total/$quantity);
            if ($pageNo > $totalpage) {
                //set the pageno for last page
                $pageNo = $totalpage;
            }
            $obj1 = new Cms_Db_Story();
            $obj1->setForcedLimit($pageNo * 10 - 4, 10);
            $obj1->set("publicationId||$publicationId", "storyPublished||Y");
            $result1 = $obj1;
            $count = $obj1->getResultCount();
        } else {
            $count = 0;
        }
//        parent::__construct("publicationId||$publicationId", "pageNumber||$pageNo");
////$i=0;
//        $record1 = array();
//        $record1 = $this->_matchedRecords; //check for error or no record
//        $count = count($record1);
        $str = "<channel>";
        $str = $str . "<totalarticle>" . $totalcount . "</totalarticle>";
        try {
            if ($count > 0) {
                $str = $str . "<lastbuilddate>";
                $obj = new Cms_Db_Story();
                $obj->set("quantity||1");
                $createdate = $obj->get("createdTime", 0);
                $strDate = date("D,d M Y H:i:s " . '+0000', strtotime($createdate));
                $str = $str . $strDate . "</lastbuilddate>";
                $obj2 = new Cms_Db_StoryTag();
                $str = $str . "<Success>True</Success>";
                $str = $str . "<Message>Record Found For the Request</Message>";

                for ($i = 0; $i < $count; $i++) {
//$i=$i+1;o
//if($i>10)
//  break;
                    $val = new Cms_Story($obj1->get("storyId", $i));
                    $path = array();


//$source=$story->find('div[class="contentImage"]')->find('img')->src;
//$source.setAttribute("width","320");
//$source1=$story->find('div[class="contentImage"]')->find('img')->src;
//$source1.setAttribute("width","50");
//$source1.setAttribute("height","50");
//$stag= "<![CDATA[$story]]>";
                    $storyId = $val->getStoryId();
                    $str = $str . "<item>";
                    $str = $str . "<storyid>" . $val->getstoryId() . "</storyid>";
                    $str = $str . "<creator>" . htmlentities($val->getauthorByLine()) . "</creator>";
                    $strdate = $val->getCreatedTime();
                    $strDate = date("D,d M Y H:i:s +0000", strtotime($strdate));
                    $str = $str . "<pubDate>" . $strDate . "</pubDate>";
                    $str = $str . "<title><![CDATA[" . $val->getstoryTitle() . "]]> </title>";
                    $str = $str . '<guid isPermaLink="true">' . $val->getPermalink() . "</guid>";
//                    $str = $str . "<storyExcerpt>" . $val->getstoryExcerpt() . "</storyExcerpt>";
//                    $str = $str . "<storyTags>" . htmlentities($val->getstoryTags()) . "</storyTags>";
                    $story = Instapress_Core_Text::processBeforeView(Cms_StoryUtility::processBeforeViewforMobile($val->getstoryWebContent()));
                    $str = $str . "<content>" . "<![CDATA[$story]]>" . "</content>";
//$str=$str."<publishedOn>".$obj2->getstoryTime($val->getstoryId())."</publishedOn>";
//$str=$str."<publishDate>".$val->getstoryTime()."</publishDate>";

                    $str = $str . "<primaryImagePath>" . $val->getprimaryImagePath() . "</primaryImagePath>";
//echo "url for the image is".$val->getprimaryImagePath();
                    $libImageObj = new Instapress_Core_Image(CLIENT_ID);
                    $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 320, 200);
                    $str = $str . "<big_image>" . $imageUrl . "</big_image>";
                    $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 127, 95);
                    $str = $str . "<small_image>" . $imageUrl . "</small_image>";
//$str=$str."<smallImage>".$source1."</smallImage>";
                    $str = $str . "</item>";
                }
            } else {
                $str = $str . "<Success>False</Success>";
                $str = $str . "<Message>No Record Found</Message>";
            }
        } catch (Exception $e) {

            $str = $str . "<Success>False</Success>";
            $str = $str . "<Message>" . $e->getMessage() . "</Message>";
        }
        $str = $str . "</channel>";
        $str = $str . "";
        return $str;
    }

    public function getAllStoriesXML($CategoryId, $pageNo) {

        $CategoryId = intval($CategoryId);
        // checking the validity of page number
        $obj = new Cms_Db_Story();
        $obj->set("firstCategoryId||$CategoryId", "count||Y", "storyPublished||Y");
        $totalpage = $obj->getTotalPages();
        $totalcount = $obj->getTotalCount();
        if ($totalcount > 0) {
//        $Totalpage=ceil($total/$quantity);
            if ($pageNo > $totalpage) {
                //set the pageno for last page
                $pageNo = $totalpage;
            }


//        parent::__construct("firstCategoryId||$CategoryId", "quantity||10", "pageNumber||$pageNo");
//        $record1 = array();
//        $record1 = $this->_matchedRecords; //check for error or no record
//        $count = count($record1);
            $obj1 = new Cms_Db_Story();

            $obj1->set("firstCategoryId||$CategoryId", "storyPublished||Y", "pageNumber||$pageNo");
            $result1 = $obj1;
            $count = $obj1->getResultCount();
        } else {
            $count = 0;
        }
        $str = "<channel>";
        $str = $str . "<totalarticle>" . $count . "</totalarticle>";
        try {
            if ($count > 0) {
                $str = $str . "<lastbuilddate>";
                $obj = new Cms_Db_Story();
                $obj->set("quantity||1");
                $createdate = $obj->get("createdTime", 0);
                $strDate = date("D,d M Y H:i:s " . '+0000', strtotime($createdate));
                $str = $str . $strDate . "</lastbuilddate>";
                $obj2 = new Cms_Db_StoryTag();
                $str = $str . "<Success>True</Success>";
                $str = $str . "<Message>Record Found For the Request</Message>";
                for ($i = 0; $i < $count; $i++) {
//$i=$i+1;
//if($i>10)
//  break;
                    $val = new Cms_Story($obj1->get("storyId", $i));
                    $path = array();


//$source=$story->find('div[class="contentImage"]')->find('img')->src;
//$source.setAttribute("width","320");
//$source1=$story->find('div[class="contentImage"]')->find('img')->src;
//$source1.setAttribute("width","50");
//$source1.setAttribute("height","50");
//$stag= "<![CDATA[$story]]>";
                    $storyId = $val->getStoryId();
                    $str = $str . "<item>";
                    $str = $str . "<storyid>" . $val->getstoryId() . "</storyid>";
                    $str = $str . "<creator>" . htmlentities($val->getauthorByLine()) . "</creator>";
                    $strdate = $val->getCreatedTime();
                    $strDate = date("D,d M Y H:i:s +0000", strtotime($strdate));
                    $str = $str . "<pubDate>" . $strDate . "</pubDate>";
                    $str = $str . "<title><![CDATA[" . $val->getstoryTitle() . "]]> </title>";
                    $str = $str . '<guid isPermaLink="true">' . $val->getPermalink() . "</guid>";
//                    $str = $str . "<storyExcerpt>" . $val->getstoryExcerpt() . "</storyExcerpt>";
//                    $str = $str . "<storyTags>" . htmlentities($val->getstoryTags()) . "</storyTags>";
                    $story = Instapress_Core_Text::processBeforeView(Cms_StoryUtility::processBeforeViewforMobile($val->getstoryWebContent()));
                    $str = $str . "<content>" . "<![CDATA[$story]]>" . "</content>";
//$str=$str."<publishedOn>".$obj2->getstoryTime($val->getstoryId())."</publishedOn>";
//$str=$str."<publishDate>".$val->getstoryTime()."</publishDate>";




                    $str = $str . "<primaryImagePath>" . $val->getprimaryImagePath() . "</primaryImagePath>";
//echo "url for the image is".$val->getprimaryImagePath();
                    $libImageObj = new Instapress_Core_Image(CLIENT_ID);
                    $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 320, 200);
                    $str = $str . "<big_image>" . $imageUrl . "</big_image>";
                    $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 127, 95);
                    $str = $str . "<small_image>" . $imageUrl . "</small_image>";
//$str=$str."<smallImage>".$source1."</smallImage>";
                    $str = $str . "</item>";
                }
            } else {
                $str = $str . "<Success>False</Success>";
                $str = $str . "<Message>No Record Found</Message>";
            }
        } catch (Exception $e) {

            $str = $str . "<Success>False</Success>";
            $str = $str . "<Message>" . $e->getMessage() . "</Message>";
        }
        $str = $str . "</channel>";
        $str = $str . "";
        return $str;
    }

    public function searchStoryXML($searchtag, $resultQuantity, $position) {//$searchtag is any word from story
        $objsearch = new Cms_Db_Story();
        $tag = array();
        $str = "";
        $objsearch->setForcedConditon(" storyExcerpt like '%" . $searchtag . "%' or storyTitle like '%" . $searchtag . "%'");
        $objsearch->set("count||Y", "order||N");
        $objSearchCount = $objsearch->getTotalCount();
        $str = "<channel>";

        $str = $str . "<totalarticle>" . $objSearchCount . "</totalarticle>";
//        echo"Total Records :" . $objSearchCount . "<br/s>";
//        $resultQuantity = 30;
//        $position = 33;
        if ($position > $objSearchCount) {
            $objSearchCount = 0;
        }
        $Totalpage = ceil($objSearchCount / $resultQuantity);
        $lastpage = $objSearchCount - ($Totalpage - 1) * $resultQuantity;
//        echo"Last Page total records :" . $lastpage . "<br/>";
//        echo"TOTAL page :" . $Totalpage . "<br/>";
        $pageNo = $position / $resultQuantity;
        if (is_int($pageNo)) {
//            echo"needed page no :" . $pageNo;
            $two_page = false;
        } else {
            $pageNo = ceil($pageNo);
//            echo"needed page no :" . $pageNo;
            $two_page = true;
        }
        if ($resultQuantity == $position) {
            $pageNo = 2;
            $two_page = false;
        }
//        echo"<br/>";


        try {
            if ($objSearchCount > 0) {
                $objsearch = new Cms_Db_Story();
                $objsearch->set("quantity||1");
                $str = $str . "<lastbuilddate>";
                $createdate = $objsearch->get("createdTime", 0);
                $strDate = date("D,d M Y H:i:s " . '+0000', strtotime($createdate));
                $str = $str . $strDate . "</lastbuilddate>";
                $str = $str . "<success>true</success><message>record found for the request</message>";
                $objsearch->setForcedConditon(" storyExcerpt like '%" . $searchtag . "%' or storyTitle like '%" . $searchtag . "%'");


                if ($pageNo != $Totalpage) {
                    if ($two_page) {
                        $position = $position - $resultQuantity * ($pageNo - 1);
//                        echo"Position" . $position . "<br/>";
                        $objsearch->set("quantity||$resultQuantity", "pageNumber||$pageNo");
                        for ($i = $position; $i < $resultQuantity; $i++) {
                            $storyId = $objsearch->get("storyId", $i);
                            $val = new Cms_Story($storyId);
                            $str = $str . "<item>";
                            $str = $str . "<storyId>" . $storyId . "</storyId>";
                            $str = $str . "<creator>" . htmlentities($val->getauthorByLine()) . "</creator>";
                            $str = $str . "<title><![CDATA[" . $val->getstoryTitle() . "]]> </title>";
                            $strDate = $val->getcreatedTime();
                            $strDate = date("D,d M Y H:i:s " . '+0000', strtotime($strDate));
                            $str = $str . "<pubDate>" . $strDate . "</pubDate>";
                            $content = Instapress_Core_Text::processBeforeView(Cms_StoryUtility::processBeforeViewforMobile($val->getstoryWebContent()));
                            $str = $str . "<content>" . "<![CDATA[$content]]>" . "</content>";
                            $str = $str . '<guid isPermaLink="true">' . $val->getPermalink() . "</guid>";
                            $str = $str . "<primaryImagePath>" . $val->getprimaryImagePath() . "</primaryImagePath>";
                            $libImageObj = new Instapress_Core_Image(CLIENT_ID);
                            $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 320, 200);
                            $str = $str . "<big_image>" . $imageUrl . "</big_image>";
                            $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 127, 95);
                            $str = $str . "<small_image>" . $imageUrl . "</small_image>";
                            $str = $str . "</item>";
                        }
                        $pageNo = $pageNo + 1;
                        $objsearch = new Cms_Db_Story();
                        $objsearch->setForcedConditon(" storyExcerpt like '%" . $searchtag . "%' or storyTitle like '%" . $searchtag . "%'");
                        $objsearch->set("quantity||$resultQuantity", "pageNumber||$pageNo");
                        //    $position=$resultQuantity-$position;
                        for ($i = 0; $i < $position; $i++) {
                            $storyId = $objsearch->get("storyId", $i);
                            $val = new Cms_Story($storyId);
                            $str = $str . "<item>";
                            $str = $str . "<storyId>" . $storyId . "</storyId>";
                            $str = $str . "<creator>" . htmlentities($val->getauthorByLine()) . "</creator>";
                            $str = $str . "<title><![CDATA[" . $val->getstoryTitle() . "]]> </title>";
                            $strDate = $val->getcreatedTime();
                            $strDate = date("D,d M Y H:i:s " . '+0000', strtotime($strDate));
                            $str = $str . "<pubDate>" . $strDate . "</pubDate>";
                            $content = Instapress_Core_Text::processBeforeView(Cms_StoryUtility::processBeforeViewforMobile($val->getstoryWebContent()));
                            $str = $str . "<content>" . "<![CDATA[$content]]>" . "</content>";
                            $str = $str . '<guid isPermaLink="true">' . $val->getPermalink() . "</guid>";
                            $str = $str . "<primaryImagePath>" . $val->getprimaryImagePath() . "</primaryImagePath>";
                            $libImageObj = new Instapress_Core_Image(CLIENT_ID);
                            $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 320, 200);
                            $str = $str . "<big_image>" . $imageUrl . "</big_image>";
                            $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 127, 95);
                            $str = $str . "<small_image>" . $imageUrl . "</small_image>";
                            $str = $str . "</item>";
                        }
                    } else {
                        if ($pageNo == 0) {
                            $objsearch->set("quantity||$resultQuantity");
                            for ($i = 0; $i < $resultQuantity; $i++) {
                                $storyId = $objsearch->get("storyId", $i);
                                $val = new Cms_Story($storyId);
                                $str = $str . "<item>";
                                $str = $str . "<storyId>" . $storyId . "</storyId>";
                                $str = $str . "<creator>" . htmlentities($val->getauthorByLine()) . "</creator>";
                                $str = $str . "<title><![CDATA[" . $val->getstoryTitle() . "]]> </title>";
                                $strDate = $val->getcreatedTime();
                                $strDate = date("D,d M Y H:i:s " . '+0000', strtotime($strDate));
                                $str = $str . "<pubDate>" . $strDate . "</pubDate>";
                                $content = Instapress_Core_Text::processBeforeView(Cms_StoryUtility::processBeforeViewforMobile($val->getstoryWebContent()));
                                $str = $str . "<content>" . "<![CDATA[$content]]>" . "</content>";
                                $str = $str . '<guid isPermaLink="true">' . $val->getPermalink() . "</guid>";
                                $str = $str . "<primaryImagePath>" . $val->getprimaryImagePath() . "</primaryImagePath>";
                                $libImageObj = new Instapress_Core_Image(CLIENT_ID);
                                $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 320, 200);
                                $str = $str . "<big_image>" . $imageUrl . "</big_image>";
                                $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 127, 95);
                                $str = $str . "<small_image>" . $imageUrl . "</small_image>";
                                $str = $str . "</item>";
                            }
                        } else {
                            $objsearch->set("quantity||$resultQuantity", "pageNumber||$pageNo");
                            for ($i = 0; $i < $resultQuantity; $i++) {

                                $storyId = $objsearch->get("storyId", $i);
                                $val = new Cms_Story($storyId);
                                $str = $str . "<item>";
                                $str = $str . "<storyId>" . $storyId . "</storyId>";
                                $str = $str . "<creator>" . htmlentities($val->getauthorByLine()) . "</creator>";
                                $str = $str . "<title><![CDATA[" . $val->getstoryTitle() . "]]> </title>";
                                $strDate = $val->getcreatedTime();
                                $strDate = date("D,d M Y H:i:s " . '+0000', strtotime($strDate));
                                $str = $str . "<pubDate>" . $strDate . "</pubDate>";
                                $content = Instapress_Core_Text::processBeforeView(Cms_StoryUtility::processBeforeViewforMobile($val->getstoryWebContent()));
                                $str = $str . "<content>" . "<![CDATA[$content]]>" . "</content>";
                                $str = $str . '<guid isPermaLink="true">' . $val->getPermalink() . "</guid>";
                                $str = $str . "<primaryImagePath>" . $val->getprimaryImagePath() . "</primaryImagePath>";
                                $libImageObj = new Instapress_Core_Image(CLIENT_ID);
                                $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 320, 200);
                                $str = $str . "<big_image>" . $imageUrl . "</big_image>";
                                $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 127, 95);
                                $str = $str . "<small_image>" . $imageUrl . "</small_image>";
                                $str = $str . "</item>";
                            }
                        }
                    }
                } else {
                    $objsearch->set("quantity||$resultQuantity", "pageNumber||$pageNo");
                    for ($i = 0; $i < $lastpage; $i++) {

                        $storyId = $objsearch->get("storyId", $i);
                        $val = new Cms_Story($storyId);
                        $str = $str . "<item>";
                        $str = $str . "<storyId>" . $storyId . "</storyId>";
                        $str = $str . "<creator>" . htmlentities($val->getauthorByLine()) . "</creator>";
                        $str = $str . "<title><![CDATA[" . $val->getstoryTitle() . "]]> </title>";
                        $strDate = $val->getcreatedTime();
                        $strDate = date("D,d M Y H:i:s " . '+0000', strtotime($strDate));
                        $str = $str . "<pubDate>" . $strDate . "</pubDate>";
                        $content = Instapress_Core_Text::processBeforeView(Cms_StoryUtility::processBeforeViewforMobile($val->getstoryWebContent()));
                        $str = $str . "<content>" . "<![CDATA[$content]]>" . "</content>";
                        $str = $str . '<guid isPermaLink="true">' . $val->getPermalink() . "</guid>";
                        $str = $str . "<primaryImagePath>" . $val->getprimaryImagePath() . "</primaryImagePath>";
                        $libImageObj = new Instapress_Core_Image(CLIENT_ID);
                        $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 320, 200);
                        $str = $str . "<big_image>" . $imageUrl . "</big_image>";
                        $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 127, 95);
                        $str = $str . "<small_image>" . $imageUrl . "</small_image>";
                        $str = $str . "</item>";
                    }
                }
            } else {
                $str = $str . "<error><Success>False</Success>";
                $str = $str . "<Message>No Record Found</Message></error>";
            }
        } catch (Exception $e) {
            $str = $str . "<error><Success>False</Success>";
            $str = $str . "<Message>" . $e->getMessage() . "</Message></error>";
        }
        $str = $str . "</channel>" . "";
        return $str;
    }

    public function getAllStory($categoryId, $level='first') {
        $objStory = new Cms_db_Story();
        $objStory->set("order||N", "count||Y", "${level}CategoryId||$categoryId", 'publicationId||' . PUBLICATION_ID);
        return $objStory->getTotalCount();
    }

}