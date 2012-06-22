<?php
/**
 * InstaPress Cms_Db_Archive
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_Archive extends Cms_Db_Abstract
{
	protected $_mainTable = "cms_archive";
	protected $_clauseColumnNames = array( "archiveId","clientId","publicationId", "yearmonth", "createdTime" );

	protected $_sortColumnNames = array('createdTime', 'yearmonth');
	protected $_foreignKey = "archiveId";
	protected $_expandableTables = array();

	protected $_updateColumnNames = array( "clientId", "publicationId", "yearmonth", "totalStories" );

	function add() {
		// Required
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId )) {
			throw new Exception(gettext("archive : ").gettext("clientId should be a natural number!"));

		}

		$publicationId = isset( $this->_arrayUpdatedData['publicationId'])?trim($this->_arrayUpdatedData['publicationId']): false;
		if( !$publicationId || !is_numeric( $publicationId ) ) {
			throw new Exception(gettext("archive : ").gettext("publicationId should be a natural number!"));
		}

		$yearmonth = isset($this->_arrayUpdatedData['yearmonth'])?trim($this->_arrayUpdatedData['yearmonth']):"";
		if( !$yearmonth ) {
			throw new Exception(gettext("archive : ").gettext("yearmonth is a required parameter and cannot be blank!"));
		}

		$storyDbObj = new Cms_Db_Story();
		$storyDbObj->set( "publicationId||$publicationId", "yearmonth||$yearmonth", 'count||Y' );
		$totalStories = $storyDbObj->getTotalCount();//isset($this->_arrayUpdatedData['totalStories'])?trim($this->_arrayUpdatedData['totalStories']):1;

		$selfObj = new self();
		$selfObj->set( "publicationId||$publicationId", "yearmonth||$yearmonth" );

		if( $selfObj->getResultCount() > 0 ) {
			$selfEditObj = new self( 'edit' );
			$selfEditObj->set( 'archiveId||' . $selfObj->getArchiveId(), "totalStories||$totalStories" );
		} else {

			$queryData = array();
			$queryData['clientId'] = $clientId;
	        $queryData['publicationId'] = $publicationId;
	        $queryData['yearmonth'] = $yearmonth;
	        $queryData['totalStories'] = $totalStories;
			$queryData['createdTime'] = 'now()';

			try {
				$this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $queryData);
			} catch( Exception $ex ) {
				throw $ex;
			}
		}
	}

	function edit()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']): false;
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception(gettext("StoryArchive : ").gettext("clientId should be a natural number!"));
		}
		
		$archiveId = isset($this->_arrayUpdatedData['archiveId'])?trim($this->_arrayUpdatedData['archiveId']): false;
	
		if( !( $archiveId && is_numeric( $archiveId ) )  ) {
			throw new Exception(gettext("StoryArchive : ").gettext("ArchiveId should be a natural number!"));
		}

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}
				

		try
		{			
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and archiveId = '$archiveId'");			
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}

	function delete()
	{
		echo gettext("delete code");
	}

}
