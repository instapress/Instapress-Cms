<?php

/**
 * Description of App_Db_ElementsNv
 * *
 */
class Cms_Db_StoryEntityRel extends Cms_Db_Abstract {

    //Basic Table Info
    protected $_mainTable = "cms_story_entity_rel";
    protected $_clauseColumnNames = array("storyEntityRelId", "entityId", "storyId","metaDataString1","metaDataString2","metaDataString3", "createdBy", "createdTime");
    protected $_sortColumnNames = array("entityId","storyId");
    protected $_foreignKey = "";
    protected $_expandableTables = array();
    protected $_updateColumnNames = array("entityId", "storyId", "createdBy", "storyEntityRelId","metaDataString1","metaDataString2","metaDataString3");

    /*
     * Insert record
     */

    function add() {



        $entityId = isset($this->_arrayUpdatedData['entityId']) ? trim($this->_arrayUpdatedData['entityId']) : "0";
//        if (!$entityId) {
//            throw new Exception(gettext("Cms_story_entity_rel:") . gettext(" entityId cannot be blank!"));
//        }
        $storyId = isset($this->_arrayUpdatedData['storyId']) ? trim($this->_arrayUpdatedData['storyId']) : "";

        if (!$storyId) {
            throw new Exception(gettext("Cms_story_entity_rel: ") . gettext(" storyId field cannot be blank!"));
        }

        $metaDataString1 = isset($this->_arrayUpdatedData['metaDataString1']) ? trim($this->_arrayUpdatedData['metaDataString1']) : "";
        $metaDataString2 = isset($this->_arrayUpdatedData['metaDataString2']) ? trim($this->_arrayUpdatedData['metaDataString2']) : "";
        $metaDataString3 = isset($this->_arrayUpdatedData['metaDataString3']) ? trim($this->_arrayUpdatedData['metaDataString3']) : "";

        $createdBy = isset($this->_arrayUpdatedData['createdBy']) ? trim($this->_arrayUpdatedData['createdBy']) : "";
        if (!$createdBy) {
            throw new Exception(gettext("Cms_story_entity_rel: ") . gettext(" createdBy field cannot be blank!"));
        }
        $queryData = array();


        $queryData['entityId'] = $entityId;
        $queryData['storyId'] = $storyId;
          $queryData['metaDataString1'] = $metaDataString1;
            $queryData['metaDataString2'] = $metaDataString2;
              $queryData['metaDataString3'] = $metaDataString3;
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
        $storyEntityRelId = isset($this->_arrayUpdatedData['storyEntityRelId']) ? trim($this->_arrayUpdatedData['storyEntityRelId']) : "";
        if (!$storyEntityRelId) {
            throw new Exception(gettext("Cms_story_entity_rel : ") . gettext("storyEntityRelId should be provided & should be a natural no!"));
        }


        try {
            $this->_databaseConnection->QueryDelete($this->_mainTable, " storyEntityRelId = '$storyEntityRelId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    ////////////////////////////////////////////
    /*
     * Edit record
     */
    function edit() {

        $storyEntityRelId = isset($this->_arrayUpdatedData['storyEntityRelId']) ? trim($this->_arrayUpdatedData['storyEntityRelId']) : "";
        if (!$storyEntityRelId) {
            throw new Exception(gettext("Cms_story_entity_rel : ") . gettext(" should be provided !"));
        }

        $queryData = array();




      foreach ($this->_updateColumnNames as $updateColumn) {
			if (isset($this->_arrayUpdatedData[$updateColumn])) {
				$queryData[$updateColumn] = trim($this->_arrayUpdatedData[$updateColumn]);
			}
		}

        try {
            $this->_databaseConnection->QueryUpdate($this->_mainTable, $queryData, " storyEntityRelId = '$storyEntityRelId'");
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}
?>
