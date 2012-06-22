<?php
/**
 * InstaPress Cms_Db_Image
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */

class Cms_Db_Image extends Cms_Db_Abstract {
	protected $_mainTable = "cms_image";
	protected $_clauseColumnNames = array("clientId", "imageUri", "imageId", "imageTitle", "imageWidth", "imageHeight",
										"imageStatus", "usedCount", "createdUserId", "createdTime");

	protected $_sortColumnNames = array();
	protected $_foreignKey = "imageId";
	protected $_expandableTables = array("cms_image_expandable","cms_image_expandable_text");

	protected $_updateColumnNames = array( 'imageId', 'clientId', 'imageUri', 'imageTitle', 'imageDescription', 'imageKeywords', 'imageWidth', 'imageHeight', 'imageCredit', 'imageSourceDomain', 'imageCopyright', 'imageStatus', 'createdUserId', 'createdTime' );

	function add() {

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			} else {
				throw new Exception( gettext( "Image : " ) . gettext( $column . " is a required field!" ) );
			}
		}

		try {
			$this->_lastInsertedId = $this->_databaseConnection->QueryInsert( $this->_mainTable, $queryData, false );
		} catch( Exception $ex ) {
			throw $ex;
		}
	}

	function edit()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception(gettext("Image : ").gettext("clientId should be a natural number!"));
		}

		$imageId = isset($this->_arrayUpdatedData['imageId'])?trim($this->_arrayUpdatedData['imageId']):"";
		if( !$imageId || !is_numeric( $imageId ) )
		{
			throw new Exception(gettext("Image : ").gettext("imageId should be a natural number!"));
		}

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		try
		{
			//$this->_lastInsertedId =
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and imageId = '$imageId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}

	function delete()
	{
		echo gettext("delete code");
	}
}
