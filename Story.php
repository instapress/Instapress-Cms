<?php

class Cms_Story extends Cms_AbstractSingular {

    protected $_dbClass = 'Story';
    protected $_relatedClasses = array('Cms_Db_StoryContent', 'Cms_Db_ShoppingGuide');
    protected $_foreignKey = 'storyId';

    public function postComment( $commentText ) {
    	$dossierId = $this->getStoryId();
    	$dossierObj = new Editorial_Dossier( $dossierId );
    	$aliasDossierId = $dossierObj->getAliasDossierId();
    	if( $aliasDossierId == 0 ) {
    		$aliasDossierId = Editorial_Utility::addForum( 'forum', PUBLICATION_ID,
				$this->getStoryTitle(), $this->getStoryExcerpt(),
				$this->getFirstCategoryId(), $this->getSecondCategoryId(), $this->getThirdCategoryId(),
				'M', USER_ID, $this->getPrimaryImagePath(), $dossierId, 0, '', 0, 0 );
			$dossierObj->update( "aliasDossierId||$aliasDossierId" );
    	}
    	Editorial_Utility::addUGCAnswer( PUBLICATION_ID, $aliasDossierId, $commentText, USER_ID );
		@file_get_contents( HOME_PATH . "PULL/nv/did/$aliasDossierId/" );
		return true;
    }

	public function getContentImages() {
		try {
			$assets = Cms_StoryUtility::getContentAssets( $this->getStoryWebContent() );
			$images = array();
			foreach( $assets as $asset ) {
				if( stripos( $asset, 'image' ) !== false ) {
					$imageId = str_ireplace( 'image:', '', $asset );
					$images[] = new Cms_Image( $imageId );
				}
			}
			if( count( $images ) > 0 ) {
				return new Cms_Images( $images );
			}
			return new Cms_Images();
		} catch( Exception $ex ) {
			return new Cms_Images();
		}
	}

	public function getComments() {
		$questions = new Cms_Questions( "parentDossierId||" . $this->getStoryId() );
		if( $questions->getResultCount() == 0 ) {
			return array();
		} else {
			$question = $questions->get( 0 );
			$answers = new Cms_Answers( 'questionId||' . $question->getQuestionId(), 'publicationId||' . PUBLICATION_ID, 'answerStatus||publish', 'quantity||1000' );
			$answerArray = array();
			foreach( $answers() as $answer ) {
				$user = new Network_User( $answer->getAutoUserId() );
				$answerArray[] = array(
					'id' => $answer->getAnswerId(),
					'image' => $user->getProfileImage( 'small' ),
					'name' => $user->fullName(),
					'text' => $answer->getAnswerContent(),
					'time' => Helper::calculatePreModifiedDate( $answer->getCreatedTime() )
				);
			}
			return $answerArray;
		}
	}

	public function getApprovedItems( $onlyCount = false ) {
		if( $onlyCount === true ) {
			$items = new Cms_ListItems( 'storyId||' . $this->getStoryId(), 'moderationStatus||approve', 'quantity||1000', 'count||Y' );
			return $items->getTotalCount();
		}
		return new Cms_ListItems( 'storyId||' . $this->getStoryId(), 'moderationStatus||approve', 'quantity||1000' );
	}

	public function getSandboxItems( $onlyCount = false ) {
		if( $onlyCount === true ) {
			$items = new Cms_ListItems( 'storyId||' . $this->getStoryId(), 'moderationStatus||queue', 'quantity||1000', 'count||Y' );
			return $items->getTotalCount();
		}
		return new Cms_ListItems( 'storyId||' . $this->getStoryId(), 'moderationStatus||queue', 'quantity||1000' );
	}

    public function getShortContent() {
        if (!isset($this->shortContent)) {
            $storyContent = $this->getStoryWebContent();

            $shortContent = explode("<!--more-->", $storyContent);

            $shortContent = $shortContent[0];

            $pattern = '"(<\!--[^>]*)(-->)"';
            $replacement = "";
            $this->shortContent = Instapress_Core_Text::processBeforeView(preg_replace($pattern, $replacement, $shortContent));
        }
        return $this->shortContent;
    }

