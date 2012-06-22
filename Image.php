<?php

	/**
	 * Singular Image class.
	 *
	 * @author Rashid Moahamd <rashid.mohamad@instamedia.com>
	 * @version 1.0
	 * @copyright Instamedia
	 */
	class Cms_Image extends Cms_AbstractSingular {

		protected $_foreignKey = 'imageId';
		protected $_dbClass = 'Image';
	}
