<?php

class Cms_Db_Comment extends Cms_Db_Abstract {

    //Basic Table Info
    protected $_mainTable = "cms_comment";
    protected $_clauseColumnNames = array("commentId", "dossierId", "userId", "userIPAddress", "parentCommentId", "parentCommentId2", "commentStatus", "clientId", "createdTime");
    protected $_sortColumnNames = array("createdTime");
    protected $_foreignKey = "commentId";
    protected $_expandableTables = array();
    protected $_updateColumnNames = array("commentId", "dossierId", "userId", "userIPAddress", "parentCommentId", "parentCommentId2", "commentStatus", "clientId", "createdTime");

    function add() {
       $dossierId = isset($this->_arrayUpdatedData['dossierId']) ? trim($this->_arrayUpdatedData['dossierId']) : 0;
        if (empty($dossierId) || !is_numeric($dossierId))
            throw new Exception(gettext("ugc_comment: ") . gettext("dossierId should be a natural number!"));
        $userId = isset($this->_arrayUpdatedData['userId']) ? trim($this->_arrayUpdatedData['userId']) : 0;
        if (empty($userId) || !is_numeric($userId))
            throw new Exception(gettext("ugc_comment: ") . gettext("userId should be a natural number!"));

        $userIPAddress = isset($this->_arrayUpdatedData['userIPAddress']) ?ip2long(trim($this->_arrayUpdatedData['userIPAddress'])): 0;
        if (empty($userIPAddress) || !is_numeric($userIPAddress))
            throw new Exception(gettext("ugc_comment: ") . gettext(" userIPAddress should be a natural number!"));
        $parentCommentId = isset($this->_arrayUpdatedData['parentCommentId']) ? trim($this->_arrayUpdatedData['parentCommentId']) : 0;
        $parentCommentId2 = isset($this->_arrayUpdatedData['parentCommentId2']) ? trim($this->_arrayUpdatedData['parentCommentId2']) : 0;
        $commentStatus = isset($this->_arrayUpdatedData['commentStatus']) ? trim($this->_arrayUpdatedData['commentStatus']) : '';
        if (empty($commentStatus))
            throw new Exception(gettext("ugc_comment: ") . gettext("commentStatus couldn't blank."));
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : 0;
        if (empty($clientId) || !is_numeric($clientId))
            throw new Exception(gettext("ugc_comment: ") . gettext("clientId should be a natural number!"));
        $createdTime = isset($this->_arrayUpdatedData['createdTime']) ? trim($this->_arrayUpdatedData['createdTime']) : "NOW()";

        $queryData = array();
        $queryData['dossierId'] = $dossierId;
        $queryData['userId'] = $userId;
        $queryData['userIPAddress'] = $userIPAddress;
        $queryData['parentCommentId'] = $parentCommentId;
        $queryData['parentCommentId2'] = $parentCommentId2;
        $queryData['commentStatus'] = $commentStatus;
        $queryData['clientId'] = $clientId;
        $queryData['createdTime'] = $createdTime;

        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $queryData);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function edit() {
        //required and numeric.
        $commentId = isset($this->_arrayUpdatedData['commentId']) ? trim($this->_arrayUpdatedData['commentId']) : 0;
        if (empty($commentId) || !is_numeric($commentId))
            throw new Exception(gettext("ugc_comment: ") . gettext("commentId should be a natural number!"));
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : 0;
        if (empty($clientId) || !is_numeric($clientId))
            throw new Exception(gettext("ugc_comment: ") . gettext("clientId should be a natural number!"));

        $queryData = array();
        foreach ($this->_updateColumnNames as $column) {
            if (isset($this->_arrayUpdatedData[$column])) {
                $queryData[$column] = trim($this->_arrayUpdatedData[$column]);
            }
        }

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " commentId= '$commentId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function delete() {
        //required and numeric.
        $commentId = isset($this->_arrayUpdatedData['commentId']) ? trim($this->_arrayUpdatedData['commentId']) : 0;
        if (empty($commentId) || !is_numeric($commentId))
            throw new Exception(gettext("ugc_comment: ") . gettext("commentId should be a natural number!"));
        
        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " commentId = '$commentId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>