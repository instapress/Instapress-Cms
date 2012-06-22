<?php

/**
 * Singular Question class.
 *
 * @author Rashid Moahamd <rashid.mohamad@instamedia.com>
 * @version 1.0
 * @copyright Instamedia
 */
class Cms_Question extends Cms_AbstractSingular {

    protected $_foreignKey = 'questionId';
    protected $_dbClass = 'Question';
    protected $_relatedClasses = array( 'Cms_Db_QuestionContent' );
    
    public function postComment( $commentText ) {
    	$dossierId = $this->getQuestionId();
    	$answerId = Editorial_Utility::addUGCAnswer( PUBLICATION_ID, $dossierId, $commentText, USER_ID );
    	if( $answerId > 0 ){
    		@file_get_contents( HOME_PATH . "PULL/nv/did/$dossierId/" );
    		return true;
    	}
    	return false;
    }
    
    public function getAnswers() {
    	$answers = new Cms_Answers( 'questionId||' . $this->getQuestionId(), 'publicationId||' . PUBLICATION_ID, 'quantity||1000' );
    	$answerArray = array();
    	foreach( $answers() as $answer ) {
    		$user = new Network_User( $answer->getAutoUserId() );
    		$answerArray[] = array(
					'id' => $answer->getAnswerId(),
					'image' => $user->getProfileImage( 'small' ),
					'name' => $user->fullName(),
					'text' => $answer->getAnswerContent(),
					'time' => Helper::calculatePreModifiedDate( $answer->getCreatedTime() )
    		);
    	}
    	return $answerArray;
    }
    
	public function unpublish() {
		$answers = new Cms_Answers( 'questionId||' . $this->getQuestionId() );
		foreach( $answers() as $answer ) {
			$answer->update( 'publicationId||0', 'answerStatus||unpublish' );
		}
		$this->update( 'questionStatus||unpublish', 'publicationId||0' );
	}

    public function getRelatedQuestions($pageNumber, $quantity) {
        $searchObj = new Instapress_Core_Search('192.168.100.84', 18983);
        $searchObj->searchRelatedStoriesForCms($this->getQuestionId(), $pageNumber, $quantity, "qna");
        $searchResults = $searchObj->getSearchResults();
        $questions = array();
        foreach ($searchResults as $storyId) {
            $questions[] = new Cms_Question($storyId);
        }

        $count = count($questions);
        $left = 6 - $count;
        if ($count < 5) {
            $QuestionObj = new Cms_Questions("count||Y", 'firstCategoryId||' . $this->getFirstCategoryId());
            $newCount = $QuestionObj->getTotalCount();
            if ($newCount < $left) {
                $left = $newCount;
            }
            $QuestionObj = new Cms_Questions('firstCategoryId||' . $this->getFirstCategoryId(), "quantity||$left");
            foreach ($QuestionObj () as $question) {
                if ($count == 5) {
                    break;
                }
                if (($question->getQuestionId()) != $this->getQuestionId()) {
                    $count++;
                    $questions[] = new Cms_Question($question->getQuestionId());
                }
            }
        }

        if (count($questions) > 0) {
            return new Cms_Questions( $questions );
        } else {
            return new Cms_Questions();
        }
    }

	public function getPermalink() {       
        return HOME_PATH . $this->getQuestionSlug() . '.html';        
    }
    
    public function getRelatedStories($pageNumber, $quantity) {
        $searchObj = new Instapress_Core_Search('192.168.100.84', 18983);
        if (trim($this->getKeyword()) == '') {
            //echo "question title ".$this->getQuestionTitle();
            $searchObj->dossierSearch($this->getQuestionTitle(), "story", $this->getPublicationId(), $pageNumber, $quantity, "published");
        } else {
            //echo "question keydwor ".$this->getKeyword();
            $searchObj->dossierSearch($this->getKeyword(), "story", $this->getPublicationId(), $pageNumber, $quantity, "published");
        }
        $searchResults = $searchObj->getSearchResults();
        $stories = array();
        foreach ($searchResults as $storyId) {
            $stories[] = new Cms_Story($storyId);
        }

        $count = count($stories);
        $left = 6 - $count;
        if ($count < 5) {
            $StoryObj = new Cms_Stories("count||Y", 'firstCategoryId||' . $this->getFirstCategoryId());
            $newCount = $StoryObj->getTotalCount();
            if ($newCount < $left) {
                $left = $newCount;
            }
            $StoryObj = new Cms_Stories('firstCategoryId||' . $this->getFirstCategoryId(), "quantity||$left");
            foreach ($StoryObj () as $story) {
                if ($count == 5) {
                    break;
                }
                $stories[] = new Cms_Story($story->getStoryId());
                $count++;
            }
        }

        if (count($stories) > 0) {
            return new Cms_Stories($stories);
        } else {
            return new Cms_Stories();
        }
    }

}