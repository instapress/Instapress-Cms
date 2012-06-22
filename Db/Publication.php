<?php
class Cms_Db_Publication extends Cms_Db_Abstract
{
	protected $_mainTable = "cms_publication";
	protected $_clauseColumnNames = array("publicationId","clientId","publicationName",
											"publicationPublishedStory", "publicationStartTime", "publicationCurrentTime", "sectionId",
											"priority", "gaProfileName", "webPropertyIdentity", "webProfileId", 
											 "createdUserId", "publicationActiveStatus");

	protected $_sortColumnNames = array( "publicationId", "publicationName","publicationPublishedStory", "createdTime" );

	protected $_foreignKey = "publicationId";
	protected $_expandableTables = array();

	protected $_updateColumnNames = array( 'publicationId', 'clientId', 'publicationName', 'publicationDescription', 'publicationActiveStatus', 'publicationPublishedStory', 'publicationStartTime', 'publicationUrl', 'sectionId', 'priority', 'gaProfileName', 'webPropertyIdentity', 'webProfileId', 'statCounterCode', 'qualityAnalysis', 'createdUserId', 'createdTime' );

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
			throw new Exception(  gettext( "Publication : ") . gettext( "clientId should be a natural number!" ));
		}
		$publicationId = isset($this->_arrayUpdatedData['publicationId'])?trim($this->_arrayUpdatedData['publicationId']):"";
		if( !$publicationId || !is_numeric( $publicationId ) )
		{
			throw new Exception(  gettext( "Publication : ") . gettext( "publicationId should be a natural number!" ));
		}
		if( self::checkDependencies($clientId,$publicationId) == 0 )
		{
			try
			{
				$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and publicationId = '$publicationId'");
			}
			catch( Exception $ex )
			{
				throw $ex;
			}
		}
	}

	function edit()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception(  gettext( "Publication : ") . gettext( "clientId should be a natural number!" ));
		}

		$publicationId = isset($this->_arrayUpdatedData['publicationId'])?trim($this->_arrayUpdatedData['publicationId']):"";
		if( !$publicationId || !is_numeric( $publicationId ) )
		{
			throw new Exception(  gettext( "Publication : ") . gettext( "publicationId should be a natural number!" ));
		}

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );

				/*if( $column = "publicationName" ){
					if( !self::checkEditDuplicate( $tagId, $queryData['tagName'] ) ){
					throw new Exception(  "Publication : This publication name already in use please try another!" );
					}
					}*/
			}
		}
		try
		{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and publicationId = '$publicationId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}


	function checkDependencies($clientId,$publicationId)
	{
		$objPublicationArchive = new Network_Db_PublicationArchive();
		$objPublicationArchive->set("clientId||$clientId", "publicationId||$publicationId");
		$totalCount = $objPublicationArchive->getTotalCount();
		if( $totalCount > 0 )
		{
			throw new Exception( gettext( "Publication : ") . gettext( "This record can't deleted because entry exist in Publication archive!"));
		}

		$objDossier = new Editorial_Db_Dossier();
		$objDossier->set("clientId||$clientId", "publicationId||$publicationId");
		$totalCount2 = $objDossier->getTotalCount();
		if( $totalCount2 > 0 )
		{
			throw new Exception( gettext( "Publication : ") . gettext( "This record can't deleted because entry exist in Entn Dossier!"));
		}

		return $totalCount + $totalCount2;
	}

	/*
	 * Check weather publication name already in use except this publication id.
	 */
	function checkEditDuplicate( $publicationId , $publicationName )
	{
		try{
			$objPublication = new Network_Db_Publication();
			$objPublication->set( "publicationName||$publicationName" );
			if( $objPublication->getTotalCount() > 0 ){
				if( $publicationId ==  $objPublication->get( "publicationId" ) ){
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