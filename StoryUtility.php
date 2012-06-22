<?php

class Cms_StoryUtility {

    private static $_keyPhrases = array(
        "26" => array('Weddingclan', 'Wedding', 'Wedding Dresses', 'Wedding Gowns', 'Wedding Blog', 'Wedding Planning', 'Wedding Ideas'),
        "62" => array('Beautyramp', 'Beauty', 'Skin Care', 'Beauty Tips', 'Makeup Tips', 'Beauty Blog', 'Beauty Products'),
        "44" => array('Parentingclan', 'Parenting', 'Parenting Magazine', 'Pregnancy Symptoms', 'Baby Products', 'New Born Baby', 'Parents Magazine'),
        "502" => array('Diyhealth', 'Health', 'Health Care', 'Health Tips', 'Mens Health', 'Womens Health', 'Fitness Magazine', 'Mens Fitness', 'Health News', 'Health Magazine', 'Health and Fitness', 'Home Remedies', 'Health Forum'),
        "90" => array('Cellphonebeat', 'Cell Phones', 'Cell Phone Reviews', 'Smartphone Reviews', 'Mobile Review', 'Mobile Phones', 'Mobile Phone Reviews', 'Mobile Games', 'Mobile Applications'),
        "19" => array('Gardeningclan', 'Gardening', 'Gardening Tips', 'Home and Garden', 'Garden Design', 'Garden Ideas', 'Landscaping Ideas', 'House Plants'),
        "20" => array('Alwaysfoodie', 'Food', 'Healthy Food', 'Food Blog', 'Food Recipes', 'Food and Wine', 'Cooking Recipes'),
        "508" => array('Audiovideoclan', 'Audio Video', 'Home Theater', 'Home Theater Systems', 'Television Guide', 'Digital Camera', 'Digital Camera Reviews'),
        "16" => array('Petsclan', 'Pets', 'Dog Breeds', 'Dog Training', 'Dog Food', 'Dog Grooming', 'Dog House', 'Cat Breeds'),
        "151" => array('Hometone', 'Home', 'Home Decor', 'Home Design', 'Home Improvement', 'Home Automation', 'Home Decoration', 'Home and Garden', 'Home Interiors', 'Home Furniture', 'Kitchen Appliances', 'Bathrooms Vanities', 'Bathroom Ideas', 'Bathroom Design', 'Bathroom Furniture', 'Interior Design', 'Interior Decoration', 'Kitchen Design'),
        "10" => array('Gizmowatch', 'Gadgets', 'Cool Gadgets', 'Latest Gadgets', 'Digital Camera', 'Digital Camera Reviews', 'Laptop Reviews', 'Technology News', 'Mobile Phone Reviews', 'Notebook Review'),
        "14" => array('Greendiary', 'Eco Friendly', 'Environment', 'Ecofriend.com', 'Ecofriend', 'Eco Friendly', 'Environment'),
        "153" => array('Automotto', 'Auto News', 'Car Reviews', 'Cars Guide', 'Hybrid Cars', 'Car Accessories', 'Electric Car', 'Sports Cars'),
        "149" => array('Bornrich', 'Luxury', 'Most Expensive'),
        "152" => array('Designbuzz', 'Design', 'Interior Design', 'Interior Design Ideas', 'Home Design', 'Product Design', 'Furniture Design', 'Landscape Design'),
        "150" => array('Styleguru', 'Fashion', 'Fashion Blog', 'Fashion Magazine')
    );
    private static $_errorMessage = '';
    private static $_storyObj = false;
    private static $_secondAdAfter = 300;

    public static function getMessage() {
        return self::$_errorMessage;
    }

    private function __construct() {

    }

	public static function getHotList( $listSlug ) {
		try {
			return self::getUserList( $listSlug );
		} catch( Exception $ex ) {
	        $items = new Cms_ListItems( "itemSlug||$listSlug", 'publicationId||' . PUBLICATION_ID, 'moderationStatus||approve' );
	        if( $items->getResultCount() > 0 ) {
	            return $items->get( 0 );
	        }
		}
        throw new Exception( 'Could not find userlist!' );
	}

	public static function getUserList( $listSlug ) {
        $stories = new Cms_Stories( "storySlug||$listSlug", 'publicationId||' . PUBLICATION_ID, "storyPublished||Y", 'storyType||userlist' );
        if( $stories->getResultCount() > 0 ) {
            return $stories->get( 0 );
        }
        throw new Exception( 'Could not find userlist!' );
	}

    public static function isStoryPresent($dossierId) {
        $objDossierStory = new Editorial_Db_DossierStory();
        $objDossierStory->set("dossierId||$dossierId");
        return $objDossierStory->getResultCount();
    }

    public static function getClusters($quantity = 4) {
        return new Cms_Clusters("quantity||$quantity", 'publicationId||' . PUBLICATION_ID, 'sortColumn||clusterOrder', 'sortOrder||asc');
    }

    public static function getClusterStories($clusterId, $quantity = 2) {
        return new Cms_ClusterStories("quantity||$quantity", 'publicationId||' . PUBLICATION_ID, "clusterId||$clusterId");
    }

