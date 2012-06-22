<?php

	/**
	 * LICENSE
	 * @category	Model
	 * @author		IBTeam
	 * @package		InstaPress
	 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
	 */
	class Cms_Db_DeeplinkStory extends Cms_Db_Abstract {

		//Basic Table Info
		protected $_mainTable = 'cms_deeplink_story';
		protected $_clauseColumnNames = array( 'deeplinkStoryId', 'clientId', 'deeplinkId', 'storyId', 'publicationId', 'deleted', 'createdTime', 'createdUserId' );

		protected $_sortColumnNames =  array( 'deeplinkStoryId', 'clientId', 'deeplinkId', 'storyId', 'publicationId', 'deleted', 'createdTime', 'createdUserId' );

		protected $_foreignKey = 'deeplinkStoryId';
		protected $_expandableTables = array();

		protected $_updateColumnNames =  array( 'deeplinkStoryId', 'clientId', 'deeplinkId', 'storyId', 'publicationId', 'deleted', 'createdTime', 'createdUserId' );

		function add() {

			$queryData = array();
			foreach( $this->_updateColumnNames as $column ) {
				if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
					$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
				} else {
					throw new Exception( gettext( "ListItem : " ) . gettext( $this->_arrayUpdatedData[ $column ] . " is a required field!" ) );
				}
			}

			try {
				$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData, false );
			} catch( Exception $ex ) {
				throw $ex;
			}
		}

		function edit() {
			$pkValue = isset( $this->_arrayUpdatedData[ $this->_foreignKey ] ) ? trim( $this->_arrayUpdatedData[ $this->_foreignKey ] ) : false;
			if( !$pkValue || !is_numeric( $pkValue) ) {
				throw new Exception( __CLASS__ . " : $this->foreignKey should be a natural number!." );
			}

			$queryData = array();
			foreach( $this->_updateColumnNames as $column ) {
				if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
					$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
				}
			}

			try {
				$this->_databaseConnection->QueryUpdate( $this->_mainTable, $queryData, " $this->_foreignKey = '$pkValue'");
			} catch (Exception $ex) {
				throw $ex;
			}
		}

		public function delete() {
			//required and numeric
			$pkValue = isset( $this->_arrayUpdatedData[ $this->_foreignKey ] ) ? trim( $this->_arrayUpdatedData[ $this->_foreignKey ] ) : false;
			if( !$pkValue || !is_numeric( $pkValue)) {
				throw new Exception( __CLASS__ . " : $this->foreignKey should be a natural number!." );
			}

			try {
				$this->_databaseConnection->QueryDelete( $this->_mainTable, " $this->_foreignKey = '$pkValue'");
			} catch( Exception $ex ) {
				throw new Exception( __CLASS__ . 'Problem in record deletion' . $ex->getMessage() );
			}
		}
	}
