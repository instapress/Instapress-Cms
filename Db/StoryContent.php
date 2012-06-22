<?php
/**
 * InstaPress Cms_Db_StoryContent
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */

class Cms_Db_StoryContent extends Cms_Db_Abstract {

	protected $_mainTable = "cms_story_content";
	protected $_clauseColumnNames = array('storyId', 'clientId', 'createdTime', 'storyYear');
	protected $_sortColumnNames = array('storyId', 'createdTime');
	protected $_foreignKey = "storyId";
	protected $_expandableTables = array();
	protected $_updateColumnNames = array( 'storyId', 'clientId', 'storyWebContent', 'storyYear', 'createdTime' );

	/*public function Cms_Db_StoryContent($task = 'show', $year = null) {
	 $year = $year !== null ? $year : date('Y');
	 //$this->_mainTable .= $year;
	 parent::__construct($task);
	 }*/

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
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']): false;
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception("StoryContent : clientId should be a natural number!");
		}

		$storyId = isset($this->_arrayUpdatedData['storyId'])?trim($this->_arrayUpdatedData['storyId']): false;
		if( !$storyId || !is_numeric( $storyId ) )
		{
			throw new Exception("StoryContent : storyId should be a natural number!");
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

	function edit() {
		$clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : false;
		if (!$clientId || !is_numeric($clientId)) {
			throw new Exception(gettext("StoryContent : ") . gettext("clientId should be a natural number!"));
		}

		$storyId = isset($this->_arrayUpdatedData['storyId']) ? trim($this->_arrayUpdatedData['storyId']) : false;
		if (!$storyId || !is_numeric($storyId)) {
			throw new Exception(gettext("StoryContent : ") . gettext("storyId should be a natural number!"));
		}

		$queryData = array();

		foreach ($this->_updateColumnNames as $updateColumn) {
			if (isset($this->_arrayUpdatedData[$updateColumn])) {
				$queryData[$updateColumn] = trim($this->_arrayUpdatedData[$updateColumn]);
			}
		}

		$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and storyId = '$storyId'");
	}

}