    public static function processBeforeInsert($content, $dossierId) {
        $pieces = explode('<img ', $content);
        $imgInfo = array();

        for ($i = 1; $i < count($pieces); $i++) {
            $info = explode('>', $pieces[$i]);
            $imgInfo[] = '<img ' . $info[0] . '>';
        }

        $alts = array();
        foreach ($imgInfo as $key => $img) {
            $imgAlt = explode('alt="', $img);
            if (count($imgAlt) == 2) {
                $alt = $imgAlt[1];
                $alt = explode('"', $alt);
                $alts[$key] = $alt[0];
            }
        }

        foreach ($imgInfo as $key => $img) {
            if (count(explode(':', $alts[$key])) == 2) {
                $content = str_replace($imgInfo[$key], '<!--' . $alts[$key] . '-->', $content);
            }
        }

		// For dossier-asset usage track
        $dossierAssetObj = new Editorial_Db_DossierAsset();
        $dossierAssetObj->set("dossierId||$dossierId", 'quantity||100');
        $assetTypes = Network_AssetType::getAssetTypes();
        for ($i = 0; $i < $dossierAssetObj->getResultcount(); $i++) {
            if (array_search(array_search($dossierAssetObj->get('assetTypeId', $i), $assetTypes) . ':' . $dossierAssetObj->get('assetId', $i), $alts) !== false) {
                if ($dossierAssetObj->get('dossierAssetActive', $i) != 'Y') {
                    $dossierAssetEditObj = new Editorial_Db_DossierAsset('edit');
                    $dossierAssetEditObj->set('dossierAssetRelId||' . $dossierAssetObj->get('dossierAssetRelId', $i), 'dossierAssetActive||Y');
                }
            } else {
                if ($dossierAssetObj->get('dossierAssetActive', $i) != 'N') {
                    $dossierAssetEditObj = new Editorial_Db_DossierAsset('edit');
                    $dossierAssetEditObj->set('dossierAssetRelId||' . $dossierAssetObj->get('dossierAssetRelId', $i), 'dossierAssetActive||N');
                }
            }
        }
        return $content;
    }

    public static function getContentAssets($text) {
        preg_match_all("<!--(.*?)-->", $text, $regs);
        if (count($regs) < 1) {
            return array();
        } else {
            return $regs[1];
        }
    }

    private static function getImageHtml($imageId, $position, $ad='', $imageWidth=false) {
        $imageObj = new Cms_Db_Image();
        $imageObj->set('imageId||' . $imageId);

        if ($imageObj->getResultCount() == 0) {
            if ($ad)
                return $ad;
            return '';
        }

        $imageUri = S3_IMAGE_PUBLIC_LOCATION . $imageObj->get('imageUri');
        $width = $imageObj->getImageWidth();
        
        if( $imageWidth !== false ) {
        	$libImage = new Instapress_Core_Image( CLIENT_ID );
        	$imageUri = S3_IMAGE_PUBLIC_LOCATION . $libImage->getScaledImage( $imageObj->get('imageUri'), $imageWidth );
        	$width = $imageWidth;
        }

        $imageClass = $width >= 400 ? "contentImage" : "contentSmallImage";
        

        if ($position == 0) {
            $infographicContent = '';
            if (self::$_storyObj) {
                $storyObj = self::$_storyObj;
                $firstCategoryId = $storyObj->getFirstCategoryId();
                $moduleType = strtolower($storyObj->getModuleType());
                if($moduleType == 'infographics')
                {
                	try {
                        $rand = rand() % count(self::$_keyPhrases[PUBLICATION_ID]);
                        $infographicContent = '<div class="infographics"><p><b>Embed this Infographic on Your Website:</b></p><textarea style="width:100%;margin-bottom:15px;" rows="5" onclick="this.select();" readonly="readonly"><a href="' . $storyObj->getPermalink() . '"><img src="' . $imageUri . '" alt="' . $imageObj->get('imageTitle') . '" /></a><br />Created by: <a href="' . HOME_PATH . '">' . self::$_keyPhrases[PUBLICATION_ID][$rand] . '</a></textarea></div><b style="display:none">Hello2' . $rand . '</b>';
                    } catch (Exception $ex) {

                    }
                }
                elseif ($firstCategoryId > 0) {
                    try {
                        $categoryObj = new Cms_Category($firstCategoryId);
                        if (strtolower($categoryObj->getCategoryName()) == 'infographics') {
                            $rand = rand() % count(self::$_keyPhrases[PUBLICATION_ID]);
                            $infographicContent = '<div class="infographics"><p><b>Embed this Infographic on Your Website:</b></p><textarea style="width:100%;margin-bottom:15px;" rows="5" onclick="this.select();" readonly="readonly"><a href="' . $storyObj->getPermalink() . '"><img src="' . $imageUri . '" alt="' . $imageObj->get('imageTitle') . '" /></a><br />Created by: <a href="' . HOME_PATH . '">' . self::$_keyPhrases[PUBLICATION_ID][$rand] . '</a></textarea></div><b style="display:none">Hello2' . $rand . '</b>';
                        }
                    } catch (Exception $ex) {

                    }
                }
            }
            $imageHtml = '<div class="' . $imageClass . '"><img src="' . $imageUri . '" alt="' . $imageObj->get('imageTitle') . '" title="' . $imageObj->getImageTitle() .
                    '" width="' . $width . '" /><div class="imgContent" style="width:' . ($width - 20) . 'px"><b>' . $imageObj->getImageTitle() . "</b></div></div>$infographicContent<div style=\"clear:both\"></div><div class=\"permaAd\">" . $ad . '</div>';
        } else {
            $imageHtml = '<div class="' . $imageClass . '"><img src="' . $imageUri . '" alt="' . $imageObj->get('imageTitle') . '" title="' . $imageObj->getImageTitle() .
                    '" width="' . $width . '" /><div class="imgContent" style="width:' . ($width - 20) . 'px"><b>' . $imageObj->getImageTitle() . '</b></div></div>';
        }
        return $imageHtml;
    }

    private static function getVideoHtml($videoId) {
        $videoObj = new Cms_Db_Video();
        $videoObj->set('videoId||' . $videoId);
        $videoEmbeddCode = $videoObj->get('videoEmbeddCode', 0, 1);

        $videoHtml = '<div class="contentImage" title="' . $videoObj->getVideoTitle() . '">' . $videoEmbeddCode .
                '<div class="imgContent"><b>' . $videoObj->getVideoTitle() . '</b><span>' . $videoObj->getVideoDescription() . '</span></div></div>';
        return $videoHtml;
    }

