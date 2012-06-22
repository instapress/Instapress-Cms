<?php

class Cms_CmsDbUtility {

    private $_publicationDatabaseName = '';
    public function __construct($publicationid) {
        require ('CmsDbConstant.php');
        //$str = "PUB" . $publicationid;
        $publicationVariableValue = "PUB".$publicationid;
         $this->_publicationDatabaseName = $$publicationVariableValue;
       // global $dbvariable;
       
       // $dbvariable= $$publicationVariableValue;
        defined('CMS_MASTER_SERVER')
                || define('CMS_MASTER_SERVER', '192.168.100.85');
        defined('CMS_MASTER_USER')
                || define('CMS_MASTER_USER', 'extblog');
        defined('CMS_MASTER_PSWD')
                || define('CMS_MASTER_PSWD', 'nan2010');
        defined('CMS_MASTER_DB') ||
                define('CMS_MASTER_DB', $this->_publicationDatabaseName); //contains reference of publication db Name.
        defined('CMS_MASTER_PREFIX')
                || define('CMS_MASTER_PREFIX', '');

        defined('CMS_SLAVE_SERVER')
                || define('CMS_SLAVE_SERVER', '192.168.100.85');
        defined('CMS_SLAVE_USER')
                || define('CMS_SLAVE_USER', 'extblog');
        defined('CMS_SLAVE_PSWD')
                || define('CMS_SLAVE_PSWD', 'nan2010');
        defined('CMS_SLAVE_DB') ||
                define('CMS_SLAVE_DB', $this->_publicationDatabaseName);
        defined('CMS_SLAVE_PREFIX')
                || define('CMS_SLAVE_PREFIX', '');
    }

    public function  __destruct() {
        $this->_publicationDatabaseName;
    }

}