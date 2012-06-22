<?php

abstract class Cms_Db_Abstract {

    private $_reservedParameters = array("order", "count", "sortOrder", "sortColumn", "pageNumber", "quantity");
    //reserve request arguments
    private $_sortEnabled = false;
    private $_sortOrder = "DESC";
    private $_sortColumn = "createdTime";
    private $_pageNumber = 1;
    protected $_quantity = 10;
    private $_count = 'N';
    private $_order = 'Y';
    protected $_enableForcedCondition = false;
    protected $_betweenClause = '';
    //basic db and table info
    protected $_databaseConnection = null;
    protected $_mainTable = null;
    protected $_clauseColumnNames = null;
    protected $_sortColumnNames = null;
    protected $_foreignKey = null;
    protected $_expandableTables = null;
    //permissible values variables
    private $_mainTablePermissibleValues = '';
    private $_expandableTablesPermissibleValues = '';
    private $_arrayExpandablePermissibleValues = array();
    //array of all column conditions, where clauses
    private $_arrayClauses = array();
    protected $_enableForcedLimit = false;
    protected $_limitClause1 = '';
    protected $_limitClause2 = '';
    //add or update variables
    protected $_lastInsertedId = 0;
    protected $_arrayUpdatedData = array();
    private $_task = 'show';
    //resultset information variables
    private $_dataResultArray = array();
    private $_dataResultCount = 0;
    private $_dataTotalCount = 0;
    private $_dataTotalPages = 0;

    public function setForcedConditon($betweenClause) {
        $this->_betweenClause = $betweenClause;
        if (!empty($betweenClause)) {
            $this->_enableForcedCondition = true;
        }
    }

    public function setForcedLimit($limitClause1, $limitClause2) {
        $this->_limitClause1 = $limitClause1;
        $this->_limitClause2 = $limitClause2;
        if (is_int($limitClause1) and is_int($limitClause2)) {
            $this->_enableForcedLimit = true;
        }
    }

    public function __construct($task = 'show') {
        $this->_task = strtolower($task);
        $this->_databaseConnection = Cms_Connection::GetInstance();
    }