    public function getRelatedStories($pageNumber, $quantity) {
        try {
            $searchObj = new Instapress_Core_Search('192.168.100.84', 18983);
            $searchObj->searchRelatedStoriesForCms($this->getStoryId(), $pageNumber, $quantity);
            $searchResults = $searchObj->getSearchResults();
            $stories = array();
            foreach ($searchResults as $storyId) {
                $stories[] = new Cms_Story($storyId);
            }

            $count = count($stories);
            $left = 6 - $count;
            if ($count < 5) {
                $StoryObj = new Cms_Stories("count||Y", 'firstCategoryId||' . $this->getFirstCategoryId());
                $newCount = $StoryObj->getTotalCount();
                if ($newCount < $left) {
                    $left = $newCount;
                }
                $StoryObj = new Cms_Stories('firstCategoryId||' . $this->getFirstCategoryId(), "quantity||$left");
                foreach ($StoryObj () as $story) {
                    if ($count == 5) {
                        break;
                    }
                    $stories[] = new Cms_Story($story->getStoryId());
                    $count++;
                }
            }
            if (count($stories) > 0) {
                return new Cms_Stories($stories);
            } else {
                return new Cms_Stories();
            }
        } catch (Exception $ex) {
            return new Cms_Stories();
        }
    }

    public function getRelatedQnA($pageNumber, $quantity) {
        try{
	    	$searchObj = new Instapress_Core_Search('192.168.100.84', 18983);
	        if (trim($this->getKeyword()) == '') {
	            //echo "story title=".$this->getStoryTitle();
	            $searchObj->dossierSearch($this->getStoryTitle(), "qna", $this->getPublicationId(), $pageNumber, $quantity, "published", $this->getStoryId());
	        } else {
	            //echo "story keyword=".$this->getKeyword();
	            $searchObj->dossierSearch($this->getKeyword(), "qna", $this->getPublicationId(), $pageNumber, $quantity, "published", $this->getStoryId());
	        }
	
	        $searchResults = $searchObj->getSearchResults();
	        $questions = array();
	        foreach ($searchResults as $storyId) {
	            $questions[] = new Cms_Question($storyId);
	        }
	
	        $count = count($questions);
	       
	        $left = 6 - $count;
	        if ($count < 5) {
	            $QuestionObj = new Cms_Questions("count||Y", 'firstCategoryId||' . $this->getFirstCategoryId());
	            $newCount = $QuestionObj->getTotalCount();
	            if ($newCount < $left) {
	                $left = $newCount;
	            }
	            $QuestionObj = new Cms_Questions('firstCategoryId||' . $this->getFirstCategoryId(), "quantity||$left");
	            foreach ($QuestionObj () as $question) {
	                if ($count == 5) {
	                    break;
	                }
	               
	                $count++;
	                $questions[] = new Cms_Question($question->getQuestionId());
	            }
	        }
	        if (count($questions) > 0) {
	            return new Cms_Questions($questions);
	        } else {
	            return new Cms_Questions();
	        }
        }
    	catch (Exception $ex) {
            return new Cms_Stories();
        }
    }

    public function unpublish() {
        if ($this->getStoryPublished() == 'Y') {
            $storyDbObj = new Cms_Db_Story('edit');
            $storyDbObj->set('storyId||' . $this->getStoryId(), 'publicationId||0', 'storyPublished||N', 'storySlug||' . $this->getStorySlug() . '-old');
        }

        // Remove story tag relations
        $storyTagDbObj = new Cms_Db_StoryTag('delete');
        $storyTagDbObj->set('storyId||' . $this->getStoryId());

        // Remove story asset relations
        $storyAssetDbObj = new Cms_Db_StoryAsset('delete');
        $storyAssetDbObj->set('storyId||' . $this->getStoryId());
    }

    public function getLinkedTags() {
        if (!isset($this->linkedTags)) {
            $tags = explode(', ', $this->getTags());
            $linkedTags = array();
            foreach ($tags as $tag) {
                $linkedTags[$tag] = HOME_PATH . 'tag/' . Helper::sanitizeWithDashes($tag) . '/';
            }
            $this->linkedTags = $linkedTags;
        }
        return $this->linkedTags;
    }

