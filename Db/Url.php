<?php
/**
 * InstaPress Cms_Db_Url
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_Url extends Cms_Db_Abstract
{
	protected $_mainTable = "cms_url";
	protected $_clauseColumnNames = array("urlId","clientId","urlSlug", "urlType",
											"subDomain","hostName", "createdUserId","createdTime");

	protected $_sortColumnNames = array("urlType","subDomain","hostName", "createdUserId","createdTime");
	protected $_foreignKey = "urlId";
	protected $_expandableTables = array();

	protected $_updateColumnNames = Array("clientId","urlSlug","urlType", "phpFilePath","phpFile","subDomain","hostName", "createdUserId");

	function add()
	{
		$clientId = isset($this->_arrayUpdatedData['clientId'])?trim($this->_arrayUpdatedData['clientId']):"";
		if( !$clientId || !is_numeric( $clientId ))
		{
			throw new Exception( gettext( "Url : " ) . gettext( "clientId should be a natural number!"));
		}
		$Slug = isset($this->_arrayUpdatedData['urlSlug'])?trim($this->_arrayUpdatedData['urlSlug']):"";
		if( !$Slug )
		{
			throw new Exception( gettext( "Url : " ) . gettext( "urlSlug is a required parameter and cannot be blank!"));
		}
		$urlSlug = $Slug;
		$urlType = isset($this->_arrayUpdatedData['urlType'])?trim($this->_arrayUpdatedData['urlType']):"";
		$phpFilePath = isset($this->_arrayUpdatedData['phpFilePath'])?trim($this->_arrayUpdatedData['phpFilePath']):"";
		if( !$phpFilePath )
		{
			throw new Exception( gettext( "Url : " ) . gettext( "php File Path is a required parameter and cannot be blank!"));
		}
		$phpFile = isset($this->_arrayUpdatedData['phpFile'])?trim($this->_arrayUpdatedData['phpFile']):"";
		if( !$phpFile )
		{
			throw new Exception( gettext( "Url : " ) . gettext( "php File is a required parameter and cannot be blank!"));
		}
		$subDomain = isset($this->_arrayUpdatedData['subDomain'])?trim($this->_arrayUpdatedData['subDomain']):"";
		$hostName = isset($this->_arrayUpdatedData['hostName'])?trim($this->_arrayUpdatedData['hostName']):"";
		if( !$hostName )
		{
			throw new Exception( gettext( "Url : " ) . gettext( "Host Name is a required parameter and cannot be blank!"));
		}

		$createdUserId = isset($this->_arrayUpdatedData['createdUserId'])?trim($this->_arrayUpdatedData['createdUserId']):"";
		if( !$createdUserId || !is_numeric($createdUserId ))
		{
			throw new Exception( gettext( "Url : " ) . gettext( "createdUserId should be a natural number!"));
		}

		$objTempUrl = new Cms_Db_Url();
		$objTempUrl->set("clientId||$clientId", "urlSlug||$urlSlug");
		$totalCount = $objTempUrl->getTotalCount();

		if($totalCount>0)
		throw new Exception( gettext( "Url : " ) . gettext( "This url Slug already exists!"));

		$queryData = array();
		$queryData['clientId'] = $clientId;
		$queryData['urlSlug'] = $urlSlug;
		$queryData['urlType'] = $urlType;
		$queryData['phpFilePath'] = $phpFilePath;
		$queryData['phpFile'] = $phpFile;
		$queryData['subDomain'] = $subDomain;
		$queryData['hostName'] = $hostName;
		$queryData['createdUserId'] = $createdUserId;
		$queryData['createdTime'] = 'now()';
			
		try
		{
			$this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $queryData);
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
			throw new Exception(  gettext( "Url : " ) . gettext( "clientId should be a natural number!" ));
		}

		$urlId = isset($this->_arrayUpdatedData['urlId'])?trim($this->_arrayUpdatedData['urlId']):"";
		if( !$urlId || !is_numeric( $urlId ) )
		{
			throw new Exception(  gettext( "Url : " ) . gettext( "urlId should be a natural number!" ));
		}
		try
		{
			$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and urlId = '$urlId'");
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
			throw new Exception(  gettext( "Url : " ) . gettext( "clientId should be a natural number!" ));
		}

		$urlId = isset($this->_arrayUpdatedData['urlId'])?trim($this->_arrayUpdatedData['urlId']):"";
		if( !$urlId || !is_numeric( $urlId ) )
		{
			throw new Exception(  gettext( "Url : " ) . gettext( "urlId should be a natural number!" ));
		}

		$queryData = array();
		foreach( $this->_updateColumnNames as $column ) {
			if( isset( $this->_arrayUpdatedData[ $column ] ) ) {
				$queryData[ $column ] = trim( $this->_arrayUpdatedData[ $column ] );
			}
		}

		try
		{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and urlId = '$urlId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}
}
