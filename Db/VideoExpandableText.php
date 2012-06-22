<?php
/**
 * InstaPress Cms_Db_VideoExpandableText
 * LICENSE
 * @author		Pramod Thakur
 * @package		InstaPress
 * @copyright	Copyright (c) Citizen Media Pvt. Ltd. (http://www.instablogs.com/)
 */
class Cms_Db_VideoExpandableText extends Cms_Db_Abstract
{
	protected $_mainTable = "cms_video_expandable_text";
	protected $_clauseColumnNames = array("videoId","xGroupId","clientId","xElementId","createdUserId","createdTime");
	protected $_sortColumnNames = array("videoId", "clientId","xGroupId","xElementId","createdUserId");
	protected $_foreignKey = array( 'videoId', 'xElementId', 'xGroupId' );
	protected $_expandableTables = array();

	protected $_updateColumnNames = array("videoId", "clientId", "createdUserId", "assetTypeId",
											"xElementId", "xElementValue", "xGroupId","createdTime");

	function add() {
		$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] ) ? trim( $this->_arrayUpdatedData[ 'clientId' ] ) : false;
		if( !$clientId || !is_numeric( $clientId ) ) {
			throw new Exception( gettext( "VideoExpandableText : " ) . gettext( "clientId should be a natural number!" ) );
		}

		$videoId = isset( $this->_arrayUpdatedData[ 'videoId' ] ) ? trim( $this->_arrayUpdatedData[ 'videoId' ] ) : false;
		if( !$videoId || !is_numeric( $videoId ) ) {
			throw new Exception( gettext( "VideoExpandableText : " ) . gettext( "videoId should be a natural number!" ) );
		}

		$xGroupId = '1';

		$xElementId = isset($this->_arrayUpdatedData['xElementId'])?trim($this->_arrayUpdatedData['xElementId']):"";
		if( !$xElementId )
		{
			throw new Exception(gettext("VideoExpandableText : ").gettext("Expandable Name is a required parameter and cannot be blank!"));
		}

		$assetTypeId = 2;
			
		$arrayAssetPermissibleValues = $this->getAssetPermissibleValuesByTypeId( $clientId, $assetTypeId );
		$tempAssetPermissibleValues = implode(", ", $arrayAssetPermissibleValues);

		if( !is_numeric( $xElementId ) ) {
			$xElementId = array_search( $xElementId, $arrayAssetPermissibleValues );
		}

		if( array_key_exists( $xElementId, $arrayAssetPermissibleValues ) === FALSE ) {
			throw new Exception(gettext("VideoExpandableText : ").gettext("Expandable Name is wrong. Values can be : ").$tempAssetPermissibleValues);
		}
			
		$xElementValue = isset($this->_arrayUpdatedData['xElementValue'])?trim($this->_arrayUpdatedData['xElementValue']):"";
		if( !$xElementValue ) {
			throw new Exception(gettext("VideoExpandableText : ").gettext("Expandable Value is a required parameter and cannot be blank!"));
			throw new Exception("Cms_Db_VideoExpandableText : Expandable Value is a required parameter and cannot be blank!");
		}

		$createdUserId = isset($this->_arrayUpdatedData['createdUserId'])?trim($this->_arrayUpdatedData['createdUserId']):"";
		if( !$createdUserId || !is_numeric($createdUserId ) )
		{
			throw new Exception(gettext("VideoExpandableText : ").gettext("createdUserId should be a natural number!"));
		}

		$objTempVideo = new Cms_Db_VideoExpandableText();
		$objTempVideo->set("clientId||$clientId", "xElementId||$xElementId", "xGroupId||$xGroupId", "videoId||$videoId");
		$totalCount = $objTempVideo->getTotalCount();

		if( $totalCount > 0 ) {
			throw new Exception(gettext("VideoExpandableText : ").gettext("This Expandable video data already exists!"));
		}

		$queryData = array();
		$queryData['videoId'] = $videoId;
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
			$this->_lastInsertedId = $videoId;
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}


	function delete()
	{
		$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] ) ? trim( $this->_arrayUpdatedData[ 'clientId' ] ) : "";
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception(gettext("VideoExpandableText : ").gettext("clientId should be a natural number!"));
		}
		$videoId = isset( $this->_arrayUpdatedData[ 'videoId' ] ) ? trim( $this->_arrayUpdatedData[ 'videoId' ] ) : "";
		if( !$videoId || !is_numeric( $videoId ) )
		{
			throw new Exception(gettext("VideoExpandableText : ").gettext("videoId should be a natural number!"));
		}
		try
		{
			$this->_databaseConnection->QueryDelete($this->_mainTable, " clientId = '$clientId' and videoId = '$videoId'");
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}


	function edit()
	{
		$clientId = isset( $this->_arrayUpdatedData[ 'clientId' ] ) ? trim( $this->_arrayUpdatedData[ 'clientId' ] ) : "";
		if( !$clientId || !is_numeric( $clientId ) )
		{
			throw new Exception(gettext("VideoExpandableText : ").gettext("clientId should be a natural number!"));
		}

		$videoId = isset( $this->_arrayUpdatedData[ 'videoId' ] ) ? trim( $this->_arrayUpdatedData[ 'videoId' ] ) : "";
		if( !$videoId || !is_numeric( $videoId ) )
		{
			throw new Exception(gettext("VideoExpandableText : ").gettext("videoId should be a natural number!"));
		}

		$xElementId = isset($this->_arrayUpdatedData['xElementId'])?trim($this->_arrayUpdatedData['xElementId']):"";
		if( !$xElementId )
		{
			throw new Exception(gettext("VideoExpandableText : ").gettext("Expandable Name is a required parameter and cannot be blank!"));
		}

		$xElementName = isset($this->_arrayUpdatedData['xElementName'])?trim($this->_arrayUpdatedData['xElementName']):"";

		$objAssetType = new Cms_Db_AssetType();
		$cId = $this->_arrayUpdatedData['clientId'];
		$objAssetType->set( "clientId||$cId", "assetTypeSlug||video");
		$assetTypeId = $objAssetType->get( 'assetTypeId', 0 );

		$arrayVideoPermissibleValues = $this->getAssetPermissibleValuesByTypeId( $clientId, $assetTypeId );
		$tempVideoPermissibleValues = implode(", ", $arrayVideoPermissibleValues);
			
		if( !$xElementId ) { // isset( $arrayVideoPermissibleValues[ $xElementId ] ) ) {
			if( array_search( $xElementName, $arrayVideoPermissibleValues ) === FALSE ) {
				throw new Exception( gettext( "Expandable Name was incorrect. Values can be : ").$tempVideoPermissibleValues);
			}
			$xElementId = array_search( $xElementName, $arrayVideoPermissibleValues );
		}

		$queryData = array();

		if( isset( $this->_arrayUpdatedData['xElementValue'] ) )
		{
			$queryData['xElementValue'] = trim($this->_arrayUpdatedData['xElementValue']);
		}

		$createdUserId = isset($this->_arrayUpdatedData['createdUserId'])?trim($this->_arrayUpdatedData['createdUserId']):"";

		if( !$createdUserId || !is_numeric($createdUserId ))
		{
			throw new Exception(gettext("VideoExpandableText : ").gettext("createdUserId should be a natural number!"));
		}
		try
		{
			$this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " clientId = '$clientId' and videoId = '$videoId' and " . $xElementId);
		}
		catch( Exception $ex )
		{
			throw $ex;
		}
	}

}