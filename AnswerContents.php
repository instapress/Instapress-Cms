<?php

/**
 * Description of Answers
 *
 * @author root
 */
class Cms_AnswerContents extends Cms_AbstractPlural {

    protected $_dbClass = 'AnswerContent';

    public function getAnswerById($answerId,$publicationId) {
        //echo "hi";
        parent::__construct("answerId||$answerId","publicationId||$publicationId");
        print_r( $this->_matchedRecords);
    }


}

?>