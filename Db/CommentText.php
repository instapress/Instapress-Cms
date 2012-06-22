<?php

class Cms_Db_CommentText extends Cms_Db_Abstract {

    //Basic Table Info
    protected $_mainTable = "cms_comment_text";
    protected $_clauseColumnNames = array("commentId", "dossierId", "commentText", "clientId", "createdTime");
    protected $_sortColumnNames = array("createdTime");
    protected $_foreignKey = "";
    protected $_expandableTables = array();
    protected $_updateColumnNames = array("commentId", "dossierId", "commentText", "clientId", "createdTime");

    function add() {
        $commentId = isset($this->_arrayUpdatedData['commentId']) ? trim($this->_arrayUpdatedData['commentId']) : 0;
        if (empty($commentId) || !is_numeric($commentId))
            throw new Exception(gettext("ugc_comment_text: ") . gettext('commentId should be a natural number!'));
        $dossierId = isset($this->_arrayUpdatedData['dossierId']) ? trim($this->_arrayUpdatedData['dossierId']) : 0;
        if (empty($dossierId) || !is_numeric($dossierId))
            throw new Exception(gettext("ugc_comment_text: ") . gettext('dossierId should be a natural number!'));
        $commentText = isset($this->_arrayUpdatedData['commentText']) ? trim($this->_arrayUpdatedData['commentText']) : '';
        if (empty($commentText))
            throw new Exception(gettext("ugc_comment_text: ") . gettext("commentText couldn't blank."));
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : 0;
        if (empty($clientId) || !is_numeric($clientId))
            throw new Exception(gettext("ugc_comment_text: ") . gettext("clientId should be a natural number!"));
        $createdTime = isset($this->_arrayUpdatedData['createdTime']) ? trim($this->_arrayUpdatedData['createdTime']) : "NOW()";

        $queryData = array();
        $queryData['commentId'] = $commentId;
        $queryData['dossierId'] = $dossierId;
        $queryData['commentText'] = $commentText;
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
            throw new Exception(gettext("ugc_comment_text: ") . gettext('commentId should be a natural number!'));
        $clientId = isset($this->_arrayUpdatedData['clientId']) ? trim($this->_arrayUpdatedData['clientId']) : 0;
        if (empty($clientId) || !is_numeric($clientId))
            throw new Exception(gettext("ugc_comment_text: ") . gettext("clientId should be a natural number!"));

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
        if (!$commentId || !is_numeric($commentId)) {
            throw new Exception(gettext("ugc_comment_text: ") . gettext("Comment Id should be a natural number!"));
        }

        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " commentId = '$commentId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}

?>