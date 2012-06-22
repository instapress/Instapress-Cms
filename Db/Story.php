<?php
/**
 * InstaPress Cms_Db_Story
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */

class Cms_Db_Story extends Cms_Db_Abstract
{
	//Basic Table Info
	protected $_mainTable = "cms_story";
	protected $_clauseColumnNames = array( 'storyId','clientId', 'oldStoryId', 'authorId','authorLogin','authorByLine','publicationId','assetId','storyTemplateId','firstCategoryId','secondCategoryId','thirdCategoryId','keyword','storyType','moduleType','viewType','moderationStatus','moderatorUserId','moderationTime','countListItem','countComment','voteUp','voteDown','netVote','updatedTime','updatedUserId','productClusterId','storyPublished','createdTime','storyTitle','storySlug','storyExcerpt','storyTags','canonicalUrl','sectionId','primaryImagePath','yearmonth','storyYear' );

	protected $_sortColumnNames = array( "storyId", 'storySlug', 'oldStoryId', "storyYear", "createdUserId","storyTemplateId", "createdTime", 'canonicalUrl');

	protected $_foreignKey = "storyId";
	protected $_expandableTables = array();

	protected $_updateColumnNames = array( 'storyId','clientId', 'oldStoryId','authorId','authorLogin','authorByLine','publicationId','assetId','storyTemplateId','firstCategoryId','secondCategoryId','thirdCategoryId','keyword','storyType','moduleType','viewType','moderationStatus','moderatorUserId','moderationTime','countListItem','countComment','voteUp','voteDown','netVote','updatedTime','updatedUserId','productClusterId','storyPublished','createdTime','storyTitle','storySlug','storyExcerpt','storyTags','canonicalUrl','sectionId','primaryImagePath','yearmonth','storyYear' );

	function add() {

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			} else {
				throw new Exception( gettext( "Story : " ) . gettext( $this->_arrayUpdatedData[ $column ] . " is a required field!" ) );
			}
		}

		try {
			$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData, false );

			if( $queryData[ 'firstCategoryId' ] > 0 ) {
				$storiesObj = new Cms_Stories( 'firstCategoryId||' . $queryData[ 'firstCategoryId' ], 'count||Y' );
				$categoryObj = new Cms_Category( $queryData[ 'firstCategoryId' ] );
				$categoryObj->update( 'storiesCount||' . $storiesObj->getTotalCount() );
			}

			if( $queryData[ 'secondCategoryId' ] > 0 ) {
				$storiesObj = new Cms_Stories( 'secondCategoryId||' . $queryData[ 'secondCategoryId' ], 'count||Y' );
				$categoryObj = new Cms_Category( $queryData[ 'secondCategoryId' ] );
				$categoryObj->update( 'storiesCount||' . $storiesObj->getTotalCount() );
			}

			if( $queryData[ 'thirdCategoryId' ] > 0 ) {
				$storiesObj = new Cms_Stories( 'thirdCategoryId||' . $queryData[ 'thirdCategoryId' ], 'count||Y' );
				$categoryObj = new Cms_Category( $queryData[ 'thirdCategoryId' ] );
				$categoryObj->update( 'storiesCount||' . $storiesObj->getTotalCount() );
			}
		} catch( Exception $ex ) {
			throw $ex;
		}
	}


	function delete()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']): false;
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception("Story : clientId should be a natural number!");
		}

		$storyId = isset($this->_arrayUpdatedData['storyId'])?trim($this->_arrayUpdatedData['storyId']): false;
		if( !$storyId || !is_numeric( $storyId ) )
		{
			throw new Exception("Story : storyId should be a natural number!");
		}
		try
		{
			$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and storyId = '$storyId'");
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
			throw new Exception(gettext("Story : ").gettext("clientId should be a natural number!"));
		}

		$storyId = isset($this->_arrayUpdatedData['storyId'])?trim($this->_arrayUpdatedData['storyId']): false;
		if( !$storyId || !is_numeric( $storyId ) )
		{
			throw new Exception(gettext("Story : ").gettext("storyId should be a natural number!"));
		}

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		try
		{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and storyId = '$storyId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}
}

?>
