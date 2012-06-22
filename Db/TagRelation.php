<?php
/**
 * InstaPress Cms_Db_TagRelation
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */

class Cms_Db_TagRelation extends Cms_Db_Abstract {

	protected $_mainTable = "cms_tag_relation";
	protected $_clauseColumnNames = array( 'tagRelationId', 'clientId', 'tagId', 'relatedTagId', 'weightage', 'createdTime', 'publicationId' );
	protected $_sortColumnNames = array( 'tagRelationId', 'clientId', 'createdTime' );
	protected $_foreignKey = "tagRelationId";
	protected $_expandableTables = array();

	protected $_updateColumnNames = array( 'clientId', 'tagId', 'weightage', 'relatedTagId', 'publicationId' );

	public function add() {
		$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] )?trim( $this->_arrayUpdatedData[ 'clientId' ] ) : false;
		if( !$clientId || !is_numeric( $clientId ) ) {
			throw new Exception( gettext( "TagRelation : " ) . gettext( "clientId should be a natural number!" ) );
		}

		$publicationId = isset( $this->_arrayUpdatedData[ 'publicationId' ] )?trim( $this->_arrayUpdatedData[ 'publicationId' ] ) : false;
		if( !$publicationId || !is_numeric( $publicationId ) ) {
			throw new Exception( gettext( "TagRelation : " ) . gettext( "publicationId should be a natural number!" ) );
		}

		$tagId = isset( $this->_arrayUpdatedData[ 'tagId' ] )?trim( $this->_arrayUpdatedData[ 'tagId' ] ) : false;
		if( !$tagId ) {
			throw new Exception( gettext( "TagRelation : " ) . gettext( "tagId is a required field!" ) );
		}

		$relatedTagId = isset( $this->_arrayUpdatedData[ 'relatedTagId' ] )?trim( $this->_arrayUpdatedData[ 'relatedTagId' ] ) : false;
		if( !$relatedTagId ) {
			throw new Exception( gettext( "TagRelation : " ) . gettext( "relatedTagId is a required field!" ) );
		}
			
		$selfObj = new Cms_Db_TagRelation( 'show' );
		$selfObj->set( "tagId||$tagId", "relatedTagId||$relatedTagId", "publicationId||$publicationId" );
		if( $selfObj->getResultcount() ) {
			$tagRelationId = $this->_lastInsertedId = $selfObj->get( $this->_foreignKey );
			$newWeightage = $selfObj->get( 'weightage' ) + 1;

			$selfObj = new Cms_Db_TagRelation( 'edit');
			$selfObj->set( "clientId||$clientId", "{$this->_foreignKey}||$tagRelationId", "weightage||$newWeightage" );
		} else {
			$queryData = array();
			$weightage = 1;
			$createdTime = 'now()';

			$queryData['publicationId'] = $publicationId;
			$queryData[ 'clientId' ] = $clientId;
			$queryData[ 'tagId' ] = $tagId;
			$queryData[ 'relatedTagId' ] = $relatedTagId;
			$queryData[ 'weightage' ] = $weightage;
			$queryData[ 'createdTime' ] = $createdTime;

			try {
				$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData );
			} catch( Exception $ex ) {
				throw $ex;
			}
		}
	}

	function delete()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception(  gettext( "Tag : " ) . gettext( "clientId should be a natural number!" ));
		}

		$primaryKeyValue = isset( $this->_arrayUpdatedData[ $this->_foreignKey ] )?trim( $this->_arrayUpdatedData[ $this->_foreignKey ] ) : false;
		if( !$primaryKeyValue || !is_numeric( $primaryKeyValue ) ) {
			throw new Exception( gettext( "Tag : " ) . gettext( "{$this->_foreignKey} should be a natural number!" ) );
		}
		try
		{
			$this->_databaseConnection->QueryDelete($this->_mainTable, $queryData, " clientId = '$clientId' and {$this->_foreignKey} = '$primaryKeyValue'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}

	}

	public function edit() {
		$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] )?trim( $this->_arrayUpdatedData[ 'clientId' ] ) : false;
		if( !$clientId || !is_numeric( $clientId ) ) {
			throw new Exception( gettext( "Tag : " ) . gettext( "clientId should be a natural number!" ) );
		}

		$primaryKeyValue = isset( $this->_arrayUpdatedData[ $this->_foreignKey ] )?trim( $this->_arrayUpdatedData[ $this->_foreignKey ] ) : false;
		if( !$primaryKeyValue || !is_numeric( $primaryKeyValue ) ) {
			throw new Exception( gettext( "Tag : " ) . gettext( "{$this->_foreignKey} should be a natural number!" ) );
		}

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		try {
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and {$this->_foreignKey} = '$primaryKeyValue'");
		} catch( Exception $ex ) {
			throw $ex;
		}
	}
}
