<?php

/**
 * InstaPress Cms_Db_TagGroup
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_TagGroup extends Cms_Db_Abstract {
	protected $_mainTable = "cms_tag_group";
	protected $_clauseColumnNames = array( "tagGroupId","clientId","groupSlug","tagCount","createdTime");
	protected $_sortColumnNames = array( "groupSlug", "tagGroupId" );
	protected $_foreignKey = "tagGroupId";
	protected $_expandableTables = array();
	protected $_updateColumnNames = array( "tagGroupId","clientId",'groupName',"groupSlug","tagCount","createdTime" );

	function add() {

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			} else {
				throw new Exception( gettext( "Tag : " ) . gettext( $this->_arrayUpdatedData[ $column ] . " is a required field!" ) );
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
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception("TagGroup : clientId should be a natural number!");
		}

		$tagGroupId = isset($this->_arrayUpdatedData['tagGroupId'])?trim($this->_arrayUpdatedData['tagGroupId']):"";
		if( !$tagGroupId || !is_numeric( $tagGroupId ) )
		{
			throw new Exception("TagGroup : tagGroupId should be a natural number!");
		}
		if( self::checkDependencies( $tagGroupId ) == 0 )
		{
			try
			{
				$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and tagGroupId = '$tagGroupId'");
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
			throw new Exception("TagGroup : clientId should be a natural number!");
		}

		$tagGroupId = isset($this->_arrayUpdatedData['tagGroupId'])?trim($this->_arrayUpdatedData['tagGroupId']):"";
		if( !$tagGroupId || !is_numeric( $tagGroupId ) )
		{
			throw new Exception("TagGroup : tagGroupId should be a natural number!");
		}
		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		try
		{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and tagGroupId = '$tagGroupId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}


	function checkDependencies($tagGroupId)
	{
		$objTag = new Cms_Db_Tag();
		$objTag->set( "tagGroupId||$tagGroupId","count||Y" );
		$totalCount = $objTag->getTotalCount();
		if( $totalCount > 0 )
		{
			throw new Exception("TagGroup : This record can't deleted because entry exist in Tag table.");
		}

		return $totalCount + $totalCount2;
	}

	/*
	 * Check weather tag name already in use except this tagGroup id.
	 */
	function checkEditDuplicate( $tagGroupId , $groupName )
	{
		try{
			$objTagGroup = new Cms_Db_TagGroup();
			$objTagGroup->set( "groupName||$groupName" );
			if( $objTagGroup->getTotalCount() > 0 ){
				if( $tagGroupId ==  $objTagGroup->get( "tagGroupId" ) ){
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
