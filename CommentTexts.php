<?php

class Cms_CommentTexts extends Cms_AbstractPlural {

    protected $_dbClass = 'CommentText';

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
                        $commentTime=Helper::timeAgo($dbCommentObj->get('createdTime'));//$utilityObj->timeAgo($dbCommentObj->get('createdTime'));
                        //retrieve writer name.
                        $userId[]=$createdById;
                        $userData = Cms_CmsUtility::getUsersData($userId);
                        
                        if($userData!==false)
                        {
                        $userName=$userData[$createdById]['userFirstName'].' '.$userData[$createdById]['userLastName'];

                        $imageSrc=S3_IMAGE_PUBLIC_LOCATION."ic-user/s_".$userData[$createdById]['userLogin'].'.jpg';
                        }
                     $imageSrc=!empty( $imageSrc) ? $imageSrc : "http://$_SERVER[SERVER_NAME]/images/noimage.jpg";
                    $commentDataArr[$i]['commentText']=$commentText;
                    $commentDataArr[$i]['imageSrc']=$imageSrc;
                    $commentDataArr[$i]['userName']=$userName;
                    $commentDataArr[$i]['commentId']=$commentId;
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