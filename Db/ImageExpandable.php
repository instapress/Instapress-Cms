<?php
/**
 * InstaPress Cms_Db_ImageExpandable
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_ImageExpandable extends Cms_Db_Abstract
{
	protected $_mainTable = "cms_image_expandable";
	protected $_clauseColumnNames = array("imageId","xGroupId","clientId",
											"xElementId","createdUserId","createdTime");

	protected $_sortColumnNames = array();
	protected $_foreignKey = array( 'imageId', 'xElementId', 'xGroupId' );
	protected $_expandableTables = array();
	protected $_updateColumnNames = array( "clientId", "createdUserId", "xElementId", "xElementValue", "xGroupId","createdTime" );

	function add()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ))
		{
			throw new Exception(gettext("ImageExpandable : ").gettext("clientId should be a natural number!"));
		}
			
		$imageId = isset($this->_arrayUpdatedData['imageId'])?trim($this->_arrayUpdatedData['imageId']):"";
		if( !$imageId || !is_numeric( $imageId ))
		{
			throw new Exception(gettext("ImageExpandable : ").gettext("imageId should be a natural number!"));
		}
			
		$xGroupId = '1';
			
		$xElementId = isset($this->_arrayUpdatedData['xElementId'])?trim($this->_arrayUpdatedData['xElementId']):"";
		if( !$xElementId )
		{
			throw new Exception(gettext("ImageExpandable : ").gettext("Expandable Name is a required parameter and cannot be blank!"));
		}
			
		$xElementValue = isset($this->_arrayUpdatedData['xElementValue'])?trim($this->_arrayUpdatedData['xElementValue']):"";
		if( !$xElementValue ) {
			throw new Exception(gettext("ImageExpandable : ").gettext("Expandable Value is a required parameter and cannot be blank!"));
		}

		$createdUserId = isset($this->_arrayUpdatedData['createdUserId'])?trim($this->_arrayUpdatedData['createdUserId']):"";
		if( !$createdUserId || !is_numeric($createdUserId ) )
		{
			throw new Exception(gettext("ImageExpandable : ").gettext("createdUserId should be a natural number!"));
		}

		$objTempImage = new Cms_Db_ImageExpandable();
		$objTempImage->set("clientId||$clientId", "xElementId||$xElementId", "xGroupId||$xGroupId", "imageId||$imageId");
		$totalCount = $objTempImage->getTotalCount();

		if( $totalCount > 0 ) {
			throw new Exception(gettext("ImageExpandable : ").gettext("This Expandable image data already exists!"));
		}

		$queryData = array();
		$queryData['imageId'] = $imageId;
		$queryData['xElementId'] = $xElementId;
		$queryData['xElementValue'] = $xElementValue;
		$queryData['xGroupId'] = $xGroupId;
		$queryData['clientId'] = $clientId;
		$queryData['createdUserId'] = $createdUserId;
		$createdTime = isset($this->_arrayUpdatedData['createdTime'])?trim($this->_arrayUpdatedData['createdTime']):"now()";
		$queryData['createdTime'] = $createdTime;

		try
		{
			$this->_databaseConnection->QueryInsert($this->_mainTable, $queryData, false);
			$this->_lastInsertedId = $imageId;
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}


	function delete()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception(gettext("ImageExpandable : ").gettext("clientId should be a natural number!"));
		}
		$imageId = isset($this->_arrayUpdatedData['imageId'])?trim($this->_arrayUpdatedData['imageId']):"";
		if( !$imageId || !is_numeric( $imageId ) )
		{
			throw new Exception(gettext("ImageExpandable : ").gettext("imageId should be a natural number!"));
		}
		try
		{
			$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and imageId = '$imageId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}

	function edit()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception(gettext("ImageExpandable : ").gettext("clientId should be a natural number!"));
		}

		$imageId = isset($this->_arrayUpdatedData['imageId'])?trim($this->_arrayUpdatedData['imageId']):"";
		if( !$imageId || !is_numeric( $imageId ) )
		{
			throw new Exception(gettext("ImageExpandable : ").gettext("imageId should be a natural number!"));
		}

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		try
		{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and imageId = '$imageId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}
}