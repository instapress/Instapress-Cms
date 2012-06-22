<?php
/**
 * InstaPress Cms_Db_Tag
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */

class Cms_Db_Tag extends Cms_Db_Abstract {

	protected $_mainTable = "cms_tag";
	protected $_clauseColumnNames = array( 'tagId', 'clientId', 'tagName', 'tagSlug', 'tagGroupId', 'storyCount', 'createdTime' );
	protected $_sortColumnNames = array( 'tagId', 'clientId', 'tagSlug', 'tagGroupId', 'createdTime' );
	protected $_foreignKey = "tagId";
	protected $_expandableTables = array( 'cms_tag_expandable' );

	protected $_updateColumnNames = array( 'tagId', 'clientId', 'tagName', 'tagSlug', 'tagGroupId', 'storyCount', 'createdTime' );

	public function add() {

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			} else {
				throw new Exception( gettext( "Tag : " ) . gettext( $column . " is a required field!" ) );
			}
		}
		$queryData[ 'storyCount' ] = 1;

		try {
			$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData, false );
		} catch( Exception $ex ) {
			throw $ex;
		}
	}


	function delete()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception(  gettext( "Tag : " ) . gettext( "clientId should be a natural number!" ));
		}

		$tagId = isset($this->_arrayUpdatedData['tagId'])?trim($this->_arrayUpdatedData['tagId']):"";
		if( !$tagId || !is_numeric( $tagId ) )
		{
			throw new Exception(  gettext( "Tag : " ) . gettext( "tagId should be a natural number!" ));
		}
		if( self::checkDependencies( $tagId ) == 0 )
		{
			try
			{
				$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and tagId = '$tagId'");
			}
			catch( Exception $ex )
			{
				throw $ex;
			}
		}
	}

	function checkDependencies( $tagId )
	{
		return 0;
	}

	public function edit() {
		$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] )?trim( $this->_arrayUpdatedData[ 'clientId' ] ) : false;
		if( !$clientId || !is_numeric( $clientId ) ) {
			throw new Exception( gettext( "Tag : " ) . gettext( "clientId should be a natural number!" ) );
		}

		$tagId = isset( $this->_arrayUpdatedData[ 'tagId' ] )?trim( $this->_arrayUpdatedData[ 'tagId' ] ) : false;
		if( !$tagId || !is_numeric( $tagId ) ) {
			throw new Exception( gettext( "Tag : " ) . gettext( "tagId should be a natural number!" ) );
		}
			
		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		$storyTagDbObj = new Cms_Db_StoryTag();
		$storyTagDbObj->set( "tagId||$tagId", 'count||Y' );
		$queryData[ 'storyCount' ] = $storyTagDbObj->getTotalCount();

		try {
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and tagId = '$tagId'");
		} catch( Exception $ex ) {
			throw $ex;
		}
	}


	/*
	 * Check weather tag name already in use except this tag id.
	 */
	function checkEditDuplicate( $tagId , $tagName )
	{
		try{
			$objtag = new Network_Db_Tag();
			$objtag->set( "tagName||$tagName" );
			if( $objtag->getTotalCount() > 0 ){
				if( $tagId ==  $objtag->get( "tagId" ) ){
					return true;
				}else{
					return false;
				}
			}else{
				return true;
			}
		} catch( Exception $ex ){
			throw $ex;
		}
	}

}