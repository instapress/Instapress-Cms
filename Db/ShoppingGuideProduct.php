<?php
/**
 * InstaPress Framework ShoppingGuideProduct
 * LICENSE
 * @category	Model
 * @author		IBTeam
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_ShoppingGuideProduct extends Cms_Db_Abstract
{
	//Basic Table Info
	protected $_mainTable = "cms_shopping_guide_product";
	protected $_clauseColumnNames = array( 'shoppingGuideProductId', 'publicationId', "clientId", 'productName', 'productBrandName', "assetId", "productPrice", 'ratingParameter1', 'ratingParameter2', 'ratingParameter3', 'priceLink', 'ratingParameter4', 'ratingParameter5', "storyId","averageRating", 'createdUserId');

	protected $_sortColumnNames =  array( 'shoppingGuideProductId', 'createdTime', 'serialNumber' );

	protected $_foreignKey = "shoppingGuideProductId";
	protected $_expandableTables = array();

	protected $_updateColumnNames =  array( 'shoppingGuideProductId', 'clientId', 'publicationId', 'storyId', 'productName', 'productBrandName', 'assetId', 'productPrice', 'priceLink', 'serialNumber', 'ratingParameter1', 'ratingParameter2', 'ratingParameter3', 'ratingParameter4', 'ratingParameter5', 'averageRating', 'createdUserId', 'createdTime' );

	function add() {

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			} else {
				throw new Exception( gettext( "ShoppingGuideProduct : " ) . gettext( $this->_arrayUpdatedData[ $column ] . " is a required field!" ) );
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
			throw new Exception("ShoppingGuideProduct : clientId should be a natural number!");
		}

		$shoppingGuideProductId = isset($this->_arrayUpdatedData['shoppingGuideProductId'])?trim($this->_arrayUpdatedData['shoppingGuideProductId']): false;
		if( !$shoppingGuideProductId || !is_numeric( $shoppingGuideProductId ) )
		{
			throw new Exception("ShoppingGuideProduct : shoppingGuideProductId should be a natural number!");
		}
		try
		{
			$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and shoppingGuideProductId = '$shoppingGuideProductId'");
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
			throw new Exception("ShoppingGuideProduct : clientId should be a natural number!");
		}

		$shoppingGuideProductId = isset($this->_arrayUpdatedData['shoppingGuideProductId'])?trim($this->_arrayUpdatedData['shoppingGuideProductId']): false;
		if( !$shoppingGuideProductId || !is_numeric( $shoppingGuideProductId ) ){
			throw new Exception("ShoppingGuideProduct : shoppingGuideProductId should be a natural number!");
		}
		
		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		try{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and shoppingGuideProductId = '$shoppingGuideProductId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}

}

?>
