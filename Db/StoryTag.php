<?php
/**
 * InstaPress Cms_Db_StoryTag
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */

class Cms_Db_StoryTag extends Cms_Db_Abstract {

	protected $_mainTable = "cms_story_tag";
	protected $_clauseColumnNames = array( 'storyTagId', 'clientId', 'storyId', 'tagId', 'publicationId', 'createdTime', 'storyTime' );
	protected $_sortColumnNames = array( 'storyTagId', 'clientId', 'storyId', 'tagId', 'publicationId', 'createdTime', 'storyTime' );
	protected $_foreignKey = "storyTagId";
	protected $_expandableTables = array();

	protected $_updateColumnNames = array( 'clientId', 'storyId', 'tagId', 'publicationId', 'storyTime' );

	public function add() {
		$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] )?trim( $this->_arrayUpdatedData[ 'clientId' ] ) : false;
		if( !$clientId || !is_numeric( $clientId ) ) {
			throw new Exception( gettext( "StoryTag : " ) . gettext( "clientId should be a natural number!" ) );
		}

		$storyId = isset( $this->_arrayUpdatedData[ 'storyId' ] )?trim( $this->_arrayUpdatedData[ 'storyId' ] ) : false;
		if( !$storyId || !is_numeric( $storyId ) ) {
			throw new Exception( gettext( "StoryTag : " ) . gettext( "storyId should be a natural number!" ) );
		}

		$tagId = isset( $this->_arrayUpdatedData[ 'tagId' ] )?trim( $this->_arrayUpdatedData[ 'tagId' ] ) : false;
		if( !$tagId || !is_numeric( $tagId ) ) {
			throw new Exception( gettext( "StoryTag : " ) . gettext( "tagId should be a natural number!" ) );
		}

		$publicationId = isset( $this->_arrayUpdatedData[ 'publicationId' ] )?trim( $this->_arrayUpdatedData[ 'publicationId' ] ) : false;
		if( !$publicationId || !is_numeric( $publicationId ) ) {
			throw new Exception( gettext( "StoryTag : " ) . gettext( "publicationId should be a natural number!" ) );
		}

		$storyTime = isset( $this->_arrayUpdatedData[ 'storyTime' ] )?trim( $this->_arrayUpdatedData[ 'storyTime' ] ) : 'now()';
		if( !$storyTime ) {
			throw new Exception( gettext( "StoryTag : " ) . gettext( "storyTime is required!" ) );
		}

		$createdTime = 'now()';

		$queryData = array();
		$queryData[ "clientId" ] = $clientId;
		$queryData[ "storyId" ] = $storyId;
		$queryData[ "tagId" ] = $tagId;
		$queryData[ "publicationId" ] = $publicationId;
		$queryData['storyTime'] = $storyTime;
		$queryData[ "createdTime" ] = $createdTime;

		try {
			$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData );

		} catch( Exception $ex ) {
			throw $ex;
		}
	}

	public function edit() {
		$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] )?trim( $this->_arrayUpdatedData[ 'clientId' ] ) : false;
		if( !$clientId || !is_numeric( $clientId ) ) {
			throw new Exception( gettext( "StoryTag : " ) . gettext( "clientId should be a natural number!" ) );
		}

		$storyTagId = isset( $this->_arrayUpdatedData[ 'storyTagId' ] )?trim( $this->_arrayUpdatedData[ 'storyTagId' ] ) : false;
		if( !$storyTagId || !is_numeric( $storyTagId ) ) {
			throw new Exception( gettext( "StoryTag : " ) . gettext( "storyTagId should be a natural number!" ) );
		}

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		try {
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and storyTagId = '$storyTagId'");
		} catch( Exception $ex ) {
			throw $ex;
		}
	}

	public function delete() {
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']): false;
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception(gettext("StoryTag : ").gettext("clientId should be a natural number!"));
		}

		$storyId = isset($this->_arrayUpdatedData['storyId'])?trim($this->_arrayUpdatedData['storyId']): false;
		if(  !( $storyId && is_numeric( $storyId ) ) ) {
			throw new Exception(gettext("StoryTag : ").gettext(" storyId should be a natural number!"));
		}

		try {
			$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and storyId = '$storyId'");
		} catch( Exception $ex ) {
			throw $ex;
		}
	}
}