<?php

	class Cms_Db_CategorySection extends Cms_Db_Abstract {
		protected $_mainTable = 'cms_category_section';
		protected $_clauseColumnNames = array( 'categorySectionId', 'clientId', 'sectionName', 'sectionSlug', 'sectionDescription', 'publicationId', 'categoryShowCount', 'createdUserId', 'createdTime' );
		protected $_sortColumnNames = array( 'sectionSlug', 'sectionName', 'categorySectionId' );
		protected $_foreignKey = 'categorySectionId';
		protected $_expandableTables = array();
		protected $_updateColumnNames = array( 'categorySectionId', 'clientId', 'sectionName', 'sectionSlug', 'sectionDescription', 'publicationId', 'categoryShowCount', 'createdUserId', 'createdTime' );

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
			$categorySectionId = isset( $this->_arrayUpdatedData[ 'categorySectionId' ] ) ? trim( $this->_arrayUpdatedData[ 'categorySectionId' ] ) : false;
			if( !$categorySectionId || !is_numeric( $categorySectionId ) ) {
				throw new Exception(  gettext( 'CategorySection : ' ) . gettext( 'categorySectionId should be a natural number!' ));
			}

			try {
				$this->_databaseConnection->QueryDelete( $this->_mainTable, "categorySectionId = '$categorySectionId'" );
			} catch( Exception $ex ) {
				throw $ex;
			}
		}

		function edit() {
			$categorySectionId = isset( $this->_arrayUpdatedData[ 'categorySectionId' ] ) ? trim( $this->_arrayUpdatedData[ 'categorySectionId' ] ) : false;
			if( !$categorySectionId || !is_numeric( $categorySectionId ) ) {
				throw new Exception(  gettext( 'CategorySection : ' ) . gettext( 'categorySectionId should be a natural number!' ));
			}

			$queryData = array();
			foreach( $this->_updateColumnNames as $column ) {
				if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
					$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
				}
			}
	
			try {
				$this->_databaseConnection->QueryUpdate( $this->_mainTable, $queryData, " categorySectionId = '$categorySectionId'");
			} catch( Exception $ex ) {
				throw $ex;
			}
		}
	}
