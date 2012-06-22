<?php
/**
 * InstaPress Cms_Db_AssetTypeElement
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_AssetTypeElement extends Cms_Db_Abstract
{
	protected $_mainTable = "cms_asset_type_element";
	protected $_clauseColumnNames = array("assetTypeElementId","elementName", "assetTypeId",
											"clientId", "dataTypeId", "elementIsForm",
											"elementRequired", "createdUserId", "createdTime");

	protected $_sortColumnNames = array();

	protected $_foreignKey = "assetTypeElementId";
	protected $_expandableTables = array();

	protected $_updateColumnNames = array("clientId","assetTypeId","elementCaption",
											"dataTypeId","elementValue","elementRequired","elementIsForm","createdUserId");

	function add()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ))
		{
			throw new Exception("AssetTypeElement : clientId should be a natural number!");
		}
		$assetTypeId = isset($this->_arrayUpdatedData['assetTypeId'])?trim($this->_arrayUpdatedData['assetTypeId']):"";
		if( !$assetTypeId || !is_numeric( $assetTypeId ))
		{
			throw new Exception("AssetTypeElement : assetTypeId should be a natural number!");
		}
		$dataTypeId = isset($this->_arrayUpdatedData['dataTypeId'])?trim($this->_arrayUpdatedData['dataTypeId']):"";
		if( !$dataTypeId || !is_numeric( $dataTypeId ))
		{
			throw new Exception("AssetTypeElement : dataTypeId should be a natural number!");
		}

		$elementCaption = isset($this->_arrayUpdatedData['elementCaption'])?trim($this->_arrayUpdatedData['elementCaption']):"";
		if( !$elementCaption )
		{
			throw new Exception( "AssetTypeElement : element Caption is a required parameter and cannot be blank!");
		}

		$elementName = $this->toCamelCase( Helper::sanitizeWithDashes( $elementCaption ) );

		$elementIsForm = isset($this->_arrayUpdatedData['elementIsForm'])?trim($this->_arrayUpdatedData['elementIsForm']):"Y";
			
		$elementValue = isset($this->_arrayUpdatedData['elementValue'])?trim($this->_arrayUpdatedData['elementValue']):"";
		$elementRequired = isset($this->_arrayUpdatedData['elementRequired'])?trim($this->_arrayUpdatedData['elementRequired']):"N";

		$createdUserId = isset($this->_arrayUpdatedData['createdUserId'])?trim($this->_arrayUpdatedData['createdUserId']):"";
		if( !$createdUserId || !is_numeric($createdUserId ))
		{
			throw new Exception("AssetTypeElement : createdUserId should be a natural number!");
		}

		$objAssetTypeElement = new Cms_Db_AssetTypeElement();
		$objAssetTypeElement->set("clientId||$clientId", "elementName||$elementName", "count||Y");
		if( $objAssetTypeElement->getTotalCount() > 0 )
		throw new Exception("AssetTypeElement : This Element Name already exists!");

		$queryData = array();
		$queryData['clientId'] = $clientId;
		$queryData['assetTypeId'] = $assetTypeId;
		$queryData['elementCaption'] = $elementCaption;
		$queryData['elementName'] = $elementName;
		$queryData['dataTypeId'] = $dataTypeId;
		$queryData['elementValue'] = $elementValue;
		$queryData['elementRequired'] = $elementRequired;
		$queryData['elementIsForm'] = $elementIsForm;
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
			throw new Exception( gettext("AssetTypeElement : ") . gettext( "AssetTypeElement : clientId should be a natural number!" ));
		}
		$assetTypeElementId = isset($this->_arrayUpdatedData['assetTypeElementId'])?trim($this->_arrayUpdatedData['assetTypeElementId']):"";
		if( !$assetTypeElementId || !is_numeric( $assetTypeElementId ) )
		{
			throw new Exception("AssetTypeElement : assetTypeElementId should be a natural number!");
		}

		//if( self::checkDependencies( $clientId , $assetTypeElementId ) == 0 ){
		try
		{
			$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and assetTypeElementId = '$assetTypeElementId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
		//}

	}

	function edit()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception("AssetTypeElement : clientId should be a natural number!");
		}

		$assetTypeElementId =isset($this->_arrayUpdatedData['assetTypeElementId'])?trim($this->_arrayUpdatedData['assetTypeElementId']):"";
		if( !$assetTypeElementId || !is_numeric( $assetTypeElementId ) )
		{
			throw new Exception("AssetTypeElement : assetTypeElementId should be a natural number!");
		}

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}
		try
		{
			$this->_databaseConnection->QueryUpdate( $this->_mainTable, $queryData, " clientId = '$clientId' and assetTypeElementId = '$assetTypeElementId'" );
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}

	function toCamelCase( $thisString ) {
		$tempString = $thisString;
		$pieces = explode( '-', $thisString );
		$retStr = $pieces[0];
		for( $len = 1; $len < count( $pieces ); $len++ ) {
			$retStr .= ucfirst( $pieces[$len] );
		}
		return $retStr;
	}

	function checkDependencies( $clientId, $assetTypeElementId )
	{
		$objAssetExpandable = new Cms_Db_AssetExpandable();
		$objAssetExpandable->set("clientId||$clientId", "xElementId||$assetTypeElementId");
		$totalCount = $objAssetExpandable->getTotalCount();
		if( $totalCount > 0 )
		{
			throw new Exception("AssetTypeElement : This record can't deleted because entry exist in Asset Type Element.");
		}

		$objAssetExpandableText = new Cms_Db_AssetExpandableText();
		$objAssetExpandableText->set("clientId||$clientId", "xElementId||$assetTypeElementId");
		$totalCount2 = $objAssetExpandableText->getTotalCount();
		if( $totalCount2 > 0 )
		{
			throw new Exception("AssetTypeElement : This record can't deleted because entry exist in Entn Asset.");
		}

		return $totalCount + $totalCount2;
	}

}