    private static function getFactboxHtml($factboxId) {
        $assetObj = new Cms_Db_Asset();
        $assetObj->set('assetId||' . $factboxId);

        $assetLink = "javascript:void(0);";

        $assetTitle = $assetObj->getAssetTitle();
        $factboxContent = Instapress_Core_Text::processBeforeView($assetObj->get('factboxText', 0, 1));

        $assetHtml = '<div class="contentSlideshow"><div class="head">Factbox</div><div class="title"><a href="' . $assetLink . '">' . $assetTitle .
                '</a></div><div class="desc">' . $factboxContent . '</div></div>';

        return $assetHtml;
    }

    private static function getSlideshowHtml($slideshowId) {
        $assetObj = new Cms_Asset($slideshowId);
        $assetSlug = $assetObj->getAssetSlug();

        $imgObj = new Instapress_Core_Image(CLIENT_ID);
        $assetUri = S3_IMAGE_PUBLIC_LOCATION . $imgObj->getScaledImage($assetObj->getAssetPrimaryImagePath(), 250, 200);
        $assetLink = $assetObj->getPermalink();

        $assetTitle = $assetObj->getAssetTitle();

        $assetHtml = '<div class="contentSlideshow"><div class="head">Slideshow</div><div class="title"><a href="' . $assetLink . '">' . $assetTitle .
                '</a></div><div class="img"><a href="' . $assetLink . '"><img src="' . $assetUri . '" alt="' . $assetTitle . '" title="' . $assetTitle . '" /></a></div>' .
                '<div class="desc">' . $assetObj->getAssetDescription() . '</div></div>';

        return $assetHtml;
    }

    private static function getGalleryHtml($galleryId) {
        $assetObj = new Cms_Asset($galleryId);
        $assetSlug = $assetObj->getAssetSlug();

        $imgObj = new Instapress_Core_Image(CLIENT_ID);
        $assetUri = S3_IMAGE_PUBLIC_LOCATION . $imgObj->getScaledImage($assetObj->getAssetPrimaryImagePath(), 250, 200);
        $assetLink = $assetObj->getPermalink();

        $assetTitle = $assetObj->getAssetTitle();

        $assetHtml = '<div class="contentSlideshow"><div class="head">Picture Gallery</div><div class="title"><a href="' . $assetLink . '">' . $assetTitle .
                '</a></div><div class="img"><a href="' . $assetLink . '"><img src="' . $assetUri . '" alt="' . $assetTitle . '" title="' . $assetTitle . '" /></a></div>' .
                '<div class="desc">' . $assetObj->getAssetDescription() . '</div></div>';

        return $assetHtml;
    }

    private static function getParaInfo($para) {
        $text = trim(strip_tags($para));
        $hasImage = strpos($para, '<!--Image:') !== false;
        $wordCount = Helper::getWordCount($text);
        if (substr($para, 0, 3) != '<p>') {
            $para = str_replace('<p>', '', $para);
            $para = '<p>' . $para;
        }
        return array(
            'text' => $text,
            'hasImage' => $hasImage,
            'wordCount' => $wordCount,
            'html' => $para
        );
    }

    private static function getMetaParas($content) {
        $content = str_replace('<br />', '<br>', str_replace('<br/>', '<br>', $content));
        $returnParas = array();
        $paras = explode('</p>', $content);
        if (trim(end($paras)) == '') {
            array_pop($paras);
        }
        foreach ($paras as $i => $para) {
            $subParas = explode("\n", $para);
            if (trim(end($subParas)) == '') {
                array_pop($subParas);
            }
            foreach ($subParas as $j => $subPara) {
                $lastLevelParas = explode('<br>', $subPara);
                if (trim(end($lastLevelParas)) == '') {
                    array_pop($lastLevelParas);
                }
                foreach ($lastLevelParas as $j => $lastLevelPara) {
                    if (trim($lastLevelPara) != '') {
                        $returnParas[] = self::getParaInfo(trim($lastLevelPara));
                    }
                }
            }
        }
        return $returnParas;
    }

    private static function fixParas($storyParas) {
        $fixedParas = array();
        foreach ($storyParas as $storyPara) {
            if (substr($storyPara['html'], -4) != '</p>') {
                $storyPara['html'] = str_replace('</p>', '', $storyPara['html']);
                $storyPara['html'] = $storyPara['html'] . '</p>';
            }
            if ($storyPara['hasImage'] && $storyPara['wordCount'] > 0) {
                $imageId = explode('<!--Image:', $storyPara['html']); //
                $imageId = explode('-->', $imageId[1]);
                $imageId = $imageId[0];
                $fixedParas[] = array(
                    'text' => '',
                    'hasImage' => true,
                    'wordCount' => 0,
                    'html' => "<p><!--Image:$imageId--></p>"
                );
                $storyPara['html'] = str_replace("<!--Image:$imageId-->", '', $storyPara['html']);
                if (strpos($storyPara['html'], '<!--more-->') !== false) {
                    $storyPara['html'] = '<!--more-->' . str_replace('<!--more-->', '', $storyPara['html']);
                }
                $fixedParas[] = $storyPara;
            } else if (strpos($storyPara['html'], '<!--more-->') !== false) {
                $storyPara['html'] = '<!--more-->' . str_replace('<!--more-->', '', $storyPara['html']);
                $fixedParas[] = $storyPara;
            } else {
                $fixedParas[] = $storyPara;
            }
        }
        return $fixedParas;
    }

