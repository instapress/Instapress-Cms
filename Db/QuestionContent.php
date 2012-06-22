<?php
/**
 * InstaPress Framework QuestionContent
 * LICENSE
 * @author		IBTeam
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_QuestionContent extends Cms_Db_Abstract
{
	//Basic Table Info
	protected $_mainTable = "cms_question_content";

	protected $_clauseColumnNames = array( "questionId","clientId", "questionYear", 'createdTime', 'publicationId' );

	protected $_sortColumnNames = array( "questionId","clientId", "questionYear", 'createdTime' );
	protected $_foreignKey = "questionId";
	protected $_expandableTables = array();
	protected $_enableForcedCondition = true;

	protected $_updateColumnNames =  array( 'questionId', 'questionYear', 'clientId', 'publicationId', 'questionContent', 'createdTime' );

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

	function edit()
	{
		$clientId = isset( $this->_arrayUpdatedData['clientId'] ) ? $this->_arrayUpdatedData['clientId'] : false;
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception("QuestionContent : clientId should be a natural number!");
		}

		$questionId = isset( $this->_arrayUpdatedData['questionId'] ) ? $this->_arrayUpdatedData['questionId'] : false;
		if( !$questionId || !is_numeric( $questionId ) )
		{
			throw new Exception("QuestionContent : questionId should be a natural number!");;
		}
		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}
		try
		{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and questionId = '$questionId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}

	function delete()
	{
		$clientId = isset( $this->_arrayUpdatedData['clientId'] ) ? $this->_arrayUpdatedData['clientId'] : false;
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception("QuestionContent : clientId should be a natural number!");
		}

		$questionId = isset( $this->_arrayUpdatedData['questionId'] ) ? $this->_arrayUpdatedData['questionId'] : false;
		if( !$questionId || !is_numeric( $questionId ) )
		{
			throw new Exception("QuestionContent : questionId should be a natural number!");
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

}

?>