    public function getTags() {
        if (!isset($this->tags)) {
            $storyTags = explode(',', $this->getStoryTags());
            $tags = array();
            foreach ($storyTags as $tag) {
                $tag = trim($tag);
                if (substr($tag, 0, 1) != '#') {
                    $tags[] = $tag;
                }
            }
            $this->tags = implode(', ', $tags);
        }
        return $this->tags;
    }

    public function getFirstTag() {
        $firstTag = explode(', ', $this->getTags());
        return $firstTag[0];
    }

    public function getSecondTag() {
        $secondTag = explode(', ', $this->getTags());
        return $secondTag[1];
    }

    public function getNextStory() {
        if (!isset($this->nextStory)) {
            $storyDbObj = new Cms_Db_Story();
            $storyDbObj->set('publicationId||' . PUBLICATION_ID, 'storyId||>' . $this->getStoryId(), 'quantity||1', 'sortOrder||asc');
            if ($storyDbObj->getResultCount() > 0) {
                $this->nextStory = new Cms_Story($storyDbObj->getStoryId());
            } else {
                $this->nextStory = false;
            }
        }
        return $this->nextStory;
    }

    public function getPrevStory() {
        if (!isset($this->prevStory)) {
            $storyDbObj = new Cms_Db_Story();
            $storyDbObj->set('publicationId||' . PUBLICATION_ID, 'storyId||<' . $this->getStoryId(), 'quantity||1');

            if ($storyDbObj->getResultCount() > 0) {
                $this->prevStory = new Cms_Story($storyDbObj->getStoryId());
            } else {
                $this->prevStory = false;
            }
        }
        return $this->prevStory;
    }

    public function getPermalink() {
        $oldEnteriesId = defined('OLD_POSTS_UPTO') ? OLD_POSTS_UPTO : 0;

		$checkDossierId = $this->getOldStoryId() > 0 ? $this->getOldStoryId() : $this->getStoryId();

        if (Cms_Url::getUrlFileUri() == 'error_404.php') {
            return HOME_PATH . 'entry/' . $this->getStorySlug() . '/';
        }
        if( $checkDossierId > $oldEnteriesId ) {
            return HOME_PATH . $this->getStorySlug() . '.html';
        } else {
            return HOME_PATH . 'entry/' . $this->getStorySlug() . '/';
        }
    }

