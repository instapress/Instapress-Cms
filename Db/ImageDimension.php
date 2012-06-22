<?php
/**
 * InstaPress Cms_Db_ImageDimension
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_ImageDimension extends Cms_Db_Abstract {

		protected $_mainTable = "cms_image_dimension";
		protected $_clauseColumnNames = array( 'imageDimensionId', 'clientId', 'imageId', 'imageUri', 'imageWidth', 'imageHeight' );
		protected $_sortColumnNames = array( "createdTime" );
		protected $_foreignKey = "imageDimensionId";
		protected $_expandableTables = array();
		protected $_updateColumnNames = array( 'imageDimensionId', 'clientId', 'imageId', 'imageUri', 'imageWidth', 'imageHeight', 'createdUserId', 'createdTime' );

		function add() {
			$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] ) ? trim( $this->_arrayUpdatedData[ 'clientId' ] ) : false;
			if( !$clientId || !is_numeric( $clientId ) ) {
				throw new Exception( gettext( "ImageDimension : " ) . gettext( "clientId should be a natural number!" ) );
			}

			$imageId = isset( $this->_arrayUpdatedData[ 'imageId' ] ) ? trim( $this->_arrayUpdatedData[ 'imageId' ] ) : false;
			if( !$imageId || !is_numeric( $imageId ) ) {
				throw new Exception( gettext( "ImageDimension : " ) . gettext( "imageId should be a natural number!" ) );
			}

			$imageUri = isset( $this->_arrayUpdatedData[ 'imageUri' ] ) ? trim( $this->_arrayUpdatedData[ 'imageUri' ] ) : false;
			if( !$imageUri ) {
				throw new Exception( gettext( "ImageDimension : " ) . gettext( "imageUri should be a natural number!" ) );
			}

			$imageHeight = isset( $this->_arrayUpdatedData[ 'imageHeight' ] ) ? trim( $this->_arrayUpdatedData[ 'imageHeight' ] ) : false;
			if( !$imageHeight || !is_numeric( $imageHeight ) ) {
				throw new Exception( gettext( "ImageDimension : " ) . gettext( "imageHeight should be a natural number!" ) );
			}

			$imageWidth = isset( $this->_arrayUpdatedData[ 'imageWidth' ] ) ? trim( $this->_arrayUpdatedData[ 'imageWidth' ] ) : false;
			if( !$imageWidth || !is_numeric( $imageWidth ) ) {
				throw new Exception( gettext( "ImageDimension : " ) . gettext( "imageWidth should be a natural number!" ) );
			}

			$selfObj = new self();
			$selfObj->set( "imageUri||$imageUri" );
			$imageCount = $selfObj->getTotalCount();
			
			if( $imageCount > 0 ) {
				$this->_lastInsertedId = $selfObj->get( 'imageDimensionId' );
			} else {
				$queryData = array();
				foreach( $this->_updateColumnNames as $column ) {
					if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
						$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
					}
				}
	
				try {
					$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData, false );
				} catch( Exception $ex ) {
					throw $ex;
				}
			}
		}

		function delete() 
		{
			$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] ) ? trim( $this->_arrayUpdatedData[ 'clientId' ] ) : false;
			if( !$clientId || !is_numeric( $clientId ) ) 
			{
				throw new Exception( gettext( "ImageDimension : " ) . gettext( "clientId should be a natural number!" ) );
			}
			${$this->_foreignKey} = isset( $this->_arrayUpdatedData[ $this->_foreignKey ] ) ? trim( $this->_arrayUpdatedData[ $this->_foreignKey ] ) : false;
			$imageUri = isset( $this->_arrayUpdatedData[ 'imageUri' ] ) ? trim( $this->_arrayUpdatedData[ 'imageUri' ] ) : false;
			if( !${$this->_foreignKey} || !is_numeric( ${$this->_foreignKey} ) )
			{
				if( !$imageUri ) {
					throw new Exception( "ImageDimension : {$this->_foreignKey} should be a natural number!" );
				}
			}
			
			try 
			{
				if( $imageUri ) {
					$this->_databaseConnection->QueryDelete( $this->_mainTable, " clientId = '$clientId' and imageUri = '$imageUri'" );
				} else {
					$this->_databaseConnection->QueryDelete( $this->_mainTable, " clientId = '$clientId' and {$this->_foreignKey} = '${$this->_foreignKey}'");
				}
			} 
			catch( Exception $ex ) 
			{
				throw $ex;
			}
		}

		function edit() {
			$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] ) ? trim( $this->_arrayUpdatedData[ 'clientId' ] ) : false;
			if( !$clientId || !is_numeric( $clientId ) ) {
				throw new Exception( gettext( "ImageDimension : " ) . gettext( "clientId should be a natural number!" ) );
			}
			${$this->_foreignKey} = isset( $this->_arrayUpdatedData[ $this->_foreignKey ] ) ? trim( $this->_arrayUpdatedData[ $this->_foreignKey ] ) : false;
			if( !${$this->_foreignKey} || !is_numeric( ${$this->_foreignKey} ) ) {
				throw new Exception( "ImageDimension : {$this->_foreignKey} should be a natural number!" );
			}

			$queryData = array();
			foreach( $this->_updateColumnNames as $column ) {
				if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
					$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
				}
			}

			if( count( $queryData ) > 0 ) {
				try {
					$this->_databaseConnection->QueryUpdate( $this->_mainTable, $queryData, " clientId = '$clientId' and {$this->_foreignKey} = '${$this->_foreignKey}'");
				} catch( Exception $ex ) {
					throw $ex;
				}
			}
		}
	}