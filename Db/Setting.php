<?php

	class Cms_Db_Setting extends Cms_Db_Abstract {
		protected $_mainTable = 'cms_setting';
		protected $_clauseColumnNames = array( 'settingId', 'name', 'value', 'createdUserId', 'createdTime' );
		protected $_sortColumnNames = array( 'settingId', 'name', 'value', 'createdUserId', 'createdTime' );
		protected $_foreignKey = 'settingId';
		protected $_expandableTables = array();
		protected $_updateColumnNames = array( 'name', 'value' );

		function add() {
			$queryData = array();
			foreach( $this->_updateColumnNames as $column ) {
				if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
					$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
				} else {
					throw new Exception( gettext( "Setting : " ) . gettext( $this->_arrayUpdatedData[ $column ] . " is a required field!" ) );
				}
			}
			$queryData[ 'createdUserId' ] = USER_ID;
			$queryData[ 'createdTime'] = 'now()';

			try {
				$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData, false );
			} catch( Exception $ex ) {
				throw $ex;
			}
		}

		function delete() {
			$settingId = isset( $this->_arrayUpdatedData[ 'settingId' ] ) ? trim( $this->_arrayUpdatedData[ 'settingId' ] ) : false;
			if( !$settingId || !is_numeric( $settingId ) ) {
				throw new Exception(  gettext( 'Setting : ' ) . gettext( 'settingId should be a natural number!' ));
			}

			try {
				$this->_databaseConnection->QueryDelete( $this->_mainTable, "settingId = '$settingId'" );
			} catch( Exception $ex ) {
				throw $ex;
			}
		}

		function edit() {
			$settingId = isset( $this->_arrayUpdatedData[ 'settingId' ] ) ? trim( $this->_arrayUpdatedData[ 'settingId' ] ) : false;
			if( !$settingId || !is_numeric( $settingId ) ) {
				throw new Exception(  gettext( 'Setting : ' ) . gettext( 'settingId should be a natural number!' ));
			}

			$queryData = array();
			foreach( $this->_updateColumnNames as $column ) {
				if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
					$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
				}
			}
	
			try {
				$this->_databaseConnection->QueryUpdate( $this->_mainTable, $queryData, " settingId = '$settingId'");
			} catch( Exception $ex ) {
				throw $ex;
			}
		}
	}
