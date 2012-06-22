<?php
class Cms_AssetUtility {

	private static $_errorMessage = '';

	public static function getMessage() {
		return self::$_errorMessage;
	}

	private function __construct() {}

	public static function getAsset( $assetSlug ) {
		$assetsObj = new Cms_Assets( "assetSlug||$assetSlug", 'assetStatus||publish' );
		if( $assetsObj->getResultCount() > 0 ) {
			return $assetsObj->get( 0 );
		} else {
			throw new Exception( 'Asset not found!' );
		}
	}
	
	public static function getAssetById( $assetId ) {
		$assetsObj = new Cms_Assets( "assetId||$assetId", 'assetStatus||publish' );
		if( $assetsObj->getResultCount() > 0 ) {
			return $assetsObj->get( 0 );
		} else {
			throw new Exception( 'Asset not found!' );
		}
	}

	public static function getAssets( $assetTypeId, $pageNo = 1, $quantity = 10 ) {
		return new Cms_Assets( "assetTypeId||$assetTypeId", "pageNumber||$pageNo", "assetStatus||publish", "quantity||$quantity" );
	}
}