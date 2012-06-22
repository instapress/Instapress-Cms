<?php

	/**
	 * Singular Category class.
	 *
	 * @author Rashid Moahamd <rashid.mohamad@instamedia.com>
	 * @version 1.0
	 * @copyright Instamedia
	 */
	class Cms_Category extends Cms_AbstractSingular {

		protected $_foreignKey = 'categoryId';
		protected $_dbClass = 'Category';

		public function getPermalink() {
			return HOME_PATH . $this->getCategorySlug() . '/';
		}

	    public function getSubCategories() {
	        return new Cms_Categories( 'parentId||' . $this->getCategoryId(), 'quantity||100', 'sortColumn||storiesCount' );
	    }
	    
	 	public function getQuestionsCount() {
	 		$questionsObj = new Cms_Questions( 'count||Y', $this->getLevel().'CategoryId||'.$this->getCategoryId(), 'questionStatus||publish');
	 		return $questionsObj->getTotalCount();
	 	}
	}
