<?php
/**
 * InstaPress Cms_Db_AssetExpandable
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_AssetExpandable extends Cms_Db_Abstract
{
	protected $_mainTable = "cms_asset_expandable";
	protected $_clauseColumnNames = array( 'assetId', 'xElementId', 'xElementValue', 'xGroupId', 'clientId', 'createdUserId', 'createdTime' );
	protected $_sortColumnNames = array("clientId","xGroupId","xElementId","createdUserId");
	protected $_foreignKey = array( 'assetId', 'xElementId', 'xGroupId' );
	protected $_expandableTables = array();

	protected $_updateColumnNames = array( 'assetId', 'xElementId', 'xElementValue', 'xGroupId', 'clientId', 'createdUserId', 'createdTime' );

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

	function delete() {
		$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] ) ? trim( $this->_arrayUpdatedData[ 'clientId' ] ) : false;
		if( !$clientId || !is_numeric( $clientId ) ) {
			throw new Exception( gettext( 'AssetExpandable : ' ) . gettext( 'clientId should be a natural number!' ) );
		}

		$assetId = isset( $this->_arrayUpdatedData[ 'assetId' ] ) ? trim( $this->_arrayUpdatedData[ 'assetId' ] ) : false;
		if( !$assetId || !is_numeric( $assetId ) ) {
			throw new Exception( gettext( 'AssetExpandable : ' ) . gettext( 'assetId should be a natural number!' ) );
		}

		try {
			$this->_databaseConnection->QueryDelete( $this->_mainTable, " clientId = '$clientId' and assetId = '$assetId'" );
		} catch( Exception $ex ) {
			throw $ex;
		}
	}

	function edit() {
		$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] ) ? trim( $this->_arrayUpdatedData[ 'clientId' ] ) : false;
		if( !$clientId || !is_numeric( $clientId ) ) {
			throw new Exception( gettext( 'AssetExpandable : ' ) . gettext( 'clientId should be a natural number!' ) );
		}

		$assetId = isset( $this->_arrayUpdatedData[ 'assetId' ] ) ? trim( $this->_arrayUpdatedData[ 'assetId' ] ) : false;
		if( !$assetId || !is_numeric( $assetId ) ) {
			throw new Exception( gettext( 'AssetExpandable : ' ) . gettext( 'assetId should be a natural number!' ) );
		}

		$xElementId = isset( $this->_arrayUpdatedData[ 'xElementId' ] ) ? trim( $this->_arrayUpdatedData[ 'xElementId' ] ) : false;
		if( !$xElementId || !is_numeric( $xElementId ) ) {
			throw new Exception(gettext("AssetExpandable : ").gettext("xElementId should be a natural number!"));
		}

		$xGroupId = isset( $this->_arrayUpdatedData[ 'xGroupId' ] ) ? trim( $this->_arrayUpdatedData[ 'xGroupId' ] ) : false;
		if( !$xGroupId || !is_numeric( $xGroupId ) ) {
			throw new Exception( gettext( "AssetExpandable : " ).gettext( "xGroupId should be a natural number!" ) );
		}

		$queryData = array();

		if( isset( $this->_arrayUpdatedData['xElementValue'] ) ) {
			$queryData['xElementValue'] = trim( $this->_arrayUpdatedData[ 'xElementValue' ] );
		}

		try {
			$this->_databaseConnection->QueryUpdate( $this->_mainTable, $queryData, " clientId = '$clientId' and assetId = '$assetId' and xElementId = '$xElementId' and xGroupId = '$xGroupId'" );
		} catch( Exception $ex ) {
			throw $ex;
		}
	}
}