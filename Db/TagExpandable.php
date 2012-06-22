<?php

/**
 * InstaPress Cms_Db_TagExpandable
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_TagExpandable extends Cms_Db_Abstract {

	protected $_mainTable = "cms_tag_expandable";
	protected $_clauseColumnNames = array( 'tagId', 'xElementId', 'xElementValue', 'xGroupId', 'clientId', 'createdTime' );
	protected $_sortColumnNames = array( 'tagId', 'xElementId', 'xElementValue', 'xGroupId', 'clientId', 'createdTime' );
	protected $_foreignKey = "tagId";
	protected $_expandableTables = array();

	protected $_updateColumnNames = array( 'tagId', 'xElementId', 'xElementValue', 'xGroupId', 'clientId', 'createdTime' );

	public function add() {

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			} else {
				throw new Exception( gettext( "TagExpandable : " ) . gettext( $this->_arrayUpdatedData[ $column ] . " is a required field!" ) );
			}
		}

		try {
			$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData, false );
		} catch( Exception $ex ) {
			throw $ex;
		}
	}

	public function edit() {
		$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] )?trim( $this->_arrayUpdatedData[ 'clientId' ] ) : false;
		if( !$clientId || !is_numeric( $clientId ) ) {
			throw new Exception( gettext( "TagExpandable : " ) . gettext( "clientId should be a natural number!" ) );
		}

		$tagId = isset( $this->_arrayUpdatedData[ 'tagId' ] )?trim( $this->_arrayUpdatedData[ 'tagId' ] ) : false;
		if( !$tagId || !is_numeric( $tagId ) ) {
			throw new Exception( gettext( "TagExpandable : " ) . gettext( "tagId should be a natural number!" ) );
		}

		$xElementId = isset( $this->_arrayUpdatedData[ 'xElementId' ] )?trim( $this->_arrayUpdatedData[ 'xElementId' ] ) : false;
		if( !$xElementId ) {
			throw new Exception( gettext( "TagExpandable : " ) . gettext( "xElementId should be a natural number!" ) );
		}

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		try {
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and tagId = '$tagId' and xElementId = '$xElementId'");
		} catch( Exception $ex ) {
			throw $ex;
		}
	}
}