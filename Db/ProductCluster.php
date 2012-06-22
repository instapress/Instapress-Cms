<?php
/**
 * InstaPress Cms_Db_Story
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */

class Cms_Db_ProductCluster extends Cms_Db_Abstract
{
	//Basic Table Info
	protected $_mainTable = "cms_product_cluster";
	protected $_clauseColumnNames = array('productClusterId', 'clientId', 'publicationId');

	protected $_sortColumnNames = array( "productClusterId", 'clusterName', "totalStories", "createdTime");

	protected $_foreignKey = "productClusterId";
	protected $_expandableTables = array();

	protected $_updateColumnNames = array( 'productClusterId', 'clientId', 'publicationId', 'clusterName', 'totalStories', 'createdUserId', 'createdTime' );

	function add() {

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			} else {
				throw new Exception( gettext( "ProductCluster : " ) . gettext( $this->_arrayUpdatedData[ $column ] . " is a required field!" ) );
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
			throw new Exception("ProductCluster : clientId should be a natural number!");
		}

		$productClusterId = isset($this->_arrayUpdatedData['productClusterId'])?trim($this->_arrayUpdatedData['productClusterId']): false;
		if( !$productClusterId || !is_numeric( $productClusterId ) )
		{
			throw new Exception("ProductCluster : productClusterId should be a natural number!");
		}
		try
		{
			$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and productClusterId = '$productClusterId'");
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
			throw new Exception(gettext("ProductCluster : ").gettext("clientId should be a natural number!"));
		}

		$productClusterId = isset($this->_arrayUpdatedData['productClusterId'])?trim($this->_arrayUpdatedData['productClusterId']): false;
		if( !$productClusterId || !is_numeric( $productClusterId ) )
		{
			throw new Exception(gettext("ProductCluster : ").gettext("productClusterId should be a natural number!"));
		}

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		try
		{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and productClusterId = '$productClusterId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}
}

?>
