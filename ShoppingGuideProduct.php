<?php

	class Cms_ShoppingGuideProduct extends Cms_AbstractSingular {
	
		protected $_foreignKey = 'shoppingGuideProductId';
		protected $_dbClass = 'ShoppingGuideProduct';
		protected $_relatedClasses = array( 'Cms_Db_ShoppingGuideProductText' );
	}
