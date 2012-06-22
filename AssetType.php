<?php

	/**
	 * Singular AssetType class.
	 *
	 * @author Rashid Moahamd <rashid.mohamad@instamedia.com>
	 * @version 1.0
	 * @copyright Instamedia
	 */
	class Cms_AssetType extends Cms_AbstractSingular {

		protected $_foreignKey = 'assetTypeId';
		protected $_dbClass = 'AssetType';

		public static function getAssetTypes() {
			$assetTypesObj = new Cms_AssetTypes( 'quantity||1000' );
			$assetTypes = array();
			foreach( $assetTypesObj() as $assetTypeObj ) {
				$assetTypes[ $assetTypeObj->getAssetTypeName() ] = $assetTypeObj->getAssetTypeId();
			}
			return $assetTypes;
		}
	}