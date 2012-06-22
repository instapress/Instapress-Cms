<?php

class Cms_Comments extends Cms_AbstractPlural {

    protected $_dbClass = 'Comment';
    protected $_relatedClasses = array( 'Cms_Db_CommentText' );
    public function handleCommentData($dossierId, $userId, $userIPAddress, $commentStatus, $clientId, $commentText) {
        if (empty($dossierId))
            throw new Exception(gettext('DossierId couldn\'t blank.'));
        if (empty($userId))
            throw new Exception(gettext('UserId couldn\'t blank.'));
        if (empty($userIPAddress))
            throw new Exception(gettext('UserIpAddress couldn\'t blank.'));
        if (empty($commentStatus))
            throw new Exception(gettext('CommentText couldn\'t blank.'));
        if (empty($clientId))
            throw new Exception(gettext('ClientId couldn\'t blank.'));
        if (empty($commentText))
            throw new Exception(gettext('CommentText couldn\'t blank.'));

        $commentId = 0;
        try {
            $dbCommentObj = new Cms_Db_Comment('add');
            $dbCommentObj->set("dossierId||$dossierId", "userId||$userId", "userIPAddress||$userIPAddress", "commentStatus||$commentStatus", "clientId||$clientId");
            $commentId = $dbCommentObj->getLastInsertedId();
            if (!empty($commentId)) {
                $dbCommentTextObj = new Cms_Db_CommentText('add');
                $dbCommentTextObj->set("commentId||$commentId", "dossierId||$dossierId", "commentText||$commentText", "clientId||$clientId");
            }
          

        } catch (Exception $e) {
            throw new Exception($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }

    /**
     *
     * @param type $dossierId
     * @param type $commentText 
     */
    public function checkDuplicateComment($dossierId, $commentText) {
        if (empty($dossierId))
            throw new Exception(gettext('ArticleId couldn\'t blank.'));
        if (empty($commentText))
            throw new Exception(gettext('CommentText couldn\'t blank.'));

        try {
            $dbCommentTextObj = new Cms_Db_CommentText();
            $dbCommentTextObj->set("dossierId||$dossierId", "commentText||$commentText", "count||Y", "order||N");
            $dbCount = $dbCommentTextObj->getTotalCount();
            return ($dbCount > 0) ? true : false;
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
    
    /**
     *
     * @param type $userId
     * @return type 
     */
    public function checkSpammingOnComment($userId) {
        if (empty($userId))
            throw new Exception(gettext('UserId couldn\'t blank.'));
        try {
            $dbCommentObj = new Cms_Db_Comment();
            $dbCommentObj->set("userId||$userId", "order||N", "count||Y");
            $dbCount = $dbCommentObj->getTotalCount();
            $dbLastCreatedTime = '';
            if ($dbCount > 0) { //handle of comment request.
                $dbCommentObj = new Cms_Db_Comment();
                $dbCommentObj->set("userId||$userId", "sortColumn||createdTime", "sortOrder||desc", "order||Y", "quantity||1");
                $dbLastCreatedTime = $dbCommentObj->get('createdTime');
                $currentDateTime = date('Y-m-d H:i:s');
                //echo "$dbLastCreatedTime@@$currentDateTime";
                $timeDiff = (int) Cms_StoryUtility::getInterval($currentDateTime, $dbLastCreatedTime);
                return ($timeDiff >= 60) ? true : false; //if interval less then 1 min halt processing.
            }else
                return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }

    public function fetchUserComment($contentId,$userId) {
        if (empty($userId))
            throw new Exception(gettext('UserId couldn\'t blank.'));
         if (empty($contentId))
            throw new Exception(gettext('CountId couldn\'t blank.'));
        try {
            $dbCommentObj = new Cms_Db_Comment();
            $dbCommentObj->set( "order||N", "count||Y","userId||$userId","dossierId||$contentId");
            $dbCount = $dbCommentObj->getTotalCount();
             $dataArr[] = array('userComment' => $dbCount);

                return $dataArr;
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }


   //access comment data
     public function getCommentText($articleId) {
        if (empty($articleId))
            throw new Exception(gettext('ArticleId couldn\'t blank.'));

        try {
            $dbCommentTextObj = new Cms_Db_CommentText();
            $dbCommentTextObj->set("dossierId||$articleId", "count||Y", "order||N");
            $dbCount = $dbCommentTextObj->getTotalCount();
            if ($dbCount > 0) {
                $dbCommentTextObj = new Cms_Db_CommentText();
                $dbCommentTextObj->set("dossierId||$articleId", "quantity||$dbCount", "order||N");
                $commentDataArr = array();
                for ($i = 0; $i < $dbCount; $i++) {
                    $commentText = $dbCommentTextObj->get('commentText', $i);
                     $commentId = $dbCommentTextObj->get('commentId', $i);
                    $dbCommentObj = new Cms_Db_Comment();
                    $dbCommentObj->set("order||N","commentId||$commentId");
                    $userName ='';
                    $imageSrc ='';
                        $createdById = $dbCommentObj->get('userId');
                        $utilityObj= new Cms_StoryUtility();
                        $commentTime=Helper::timeAgo($dbCommentObj->get('createdTime'));//$utilityObj->timeAgo($dbCommentObj->get('createdTime'));
                        //retrieve writer name.
                        $userId[]=$createdById;
                        $userData = Ugc_UgcUtility::getUsersData($userId);

                        if($userData!==false)
                        {
                        $userName=$userData[$createdById]['userFirstName'].' '.$userData[$createdById]['userLastName'];

                        $imageSrc=S3_IMAGE_PUBLIC_LOCATION."ic-user/s_".$userData[$createdById]['userLogin'].'.jpg';
                        }
                     $imageSrc=!empty( $imageSrc) ? $imageSrc : "http://$_SERVER[SERVER_NAME]/images/noimage.jpg";
                    $commentDataArr[$i]['commentText']=$commentText;
                    $commentDataArr[$i]['commentId']=$commentId;
                    $commentDataArr[$i]['imagesrc']=$imageSrc;
                    $commentDataArr[$i]['username']=$userName;
                    $commentDataArr[$i]['commentTime']=$commentTime;

                    //$commentDataArr[$i] = array("comment" => $commentText);

                }
                return (count($commentDataArr) > 0) ? $commentDataArr : false;
            }
            else
                throw new Exception(gettext('No such record found.'));
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
}

?>