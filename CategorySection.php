<?php

	/**
	 * Singular Category class.
	 *
	 * @author Rashid Moahamd <rashid.mohamad@instamedia.com>
	 * @version 1.0
	 * @copyright Instamedia
	 */
	class Cms_CategorySection extends Cms_AbstractSingular {

		protected $_foreignKey = 'categorySectionId';
		protected $_dbClass = 'CategorySection';

		public function getPermalink() {
			return HOME_PATH . $this->getSectionSlug() . '/';
		}

	    public function getCategories( $onlyCount = false ) {
	    	if( $onlyCount === false ) {
				return new Cms_Categories( 'categorySectionId||' . $this->getCategorySectionId(), 'quantity||100', 'sortColumn||storiesCount' );
	   	 	} else {
		   		$count = new Cms_Categories( 'categorySectionId||' . $this->getCategorySectionId(), 'count||Y');
		   		return $count->getTotalCount();
	   		}
	   	 }	   
	}
