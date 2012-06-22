<?php
	/**
	 * InstaPress Framework ShoppingGuideProductText
	 * LICENSE
	 * @category	Model
	 * @author		IBTeam
	 * @package		InstaPress
	 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
	 */
	class Cms_Db_ListItemText extends Cms_Db_Abstract
	{
		//Basic Table Info
		protected $_mainTable = "cms_list_item_text";
		protected $_clauseColumnNames = array( 'listItemId', 'clientId', 'publicationId', 'storyId', 'listDescription', 'createdUserId', 'createdTime');

		protected $_sortColumnNames =  array();

		protected $_foreignKey = "listItemId";
		protected $_expandableTables = array();
		protected $_updateColumnNames =  array( 'listItemId', 'clientId', 'publicationId', 'storyId', 'listDescription', 'createdUserId', 'createdTime' );

		function add() {

			$queryData = array();
			foreach( $this->_updateColumnNames as $column ) {
				if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
					$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
				} else {
					throw new Exception( gettext( "ListItemText : " ) . gettext( $this->_arrayUpdatedData[ $column ] . " is a required field!" ) );
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
				throw new Exception( __CLASS__ . " : $this->_foreignKey should be a natural number!." );
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
			$storyId = isset( $this->_arrayUpdatedData[ 'storyId' ] ) ? trim( $this->_arrayUpdatedData[ 'storyId' ] ) : false;
			if( !$pkValue || !is_numeric( $pkValue)) {
				if( !$storyId ) {
					throw new Exception( __CLASS__ . " : $this->_foreignKey should be a natural number!." );
				}
			}

			try {
				if( !$storyId ) {
					$this->_databaseConnection->QueryDelete( $this->_mainTable, " $this->_foreignKey = '$pkValue'");
				} else {
					$this->_databaseConnection->QueryDelete( $this->_mainTable, " storyId = '$storyId'" );
				}
			} catch( Exception $ex ) {
				throw new Exception( __CLASS__ . 'Problem in record deletion' . $ex->getMessage() );
			}
		}
	}