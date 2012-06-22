<?php

	/**
	 * Singular AssetType class.
	 *
	 * @author Rashid Moahamd <rashid.mohamad@instamedia.com>
	 * @version 1.0
	 * @copyright Instamedia
	 */
	class Cms_Answer extends Cms_AbstractSingular {

		protected $_foreignKey = 'answerId';
		protected $_dbClass = 'Answer';
		protected $_relatedClasses = array( 'Cms_Db_AnswerContent' );
	}