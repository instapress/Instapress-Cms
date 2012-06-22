<?php

	/**
	 * Singular Asset class.
	 *
	 * @author Rashid Moahamd <rashid.mohamad@instamedia.com>
	 * @version 1.0
	 * @copyright Instamedia
	 */
	class Cms_ListItem extends Cms_AbstractSingular {

		protected $_foreignKey = 'listItemId';
		protected $_dbClass = 'ListItem';
		protected $_relatedClasses = array( 'Cms_Db_ListItemText' );
	}