    private static function getParas($content, $ad2) {
        $fixedParas = self::fixParas( self::getMetaParas( $content ) );
        $ftp = $fip = false;
        foreach( $fixedParas as $i => $para ) {
            if ($para['wordCount'] > 10 && $ftp === false) {
                $ftp = intval( $i );
            }
            if ($para['hasImage'] && $fip === false) {
                $fip = intval( $i );
            }
        }
        if ($ftp == 0 || $fip == $ftp + 1) {
            $fip = -1;
        }
        $paras = '';
        $pCount = count($fixedParas);
        $wordCount = 0;
        for ($i = 0; $i < $pCount; $i++) {
            if ($i === $fip) {
                continue;
            }
            if ($i == $ftp + 1) {
                $paras .= $fixedParas[$fip]['html'];
                $fixedParas[$fip][ 'used' ] = true;
                $ftp = -10;
                $i--;
            } else {
                $wordCount += $fixedParas[$i]['wordCount'];
                $paras .= $fixedParas[$i]['html'];
                $fixedParas[ $i ][ 'used' ] = true;
            }
            if ($wordCount > self::$_secondAdAfter) {
                $paras .= '<div class="permaAd">' . $ad2 . '</div>';
                $wordCount = -10000000;
            }
        }
        if( $i == $ftp + 1 && !isset( $fixedParas[ $fip ][ 'used' ] ) ) {
            $paras .= $fixedParas[$fip]['html'];
        }
        return $paras;
    }

    public static function processBeforeView($content, $ad='', $dossierId = 0, $ad2 = '', $imageWidth=false) {
    	$oldContent = $content;
        $ad2 = $ad2 !== '' ? $ad2 : $ad;
        $ad2 = is_string( $ad2 ) ? $ad2 : '';
        $content = self::getParas($content, $ad2);
		//describe( $content, $contentParas, true );
        $deeplinkStoryDb = new Cms_Db_DeeplinkStory();
        $deeplinkStoryDb->set("storyId||$dossierId", 'deleted||N', 'order||N');
        if ($deeplinkStoryDb->getResultCount() > 0) {
            $content = self::addDeeplink($content, $deeplinkStoryDb->getDeeplinkId());
        }

        $validAssets = Cms_AssetType::getAssetTypes();
        $assets = self::getContentAssets($content);

        $i = 0;
        foreach ($assets as $storyAsset) {
            $assetInfo = explode(':', $storyAsset);
            $assetType = $assetInfo[0];
            if (!isset($validAssets[$assetType])) {
                continue;
            }
            
            if(PUBLICATION_ID==152 && ($assetType=='Slideshow' || $assetType=='Gallery' )){
            	continue;
            }

            $pattern = "<!--$storyAsset-->";

            switch ($assetType) {
                case 'Image':
                    $imageHtml = self::getImageHtml($assetInfo[1], $i, $ad, $imageWidth);
                    $content = str_ireplace($pattern, $imageHtml, $content);
                    break;

                case 'Video':
                    $videoHtml = self::getVideoHtml($assetInfo[1]);
                    $content = str_ireplace($pattern, $videoHtml, $content);
                    break;

                case 'Factbox':
                    $factboxHtml = self::getFactboxHtml($assetInfo[1]);
                    $content = str_ireplace($pattern, $factboxHtml, $content);
                    break;

                case 'Slideshow':
                    $slideshowHtml = self::getSlideshowHtml($assetInfo[1]);
                    $content = str_ireplace($pattern, $slideshowHtml, $content);
                    break;

                case 'Gallery':
                    $galleryHtml = self::getGalleryHtml($assetInfo[1]);
                    $content = str_ireplace($pattern, $galleryHtml, $content);
                    break;

                default:
                    $content = str_ireplace($pattern, "", $content);
            }
            $i++;
        }
        return $content;
    }

    public static function addDeeplink($content, $deeplinkId) {
        try {
            $deeplink = new Cms_Deeplink($deeplinkId);
            $keyword = $deeplink->getKeyword();
            $anchorHtml = '<a id="insta-deeplink" href="' . $deeplink->getTarget() . '" rel="' . $deeplink->getRelAttribute() . '"' . ( $deeplink->getNewWindow() == 'Y' ? ' target="_blank"' : '' ) . '>%keyword%</a>';
            $keywordPosition = stripos( $content, " $keyword " );
            $keywordPosition = $keywordPosition === false ? false : $keywordPosition + 1;

            while( true ) {
	            $anchorStartPosition = stripos( $content, "<a", $keywordPosition );
	            $anchorEndPosition = stripos( $content, "</a>", $keywordPosition );            
	            
	            if( $anchorEndPosition < $anchorStartPosition ) {
	            	$keywordPosition = stripos( $content, " $keyword ", $anchorEndPosition );
					$keywordPosition = $keywordPosition === false ? false : $keywordPosition + 1;
	            	if( $keywordPosition === false ) {
	            		break;
	            	}
	            } else {
	            	break;
	            }
            }

            $contentKeyword = substr( $content,  $keywordPosition, strlen( $keyword ) );
            if( $keyword != $contentKeyword ) {
            	throw new Exception( 'sum tin wong ...' );
            }
            if( $keywordPosition === false ) {
                throw new Exception( 'Keyword not found!' );
            }
            $anchorHtml = str_replace('%keyword%', $contentKeyword, $anchorHtml );

			$newContent = substr( $content, 0, $keywordPosition ) . $anchorHtml . substr( $content, $keywordPosition + strlen( $keyword ) );
            return $newContent;
        } catch (Exception $ex) {
            return $content;
        }
    }

