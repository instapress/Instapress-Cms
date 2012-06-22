<?php
/**
 * InstaPress Cms_Db_AssetType
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_AssetType extends Cms_Db_Abstract {

	protected $_mainTable = "cms_asset_type";
	protected $_clauseColumnNames = array("assetTypeId","clientId","assetTypeSlug","createdUserId","createdTime");
	protected $_sortColumnNames = array( "assetTypeSlug", "assetTypeId" );
	protected $_foreignKey = "assetTypeId";
	protected $_expandableTables = array();
	protected $_updateColumnNames = array( "clientId","assetTypeName", "assetTypeSlug","createdUserId" );

	/*function getFormElements()
	 {
		$_formbuilder['add'] = array('assetTypeName'=>array('type'=>'smalltext','check'=>'required','caption'=>'Asset Type Name')
		);
		return $_formbuilder;
		}*/

	/**
	 * Creates AssetType for an empty object
	 * @param _arrayUpdatedData required
	 *
	 * @return void
	 * @throws exception if record already exits or parameter value are not in required format.
	 */
	function add()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";

		if( !$clientId || !is_numeric( $clientId ))
		{
			throw new Exception(gettext("AssetType : ").gettext("clientId should be a natural number!"));
		}

		$assetTypeName = isset($this->_arrayUpdatedData['assetTypeName'])?trim($this->_arrayUpdatedData['assetTypeName']):"";
		if( !$assetTypeName )
		{
			throw new Exception(gettext("AssetType : ").gettext("Asset type name is a required parameter and cannot be blank!"));
		}
			
		$assetTypeSlug = Helper :: sanitizeWithDashes($assetTypeName);
			
		$createdUserId = isset($this->_arrayUpdatedData['createdUserId'])?trim($this->_arrayUpdatedData['createdUserId']):"";
		if( !$createdUserId || !is_numeric($createdUserId ))
		{
			throw new Exception(gettext("AssetType : ").gettext("createdUserId should be a natural number!"));
		}

		$objTempAssetType = new Cms_Db_AssetType();
		$objTempAssetType->set("clientId||$clientId", "assetTypeSlug||$assetTypeSlug");
		$totalCount = $objTempAssetType->getTotalCount();

		if($totalCount>0)
		throw new Exception(gettext("AssetType : ").gettext("This asset type already exists!"));

		$queryData = array();
		$queryData['clientId'] = $clientId;
		$queryData['assetTypeName'] = $assetTypeName;
		$queryData['assetTypeSlug'] = $assetTypeSlug;
		$queryData['createdUserId'] = $createdUserId;
		$queryData['createdTime'] = 'now()';
			
		try
		{
			$this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $queryData);
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}


	function delete()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception( gettext("AssetType : ").gettext("clientId should be a natural number!"));
		}

		$assetTypeId = isset($this->_arrayUpdatedData['assetTypeId'])?trim($this->_arrayUpdatedData['assetTypeId']):"";
		if( !$assetTypeId || !is_numeric( $assetTypeId ) )
		{
			throw new Exception( gettext("AssetType : ").gettext("assetTypeId should be a natural number!"));
		}
		if( self::checkDependencies( $assetTypeId ) == 0 )
		{
			try
			{
				$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and assetTypeId = '$assetTypeId'");
			}
			catch( Exception $ex )
			{
				throw $ex;
			}
		}
	}

	function edit() {
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception( gettext("AssetType : ").gettext("clientId should be a natural number!"));
		}

		$assetTypeId = isset($this->_arrayUpdatedData['assetTypeId'])?trim($this->_arrayUpdatedData['assetTypeId']):"";
		if( !$assetTypeId || !is_numeric( $assetTypeId ) )
		{
			throw new Exception( gettext("AssetType : ").gettext("assetTypeId should be a natural number!"));
		}
		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		try
		{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and assetTypeId = '$assetTypeId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}


	function checkDependencies($assetTypeId)
	{
		$objAssetTypeElement = new Cms_Db_AssetTypeElement();
		$objAssetTypeElement->set( "assetTypeId||$assetTypeId" );
		$totalCount = $objAssetTypeElement->getTotalCount();
		if( $totalCount > 0 )
		{
			throw new Exception(gettext("AssetType : ").gettext("This record can't deleted because entry exist in Asset Type Element."));
		}

		$objAsset = new Cms_Db_Asset();
		$objAsset->set( "assetTypeId||$assetTypeId" );
		$totalCount2 = $objAsset->getTotalCount();
		if( $totalCount2 > 0 )
		{
			throw new Exception(gettext("AssetType : ").gettext("This record can't deleted because entry exist in Entn Asset."));
		}

		return $totalCount + $totalCount2;
	}
}

