<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GroupArticleRel
 *
 * @author root
 */
class Cms_Db_GroupArticleRel  extends Cms_Db_Abstract {

    //Basic Table Info
    protected $_mainTable = "cms_group_article_rel";
    protected $_clauseColumnNames = array("groupArticleRelId", "groupId", "storyId", "createdBy", "createdTime");
    protected $_sortColumnNames = array("entityId");
    protected $_foreignKey = "";
    protected $_expandableTables = array();
    protected $_updateColumnNames = array("groupId", "storyId", "createdBy", "groupArticleRelId");

    /*
     * Insert record
     */

    function add() {



        $groupId = isset($this->_arrayUpdatedData['groupId']) ? trim($this->_arrayUpdatedData['groupId']) : "";
        if (!$groupId) {
            throw new Exception(gettext("Cms_group_article_rel:") . gettext(" groupId cannot be blank!"));
        }
        $storyId = isset($this->_arrayUpdatedData['storyId']) ? trim($this->_arrayUpdatedData['storyId']) : "";

        if (!$storyId) {
            throw new Exception(gettext("Cms_group_article_rel: ") . gettext(" storyId field cannot be blank!"));
        }
       

        $createdBy = isset($this->_arrayUpdatedData['createdBy']) ? trim($this->_arrayUpdatedData['createdBy']) : "1";
        if (!$createdBy) {
            throw new Exception(gettext("Cms_group_article_rel ") . gettext(" createdBy field cannot be blank!"));
        }
        $queryData = array();


        $queryData['groupId'] = $groupId;
        $queryData['storyId'] = $storyId;
        $queryData['createdBy'] = $createdBy;
        $queryData['createdTime'] = 'now()';



        try {
            $this->_lastInsertedId = $this->_databaseConnection->QueryInsert($this->_mainTable, $queryData);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /////////////////////////////

    /**
     * Delete record
     */
    function delete() {
        $groupArticleRelId = isset($this->_arrayUpdatedData['groupArticleRelId']) ? trim($this->_arrayUpdatedData['groupArticleRelId']) : "";
        if (!$groupArticleRelId) {
            throw new Exception(gettext("Cms_group_article_rel : ") . gettext("groupArticleRelId should be provided & should be a natural no!"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " groupArticleRelId = '$groupArticleRelId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    ////////////////////////////////////////////
    /*
     * Edit record
     */
    function edit() {

        $groupArticleRelId = isset($this->_arrayUpdatedData['groupArticleRelId']) ? trim($this->_arrayUpdatedData['groupArticleRelId']) : "";
        if (!$groupArticleRelId) {
            throw new Exception(gettext("Cms_group_article_rel : ") . gettext("groupArticleRelId should be provided !"));
        }

        $queryData = array();




      foreach ($this->_updateColumnNames as $updateColumn) {
			if (isset($this->_arrayUpdatedData[$updateColumn])) {
				$queryData[$updateColumn] = trim($this->_arrayUpdatedData[$updateColumn]);
			}
		}

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " groupArticleRelId = '$groupArticleRelId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}
?>
