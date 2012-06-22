<?php
/**
 * InstaPress Cms_Db_Video
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */

class Cms_Db_Video extends Cms_Db_Abstract {

	protected $_mainTable = "cms_video";
	protected $_clauseColumnNames = array( "videoId", "clientId", "videoKeywords", 'videoSocialKey', 'assetId', 'primaryImagePath',
										    "videoSourceDomain", "videoCopyright", "createdUserId", "createdTime" );
	protected $_sortColumnNames = array();
	protected $_foreignKey = "videoId";
	protected $_expandableTables = array( "cms_video_expandable", "cms_video_expandable_text" );
	
	protected $_updateColumnNames = array( 'videoId', 'clientId', 'videoTitle', 'videoDescription', 'videoKeywords', 'videoCredit', 'videoSourceDomain', 'videoCopyright', 'videoStatus', 'videoSocialKey', 'assetId', 'primaryImagePath', 'createdUserId', 'createdTime' );

	function add() {

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			} else {
				throw new Exception( gettext( "Tag : " ) . gettext( $column . " is a required field!" ) );
			}
		}

		try {
			$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData, false );
		} catch( Exception $ex ) {
			throw $ex;
		}
	}


	function edit() {
		$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] ) ? trim( $this->_arrayUpdatedData[ 'clientId' ] ) : "";
		if( !$clientId || !is_numeric( $clientId ) ) {
			throw new Exception(gettext("clientId should be a natural number!"));
		}

		$videoId = isset( $this->_arrayUpdatedData[ 'videoId' ] ) ? trim( $this->_arrayUpdatedData[ 'videoId' ] ) : "";
		if( !$videoId || !is_numeric( $videoId ) ) {
			throw new Exception(gettext("videoId should be a natural number!"));
		}
		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}
		try {
			$this->_databaseConnection->QueryUpdate( $this->_mainTable, $queryData, " clientId = '$clientId' and videoId = '$videoId'" );
		} catch( Exception $ex ) {
			throw $ex;
		}
	}

	function delete() {
		$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] ) ? trim( $this->_arrayUpdatedData[ 'clientId' ] ) : "";
		if( !$clientId || !is_numeric( $clientId ) ) {
			throw new Exception(gettext("clientId should be a natural number!"));
		}

		$videoId = isset( $this->_arrayUpdatedData[ 'videoId' ] ) ? trim( $this->_arrayUpdatedData[ 'videoId' ] ) : "";
		if( !$videoId || !is_numeric( $videoId ) ) {
			throw new Exception(gettext("videoId should be a natural number!"));
		}
		if( self::checkDependencies($clientId, $videoId) == 0 ) {
			try {
				$this->_databaseConnection->QueryDelete( $this->_mainTable, " clientId = '$clientId' and videoId = '$videoId'" );
			} catch( Exception $ex ) {
				throw $ex;
			}
		}
	}

	function checkDependencies( $clientId, $videoId ) {
		$objVideoExpandable = new Cms_Db_VideoExpandable();
		$objVideoExpandable->set( "clientId||$clientId", "videoId||$videoId", "count||Y" );
		$totalCount = $objVideoExpandable->getTotalCount();
		if( $totalCount > 0 ) {
			throw new Exception(gettext("This record can't deleted because entry exist in Video Expandable!"));
		}
		$objVideoExpandableText = new Cms_Db_VideoExpandableText();
		$objVideoExpandableText->set( "clientId||$clientId", "videoId||$videoId", "count||Y" );
		$totalCount2 = $objVideoExpandableText->getTotalCount();
		if( $totalCount2 > 0 ) {
			throw new Exception(gettext("This record can't deleted because entry exist in Video Expandable Text!"));
		}
		return $totalCount + $totalCount2;
	}

}