    public static function processBeforeViewforMobile($content) {
        $validAssets = Cms_AssetType::getAssetTypes();
        $assets = self::getContentAssets($content);

        foreach ($assets as $storyAsset) {
            $assetInfo = explode(':', $storyAsset);
            $assetType = $assetInfo[0];
            if (!isset($validAssets[$assetType])) {
                continue;
            }

            $pattern = "<!--$storyAsset-->";

            switch ($assetType) {
                case 'Image':
                    $imageHtml = self::getImageHtmlforMobile($assetInfo[1]);
                    $content = str_ireplace($pattern, $imageHtml, $content);
                    break;

                case 'Video':
                    $videoHtml = self::getVideoHtmlforMobile($assetInfo[1]);
                    $content = str_ireplace($pattern, $videoHtml, $content);
                    break;

                case 'Factbox':
                    $factboxHtml = self::getFactboxHtmlforMobile($assetInfo[1]);
                    $content = str_ireplace($pattern, $factboxHtml, $content);
                    break;

                case 'Slideshow':
                    $slideshowHtml = self::getSlideshowHtmlforMobile($assetInfo[1]);
                    $content = str_ireplace($pattern, $slideshowHtml, $content);
                    break;

                case 'Gallery':
                    $galleryHtml = self::getGalleryHtmlforMobile($assetInfo[1]);
                    $content = str_ireplace($pattern, $galleryHtml, $content);
                    break;

                default:
                    $content = str_ireplace($pattern, "", $content);
            }
        }
        return $content;
    }

    private static function getImageHtmlforMobile($imageId) {
        $imageObj = new Cms_Db_Image();
        $imageObj->set('imageId||' . $imageId);

        if ($imageObj->getResultCount() == 0) {
            return '';
        }

        $imageUri = S3_IMAGE_PUBLIC_LOCATION . $imageObj->get('imageUri');

        $imageClass = $imageObj->getImageWidth() > 500 ? "contentImage" : "contentSmallImage";

        $imageHtml = '<div class="' . $imageClass . '"><center><img src="' . $imageUri . '" alt="' . $imageObj->get('imageTitle') . '" title="' . $imageObj->getImageTitle() .
                '" width="300" /></center><div class="imgContent" style="width:' . (300 - 20) . 'px"><b>' . $imageObj->getImageTitle() . '</b><span>' . $imageObj->getImageDescription() . '</span></div></div>';
        return $imageHtml;
    }

    private static function getVideoHtmlforMobile($videoId) {
        $videoObj = new Cms_Db_Video();
        $videoObj->set('videoId||' . $videoId);
        $videoEmbeddCode = $videoObj->get('videoEmbeddCode', 0, 1);

        $videoHtml = '<div class="contentImage" title="' . $videoObj->getVideoTitle() . '">' . $videoEmbeddCode .
                '<div class="imgContent"><b>' . $videoObj->getVideoTitle() . '</b><span>' . $videoObj->getVideoDescription() . '</span></div></div>';
        return $videoHtml;
    }

    private static function getFactboxHtmlforMobile($factboxId) {
        $assetObj = new Cms_Db_Asset();
        $assetObj->set('assetId||' . $factboxId);

        $assetLink = "javascript:void(0);";

        $assetTitle = $assetObj->getAssetTitle();
        $factboxContent = Instapress_Core_Text::processBeforeView($assetObj->get('factboxText', 0, 1));

        $assetHtml = '<div class="contentSlideshow"><div class="head">Factbox</div><div class="title"><a href="' . $assetLink . '">' . $assetTitle .
                '</a></div><div class="desc">' . $factboxContent . '</div></div>';

        return $assetHtml;
    }

    private static function getSlideshowHtmlforMobile($slideshowId) {
        $assetObj = new Cms_Db_Asset();
        $assetObj->set('assetId||' . $slideshowId);
        $assetSlug = $assetObj->getAssetSlug();

        $imgObj = new Instapress_Core_Image(CLIENT_ID);
        $assetUri = S3_IMAGE_PUBLIC_LOCATION . $imgObj->getScaledImage($assetObj->getAssetPrimaryImagePath(), 250, 200);
        $assetLink = HOME_PATH . "slideshow/$assetSlug/";

        $assetTitle = $assetObj->getAssetTitle();

        $assetHtml = '<div class="contentSlideshow"><div class="head">Slideshow</div><div class="title"><a href="' . $assetLink . '">' . $assetTitle .
                '</a></div><div class="img"><a href="' . $assetLink . '"><img src="' . $assetUri . '" alt="' . $assetTitle . '" title="' . $assetTitle . '" /></a></div>' .
                '<div class="desc">' . $assetObj->getAssetDescription() . '</div></div>';

        return $assetHtml;
    }

    private static function getGalleryHtmlforMobile($galleryId) {
        $assetObj = new Cms_Db_Asset();
        $assetObj->set('assetId||' . $galleryId);
        $assetSlug = $assetObj->getAssetSlug();

        $imgObj = new Instapress_Core_Image(CLIENT_ID);
        $assetUri = S3_IMAGE_PUBLIC_LOCATION . $imgObj->getScaledImage($assetObj->getAssetPrimaryImagePath(), 250, 200);
        $assetLink = HOME_PATH . "gallery/$assetSlug/";

        $assetTitle = $assetObj->getAssetTitle();

        $assetHtml = '<div class="contentSlideshow"><div class="head">Picture Gallery</div><div class="title"><a href="' . $assetLink . '">' . $assetTitle .
                '</a></div><div class="img"><a href="' . $assetLink . '"><img src="' . $assetUri . '" alt="' . $assetTitle . '" title="' . $assetTitle . '" /></a></div>' .
                '<div class="desc">' . $assetObj->getAssetDescription() . '</div></div>';

        return $assetHtml;
    }

    public static function getArchiveStories($yearMonth, $pageNumber = 1, $quantity = 10) {
        return new Cms_Stories("yearmonth||$yearMonth", 'publicationId||' . PUBLICATION_ID, "pageNumber||$pageNumber", "quantity||$quantity");
    }

