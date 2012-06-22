<?php

	class Cms_Db_Category extends Cms_Db_Abstract {
		protected $_mainTable = 'cms_category';
		protected $_clauseColumnNames = array( 'categoryId', 'clientId', 'publicationId', 'level', 'parentId', 'storiesCount', 'categorySectionId', 'categoryName', 'categorySlug', 'categoryDescription', 'createdTime', 'createdUserId' );
		protected $_sortColumnNames = array( 'categorySlug', 'storiesCount' );
		protected $_foreignKey = 'categoryId';
		protected $_expandableTables = array();
		protected $_updateColumnNames = array( 'categoryId', 'clientId', 'publicationId', 'level', 'parentId', 'categoryName', 'categorySlug', 'categorySectionId', 'categoryDescription', 'createdTime', 'createdUserId' );

		function add() {
			$queryData = array();
			foreach( $this->_updateColumnNames as $column ) {
				if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
					$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
				} else {
					throw new Exception( gettext( "Category : " ) . gettext( $this->_arrayUpdatedData[ $column ] . " is a required field!" ) );
				}
			}
	
			try {
				$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData, false );
			} catch( Exception $ex ) {
				throw $ex;
			}
		}

		function delete() {
			$categoryId = isset( $this->_arrayUpdatedData[ 'categoryId' ] ) ? trim( $this->_arrayUpdatedData[ 'categoryId' ] ) : false;
			if( !$categoryId || !is_numeric( $categoryId ) ) {
				throw new Exception(  gettext( 'Category : ' ) . gettext( 'categoryId should be a natural number!' ));
			}

			try {
				$this->_databaseConnection->QueryDelete( $this->_mainTable, "categoryId = '$categoryId'" );
			} catch( Exception $ex ) {
				throw $ex;
			}
		}

		function edit() {
			$categoryId = isset( $this->_arrayUpdatedData[ 'categoryId' ] ) ? trim( $this->_arrayUpdatedData[ 'categoryId' ] ) : false;
			if( !$categoryId || !is_numeric( $categoryId ) ) {
				throw new Exception(  gettext( 'Category : ' ) . gettext( 'categoryId should be a natural number!' ));
			}

			$queryData = array();
			foreach( $this->_updateColumnNames as $column ) {
				if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
					$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
				}
			}
	
			try {
				$this->_databaseConnection->QueryUpdate( $this->_mainTable, $queryData, " categoryId = '$categoryId'");
			} catch( Exception $ex ) {
				throw $ex;
			}
		}
	}
