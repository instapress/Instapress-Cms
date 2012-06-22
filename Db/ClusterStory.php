<?php

	/**
	 * InstaPress Cms_Db_ClusterStory
	 * LICENSE
	 * @author		Pramod Thakur
	 * @package		InstaPress
	 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
	 */
	class Cms_Db_ClusterStory extends Cms_Db_Abstract
	{
		protected $_mainTable = "cms_cluster_story";
		protected $_clauseColumnNames = array( 'clusterStoryId', 'storyId', 'storyImage', 'storyOrder', 'clientId', 'publicationId', 'clusterId', 'storyTitle', 'storyUrl', 'createdUserId', 'createdTime' );
		protected $_sortColumnNames = array('storyOrder');
		protected $_foreignKey = "clusterStoryId";
		protected $_expandableTables = array();

		protected $_updateColumnNames = array( 'clusterStoryId', 'storyId', 'storyImage', 'storyOrder', 'clientId', 'publicationId', 'clusterId', 'storyTitle', 'storyUrl', 'storyExcerpt', 'createdUserId', 'createdTime' );

		function add() {
			$queryData = array();
			foreach( $this->_updateColumnNames as $column ) {
				if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
					$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
				} else {
					throw new Exception( gettext( "ClusterStory : " ) . gettext( $this->_arrayUpdatedData[ $column ] . " is a required field!" ) );
				}
			}
	
			try {
				$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData, false );
			} catch( Exception $ex ) {
				throw $ex;
			}
		}

		function delete() {
			$clusterStoryId = isset( $this->_arrayUpdatedData[ 'clusterStoryId' ] ) ? trim( $this->_arrayUpdatedData[ 'clusterStoryId' ] ) : false;
			if( !$clusterStoryId || !is_numeric( $clusterStoryId ) ) {
				throw new Exception(  gettext( 'ClusterStory : ' ) . gettext( 'clusterStoryId should be a natural number!' ));
			}

			try {
				$this->_databaseConnection->QueryDelete( $this->_mainTable, "clusterStoryId = '$clusterStoryId'" );
			} catch( Exception $ex ) {
				throw $ex;
			}
		}

		function edit() {
			$clusterStoryId = isset( $this->_arrayUpdatedData[ 'clusterStoryId' ] ) ? trim( $this->_arrayUpdatedData[ 'clusterStoryId' ] ) : false;
			if( !$clusterStoryId || !is_numeric( $clusterStoryId ) ) {
				throw new Exception(  gettext( 'ClusterStory : ' ) . gettext( 'clusterStoryId should be a natural number!' ));
			}

			$queryData = array();
			foreach( $this->_updateColumnNames as $column ) {
				if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
					$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
				}
			}
	
			try {
				$this->_databaseConnection->QueryUpdate( $this->_mainTable, $queryData, " clusterStoryId = '$clusterStoryId'");
			} catch( Exception $ex ) {
				throw $ex;
			}
		}

		public function emptyTable() {
			$myObj = new self();
			$myObj->set( "quantity||5000" );
			$recordsCount = $myObj->getResultCount();
			for( $i = 0; $i < $recordsCount; $i++ ) {
				$ekAurSelf = new self( 'delete' );
				$ekAurSelf->set( "clusterStoryId||" . $myObj->getClusterStoryId( $i ) );
			}
		}
	}