<?php

class Cms_Db_ProductClass extends Cms_Db_Abstract
{
	//Basic Table Info
	protected $_mainTable = "cms_product_class";
	protected $_clauseColumnNames = array( "productClassId", "clientId","productClassName", "createdTime", 'createdUserId' );
	protected $_sortColumnNames = array();
	protected $_foreignKey = "productClassId";
	protected $_expandableTables = array();
	protected $_updateColumnNames = array( 'productClassId', 'clientId', 'productClassName', 'createdUserId', 'createdTime' );

	function add() {

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			} else {
				throw new Exception( gettext( "ProductClass : " ) . gettext( $this->_arrayUpdatedData[ $column ] . " is a required field!" ) );
			}
		}

		try {
			$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData, false );
		} catch( Exception $ex ) {
			throw $ex;
		}
	}

	/**
	 * Delete record
	 */
	function delete(){
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ))
		{
			throw new Exception( "ProductClass : clientId should be a natural number!" );
		}
		$productClassId = isset($this->_arrayUpdatedData['productClassId'])?trim($this->_arrayUpdatedData['productClassId']):"";
		if( !$productClassId || !is_numeric( $productClassId ) )
		{
			throw new Exception(  "ProductClass : productClassId should be a natural number!" );
		}

		if( self::checkDependencies( $productClassId ) == 0 ){
			try{
				$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and productClassId = '$productClassId'");
			}catch( Exception $ex ){
				throw $ex;
			}
		}
	}


	/*
	 * Edit record
	 */
	function edit() {
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ))
		{
			throw new Exception( "ProductClass : clientId should be a natural number!" );
		}

		$productClassId = isset($this->_arrayUpdatedData['productClassId'])?trim($this->_arrayUpdatedData['productClassId']):"";
		if( !$productClassId || !is_numeric( $productClassId ) )
		{
			throw new Exception( "ProductClass : productClassId should be a natural number!" );
		}

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}
		try
		{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and productClassId = '$productClassId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}

	function checkDependencies( $productClassId ){
		$objProductClassRatingParameter = new Network_Db_ProductClassRatingParameter();
		$objProductClassRatingParameter->set( "productClassId||$productClassId", "count||Y" );
		$totalCount = $objProductClassRatingParameter->getTotalCount();
		if( $totalCount > 0 ){
			throw new Exception( "ProductClass : This record can't deleted because entry exist in Client Info!" );
		}

		return $totalCount;
	}

}
