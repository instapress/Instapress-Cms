<?php
/**
 * InstaPress Framework ShoppingGuide
 * LICENSE
 * @category	Model
 * @author		IBTeam
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_ShoppingGuide extends Cms_Db_Abstract
{
	//Basic Table Info
	protected $_mainTable = "cms_shopping_guide";
	protected $_clauseColumnNames = array( 'publicationId', "clientId","totalProducts", "productClassId", 'ratingParameter1', 'ratingParameter2', 'ratingParameter3', 'ratingParameter4', 'ratingParameter5', "storyId","pros", 'cons', 'technicalSpecification', 'createdUserId');

	protected $_sortColumnNames =  array();

	protected $_foreignKey = "storyId";
	protected $_expandableTables = array();

	protected $_updateColumnNames =  array( 'storyId', 'clientId', 'publicationId', 'totalProducts', 'productClassId', 'ratingParameter1', 'ratingParameter2', 'ratingParameter3', 'ratingParameter4', 'ratingParameter5', 'pros', 'cons', 'technicalSpecification', 'createdUserId', 'createdTime' );

	function add() {

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			} else {
				throw new Exception( gettext( "ShoppingGuide : " ) . gettext( $this->_arrayUpdatedData[ $column ] . " is a required field!" ) );
			}
		}

		try {
			$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData, false );
		} catch( Exception $ex ) {
			throw $ex;
		}
	}

	function delete(){
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']): false;
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception("ShoppingGuide : clientId should be a natural number!");
		}

		$storyId = isset($this->_arrayUpdatedData['storyId'])?trim($this->_arrayUpdatedData['storyId']): false;
		if( !$storyId || !is_numeric( $storyId ) )
		{
			throw new Exception("ShoppingGuide : storyId should be a natural number!");
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


	function edit(){
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']): false;
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception("ShoppingGuide : clientId should be a natural number!");
		}

		$storyId = isset($this->_arrayUpdatedData['storyId'])?trim($this->_arrayUpdatedData['storyId']): false;
		if( !$storyId || !is_numeric( $storyId ) ){
			throw new Exception("ShoppingGuide : storyId should be a natural number!");
		}
		
		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		try{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and storyId = '$storyId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}

}

?>
