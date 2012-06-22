<?php
/**
 * InstaPress Cms_Db_Asset
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_Asset extends Cms_Db_Abstract
{
	protected $_mainTable = "cms_asset";
	protected $_clauseColumnNames = array("clientId", "usedCount", "assetId","assetSlug","assetTypeId", "createdUserId","createdTime","assetStatus");
	protected $_sortColumnNames = array();
	protected $_foreignKey = "assetId";
	protected $_expandableTables = array("cms_asset_expandable","cms_asset_expandable_text");

	protected $_updateColumnNames = array( 'assetId', 'clientId', 'assetTitle', 'assetSlug', 'assetDescription', 'assetKeywords', 'assetTypeId', 'assetPrimaryImagePath', 'assetElementsOrder', 'assetStatus', 'createdUserId', 'createdTime' );

	function add() {

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			} else {
				throw new Exception( gettext( "Tag : " ) . gettext( $column . " is a required field!" ) );
			}
		}

		try {
			$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData, false );
		} catch( Exception $ex ) {
			throw $ex;
		}
	}

	function edit() {
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ) ) {
			throw new Exception("Asset : clientId should be a natural number!");
		}

		$assetId = isset($this->_arrayUpdatedData['assetId'])?trim($this->_arrayUpdatedData['assetId']):"";
		if( !$assetId || !is_numeric( $assetId ) ) {
			throw new Exception("Asset : assetId should be a natural number!");
		}


		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		$uploadedResult = true;
			
		if($uploadedResult) {
			try {
				//$this->_lastInsertedId =
				$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and assetId = '$assetId'");
			} catch( Exception $ex ) {
				throw $ex;
			}
		}
		else
		{
			throw new Exception("Asset : There was some error while processing this data! Please edit this asset again.");
		}
	}

	function delete()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception("Asset : clientId should be a natural number!");
		}

		$assetId = isset($this->_arrayUpdatedData['assetId'])?trim($this->_arrayUpdatedData['assetId']):"";
		if( !$assetId || !is_numeric( $assetId ) )
		{
			throw new Exception("Asset : assetId should be a natural number!");
		}

		try
		{
			$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and assetId = '$assetId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}

	}

}
