<?php

	/**
	 * Singular Slug class.
	 *
	 * @author Rashid Moahamd <rashid.mohamad@instamedia.com>
	 * @version 1.0
	 * @copyright Instamedia
	 */
	class Cms_Slug extends Cms_AbstractSingular {

		protected $_foreignKey = 'slugId';
		protected $_dbClass = 'Slug';
	}
