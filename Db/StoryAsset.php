<?php
/**
 * InstaPress Cms_Db_StoryAsset
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */

class Cms_Db_StoryAsset extends Cms_Db_Abstract
{
	//Basic Table Info
	protected $_mainTable = "cms_story_asset_rel";
	protected $_clauseColumnNames = array("storyAssetRelId","clientId","storyId", 'publicationId',
											"assetId","assetTypeId","storyAssetActive",
											"createdUserId","createdTime","assetTypeName");

	protected $_sortColumnNames = array();

	protected $_foreignKey = "storyAssetRelId";
	protected $_expandableTables = array();

	protected $_updateColumnNames = array( 'storyAssetRelId', 'clientId', 'publicationId', 'storyId', 'assetId', 'assetTypeId', 'assetTypeName', 'createdUserId', 'createdTime', 'storyAssetActive' );

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

	function delete()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']): false;
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception(gettext("StoryAsset : ").gettext("clientId should be a natural number!"));
		}
		$storyId = isset($this->_arrayUpdatedData['storyId'])?trim($this->_arrayUpdatedData['storyId']): false;
		$storyAssetRelId = isset($this->_arrayUpdatedData['storyAssetRelId'])?trim($this->_arrayUpdatedData['storyAssetRelId']): false;

		if( !( $storyAssetRelId && is_numeric( $storyAssetRelId ) ) && !( $storyId && is_numeric( $storyId ) ) ) {
			throw new Exception(gettext("StoryAsset : ").gettext("storyAssetRelId or storyId should be a natural number!"));
		}
		try
		{
			if($storyId)
				$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and storyId = '$storyId'");
			else
				$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and storyAssetRelId = '$storyAssetRelId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}

	function edit()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']): false;
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception(gettext("StoryAsset : ").gettext("clientId should be a natural number!"));
		}

		$storyId = isset( $this->_arrayUpdatedData[ 'storyId' ] ) ? trim( $this->_arrayUpdatedData[ 'storyId' ] ) : false;
		$storyAssetRelId = isset($this->_arrayUpdatedData['storyAssetRelId'])?trim($this->_arrayUpdatedData['storyAssetRelId']): false;

		if( !( $storyAssetRelId && is_numeric( $storyAssetRelId ) ) && !( $storyId && is_numeric( $storyId ) ) ) {
			throw new Exception(gettext("StoryAsset : ").gettext("storyAssetRelId or storyId should be a natural number!"));
		}

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		try
		{
			if( $storyId ) {
				$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and storyId = '$storyId'");
			} else {
				$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and storyAssetRelId = '$storyAssetRelId'");

			}
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}


}

?>