    public static function getTagStories($tagName, $pageNo=1, $quantity=10, $specialTag = false) {
        $tagId = 0;
        if (is_numeric($tagName)) {
            $tagId = $tagName;
        } else {
            $tagSlug = ( $specialTag ? 'ib-spl-' : '' ) . Helper::sanitizeWithDashes($tagName);
            $objTag = new Cms_Db_Tag();
            $objTag->set("tagSlug||$tagSlug", 'order||N');
            if ($objTag->getResultCount() == 0) {
                return new Cms_Stories();
            }
            $tagId = $objTag->getTagId();
        }
        $storyTagDbObj = new Cms_Db_StoryTag();
        $storyTagDbObj->set("tagId||$tagId", 'publicationId||' . PUBLICATION_ID, "pageNumber||$pageNo", "quantity||$quantity", 'sortColumn||storyTime');

        if (( $storiesCount = $storyTagDbObj->getResultCount() ) > 0) {
            $stories = array();
            for ($i = 0; $i < $storiesCount; $i++) {
                $stories[] = new Cms_Story($storyTagDbObj->getStoryId($i));
            }
            $tagStories = new Cms_Stories($stories);
            $tagStories->updateStats($storyTagDbObj->getTotalCount(), $storyTagDbObj->getTotalPages());
            return $tagStories;
        }
        return new Cms_Stories();
    }

	public static function getRecommendedStories() {
		$quantity = 5;
        $objStories = new Cms_Stories('publicationId||' . PUBLICATION_ID, 'moduleType||recommended', 'order||N', 'count||Y', "quantity||$quantity", 'storyPublished||Y');
        $totalPages = $objStories->getTotalPages();
        if($totalPages>0)
        {
        	$pageNo = rand(1, $totalPages);
        	$pageNo = $pageNo - 1;
        	$pageNo = $pageNo < 1 ? 1 : $pageNo;
        		
        	$objStories = new Cms_Stories('publicationId||' . PUBLICATION_ID, 'moduleType||recommended', "pageNumber||$pageNo", "quantity||$quantity", 'storyPublished||Y');
        	return $objStories;        	
        }
        else
        {
        	return false;
        }        
    }
    
    public static function getModuleStories($moduleType = 'feature', $quantity = 4, $categoryId = 0) {
        if($categoryId > 0)
        	return new Cms_Stories("firstCategoryId||$categoryId", 'publicationId||' . PUBLICATION_ID, 'storyType||<userlist', "moduleType||$moduleType", "quantity||$quantity", "storyPublished||Y");
        else
    		return new Cms_Stories('publicationId||' . PUBLICATION_ID, "moduleType||$moduleType", "quantity||$quantity", "storyPublished||Y");
    }

    public static function getNormalStories($pageNo = 1, $quantity = 10) {
        return new Cms_Stories('publicationId||' . PUBLICATION_ID, 'storyType||<userlist', "pageNumber||$pageNo", "quantity||$quantity", "storyPublished||Y");
    }
    
	public static function getUserListStories($viewType='', $categoryId = 0, $pageNo = 1, $quantity = 10) {
        if($categoryId > 0)	
		{
			if($viewType!='')
	        	return new Cms_Stories("viewType||$viewType", "firstCategoryId||$categoryId",'publicationId||' . PUBLICATION_ID, 'storyType||userlist', "pageNumber||$pageNo", "quantity||$quantity", "storyPublished||Y", "moderationStatus||approve");
	        else
	        	return new Cms_Stories("firstCategoryId||$categoryId", 'publicationId||' . PUBLICATION_ID, 'storyType||userlist', "pageNumber||$pageNo", "quantity||$quantity", "storyPublished||Y", "moderationStatus||approve");
		}
		else
		{
			if($viewType!='')
	        	return new Cms_Stories("viewType||$viewType", 'publicationId||' . PUBLICATION_ID, 'storyType||userlist', "pageNumber||$pageNo", "quantity||$quantity", "storyPublished||Y", "moderationStatus||approve");
	        else
	        	return new Cms_Stories('publicationId||' . PUBLICATION_ID, 'storyType||userlist', "pageNumber||$pageNo", "quantity||$quantity", "storyPublished||Y", "moderationStatus||approve");
		}
	}    
    
 	public static function getCmsStoriesByCategory($pageNo = 1, $quantity = 10, $type = 'story', $categoryId = 0) {
 		// type= 'story', 'shopping-guide', 'listical' 	
 		if($categoryId > 0)	
       		return new Cms_Stories("firstCategoryId||$categoryId", 'publicationId||' . PUBLICATION_ID, "storyType||$type", "pageNumber||$pageNo", "quantity||$quantity", "storyPublished||Y");
       	else
       		return new Cms_Stories('publicationId||' . PUBLICATION_ID, "storyType||$type", "pageNumber||$pageNo", "quantity||$quantity", "storyPublished||Y");
    }

    public static function getArchives() {
        $archiveDbObj = new Cms_Db_Archive();
        $archiveDbObj->set('publicationId||' . PUBLICATION_ID, 'sortColumn||yearmonth', 'sortOrder||desc', 'quantity||1000');
        $recordsCount = $archiveDbObj->getResultCount();
        $archives = array();
        $months = array('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        for ($i = 0; $i < $recordsCount; $i++) {
            $yearMonth = $archiveDbObj->getYearmonth($i);
            $year = substr($yearMonth, 0, 4);
            $month = (int) substr($yearMonth, 4);
            $archives[$year][$month]['link'] = HOME_PATH . "archives/$yearMonth/";
            $archives[$year][$month]['title'] = $months[$month];
            $archives[$year][$month]['count'] = $archiveDbObj->getTotalStories($i);
        }
        return $archives;
    }

