<?php
/**
 * InstaPress Cms_Db_Section
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_Section extends Cms_Db_Abstract
{
	protected $_mainTable = "cms_section";
	protected $_clauseColumnNames = array("sectionId","clientId","sectionSlug",
											"createdUserId", "sectionName" );
	protected $_sortColumnNames = array("sectionSlug");
	protected $_foreignKey = "sectionId";
	protected $_expandableTables = array();
	protected $_updateColumnNames = array( 'sectionId', 'clientId', 'sectionName', 'sectionSlug', 'createdUserId', 'createdTime' );

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
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception(  gettext( "Section : " ) . gettext( "clientId should be a natural number!" ));
		}

		$sectionId = isset($this->_arrayUpdatedData['sectionId'])?trim($this->_arrayUpdatedData['sectionId']):"";
		if( !$sectionId || !is_numeric( $sectionId ) )
		{
			throw new Exception(  gettext( "Section : " ) . gettext( "sectionId should be a natural number!" ));
		}

		try
		{
			$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and sectionId = '$sectionId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}

	function edit()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception( "Section : clientId should be a natural number!" );
		}
		$sectionId = isset($this->_arrayUpdatedData['sectionId'])?trim($this->_arrayUpdatedData['sectionId']): 0;
		if( !$sectionId || !is_numeric( $sectionId ) )
		{
			throw new Exception("Section : sectionId should be a natural number!");
		}
		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );

				/*if( $column = "sectionName" ){
					if( !self::checkEditDuplicate( $sectionId, $queryData['sectionName'] ) ){
					throw new Exception(  "Section : This section name already in use please try another!" );
					}
					$queryData['sectionSlug'] = Helper :: sanitizeWithDashes( $queryData['sectionName'] );// update the slug also.
					}*/
			}
		}
		try{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData," clientId = '$clientId' and sectionId = '$sectionId'");
		} catch( Exception $ex ){
			throw $ex;
		}
	}


	/*
	 * Check weather section name already in use except this section id.
	 */
	function checkEditDuplicate( $sectionId , $sectionName )
	{
		try{
			$objSection = new Cms_Db_Section();
			$objSection->set( "sectionName||$sectionName" );
			if( $objSection->getTotalCount() > 0 ){
				if( $sectionId ==  $objSection->get( "sectionId" ) ){
					return true;
				}else{
					return false;
				}
			}else{
				return true;
			}
		} catch( Exception $ex ){
			throw $ex;
		}
	}
}