    public function getShoppingGuideContent() {
        $story = array();
        $story['heading'] = $this->getStoryTitle();
        $story['desc'] = $this->getDeeplinkedContent();

        $storyId = $this->getStoryId();
        $storyData = $this->varDump();
        $totalProducts = $storyData['totalProducts'];
        $productsObj = new Cms_ShoppingGuideProducts("storyId||$storyId", "quantity||$totalProducts", 'sortOrder||asc', 'sortColumn||serialNumber');

        $stories = array();
        $i = 0;
        foreach( $productsObj () as $productObj ) {
            $stories[$i]['listNo'] = $i + 1;
            $stories[$i]['link'] = "";
            $stories[$i]['heading'] = $productObj->getProductName();
            $stories[$i]['stars'] = "star" . $productObj->getAverageRating();
            $stories[$i]['price'] = 'Price: $' . number_format(round($productObj->getProductPrice(), 0)); //"Price: $50 / &pound;22";
            $stories[$i]['numPrice'] = $productObj->getProductPrice();
            $stories[$i]['rating'] = $productObj->getAverageRating();
            $stories[$i]['priceLink'] = $productObj->getPriceLink() ? $productObj->getPriceLink() : 'javascript:;';

            $usp = $description = '';
            $pros = $cons = $buyLinks = array();
            try {
                $description = $productObj->getProductDescription();
                $productBrandName = $productObj->getProductBrandName();
                $usp = $productObj->getProductUsp();
                $techSpecs = $productObj->getTechnicalSpecification();
                $pros = $productObj->getProductPros() ? explode('#####', $productObj->getProductPros()) : array();
                $cons = $productObj->getProductCons() ? explode('#####', $productObj->getProductCons()) : array();
                $buyLinks = $productObj->getWhereToBuy() ? explode('#####', $productObj->getWhereToBuy()) : array();
            } catch (Exception $ex) {
                
            }
            $stories[$i]['usp'] = $usp;
            $stories[$i]['excerpt'] = $description;
            $stories[$i]['productBrandName'] = $productBrandName;
            $stories[$i]['techSpecs'] = ( $storyData['technicalSpecification'] == 'Y' ) ? $techSpecs : '';

            $stories[$i]['images'] = array();
            if ($productObj->getAssetId() > 0) {
                $assetObj = new Cms_Asset($productObj->getAssetId());
                $order = trim($assetObj->getAssetElementsOrder());
                if ($order != '') {
                    $order = explode(',', $order);
                    switch (count($order)) {
                        case 1 : {
                                $imageObj = new Cms_Image($assetObj->getGalleryImageId($order[ 0 ]));
                                $stories[$i]['images'][] = array(
                                    'src' => S3_IMAGE_PUBLIC_LOCATION . $imageObj->getImageUri(),
                                    'actual' => S3_IMAGE_PUBLIC_LOCATION . $imageObj->getImageUri(),
                                    'width' => $imageObj->getImageWidth(),
                                    'height' => $imageObj->getImageHeight(),
                                    'title' => $stories[$i]['heading'],
                                    'link' => Helper::isValidUrl($assetObj->getGalleryImageDescription(1)) ? $assetObj->getGalleryImageDescription(1) : 'javascript:;'
                                );
                                break;
                            } case 2 : {
                                $libImageObj = new Instapress_Core_Image();
                                foreach ($order as $group) {
                                    $imageObj = new Cms_Image($assetObj->getGalleryImageId($group));
                                    $stories[$i]['images'][] = array(
                                        'src' => S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($assetObj->getGalleryImageId($group), 300, 250),
                                        'actual' => S3_IMAGE_PUBLIC_LOCATION . $imageObj->getImageUri(),
                                        'width' => 300,
                                        'height' => 250,
                                        'title' => $stories[$i]['heading'],
                                        'link' => Helper::isValidUrl($assetObj->getGalleryImageDescription($group)) ? $assetObj->getGalleryImageDescription($group) : 'javascript:;'
                                    );
                                }
                                break;
                            } default : {
                                $cnt = 0;
                                $libImageObj = new Instapress_Core_Image();
                                foreach ($order as $group) {
                                    $width = $cnt < 2 ? 300 : 100;
                                    $height = $cnt < 2 ? 250 : 70;
                                    $imageObj = new Cms_Image($assetObj->getGalleryImageId($group));
                                    $stories[$i]['images'][] = array(
                                        'src' => S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage($assetObj->getGalleryImageId($group), $width, $height),
                                        'actual' => S3_IMAGE_PUBLIC_LOCATION . $imageObj->getImageUri(),
                                        'width' => $width,
                                        'height' => $height,
                                        'title' => $stories[$i]['heading'],
                                        'link' => Helper::isValidUrl($assetObj->getGalleryImageDescription($group)) ? $assetObj->getGalleryImageDescription($group) : 'javascript:;'
                                    );
                                    $cnt++;
                                }
                            }
                    }
                }
            }

            $stories[$i]['pros'] = array();
            foreach ($pros as $pro) {
                $stories[$i]['pros'][] = array('content' => $pro);
            }
            $stories[$i]['hasPros'] = count($stories[$i]['pros']);

            $stories[$i]['cons'] = array();
            foreach ($cons as $con) {
                $stories[$i]['cons'][] = array('content' => $con);
            }
            $stories[$i]['hasCons'] = count($stories[$i]['cons']);

            $stories[$i]['ratingParameters'] = array();
            for ($j = 1; $j < 6; $j++) {
                if (!$storyData['ratingParameter' . $j]) {
                    break;
                } else {
                    $rpObj = new Cms_Db_ProductClassRatingParameter();
                    $rpObj->set('productClassRatingParameterId||' . $storyData['ratingParameter' . $j]);
                    $stories[$i]['ratingParameters'][$j]['name'] = $rpObj->getRatingParameterName();
                    $stories[$i]['ratingParameters'][$j]['stars'] = 'star' . $productObj->{ 'getRatingParameter' . $j }();
                }
            }

            $stories[$i]['siteLinks'] = array();
            foreach ($buyLinks as $link) {
                if (!Helper::isValidUrl($link)) {
                    continue;
                }
                $info = parse_url($link);
                $name = 'Checkout item at ' . str_replace('www.', '', $info['host']);
                $stories[$i]['siteLinks'][] = array(
                    'name' => $name,
                    'href' => $link
                );
            }
            $i++;
        }
        $story['products'] = $stories;
        return $story;
    }

