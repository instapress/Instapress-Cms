<?php

class Cms_Db_ProductClassRatingParameter extends Cms_Db_Abstract
{
	//Basic Table Info
	protected $_mainTable = "cms_product_class_rating_parameter";
	protected $_clauseColumnNames = array( "productClassRatingParameterId", "clientId","ratingParameterName", "createdTime", "productClassId", 'createdUserId' );
	protected $_sortColumnNames = array();
	protected $_foreignKey = "productClassRatingParameterId";
	protected $_expandableTables = array();
	protected $_updateColumnNames = array( 'productClassRatingParameterId', 'clientId', 'productClassId', 'ratingParameterName', 'createdUserId', 'createdTime' );

	function add() {

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			} else {
				throw new Exception( gettext( "ProductClassRatingParameter : " ) . gettext( $this->_arrayUpdatedData[ $column ] . " is a required field!" ) );
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
	function delete()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ))
		{
			throw new Exception( "ProductClassRatingParameter : clientId should be a natural number!" );
		}
		$productClassRatingParameterId = isset($this->_arrayUpdatedData['productClassRatingParameterId'])?trim($this->_arrayUpdatedData['productClassRatingParameterId']):"";
		if( !$productClassRatingParameterId || !is_numeric( $productClassRatingParameterId ) )
		{
			throw new Exception(  "ProductClassRatingParameter : productClassRatingParameterId should be a natural number!" );
		}

		try{
			$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and productClassRatingParameterId = '$productClassRatingParameterId'");
		}catch( Exception $ex ){
			throw $ex;
		}
	}


	/*
	 * Edit record
	 */
	function edit() {
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ))
		{
			throw new Exception( "ProductClassRatingParameter : clientId should be a natural number!" );
		}

		$productClassRatingParameterId = isset($this->_arrayUpdatedData['productClassRatingParameterId'])?trim($this->_arrayUpdatedData['productClassRatingParameterId']):"";
		if( !$productClassRatingParameterId || !is_numeric( $productClassRatingParameterId ) )
		{
			throw new Exception( "ProductClassRatingParameter : productClassRatingParameterId should be a natural number!" );
		}

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}
		try
		{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and productClassRatingParameterId = '$productClassRatingParameterId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}

}
