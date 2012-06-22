<?php

class Cms_Answers extends Cms_AbstractPlural {

    protected $_dbClass = 'Answer';

    public function getAnswerByTopicId($topicId, $publicationId, $pageNo) {

        parent::__construct("questionId||$topicId", "publicationId||$publicationId", "pageNumber||$pageNo", "sortOrder||ASC","answerStatus||publish");
        return $this->_matchedRecords;
    }

}