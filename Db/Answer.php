<?php
/**
 * InstaPress Framework Answer
 * LICENSE
 * @author		IBTeam
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_Answer extends Cms_Db_Abstract
{
	//Basic Table Info
	protected $_mainTable = "cms_answer";

	protected $_clauseColumnNames = array( 'answerId', "questionId","clientId", "answerStatus",'plagiarismPercentage', 'createdTime', 'publicationId', 'autoUserId','createdUserId' );

	protected $_sortColumnNames = array( "questionId","clientId", "answerStatus", 'createdTime' );
	protected $_foreignKey = "answerId";
	protected $_expandableTables = array();
	protected $_enableForcedCondition = true;

	protected $_updateColumnNames =  array( 'answerId', 'clientId', 'questionId', 'publicationId', 'answerStatus', 'plagiarismPercentage', 'autoUserId', 'createdUserId', 'createdTime' );

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
			throw new Exception("Answer : clientId should be a natural number!");
		}

		$answerId = isset( $this->_arrayUpdatedData['answerId'] ) ? $this->_arrayUpdatedData['answerId'] : false;
		if( !$answerId || !is_numeric( $answerId ) )
		{
			throw new Exception("Answer : answerId should be a natural number!");;
		}
		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}
		try
		{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and answerId = '$answerId'");
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
			throw new Exception("Answer : clientId should be a natural number!");
		}

		$answerId = isset( $this->_arrayUpdatedData['answerId'] ) ? $this->_arrayUpdatedData['answerId'] : false;
		if( !$answerId || !is_numeric( $answerId ) )
		{
			throw new Exception("Answer : answerId should be a natural number!");
		}
		try
		{
			$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and answerId = '$answerId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}

}

?>
