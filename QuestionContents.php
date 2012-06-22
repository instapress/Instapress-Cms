<?php

/**
 * Description of Answers
 *
 * @author root
 */
class Cms_QuestionContents extends Cms_AbstractPlural {

    protected $_dbClass = 'QuestionContent';

    public function getAnswerById($topicId,$publicationId) {
     
        parent::__construct("topicId||$topicId","publicationId||$publicationId");
        return $this->_matchedRecords;
    }


}

?>