    public function set() {
        try {
            $argumentsList = func_get_args();

            global $clientId;

            $stringArgumentList = implode("~", $argumentsList);
            if (stristr($stringArgumentList, 'clientId||') === FALSE) {
                if (array_search('clientId', $this->_updateColumnNames) !== FALSE) {
                    $argumentsList[] = "clientId||$clientId";
                }
            }

            // $argumentsList[] = "clientId||1"; //remove on main usage just for moving data

            $totalArguments = count( $argumentsList );

            switch ($this->_task) {
                case "add":
                    if (Instapress_Core_Login::$loginStatus == Instapress_Core_Login::USER_LOGGED_IN) {
                        $createdUserId = Instapress_Core_Login::getUserInfo('userId');
                        if (array_search('createdUserId', $this->_updateColumnNames) !== FALSE and $this->_task != 'show') {
                            $argumentsList[] = "createdUserId||$createdUserId";
                        }
                    }

                    $totalArguments = count($argumentsList);

                    for ($i = 0; $i < $totalArguments; $i++) {
                        //process single argument and break into array
                        $tempArgumentArray = explode("||", $argumentsList[$i]);
                        $argumentsArrayCount = count($tempArgumentArray);
                        //if someone is not following the keyname||keyvalue rule, throw error
                        if ($argumentsArrayCount < 2)
                        //throw new Exception("Argument '$argumentsList[$i]' is invalid");
                            throw new Exception(gettext("Abstract : ") . gettext("Argument ") . $argumentsList[$i] . gettext(" is invalid"));
                        else if ($argumentsArrayCount > 2) {
                            for ($ii = 2; $ii < $argumentsArrayCount; $ii++) {
                                $tempArgumentArray[1] .= '||' . $tempArgumentArray[$ii];
                            }
                        }
                        //check for keyname present in allowed columns list
                        if (array_search($tempArgumentArray[0], $this->_updateColumnNames) === FALSE) {
                            //throw new Exception("Argument '$tempArgumentArray[0]' is invalid");
                            throw new Exception(gettext("Abstract : ") . gettext("Argument ") . $tempArgumentArray[0] . gettext(" is invalid"));
                        }

                        if ($tempArgumentArray[0] != 'xElementId' && ( substr($tempArgumentArray[0], strlen($tempArgumentArray[0]) - 2) == 'Id' and !is_numeric($tempArgumentArray[1]) )) {
                            //throw new Exception("$tempArgumentArray[0] should be a natural number.");
                            throw new Exception(gettext("Abstract : ") . $tempArgumentArray[0] . gettext("should be a natural number."));
                        }

                        $this->_arrayUpdatedData[$tempArgumentArray[0]] = $tempArgumentArray[1];
                    }

                    $this->add();
                    break;

                case "edit":
                    for ($i = 0; $i < $totalArguments; $i++) {
                        //process single argument and break into array
                        $tempArgumentArray = explode("||", $argumentsList[$i]);

                        //if someone is not following the keyname||keyvalue rule, throw error
                        if (count($tempArgumentArray) != 2)
                        //throw new Exception("Argument '$argumentsList[$i]' is invalid");
                            throw new Exception(gettext("Abstract : ") . gettext("Argument") . $argumentsList[$i] . gettext("is invalid"));

                        //check for keyname present in allowed columns list
                        if (array_search($tempArgumentArray[0], $this->_updateColumnNames) === FALSE) {
                            if ($tempArgumentArray[0] != $this->_foreignKey)
                            //throw new Exception("Argument '$tempArgumentArray[0]' is invalid");
                                throw new Exception(gettext("Abstract : ") . gettext("Argument") . $tempArgumentArray[0] . gettext("is invalid"));
                        }

                        if ($tempArgumentArray[0] != 'xElementId' && ( substr($tempArgumentArray[0], strlen($tempArgumentArray[0]) - 2) == 'Id' and !is_numeric($tempArgumentArray[1]) )) {
                            //throw new Exception("$tempArgumentArray[0] should be a natural number.");
                            throw new Exception(gettext("Abstract : ") . $tempArgumentArray[0] . gettext(" should be a natural number."));
                        }

                        $this->_arrayUpdatedData[$tempArgumentArray[0]] = $tempArgumentArray[1];
                    }

                    $this->edit();
                    break;

                case "delete":
                    for ($i = 0; $i < $totalArguments; $i++) {
                        //process single argument and break into array
                        $tempArgumentArray = explode("||", $argumentsList[$i]);

                        //if someone is not following the keyname||keyvalue rule, throw error
                        if (count($tempArgumentArray) != 2)
                        //throw new Exception("Argument '$argumentsList[$i]' is invalid");
                            throw new Exception(gettext("Abstract : ") . gettext("Argument") . $argumentsList[$i] . gettext("is invalid"));

                        //check for keyname present in allowed columns list
                        if (array_search($tempArgumentArray[0], $this->_updateColumnNames) === FALSE) {
                            if ($tempArgumentArray[0] != $this->_foreignKey)
                            //throw new Exception("Argument '$tempArgumentArray[0]' is invalid");
                                throw new Exception(gettext("Abstract : ") . gettext("Argument") . $tempArgumentArray[0] . gettext("is invalid"));
                        }

                        if (substr($tempArgumentArray[0], strlen($tempArgumentArray[0]) - 2) == 'Id' and !is_numeric($tempArgumentArray[1])) {
                            //throw new Exception("$tempArgumentArray[0] should be a natural number.");
                            throw new Exception(gettext("Abstract : ") . $tempArgumentArray[0] . gettext("should be a natural number."));
                        }

                        $this->_arrayUpdatedData[$tempArgumentArray[0]] = Helper :: escape($tempArgumentArray[1]);
                    }
                    $this->delete();
                    break;

                default:
                    //process each argument, set reserve parameters and clauses
                    for ($i = 0; $i < $totalArguments; $i++) {
                        //process single argument and break into array
                        $tempArgumentArray = explode("||", $argumentsList[$i]);

                        //if someone is not following the keyname||keyvalue rule, throw error
                        if (count($tempArgumentArray) != 2)
                        //throw new Exception("Argument '$argumentsList[$i]' is invalid");
                            throw new Exception(gettext("Abstract : ") . gettext("Argument ") . $argumentsList[$i] . gettext(" is invalid!"));

                        if ($tempArgumentArray[0] != 'xElementId' && ( strstr($tempArgumentArray[0], 'Id') !== FALSE and !is_numeric(trim($tempArgumentArray[1], '><')) )) {
                            //throw new Exception("$tempArgumentArray[0] should be a natural number.");
                            throw new Exception(gettext("Abstract : ") . gettext("Argument ") . $tempArgumentArray[0] . gettext(" is invalid!!"));
                        }

                        //check for keyname present in reserve parameter list
                        if (array_search($tempArgumentArray[0], $this->_reservedParameters) === FALSE) {
                            //check for keyname present in allowed columns list
                            if (array_search($tempArgumentArray[0], $this->_clauseColumnNames) === FALSE) {
                                //throw new Exception("Argument '$tempArgumentArray[0]' is invalid");
                                throw new Exception(gettext("Abstract : ") . gettext("Argument ") . $tempArgumentArray[0] . gettext(" is invalid!!!"));
                            } else {
                                //set column clauses
                                $firstCharacter = substr($tempArgumentArray[1], 0, 1);
                                if ('=' == $firstCharacter || '<' == $firstCharacter || '>' == $firstCharacter) {
                                    $tempArgumentArray[1] = substr($tempArgumentArray[1], 1);
                                    $clauseValue = Helper :: escape($tempArgumentArray[1]);
                                    if (is_numeric($clauseValue))
                                        $tempClause = "$tempArgumentArray[0] $firstCharacter $clauseValue";
                                    else
                                        $tempClause = "$tempArgumentArray[0] $firstCharacter '$clauseValue'";
                                }
                                else {
                                    $clauseValue = Helper :: escape($tempArgumentArray[1]);
                                    $tempClause = "$tempArgumentArray[0] = '$clauseValue'";
                                }
                                if( $tempArgumentArray[0] != 'clientId' ) {
	                                array_push($this->_arrayClauses, $tempClause);
                                }
                            }
                        } else {
                            //process keyname to populate reserve parameter variables
                            $firstCharacter = substr($tempArgumentArray[1], 0, 1);
                            if ('=' == $firstCharacter)
                                $tempArgumentArray[1] = substr($tempArgumentArray[1], 1);

                            if ('<' == $firstCharacter || '>' == $firstCharacter)
                            //throw new Exception("'<','>' are not allowed with $tempArgumentArray[0]");
                                throw new Exception(gettext("Abstract : ") . gettext("'<','>' are not allowed with ") . $tempArgumentArray[0]);

                            switch ($tempArgumentArray[0]) {
                                case 'sortOrder':
                                    if ('asc' == strtolower($tempArgumentArray[1]) || 'desc' == strtolower($tempArgumentArray[1])) {
                                        $this->_sortOrder = strtoupper($tempArgumentArray[1]);
                                        $this->_sortEnabled = true;
                                    } else
                                    //throw new Exception("$tempArgumentArray[0]'s value '$tempArgumentArray[1]' is invalid. Valid values : ASC, DESC");
                                        throw new Exception(gettext("Abstract : ") . $tempArgumentArray[0] . gettext("'s value '") . $tempArgumentArray[1] . gettext("'s value  is invalid. Valid values : ASC, DESC"));
                                    break;

                                case 'quantity':
                                    if (is_numeric($tempArgumentArray[1])) {
                                        $this->_quantity = $tempArgumentArray[1];
                                        if ($this->_quantity < 1) {
                                            $this->_quantity = 1;
                                        }
                                    }
                                    else
                                    //throw new Exception("$tempArgumentArray[0]'s value '$tempArgumentArray[1]' is invalid. It should be Number.");
                                        throw new Exception(gettext("Abstract : ") . $tempArgumentArray[0] . gettext("'s value '") . $tempArgumentArray[1] . gettext("'s value  is invalid. It should be Number."));
                                    break;

                                case 'pageNumber':
                                    if (is_numeric($tempArgumentArray[1]))
                                        $this->_pageNumber = $tempArgumentArray[1];
                                    else
                                    //throw new Exception("$tempArgumentArray[0]'s value '$tempArgumentArray[1]' is invalid. It should be Number.");
                                        throw new Exception(gettext("Abstract : ") . $tempArgumentArray[0] . gettext("'s value '") . $tempArgumentArray[1] . gettext("'s value  is invalid. It should be Number."));
                                    break;

                                case 'sortColumn':
                                    if (array_search($tempArgumentArray[1], $this->_sortColumnNames) === FALSE) {
                                        $validSortColumns = implode(', ', $this->_sortColumnNames);
                                        //throw new Exception("$tempArgumentArray[0]'s value '$tempArgumentArray[1]' is invalid. Valid values : $validSortColumns");
                                        throw new Exception(gettext("Abstract : ") . $tempArgumentArray[0] . gettext("'s value '") . $tempArgumentArray[1] . gettext("'s value  is invalid. Valid values : ") . $validSortColumns);
                                    }

                                    $this->_sortColumn = $tempArgumentArray[1];
                                    break;

                                case 'count':
                                    if ('y' == strtolower($tempArgumentArray[1]) || 'n' == strtolower($tempArgumentArray[1]))
                                        $this->_count = strtoupper($tempArgumentArray[1]);
                                    else
                                        throw new Exception("$tempArgumentArray[0]'s value '$tempArgumentArray[1]' is invalid. Valid values : Y, N");
                                    break;

                                case 'order':
                                    if ('y' == strtolower($tempArgumentArray[1]) || 'n' == strtolower($tempArgumentArray[1]))
                                        $this->_order = strtoupper($tempArgumentArray[1]);
                                    else
                                        throw new Exception("$tempArgumentArray[0]'s value '$tempArgumentArray[1]' is invalid. Valid values : Y, N");
                                    break;
                            }
                        }
                    }

                    $this->show();
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    function show() {
        $start = ($this->_pageNumber - 1) * $this->_quantity;
        $limitClause = " LIMIT $start, $this->_quantity";
        //if( $this->_sortEnabled ) {
	        $orderClause = " ORDER BY $this->_sortColumn $this->_sortOrder";
		//}

        $clauseList = "";
        if (count($this->_arrayClauses) > 0) {
            $clauseList = implode(" AND ", $this->_arrayClauses);
            $clauseList = " WHERE " . $clauseList;
            if ($this->_enableForcedCondition && $this->_betweenClause) {
                $clauseList .= ' and ' . $this->_betweenClause;
            }
            if($this->_enableForcedLimit){
                $limitClause = " LIMIT $this->_limitClause1, $this->_limitClause2";
            }
        }

        $countQueryString = "SELECT COUNT(*) as dataTotalCount FROM $this->_mainTable " . $clauseList;
        // echo $countQueryString;
        // echo "<br>";

        $queryResult = $this->_databaseConnection->FetchAllArray($countQueryString);
        $this->_dataTotalCount = $queryResult[0]['dataTotalCount'];
        $this->_dataTotalPages = ceil($this->_dataTotalCount / $this->_quantity);

        if ($this->_count == 'N') {

            $dataQueryString = "SELECT * FROM $this->_mainTable " . $clauseList . $orderClause . $limitClause;

            if ($this->_order == 'N') {
                $dataQueryString = "SELECT * FROM $this->_mainTable " . $clauseList . $limitClause;
            }
            //echo $dataQueryString;
            //echo "<br>";

            $queryResult = $this->_databaseConnection->FetchAllArray($dataQueryString);
            $this->_dataResultCount = count($queryResult);
            $this->_dataResultArray = $queryResult;
            //print_r(array_keys($queryResult[0]));
            if ($this->_dataResultCount > 0)
                $this->_mainTablePermissibleValues = implode(", ", array_keys($this->_dataResultArray[0]));
        }
    }

    function get($keyName, $index=0, $xindex=1) {
        $range = $this->_dataResultCount > 0 ? $this->_dataResultCount - 1 : $this->_dataResultCount;

        if ($index >= $this->_dataResultCount)
        //throw new Exception("Your requested index '$index' is invalid. Range for get should be 0 - $range");
            throw new Exception(gettext("Abstract : ") . gettext("Your requested index ") . $index . gettext(" is invalid. Range for get should be 0 - ") . $range);

        if (!array_key_exists($keyName, $this->_dataResultArray[$index])) {
            $countExpandableTables = count($this->_expandableTables);
            if ($countExpandableTables == 0) {
                $validArguments = $this->_mainTablePermissibleValues;
                //throw new Exception("Your requested argument '$keyName' is invalid. Valid arguments : $validArguments");
                throw new Exception(gettext("Abstract : ") . gettext("Your requested argument ") . $keyName . gettext(" is invalid. Valid arguments : ") . $validArguments);
            }

            if (isset($this->_dataResultArray[$index][$xindex])) {
                if (array_key_exists($keyName, $this->_dataResultArray[$index][$xindex])) {
                    return $this->_dataResultArray[$index][$xindex][$keyName];
                }
            }

            $assetTypeId = '';

            if ($this->_mainTable == 'cms_asset')
                $assetTypeId = $this->_dataResultArray[$index]['assetTypeId'];
            else if ($this->_mainTable == 'cms_image') {
                $objAssetType = new Cms_Db_AssetType();
                $cId = $this->_dataResultArray[$index]['clientId'];
                $objAssetType->set("clientId||$cId", "assetTypeSlug||image");
                $assetTypeId = $objAssetType->get('assetTypeId', 0);
            } else if ($this->_mainTable == 'cms_video') {
                $objAssetType = new Cms_Db_AssetType();
                $cId = $this->_dataResultArray[$index]['clientId'];
                $objAssetType->set("clientId||$cId", "assetTypeSlug||video");
                $assetTypeId = $objAssetType->get('assetTypeId', 0);
            }

            if (!array_key_exists($assetTypeId, $this->_arrayExpandablePermissibleValues)) {
                $tempClientId = $this->_dataResultArray[$index]['clientId'];
                $this->setExpandablePermissibleValues($tempClientId, $assetTypeId);
            }

            // describe( $keyName, true );
            $_xElementId = array_search($keyName, $this->_arrayExpandablePermissibleValues[$assetTypeId]);

            if ($_xElementId === FALSE) {
                $validArguments = $this->_mainTablePermissibleValues . ", " . $this->_expandableTablesPermissibleValues;
                //throw new Exception("Your requested argument '$keyName' is invalid. Valid arguments : $validArguments");
                throw new Exception(gettext("Abstract : ") . gettext("Your requested argument ") . $keyName . gettext(" is invalid. Valid arguments : ") . $validArguments);
            } else {
                $tempJoinColumnId = $this->_dataResultArray[$index][$this->_foreignKey];
                $condition = "$this->_foreignKey = $tempJoinColumnId AND xGroupId = $xindex";

                $queryCount = 0;
                foreach ($this->_expandableTables as $value) {
                    $queryString = "SELECT xElementId, xElementValue FROM $value WHERE $condition";
                    $queryResult = $this->_databaseConnection->FetchAllArray($queryString);
                    $queryCount = count($queryResult);

                    for ($_qr = 0; $_qr < $queryCount; $_qr++) {
                        $this->_dataResultArray[$index][$xindex][$this->_arrayExpandablePermissibleValues[$assetTypeId][$queryResult[$_qr]['xElementId']]] = $queryResult[$_qr]['xElementValue']; // Wow !!!
                    }
                }

                if (isset($this->_dataResultArray[$index][$xindex][$keyName]))
                    return $this->_dataResultArray[$index][$xindex][$keyName];
                else
                    return false;
            }
        }
        else {
            if ('assetElementsOrder' == $keyName) {
                if ('' == $this->_dataResultArray[$index][$keyName])
                    return array();
                else
                    return explode(",", $this->_dataResultArray[$index][$keyName]);
            }
            return $this->_dataResultArray[$index][$keyName];
        }
    }

    function getResultCount() {
        return $this->_dataResultCount;
    }

    function getTotalCount() {
        return $this->_dataTotalCount;
    }

    function getLastInsertedId() {
        return $this->_lastInsertedId;
    }

    function getQuantity() {
        return $this->_quantity;
    }

    function add() {
        echo gettext("Redefine add function");
    }

    function delete() {
        echo gettext("Redefine delete function");
    }

    function edit() {
        echo gettext("Redefine edit function");
    }

    private function setExpandablePermissibleValues($clientId, $assetTypeId = 0) {
        if ('cms_asset' == $this->_mainTable || 'cms_image' == $this->_mainTable || 'cms_video' == $this->_mainTable) {
            $this->_arrayExpandablePermissibleValues[$assetTypeId] = $this->getAssetPermissibleValuesByTypeId($clientId, $assetTypeId);
            $this->_expandableTablesPermissibleValues = implode(", ", $this->_arrayExpandablePermissibleValues[$assetTypeId]);
        }
    }

    protected function getUserPermissibleValues($clientId) {
        $userElements = new Cms_Db_UserElement();
        $userElements->set("clientId||$clientId");
        $totalResultCount = $userElements->getResultCount();

        $userPermissibleValues = Array();
        if ($totalResultCount > 0) {

            for ($_pv = 0; $_pv < $totalResultCount; $_pv++) {
                $userPermissibleValues[$userElements->get("userElementId", $_pv)] = $userElements->get("elementName", $_pv);
            }
        }
        return $userPermissibleValues;
    }

    protected function getAssetPermissibleValuesByTypeId($clientId, $assetTypeId) {
        $assetTypesObj = new Cms_AssetTypeElements("assetTypeId||$assetTypeId");

        if ($assetTypesObj->getResultCount() > 0) {
            $assetPermissibleValues = Array();
            foreach ($assetTypesObj() as $assetTypeObj) {
                $assetPermissibleValues[$assetTypeObj->getAssetTypeElementId()] = $assetTypeObj->getElementName();
            }
            return $assetPermissibleValues;
        } else {
            throw new Exception(gettext("Abstract : ") . $assetTypeId . gettext(" is invalid"));
        }
    }

    function ipmlIf($keyName, $index=0) {
        $range = $this->_dataResultCount > 0 ? $this->_dataResultCount - 1 : $this->_dataResultCount;

        if ($index >= $this->_dataResultCount)
        //throw new Exception("Your requested index '$index' is invalid. Range for get should be 0 - $range");
            throw new Exception(gettext("Abstract : ") . gettext("Your requested index ") . $index . gettext(" is invalid. Range for get should be 0 - ") . $range);

        if (!array_key_exists($keyName, $this->_dataResultArray[$index])) {
            $validArguments = $this->_mainTablePermissibleValues;
            //throw new Exception("Your requested argument '$keyName' is invalid. Valid arguments : $validArguments");
            throw new Exception(gettext("Abstract : ") . gettext("Your requested argument ") . $keyName . gettext(" is invalid. Valid arguments : ") . $validArguments);
        } else {
            if ('' == $this->_dataResultArray[$index][$keyName])
                return false;
        }

        return true;
    }

    public function getTotalPages() {
        return $this->_dataTotalPages;
    }

    function __call($functionName, $argumentsArray) {
        if (substr($functionName, 0, 3) == 'get') {
            $columnName = lcfirst(substr($functionName, 3));
            return $this->get($columnName, count($argumentsArray) > 0 ? $argumentsArray[0] : 0, count($argumentsArray) > 1 ? $argumentsArray[1] : 0 );
        } else {
            throw new Exception("Call to undefinded function '$functionName'!");
        }
    }

    function getRecord($index = false) {
        if ($index === false) {
            return $this->_dataResultArray;
        } else {
            $range = $this->_dataResultCount > 0 ? $this->_dataResultCount - 1 : $this->_dataResultCount;
            if ($index >= $this->_dataResultCount) {
                throw new Exception(gettext("Abstract : ") . gettext("Your requested index ") . $index . gettext(" is invalid. Range for get should be 0 - ") . $range);
            }
            return $this->_dataResultArray[$index];
        }
    }

    private function getClassName() {
        $tableName = ucfirst(str_replace('_rel', '', str_replace('cms_', '', $this->_mainTable)));
        $className = '';
        $i = 0;
        while ($i < strlen($tableName)) {
            if (substr($tableName, $i, 1) == '_') {
                $className .= ucfirst(substr($tableName, ++$i, 1));
            } else {
                $className .= substr($tableName, $i, 1);
            }
            $i++;
        }
        return 'Cms_Db_' . $className;
    }

    public function fillData($record) {
        $className = $this->getClassName();
        $caseIsFor = 'add';
        if (!is_array($this->_foreignKey)) {
            if (!isset($record[$this->_foreignKey])) {
                throw new Exception('Unable to process data!');
            }
            $primaryKeyValue = $record[$this->_foreignKey];
            $selfObj = new $className();
            $selfObj->set($this->_foreignKey . "||$primaryKeyValue", 'count||Y');
            if ($selfObj->getTotalCount() > 0) {
                $caseIsFor = 'edit';
            }
        } else {
            $selfObj = new $className();
            $args = array();
            foreach ($this->_foreignKey as $column) {
                if (!isset($record[$column])) {
                    throw new Exception('Unable to process data!');
                } else {
                    $args[] = "\"$column||" . addslashes($record[$column]) . '"';
                }
            }
            $args = implode(', ', $args);
            eval('$selfObj->set( ' . $args . ' );');
            if ($selfObj->getTotalCount() > 0) {
                $caseIsFor = 'edit';
            }
        }

        $this->_arrayUpdatedData = $record;
        $this->_task = $caseIsFor;
        $this->$caseIsFor();
        return $caseIsFor;
    }

}
