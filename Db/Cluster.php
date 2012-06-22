<?php
	/**
	 * InstaPress Cms_Db_Cluster
	 * LICENSE
	 * @author		Pramod Thakur
	 * @package		InstaPress
	 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
	 */
	class Cms_Db_Cluster extends Cms_Db_Abstract
	{
		protected $_mainTable = "cms_cluster";
		protected $_clauseColumnNames = array( 'clusterId', 'clientId', 'publicationId', 'clusterName', 'clusterTitle', 'clusterOrder', 'clusterDescription', 'clusterImage', 'createdUserId', 'createdTime' );
		protected $_sortColumnNames = array( 'clusterOrder', 'createdTime' );
		protected $_foreignKey = "clusterId";
		protected $_expandableTables = array();

		protected $_updateColumnNames = array( 'clusterId', 'clientId', 'publicationId', 'clusterTitle', 'clusterOrder', 'clusterImage', 'createdUserId', 'createdTime');

		function add() {
			$queryData = array();
			foreach( $this->_updateColumnNames as $column ) {
				if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
					$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
				} else {
					throw new Exception( gettext( "Cluster : " ) . gettext( $this->_arrayUpdatedData[ $column ] . " is a required field!" ) );
				}
			}
	
			try {
				$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData, false );
			} catch( Exception $ex ) {
				throw $ex;
			}
		}

		function delete() {
			$clusterId = isset( $this->_arrayUpdatedData[ 'clusterId' ] ) ? trim( $this->_arrayUpdatedData[ 'clusterId' ] ) : false;
			if( !$clusterId || !is_numeric( $clusterId ) ) {
				throw new Exception(  gettext( 'Cluster : ' ) . gettext( 'clusterId should be a natural number!' ));
			}

			try {
				$this->_databaseConnection->QueryDelete( $this->_mainTable, "clusterId = '$clusterId'" );
			} catch( Exception $ex ) {
				throw $ex;
			}
		}

		function edit() {
			$clusterId = isset( $this->_arrayUpdatedData[ 'clusterId' ] ) ? trim( $this->_arrayUpdatedData[ 'clusterId' ] ) : false;
			if( !$clusterId || !is_numeric( $clusterId ) ) {
				throw new Exception(  gettext( 'Cluster : ' ) . gettext( 'clusterId should be a natural number!' ));
			}

			$queryData = array();
			foreach( $this->_updateColumnNames as $column ) {
				if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
					$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
				}
			}
	
			try {
				$this->_databaseConnection->QueryUpdate( $this->_mainTable, $queryData, " clusterId = '$clusterId'");
			} catch( Exception $ex ) {
				throw $ex;
			}
		}
	}
