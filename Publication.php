<?php

	/**
	 * Singular Publication class.
	 *
	 * @author Pramod Thakur<pramod.thakur@instamedia.com>
	 * @version 1.0
	 * @copyright Instamedia
	 */
	class Cms_Publication extends Cms_AbstractSingular {

		protected $_foreignKey = 'publicationId';
		protected $_relatedClasses = array();
		protected $_dbClass = 'Publication';	
	}
	