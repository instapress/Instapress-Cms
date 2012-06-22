<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


class Cms_CmsUtility {

//
  public static function checkForPlagairism($text=FALSE) {
        if ($text) {
            $text = strip_tags($text);
            $pageUrl = "http://www.0xee5.info/bing_action?text=" . urlencode($text);
            $ch = curl_init($pageUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resp = curl_exec($ch);
            $res = json_decode($resp);
            return $res->percentage;
        }
    }

//
    public static function getUsersData($usersId) {
        try {

// print_r($usersId);
            If (count($usersId) == 0)
                return false;
            $userId = serialize($usersId);
            $usersId = self::encrypt($userId);
            $usersId = urlencode($usersId);
//echo "http://instacheckin.com/getUsers/?ids=$usersId";
            $usersData = self::requestCurl("http://instacheckin.com/getUsers/?ids=$usersId");
//            print_r($usersData);
//            $usersData = Forum_ForumUtility::decrypt($usersData);
//            $usersData = unserialize($usersData);
//            print_r($usersData);
            if ($usersData !== false) {
                $usersData = self::decrypt($usersData);
                $usersData = unserialize($usersData);
//print_r($usersData);
                if (isset($usersData['error']))
                    return false;
                if (count($usersData) == 0)
                    return false;

                return $usersData;
            }
            else
                return false;
        } catch (Exception $ex) {
            throw new Exception(gettext($ex->getFile()) . gettext($ex->getLine()) . getText($ex->getMessage()));
        }
    }

    public static function getUserByEmail($userEmailId) {
        try {

// print_r($usersId);

            $userEmailId = urlencode(self::encrypt($userEmailId));
//$usersId=urlencode($usersId);
//echo "http://instacheckin.com/getUsers/?ids=$usersId";
            $usersData = self::requestCurl("http://instacheckin.com/getUserByEmail/?email=$userEmailId");
//            print_r($usersData);
//            $usersData = Forum_ForumUtility::decrypt($usersData);
//            $usersData = unserialize($usersData);
//            print_r($usersData);
            if ($usersData !== false) {
                $usersData = self::decrypt($usersData);
                $usersData = unserialize($usersData);
//print_r($usersData);
                if (isset($usersData['error']))
                    return false;
                if (count($usersData) == 0)
                    return false;

                return $usersData;
            }
            else
                return false;
        } catch (Exception $ex) {
            throw new Exception(gettext($ex->getFile()) . gettext($ex->getLine()) . getText($ex->getMessage()));
        }
    }

    public static function encrypt($text) {
        $text = base64_encode($text);
        $crypttext = mcrypt_encrypt(MCRYPT_3DES, KEY, $text, MCRYPT_MODE_ECB);
        $crypttext = base64_encode($crypttext);
        return $crypttext;
//echo "encrypt text=$crypttext";
    }

    public static function decrypt($text) {

        $crypttext = base64_decode($text);
//echo "crypt text after base 64 decode  $crypttext <br/>";
        $crypttext = mcrypt_decrypt(MCRYPT_3DES, KEY, $crypttext, MCRYPT_MODE_ECB);
//echo "key ".KEY." <br/>";
//echo "decrypt text =  $crypttext <br/>";
        $crypttext = base64_decode($crypttext);
        return $crypttext;
//echo "decrypt text=$crypttext";
    }

    public static function requestCurl($url=false) {
        if ($url) {
            try {

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_HTTPGET, 1);
#curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
                $data = curl_exec($ch);
                curl_close($ch);
                return $data;
            } catch (Exception $exc) {
                return $exc->getTraceAsString();
            }
        }else
            return false;
    }


}

?>