	public function getDeeplinkedContent() {
        $deeplinkStoryDb = new Cms_Db_DeeplinkStory();
        $deeplinkStoryDb->set( 'storyId||' . $this->getStoryId(), 'deleted||N' );
		return $deeplinkStoryDb->getResultCount() > 0 ? Cms_StoryUtility::addDeeplink( $this->getStoryWebContent(), $deeplinkStoryDb->getDeeplinkId() ) : $this->getStoryWebContent();
	}

	public function getRelatedSlideshows( $pageNumber = 1, $quantity = 10 ) {
		$stories = new Cms_Stories( "pageNumber||$pageNumber", "quantity||$quantity", 'firstCategoryId||' . $this->getFirstCategoryId(), 'storyType||userlist', 'viewType||slideshow' );
		$storyArray = array();
		foreach( $stories() as $story ) {
			if( $story->getStoryId() != $this->getStoryId() ) {
				$storyArray[] = $story;
			}
		}
		return new Cms_Stories( $storyArray );
	}

	public function getListicalContent( $getGallery = false, $maxImageWidth = false ) {
		$story = array();
		$story['heading'] = $this->getStoryTitle();

		$libImageObj = new Instapress_Core_Image();
		$story['desc'] = $this->getDeeplinkedContent();

		if( $maxImageWidth === false ) {
			$story[ 'image' ] = $this->getPrimaryImagePath() ? S3_IMAGE_PUBLIC_LOCATION . $this->getPrimaryImagePath() : false;
		} else {
			$story[ 'image' ] = $this->getPrimaryImagePath() ? S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage( $this->getPrimaryImagePath(), $maxImageWidth ) : false;
		}

		$story[ 'primaryImage' ] = false;
		if( $this->getAssetId() > 0 ) {
			try {
				$asset = new Cms_Asset( $this->getAssetId() );
				if( $maxImageWidth === false ) {
					$story[ 'primaryImage' ] = $asset->getAssetPrimaryImagePath() ? S3_IMAGE_PUBLIC_LOCATION . $asset->getAssetPrimaryImagePath() : false;
				} else {
					$story[ 'primaryImage' ] = $asset->getAssetPrimaryImagePath() ? S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage( $asset->getAssetPrimaryImagePath(), $maxImageWidth ) : false;
				}
			} catch( Exception $ex ) {}
		}

		$storyId = $this->getStoryId();
		$totalProducts = 1000;
		$productsObj = new Cms_ListItems( "storyId||$storyId", "quantity||$totalProducts", 'sortOrder||asc', 'sortColumn||serialNumber' );

		$stories = array();
		$i = 0;
		foreach( $productsObj() as $i => $productObj ) {
			if( $productObj->getTitle() == '' ) {
				continue;
			}
			$stories[ $i ][ 'listNo'] = $i + 1;
			$stories[ $i ][ 'link'] = "";
			$stories[ $i ][ 'heading'] = $productObj->getTitle();
			$stories[ $i ][ 'showGallery' ] = false;
			

			$description = '';
			try {
				$description = $productObj->getListDescription();
			} catch( Exception $ex ) {}
			$stories[ $i ][ 'excerpt' ] = $description;

			$stories[ $i ][ 'images' ] = array();
			$stories[ $i ][ 'image' ] = false;
			
			if( $getGallery ) {
				$stories[ $i ][ 'galleryPages' ] = array();
			}
			if( $productObj->getAssetId() > 0 ) {
				$groupImage = 0;
				$assetObj = new Cms_Asset( $productObj->getAssetId() );
				$order = trim( $assetObj->getAssetElementsOrder() );
				if( $order != '' ) {
					$order = explode( ',', $order );

					if( $getGallery ) {
						foreach( $order as $group ) {
							if( $group == 1 ) {
								if( $maxImageWidth === false ) {
									$stories[ $i ][ 'image' ] = S3_IMAGE_PUBLIC_LOCATION . $assetObj->getGalleryImageUri( $group );
								} else {
									$stories[ $i ][ 'image' ] = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage( $assetObj->getGalleryImageUri( $group ), $maxImageWidth );
								}
								continue;
							}
							$stories[ $i ][ 'galleryPages' ][ $groupImage ][] = array(
								'link' => S3_IMAGE_PUBLIC_LOCATION . $assetObj->getGalleryImageUri( $group ),
								'thumb' => S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage( $assetObj->getGalleryImageUri( $group ), 106, 83 ),
								'isImage' => true
							);
							$groupImage = ( count( $stories[ $i ][ 'galleryPage' ][ $groupImage ] ) % 4 == 0 ) ? $groupImage++ : $groupImage;
						}
					}
					switch( count( $order ) ) {
						case 1 : {
							$imageObj = new Cms_Image( $assetObj->getGalleryImageId( $order[ 0 ] ) );
							$stories[ $i ][ 'images' ][] = array(
								'src' => S3_IMAGE_PUBLIC_LOCATION .  $imageObj->getImageUri(),
								'actual' => S3_IMAGE_PUBLIC_LOCATION .  $imageObj->getImageUri(),
								'width' => $imageObj->getImageWidth(),
								'height' => $imageObj->getImageHeight(),
								'title' => $stories[ $i ][ 'heading'],
								'link' => Helper::isValidUrl( $assetObj->getGalleryImageDescription( 1 ) ) ? $assetObj->getGalleryImageDescription( 1 ) : 'javascript:;'
							);
							break;
						} case 2 : {
							foreach( $order as $group ) {
								$imageObj = new Cms_Image( $assetObj->getGalleryImageId( $group ) );
								$stories[ $i ][ 'images' ][] = array(
									'src' => S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage( $assetObj->getGalleryImageId( $group ), 245, 245 ),
									'actual' => S3_IMAGE_PUBLIC_LOCATION .  $imageObj->getImageUri(),
									'width' => 245,
									'height' => 245,
									'title' => $stories[ $i ][ 'heading'],
									'link' => Helper::isValidUrl( $assetObj->getGalleryImageDescription( $group ) ) ? $assetObj->getGalleryImageDescription( $group ) : 'javascript:;'
								);
							}
							break;
						} default : {
							$cnt = 0;
							foreach( $order as $group ) {
								$width = $cnt < 2 ? 300 : 100;
								$height = $cnt < 2 ? 250 : 70;
								$imageObj = new Cms_Image( $assetObj->getGalleryImageId( $group ) );
								$stories[ $i ][ 'images' ][] = array(
									'src' => S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage( $assetObj->getGalleryImageId( $group ), $width, $height ),
									'actual' => S3_IMAGE_PUBLIC_LOCATION .  $imageObj->getImageUri(),
									'width' => $width,
									'height' => $height,
									'title' => $stories[ $i ][ 'heading'],
									'link' => Helper::isValidUrl( $assetObj->getGalleryImageDescription( $group ) ) ? $assetObj->getGalleryImageDescription( $group ) : 'javascript:;'
								);
								$cnt++;
							}
						}
					}
				}
				if( $getGallery ) {
					$videos = new Cms_Videos( 'assetId||' . $productObj->getAssetId() );
					foreach( $videos() as $video ) {
						$stories[ $i ][ 'galleryPages' ][ $groupImage ][] = array(
							'link' => 'http://www.youtube.com/watch?v=' . $video->getVideoSocialKey(),
							'thumb' => S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage( $video->getprimaryImagePath(), 106, 83 ),
							'isImage' => false
						);
						$groupImage = ( count( $stories[ $i ][ 'galleryPages' ][ $groupImage ] ) % 4 == 0 ) ? $groupImage++ : $groupImage;
					}
				}
			}
			$stories[ $i ][ 'showGallery' ] = count( $stories[ $i ][ 'galleryPages' ] ) > 0;
		}
		$story[ 'products' ] = $stories;
		return $story;
	}

	public function getProductLeastPrice() {
		if( $this->getStoryType() != 'shopping-guide' ) {
			return '';
		}
		$leastPrice = 0;

        $productsObj = new Cms_ShoppingGuideProducts("storyId||" . $this->getStoryId(), "quantity||1000" );
        $leastPrice = $productsObj->getProductPrice( 0 );
        foreach( $productsObj() as $productObj ) {
        	$leastPrice = $productObj->getProductPrice() < $leastPrice ? $productObj->getProductPrice() : $leastPrice;
        }
        return $leastPrice;
	}
}