    public static function getArchiveDetails() {
        $archiveDbObj = new Cms_Db_Archive();
        $archiveDbObj->set('publicationId||' . PUBLICATION_ID, 'sortColumn||yearmonth', 'sortOrder||desc', 'quantity||1000');
        $recordsCount = $archiveDbObj->getResultCount();
        $archives = array();
        for ($i = 0; $i < $recordsCount; $i++) {
            $yearMonth = $archiveDbObj->getYearmonth($i);
            $year = substr($yearMonth, 0, 4);
            $month = (int) substr($yearMonth, 4);
            $archives[$year][$month] = HOME_PATH . "archives/$yearMonth/";
        }
        foreach ($archives as $year => $months) {
            $archives[$year] = array_reverse($months, true);
        }
        return $archives;
    }

    public static function getStory($storySlug) {
        $storyDbObj = new Cms_Stories( "storySlug||$storySlug", 'publicationId||' . PUBLICATION_ID, "storyPublished||Y", 'order||N' );
        if( $storyDbObj->getResultCount() > 0 ) {
            return $storyDbObj->get( 0 );
        }
        throw new Exception('Could not find the story!');
    }

    public static function getCategoryStories($categoryId, $pageNo=1, $quantity=10, $level = 'first') {
        return $storiesObj = new Cms_Stories("{$level}CategoryId||$categoryId", 'publicationId||' . PUBLICATION_ID, "pageNumber||$pageNo", "quantity||$quantity", "storyPublished||Y", 'storyType||<userlist');
    }
    
 	public static function getAllCategoryStories($categoryId, $pageNo=1, $quantity=10, $level = 'first') {
		return $storiesObj = new Cms_Stories("{$level}CategoryId||$categoryId", 'publicationId||' . PUBLICATION_ID, "pageNumber||$pageNo", "quantity||$quantity", "storyPublished||Y");
    }

	public static function getLatestStories($pageNo=1, $quantity=50) {
		return $storiesObj = new Cms_Stories('publicationId||' . PUBLICATION_ID, "pageNumber||$pageNo", "quantity||$quantity", "storyPublished||Y");
    }
    
	public static function getCategoryQuestions($categoryId, $questionType='all', $review='N', $pageNo=1, $quantity=10, $level = 'first') {
        if($questionType == 'all')
			return $storiesObj = new Cms_Questions("{$level}CategoryId||$categoryId", 'publicationId||' . PUBLICATION_ID, "review||$review", "pageNumber||$pageNo", "quantity||$quantity", "questionStatus||publish");
		else
			return $storiesObj = new Cms_Questions("{$level}CategoryId||$categoryId", 'publicationId||' . PUBLICATION_ID, "review||$review", "questionType||$questionType", "pageNumber||$pageNo", "quantity||$quantity", "questionStatus||publish");
    }   
    

