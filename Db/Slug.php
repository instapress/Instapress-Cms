<?php

	class Cms_Db_Slug extends Cms_Db_Abstract {
		protected $_mainTable = 'cms_slug';
		protected $_clauseColumnNames = array( 'slugId', 'clientId', 'publicationId', 'slug', 'component', 'object', 'createdTime' );
		protected $_sortColumnNames = array( 'slug' );
		protected $_foreignKey = 'slugId';
		protected $_expandableTables = array();
		protected $_updateColumnNames = array( 'slugId', 'clientId', 'publicationId', 'slug', 'component', 'object', 'createdTime' );

		function add() {
			$slugId = isset( $this->_arrayUpdatedData[ 'slugId' ] ) ? trim( $this->_arrayUpdatedData[ 'slugId' ] ) : false;
			if( !$slugId || !is_numeric( $slugId ) ) {
				throw new Exception( gettext( 'Slug : ' ) . gettext( 'slugId should be a natural number!' ) );
			}
			$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] ) ? trim( $this->_arrayUpdatedData[ 'clientId' ] ) : false;
			if( !$clientId || !is_numeric( $clientId ) ) {
				throw new Exception( gettext( 'Slug : ' ) . gettext( 'clientId should be a natural number!' ) );
			}
			$slug = isset( $this->_arrayUpdatedData[ 'slug' ] ) ? trim( $this->_arrayUpdatedData[ 'slug' ] ) : false;
			if( !$slug ) {
				throw new Exception( gettext( 'Slug : ' ) . gettext( 'slug should be a non-empty string!' ) );
			}
			$publicationId = isset( $this->_arrayUpdatedData[ 'publicationId' ] ) ? trim( $this->_arrayUpdatedData[ 'publicationId' ] ) : 0;

			$selfObj = new self();
			$selfObj->set( "publicationId||$publicationId", "slug||$slug" );
			if( $selfObj->getTotalCount() > 0 ) {
				$this->_lastInsertedId = $selfObj->getSlugId();
				return;
			}

			$component = isset( $this->_arrayUpdatedData[ 'component' ] ) ? trim( $this->_arrayUpdatedData[ 'component' ] ) : false;
			if( !$component || ( array_search( $component, array( 'cms', 'qa', 'sw' ) ) === false ) ) {
				throw new Exception( gettext( 'Slug : ' ) . gettext( 'component should be a non-empty string!' ) );
			}
			$object = isset( $this->_arrayUpdatedData[ 'object' ] ) ? trim( $this->_arrayUpdatedData[ 'object' ] ) : false;
			if( !$object || ( array_search( $object, array( 'story', 'category', 'asset', 'tag', 'topic', 'entity', 'element', 'userlist', 'category_section' ) ) === false ) ) {
				throw new Exception( gettext( 'Slug : ' ) . gettext( 'object should be a non-empty string!' ) );
			}

			$queryData = array();
			$queryData[ 'slugId' ] = $slugId;
			$queryData[ 'clientId' ] = $clientId;
			$queryData[ 'publicationId' ] = $publicationId;
			$queryData[ 'slug' ] = $slug;
			$queryData[ 'component' ] = $component;
			$queryData[ 'object' ] = $object;
			$queryData[ 'createdTime' ] = 'now()';

			try {
				$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData );
			} catch( Exception $ex ) {
				throw $ex;
			}
		}

		function delete() {
			$slugId = isset( $this->_arrayUpdatedData[ 'slugId' ] ) ? trim( $this->_arrayUpdatedData[ 'slugId' ] ) : false;
			if( !$slugId || !is_numeric( $slugId ) ) {
				throw new Exception(  gettext( 'Slug : ' ) . gettext( 'slugId should be a natural number!' ));
			}

			try {
				$this->_databaseConnection->QueryDelete( $this->_mainTable, "slugId = '$slugId'" );
			} catch( Exception $ex ) {
				throw $ex;
			}
		}

		function edit() {
			$slugId = isset( $this->_arrayUpdatedData[ 'slugId' ] ) ? trim( $this->_arrayUpdatedData[ 'slugId' ] ) : false;
			if( !$slugId || !is_numeric( $slugId ) ) {
				throw new Exception(  gettext( 'Slug : ' ) . gettext( 'slugId should be a natural number!' ));
			}

			$queryData = array();
			foreach( $this->_updateColumnNames as $column ) {
				if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
					$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
				}
			}
	
			try {
				$this->_databaseConnection->QueryUpdate( $this->_mainTable, $queryData, " slugId = '$slugId'");
			} catch( Exception $ex ) {
				throw $ex;
			}
		}
	}
