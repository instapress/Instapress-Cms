<?php

	/**
	 * Singular Asset class.
	 *
	 * @author Rashid Moahamd <rashid.mohamad@instamedia.com>
	 * @version 1.0
	 * @copyright Instamedia
	 */
	class Cms_Tag extends Cms_AbstractSingular {

		protected $_foreignKey = 'tagId';
		protected $_dbClass = 'Tag';
	}