    public static function getFeaturedStoriesXML($publicationId, $pageNo) {
// checking the validity of page number
        $obj = new Cms_Db_Story();
        $obj->setForcedLimit(0, 6);
        $obj->set("publicationId||$publicationId", "count||Y", "storyPublished||Y");
        $totalpage = $obj->getTotalPages();
        $totalcount = $obj->getTotalCount();
//        $Totalpage=ceil($total/$quantity);
        if ($totalcount > 0) {
            if ($pageNo > $totalpage) {
//set the pageno for last page
                $pageNo = $totalpage;
            }
            $obj1 = new Cms_Db_Story();
            $obj->setForcedLimit(0, 6);
            $obj1->set("publicationId||$publicationId", "storyPublished||Y");
            $result1 = $obj1;
            $count = $obj1->getResultCount();
        } else {
            $count = 0;
        }
//        parent::__construct("publicationId||$publicationId", "pageNumber||$pageNo");
////$i=0;
//        $record1 = array();
//        $record1 = $this->_matchedRecords; //check for error or no record
//        $count = count($record1);
        $str = "<channel>";
        $str = $str . "<totalarticle>" . $totalcount . "</totalarticle>";
        try {
            if ($count > 0) {
                $str = $str . "<lastbuilddate>";
                $obj = new Cms_Db_Story();
                $obj->set("quantity||1");
                $createdate = $obj->get("createdTime", 0);
                $strDate = date("D,d M Y H:i:s " . '+0000', strtotime($createdate));
                $str = $str . $strDate . "</lastbuilddate>";
                $obj2 = new Cms_Db_StoryTag();
                $str = $str . "<Success>True</Success>";
                $str = $str . "<Message>Record Found For the Request</Message>";

                for ($i = 0; $i < 6; $i++) {
//$i=$i+1;o
//if($i>10)
//  break;
                    $val = new Cms_Story($obj1->get("storyId", $i));
                    $path = array();


//$source=$story->find('div[class="contentImage"]')->find('img')->src;
//$source.setAttribute("width","320");
//$source1=$story->find('div[class="contentImage"]')->find('img')->src;
//$source1.setAttribute("width","50");
//$source1.setAttribute("height","50");
//$stag= "<![CDATA[$story]]>";
                    $storyId = $val->getStoryId();
                    $str = $str . "<item>";
                    $str = $str . "<storyid>" . $val->getstoryId() . "</storyid>";
                    $str = $str . "<creator>" . htmlentities($val->getauthorByLine()) . "</creator>";
                    $strdate = $val->getCreatedTime();
                    $strDate = date("D,d M Y H:i:s +0000", strtotime($strdate));
                    $str = $str . "<pubDate>" . $strDate . "</pubDate>";
                    $str = $str . "<title><![CDATA[" . $val->getstoryTitle() . "]]> </title>";
                    $str = $str . '<guid isPermaLink="true">' . $val->getPermalink() . "</guid>";
//                    $str = $str . "<storyExcerpt>" . $val->getstoryExcerpt() . "</storyExcerpt>";
//                    $str = $str . "<storyTags>" . htmlentities($val->getstoryTags()) . "</storyTags>";
                    $story = Instapress_Core_Text::processBeforeView(Cms_StoryUtility::processBeforeViewforMobile($val->getstoryWebContent()));
                    $str = $str . "<content>" . "<![CDATA[$story]]>" . "</content>";
//$str=$str."<publishedOn>".$obj2->getstoryTime($val->getstoryId())."</publishedOn>";
//$str=$str."<publishDate>".$val->getstoryTime()."</publishDate>";

                    $str = $str . "<primaryImagePath>" . $val->getprimaryImagePath() . "</primaryImagePath>";
//echo "url for the image is".$val->getprimaryImagePath();
                    $libImageObj = new Instapress_Core_Image(CLIENT_ID);
                    $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 320, 200);
                    $str = $str . "<big_image>" . $imageUrl . "</big_image>";
                    $imageUrl = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($val->getprimaryImagePath(), 127, 95);
                    $str = $str . "<small_image>" . $imageUrl . "</small_image>";
//$str=$str."<smallImage>".$source1."</smallImage>";
                    $str = $str . "</item>";
                }
            } else {
                $str = $str . "<Success>False</Success>";
                $str = $str . "<Message>No Record Found</Message>";
            }
        } catch (Exception $e) {

            $str = $str . "<Success>False</Success>";
            $str = $str . "<Message>" . $e->getMessage() . "</Message>";
        }
        $str = $str . "</channel>";
        $str = $str . "";
        return $str;
    }

    public static function createAsciiUrlSlug($str, $replace=array(), $delimiter='-') {
        if (!empty($replace)) {
            $str = str_replace((array) $replace, ' ', $str);
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

        return $clean;
    }

    public static function saveImageFromURL($url, $fileName) {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        $imageData = curl_exec($ch);
        curl_close($ch);

        if (strpos($imageData, '<html') !== FALSE) {

            $binaryDatafilepath = 100;
        } else {

            $binaryDatafilepath = "/tmp/" . $fileName . '.jpg';

            if (file_exists($binaryDatafilepath))
                unlink($binaryDatafilepath);

            if (touch($binaryDatafilepath)) {

                chmod($binaryDatafilepath, 0777);

                $file = fopen($binaryDatafilepath, "wb") or die('couldn\'t open file.');

                fwrite($file, $imageData);

                fclose($file);

//push image on amozon server.
                $imageUploadObj = new Instapress_Core_Image(CLIENT_ID);
                return $imageUploadObj->saveImageOnS3($binaryDatafilepath, $fileName);
            } else {
                return null;
            }
        }
        return null;
    }

    public static function getAllPublication() {
        $templateObj = new Cms_Db_Publication();
        $templateObj->set("order||N", "count||Y");
        $totalCount = $templateObj->getTotalCount();
        $templateObj = new Cms_Db_Publication();
        $templateObj->set("quantity||$totalCount", "sortColumn||publicationName", "sortOrder||ASC");
        $records = Array();
        for ($i = 0; $i < $totalCount; $i++) {
            $records[$i]['publicationId'] = $templateObj->get("publicationId", $i);
            $records[$i]['publicationName'] = $templateObj->get("publicationName", $i);
        }
        return $records;
    }

    /**
     * @author Mayank Gupta 20110708
     * @param Integer $totalCount
     * @param Integer $recordsOnPage
     * @param Integer $pageNo
     * @param String $link 
     * @param Boolean $isPermalinkPage
     * @return Array $pagingArr
     */
    public static function getPaginationArr($totalCount, $recordsOnPage, $pageNo, $link, $isPermalinkPage=false) {
        $pagingArr = array();
        $totalPages = ceil($totalCount / $recordsOnPage);
        $prevLink = $nextLink = '';
        $prevLink = ($pageNo == 1) ? false : $link . ($pageNo - 1);
        $nextLink = ($pageNo <= ($totalPages - 1)) ? $link . ($pageNo + 1) : false;
        $pgnos = array();
//for first five pages.
        if ($pageNo <= 5) {
            $start = 0;
            $end = 10;
        } else { //other pages.
            $start = $pageNo - 5;
            $end = $pageNo + 4;
        }
        if ($end > $totalPages)
            $end = $totalPages;
        for ($i = $start; $i < $end; $i++) {
            $pgnos[$i]['no'] = $i + 1;
            $page = $i + 1;
            $pgnos[$i]['href'] = !empty($isPermalinkPage) ? $link . $page . '.html' : $link . $page . '/';
            $pgnos[$i]['class'] = "pgInactive";
        }
        $pgnos[$pageNo - 1]['class'] = "pgactive";

        $pagingArr['prevLink'] = $prevLink;
        $pagingArr['nextLink'] = $nextLink;
        $pagingArr['paging'] = (count($pgnos) > 0) ? $pgnos : false;
        return $pagingArr;
    }

    public static function getRelatedEntity($storyId=false) {
        if (!empty($storyId)) {
            $objStoryEntityRel = new Cms_StoryEntityRels("storyId||$storyId");
            if ($objStoryEntityRel->getTotalCount() > 0) {
                return $objStoryEntityRel->getEntityId(0);
            } else {
                return FALSE;
            }
        } else {
            return false;
        }
    }

    public static function getRelatedGroup($storyId=false) {
        if (!empty($storyId)) {
            $objStoryEntityRel = new Cms_GroupArticleRels("storyId||$storyId");
            if ($objStoryEntityRel->getTotalCount() > 0) {
                return $objStoryEntityRel->getGroupId(0);
            } else {
                return FALSE;
            }
        } else {
            return false;
        }
    }

    public static function getQuestion( $questionSlug ) {
        $questionDbObj = new Cms_Db_Question();
        $questionDbObj->set( "questionSlug||$questionSlug", 'publicationId||' . PUBLICATION_ID );
        if ($questionDbObj->getResultCount() > 0) {
            return $questionObj = new Cms_Question( $questionDbObj->getQuestionId() );
        }
        throw new Exception( 'Could not find the record!' );
    }
    
}
