<?php
/**
 * InstaPress Framework Question
 * LICENSE
 * @category	Model
 * @author		IBTeam
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_Question extends Cms_Db_Abstract
{
	//Basic Table Info
	protected $_mainTable = "cms_question";
	protected $_clauseColumnNames = array( 'questionId', 'clientId', 'questionTitle', 'questionSlug', 'publicationId', 'firstCategoryId', 'secondCategoryId', 'thirdCategoryId', 'assetId', 'questionPrimaryImagePath', 'questionTags', 'parentDossierId', 'autoDiscussion', 'questionType', 'review', 'keyword', 'postingOrientation', 'questionStatus', 'totalAnswers', 'discussionStatus', 'stickyStatus', 'updatedTime', 'updatedUserId', 'autoUserId', 'createdUserId', 'createdTime' );

	protected $_sortColumnNames =  array('totalAnswers', 'updatedTime');

	protected $_foreignKey = "questionId";
	protected $_expandableTables = array();

	protected $_updateColumnNames =  array( 'questionId', 'clientId', 'questionTitle', 'questionSlug', 'publicationId', 'firstCategoryId', 'secondCategoryId', 'thirdCategoryId', 'assetId', 'questionPrimaryImagePath', 'questionTags', 'parentDossierId', 'autoDiscussion', 'questionType', 'review', 'keyword', 'postingOrientation', 'questionStatus', 'totalAnswers', 'discussionStatus', 'stickyStatus', 'updatedTime', 'updatedUserId', 'autoUserId', 'createdUserId', 'createdTime' );

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
		} catch( Exception $ex ) {
			throw $ex;
		}
	}

	function delete()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']): false;
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception("Question : clientId should be a natural number!");
		}

		$questionId = isset($this->_arrayUpdatedData['questionId'])?trim($this->_arrayUpdatedData['questionId']): false;
		if( !$questionId || !is_numeric( $questionId ) )
		{
			throw new Exception("Question : questionId should be a natural number!");
		}
		try
		{
			$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and questionId = '$questionId'");
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
			throw new Exception("Question : clientId should be a natural number!");
		}

		$questionId = isset($this->_arrayUpdatedData['questionId'])?trim($this->_arrayUpdatedData['questionId']): false;
		
		if( !$questionId || !is_numeric( $questionId ) ){
			throw new Exception("Question : questionId should be a natural number!");
		}
		
		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		try{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and questionId = '$questionId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}

}

?>
