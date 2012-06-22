<?php
	/*
	 * Created on Jan 12, 2012
	 *
	 * @author Rashid Mohamad, rashid.mohamad@instamedia.com
	 */

	 class Cms_PushUtility {

	 	private $_tableData = array();
		private static $_errorMessage = '';

		public function __construct() {
			$this->_tableData = array();
		}

		public function pushDossier( $dossierId, $immediatePush = false ) {
			$dossierObj = false;
			try {
				$dossierObj = new Editorial_Dossier( $dossierId );
				if( $dossierObj->getPublicationId() != PUBLICATION_ID ) {
					throw new Exception( 'Invalid dossier!' );
				}
			} catch( Exception $ex ) {
				return false;
			}

			if( $dossierObj->getDossierType() == 'qna' ) {
				$this->_generateQnaData( $dossierId );
				$this->_getAssetsData( $dossierId );
			} else if( $dossierObj->getDossierType() == 'story' || $dossierObj->getDossierType() == 'hotlist' ) {
				$this->_generateStoryData( $dossierId );
				$this->_getAssetsData( $dossierId );
			}

			if( $immediatePush ) {
				$this->push();
			}
		}

		public function pushAsset( $assetId, $immediatePush = false ) {
			$this->_getAssetData( $assetId );

			if( $immediatePush ) {
				$this->push();
			}
		}

		private function push() {
			foreach( $this->_tableData as $classInfo => $records ) {
				if( count( $records ) == 0 ) {
					continue;
				}
				$class = 'Cms_Db_' . str_ireplace( 'cms', '', $classInfo );
				$classObj = false;
				try {
					$classObj = new $class();
				} catch( Exception $ex ) {
					describe( $ex );
					continue;
				}
				switch( $class ) {
					case 'Cms_Db_ImageExpandable' :
					case 'Cms_Db_ImageExpandableText' :
					case 'Cms_Db_VideoExpandable' :
					case 'Cms_Db_VideoExpandableText' :
					case 'Cms_Db_AssetExpandable' :
					case 'Cms_Db_AssetExpandableText' : {
						$ids = array();
						$workOn = '';
						foreach( $records as $record ) {
							if( isset( $record[ 'imageId' ] ) ) {
								$workOn = 'imageId';
								$ids[ $record[ 'imageId' ] ] = 'found';
							} else if( isset( $record[ 'videoId' ] ) ) {
								$workOn = 'videoId';
								$ids[ $record[ 'videoId' ] ] = 'found';
							} else if( isset( $record[ 'assetId' ] ) ) {
								$workOn = 'assetId';
								$ids[ $record[ 'assetId' ] ] = 'found';
							}
						}
						$ids = array_keys( $ids );
						$expandableDbObj = new $class( 'delete' );
						foreach( $ids as $id ) {
							$expandableDbObj->set( "$workOn||$id" );
						}
						break;
					} case 'Cms_Db_StoryAsset' : {
						foreach( $records as $record ) {
							$storyAssetDbObj = new Cms_Db_StoryAsset( 'delete' );
							$storyId = $record[ 'storyId' ];
							$storyAssetDbObj->set( "storyId||$storyId" );
						}
						break;
					} case 'Cms_Db_ImageDimension' : {
						$uris = array();
						foreach( $records as $record ) {
							$uris[] = $record[ 'imageUri' ];
						}
						$imageDimensionObj = new Cms_Db_ImageDimension( 'delete' );
						foreach( $uris as $uri ) {
							$imageDimensionObj->set( "imageUri||$uri" );
						}
						break;
					}
				}
				foreach( $records as $record ) {
					$classObj->fillData( $record );
				}
			}
			$this->_tableData = array();
		}

		public function pushImage( $imageId, $immediatePush = false ) {
			$imageObj = new Editorial_Image( $imageId );
			$imageVars = $imageObj->varDump();
			$this->_tableData[ 'cmsImage' ][] = array(
				'imageId' => $imageVars[ 'imageId' ],
				'clientId' => $imageVars[ 'clientId' ],
				'imageUri' => $imageVars[ 'imageUri' ],
				'imageTitle' => $imageVars[ 'imageTitle' ],
				'imageDescription' => $imageVars[ 'imageDescription' ],
				'imageKeywords' => $imageVars[ 'imageKeywords' ],
				'imageWidth' => $imageVars[ 'imageWidth' ],
				'imageHeight' => $imageVars[ 'imageHeight' ],
				'imageCredit' => $imageVars[ 'imageCredit' ],
				'imageSourceDomain' => $imageVars[ 'imageSourceDomain' ],
				'imageCopyright' => $imageVars[ 'imageCopyright' ],
				'imageStatus' => $imageVars[ 'imageStatus' ],
				'createdUserId' => $imageVars[ 'createdUserId' ],
				'createdTime' => $imageVars[ 'createdTime' ]
			);
			$this->_getImageDimensionData( $imageId );
			//$this->_getImageExpandableData( $imageId );
			if( $immediatePush ) {
				$this->push();
			}
		}

		private function _getAssetDossiers( $assetId, $assetTypeId ) {
			$this->_tableData[ 'cmsStoryAsset' ] = array();
			$dossierAssetDbObj = new Editorial_Db_DossierAsset();
			$dossierAssetDbObj->set( "assetId||$assetId", 'quantity||100', 'sort||N', "assetTypeId||$assetTypeId" );
			$assetsCount = $dossierAssetDbObj->getResultCount();
			for( $i =0; $i < $assetsCount; $i++ ) {
				$relInfo = $dossierAssetDbObj->getRecord( $i );
				$assetTypeObj = new Network_AssetType( $relInfo[ 'assetTypeId' ] );
				$this->_tableData[ 'cmsStoryAsset' ][] = array(
					'storyAssetRelId' => $relInfo[ 'dossierAssetRelId' ],
					'clientId' => $relInfo[ 'clientId' ],
					'publicationId' => $relInfo[ 'publicationId' ],
					'storyId' => $relInfo[ 'dossierId' ],
					'assetId' => $relInfo[ 'assetId' ],
					'assetTypeId' => $relInfo[ 'assetTypeId' ],
					'assetTypeName' => $assetTypeObj->getAssetTypeName(),
					'createdUserId' => $relInfo[ 'createdUserId' ],
					'createdTime' => $relInfo[ 'createdTime' ],
					'storyAssetActive' => $relInfo[ 'dossierAssetActive' ]
				);
			}
		}

		private function _getImageExpandableData( $imageId ) {
			$imageExpandableObj = new Editorial_Db_ImageExpandable();
			$imageExpandableObj->set( "imageId||$imageId", 'quantity||1000' );
			$recordsCount = $imageExpandableObj->getResultCount();
			for( $i = 0; $i < $recordsCount; $i++ ) {
				$this->_tableData[ 'cmsImageExpandable' ][] = $imageExpandableObj->getRecord( $i );
			}

			$imageExpandableObj = new Editorial_Db_ImageExpandableText();
			$imageExpandableObj->set( "imageId||$imageId", 'quantity||1000' );
			$recordsCount = $imageExpandableObj->getResultCount();
			for( $i = 0; $i < $recordsCount; $i++ ) {
				$this->_tableData[ 'cmsImageExpandableText' ][] = $imageExpandableObj->getRecord( $i );
			}
		}

		private function _getImageDimensionData( $imageId ) {
			$imageDimensionDbObj = new Editorial_Db_ImageDimension();
			$imageDimensionDbObj->set( "imageId||$imageId", 'quantity||1000' );
			$recordsCount = $imageDimensionDbObj->getResultCount();
			for( $i = 0; $i < $recordsCount; $i++ ) {
				$this->_tableData[ 'cmsImageDimension' ][] = $imageDimensionDbObj->getRecord( $i );
			}
		}

		private function _generateStoryData( $dossierId ) {
			$storyObj = false;
			try {
				$storyObj = new Editorial_DossierStory( $dossierId );
			} catch( Exception $ex ) {
				describe( $ex, true );
				return false;
			}

			$dossier = new Editorial_Dossier( $dossierId );

			$createdTime = $storyObj->getCreatedTime();
			$storyCreatedUserId = $storyObj->getCreatedUserId();
			$storyVars = $storyObj->varDump();

			$userObj = new Network_User( $storyCreatedUserId );
			$sectionId = $storyVars[ 'sectionId' ];
			$this->_tableData[ 'cmsPublication' ] = $this->_tableData[ 'cmsSection' ] = array();

			$this->_tableData[ 'cmsSlug' ] = array();
			$this->_tableData[ 'cmsStory' ] = array( 0 => array(
				'storyId' => $dossierId,
				'clientId' => $storyVars[ 'clientId' ],
				'authorId' => $storyCreatedUserId,
				'authorLogin' => $userObj->getUserLogin(),
				'authorByLine' => $userObj->fullName(),
				'keyword' => $storyVars[ 'keyword' ],
				'assetId' => $storyVars[ 'assetId' ],
				'oldStoryId' => $dossier->getOldDossierId(),
				'publicationId' => $storyVars[ 'publicationId' ],
				'storyTemplateId' => $storyVars[ 'templateId' ],
				'firstCategoryId' => $storyVars[ 'firstCategoryId' ],
				'secondCategoryId' => $storyVars[ 'secondCategoryId' ],
				'thirdCategoryId' => $storyVars[ 'thirdCategoryId' ],
				'storyPublished' => 'Y',
				'viewType' => $storyVars[ 'viewType' ],
				'moderationStatus' => $storyVars[ 'moderationStatus' ],
				'moderatorUserId' => $storyVars[ 'moderatorUserId' ],
				'moderationTime' => $storyVars[ 'moderationTime' ],
				'countListItem' => $storyVars[ 'countListItem' ],
				'countComment' => $storyVars[ 'countComment' ],
				'voteUp' => $storyVars[ 'voteUp' ],
				'voteDown' => $storyVars[ 'voteDown' ],
				'netVote' => $storyVars[ 'netVote' ],
				'updatedTime' => $storyVars[ 'updatedTime' ],
				'updatedUserId' => $storyVars[ 'updatedUserId' ],
				'moduleType' => $storyVars[ 'moduleType' ],
				'productClusterId' => $storyVars[ 'productClusterId' ],
				'storyType' => $storyVars[ 'storyType' ],
				'createdTime' => $createdTime,
				'storyTitle' => $storyVars[ 'storyTitle' ],
				'storySlug' => $storyVars[ 'storySlug' ],
				'storyExcerpt' => $storyVars[ 'storyExcerpt' ],
				'storyTags' => $storyVars[ 'storyTags' ],
				'canonicalUrl' => $storyVars[ 'canonicalUrl' ],
				'sectionId' => $storyVars[ 'sectionId' ],
				'primaryImagePath' => $storyVars[ 'storyPrimaryImagePath' ],
				'yearmonth' => str_replace( '-', '', substr( $createdTime, 0, 7 ) ),
				'storyYear' => substr( $createdTime, 0, 4 )
			));

			if( $storyVars[ 'productClusterId' ] > 0 ) {
				$this->_getProductClusterData( $storyVars[ 'productClusterId' ] );
			}

			$this->_getSlugData( $storyVars[ 'storySlug' ], $storyVars[ 'publicationId' ] );

			$this->_tableData[ 'cmsStoryContent' ] = array( 0 => array(
				'storyId' => $dossierId,
				'storyYear' => substr( $storyVars[ 'createdTime' ], 0, 4 ),
				'clientId' => $storyVars[ 'clientId' ],
				'storyWebContent' => $storyVars[ 'storyWebContent' ],
				'createdTime' => $storyVars[ 'createdTime' ]
			));
			$this->_getPublicationData( $storyObj->getPublicationId() );
			$this->_getSectionData( $sectionId );
			if( $storyObj->getStoryType() == 'shopping-guide' ) {
				$this->_getShoppingGuideData( $dossierId );
				return false;
			} else if( $storyObj->getStoryType() == 'listical' || $storyObj->getStoryType() == 'userlist' ) {
				$this->_getListicalData( $dossierId );
				return false;
			}
		}

		private function _generateQnaData( $dossierId ) {
			$questionObj = new Editorial_Question( $dossierId );
			$this->_tableData[ 'cmsQuestion' ][ 0 ] = array(
				'questionId' => $questionObj->getDossierId(),
				'clientId' => $questionObj->getClientId(),
				'questionTitle' => $questionObj->getQuestionTitle(),
				'questionSlug' => $questionObj->getQuestionSlug(),
				'publicationId' => $questionObj->getPublicationId(),
				'firstCategoryId' => $questionObj->getFirstCategoryId(),
				'secondCategoryId' => $questionObj->getSecondCategoryId(),
				'thirdCategoryId' => $questionObj->getThirdCategoryId(),
				'assetId' => $questionObj->getAssetId(),
				'questionPrimaryImagePath' => $questionObj->getQuestionPrimaryImagePath(),
				'questionTags' => $questionObj->getQuestionTags(),
				'parentDossierId' => $questionObj->getParentDossierId(),
				'autoDiscussion' => $questionObj->getAutoDiscussion(),
				'questionType' => $questionObj->getQuestionType(),
				'review' => $questionObj->getReview(),
				'keyword' => $questionObj->getKeyword(),
				'postingOrientation' => $questionObj->getPostingOrientation(),
				'questionStatus' => $questionObj->getQuestionStatus(),
				'totalAnswers' => $questionObj->getTotalAnswers(),
				'discussionStatus' => $questionObj->getDiscussionStatus(),
				'stickyStatus' => $questionObj->getStickyStatus(),
				'updatedTime' => $questionObj->getUpdatedTime(),
				'updatedUserId' => $questionObj->getUpdatedUserId(),
				'autoUserId' => $questionObj->getAutoUserId(),
				'createdUserId' => $questionObj->getCreatedUserId(),
				'createdTime' => $questionObj->getCreatedTime()
			);

			$this->_getSlugData( $questionObj->getQuestionSlug(), $questionObj->getPublicationId() );

			$this->_tableData[ 'cmsQuestionContent' ][ 0 ] = array(
				'questionId' => $questionObj->getDossierId(),
				'questionYear' => $questionObj->getQuestionYear(),
				'clientId' => $questionObj->getClientId(),
				'publicationId' => $questionObj->getPublicationId(),
				'questionContent' => $questionObj->getQuestionContent(),
				'createdTime' => $questionObj->getCreatedTime()
			);

			$this->_tableData[ 'cmsAnswer' ] = array();
			$this->_tableData[ 'cmsAnswerContent' ] = array();
			$answersObj = new Editorial_Answers( 'dossierId||' . $dossierId );
			foreach( $answersObj() as $answerObj ) {
				$this->_tableData[ 'cmsAnswer' ][] = array(
					'answerId' => $answerObj->getAnswerId(),
					'clientId' => $answerObj->getClientId(),
					'questionId' => $answerObj->getDossierId(),
					'publicationId' => $answerObj->getPublicationId(),
					'answerStatus' => $answerObj->getAnswerStatus(),
					'plagiarismPercentage' => $answerObj->getPlagiarismPercentage(),
					'autoUserId' => $answerObj->getAutoUserId(),
					'createdUserId' => $answerObj->getCreatedUserId(),
					'createdTime' => $answerObj->getCreatedTime()
				);
				$this->_tableData[ 'cmsAnswerContent' ][] = array(
					'answerId' => $answerObj->getAnswerId(),
					'questionId' => $answerObj->getDossierId(),
					'answerYear' => $answerObj->getAnswerYear(),
					'clientId' => $answerObj->getClientId(),
					'publicationId' => $answerObj->getPublicationId(),
					'answerContent' => $answerObj->getAnswerContent(),
					'createdTime' => $answerObj->getCreatedTime(),
				);
			}
		}

		private function _getClusterData() {
			$this->_tableData[ 'cmsCluster' ] = array();
			$clusterDbObj = new Editorial_Db_Cluster();
			$clusterDbObj->set( 'publicationId||' . PUBLICATION_ID, 'count||Y' );
			$records = $clusterDbObj->getTotalCount();
			$clusterDbObj = new Editorial_Db_Cluster();
			$clusterDbObj->set( 'publicationId||' . PUBLICATION_ID, "quantity||$records" );
			for( $i = 0; $i < $records; $i++ ) {
				$this->_tableData[ 'cmsCluster' ][] = $clusterDbObj->getRecord( $i );
			}

			$this->_tableData[ 'cmsClusterStory' ] = array();
			$clusterStoryDbObj = new Editorial_Db_ClusterStory();
			$clusterStoryDbObj->set( 'publicationId||' . PUBLICATION_ID, 'count||Y' );
			$records = $clusterStoryDbObj->getTotalCount();
			$clusterStoryDbObj = new Editorial_Db_ClusterStory();
			$clusterStoryDbObj->set( 'publicationId||' . PUBLICATION_ID, "quantity||$records" );
			for( $i = 0; $i < $records; $i++ ) {
				$this->_tableData[ 'cmsClusterStory' ][] = $clusterStoryDbObj->getRecord( $i );
			}
		}

		private function _getAssetsData( $dossierId ) {
			$dossierAssetDbObj = new Editorial_Db_DossierAsset();
			$this->_tableData[ 'cmsStoryAsset' ] = array();
			$this->_tableData[ 'cmsImage' ] = array();
			$this->_tableData[ 'cmsImageExpandable' ] = array();
			$this->_tableData[ 'cmsImageExpandableText' ] = array();
			$this->_tableData[ 'cmsImageDimension' ] = array();
			$this->_tableData[ 'cmsVideo' ] = array();
			$this->_tableData[ 'cmsVideoExpandable' ] = array();
			$this->_tableData[ 'cmsVideoExpandableText' ] = array();
			$this->_tableData[ 'cmsAsset' ] = array();
			$this->_tableData[ 'cmsAssetExpandable' ] = array();
			$this->_tableData[ 'cmsAssetExpandableText' ] = array();
			$dossierAssetDbObj->set( 'dossierId||' . $dossierId, 'quantity||100', 'sortColumn||assetTypeId', 'sortOrder||asc' );
			$assetsCount = $dossierAssetDbObj->getResultCount();
			for( $i =0; $i < $assetsCount; $i++ ) {
				$relInfo = $dossierAssetDbObj->getRecord( $i );
				$assetTypeObj = new Network_AssetType( $relInfo[ 'assetTypeId' ] );
				$this->_tableData[ 'cmsStoryAsset' ][] = array(
					'storyAssetRelId' => $relInfo[ 'dossierAssetRelId' ],
					'clientId' => $relInfo[ 'clientId' ],
					'publicationId' => $relInfo[ 'publicationId' ],
					'storyId' => $relInfo[ 'dossierId' ],
					'assetId' => $relInfo[ 'assetId' ],
					'assetTypeId' => $relInfo[ 'assetTypeId' ],
					'assetTypeName' => $assetTypeObj->getAssetTypeName(),
					'createdUserId' => $relInfo[ 'createdUserId' ],
					'createdTime' => $relInfo[ 'createdTime' ],
					'storyAssetActive' => $relInfo[ 'dossierAssetActive' ]
				);
				switch( $dossierAssetDbObj->getAssetTypeId( $i ) ) {
					case 1 : {
						$this->pushImage( $dossierAssetDbObj->getAssetId( $i ) );
						break;
					} case 2 : {
						$this->_getVideoData( $dossierAssetDbObj->getAssetId( $i ) );
						break;
					} default : {
						$this->_getAssetData( $dossierAssetDbObj->getAssetId( $i ) );
					}
				}
			}
			$this->_getClusterData();
		}

		private function _getVideoData( $videoId ) {
			$videoObj = new Editorial_Video( $videoId );
			$videoVars = $videoObj->varDump();
			$this->_tableData[ 'cmsVideo' ][] = array(
				'videoId' => $videoVars[ 'videoId' ],
				'clientId' => $videoVars[ 'clientId' ],
				'videoTitle' => $videoVars[ 'videoTitle' ],
				'videoDescription' => $videoVars[ 'videoDescription' ],
				'videoKeywords' => $videoVars[ 'videoKeywords' ],
				'videoCredit' => $videoVars[ 'videoCredit' ],
				'videoSourceDomain' => $videoVars[ 'videoSourceDomain' ],
				'videoCopyright' => $videoVars[ 'videoCopyright' ],
				'videoStatus' => $videoVars[ 'videoStatus' ],
				'videoSocialKey' => $videoVars[ 'videoSocialKey' ],
				'createdUserId' => $videoVars[ 'createdUserId' ],
				'createdTime' => $videoVars[ 'createdTime' ]
			);
			$this->_getVideoExpandableData( $videoId );
		}

		private function _getVideoExpandableData( $videoId ) {
			$videoExpandableObj = new Editorial_Db_VideoExpandable();
			$videoExpandableObj->set( "videoId||$videoId", 'quantity||1000' );
			$recordsCount = $videoExpandableObj->getResultCount();
			for( $i = 0; $i < $recordsCount; $i++ ) {
				$this->_tableData[ 'cmsVideoExpandable' ][] = $videoExpandableObj->getRecord( $i );
			}

			$videoExpandableObj = new Editorial_Db_VideoExpandableText();
			$videoExpandableObj->set( "videoId||$videoId", 'quantity||1000' );
			$recordsCount = $videoExpandableObj->getResultCount();
			for( $i = 0; $i < $recordsCount; $i++ ) {
				$this->_tableData[ 'cmsVideoExpandableText' ][] = $videoExpandableObj->getRecord( $i );
			}
		}

		private function _getAssetData( $assetId ) {
			$assetObj = new Editorial_Asset( $assetId );
			$assetVars = $assetObj->varDump();
			$this->_tableData[ 'cmsAsset' ][] = array(
				'assetId' => $assetVars[ 'assetId' ],
				'clientId' => $assetVars[ 'clientId' ],
				'assetTitle' => $assetVars[ 'assetTitle' ],
				'assetSlug' => $assetVars[ 'assetSlug' ],
				'assetDescription' => $assetVars[ 'assetDescription' ],
				'assetKeywords' => $assetVars[ 'assetKeywords' ],
				'assetTypeId' => $assetVars[ 'assetTypeId' ],
				'assetPrimaryImagePath' => $assetVars[ 'assetPrimaryImagePath' ],
				'assetElementsOrder' => $assetVars[ 'assetElementsOrder' ],
				'assetStatus' => $assetVars[ 'assetStatus' ],
				'createdUserId' => $assetVars[ 'createdUserId' ],
				'createdTime' => $assetVars[ 'createdTime' ]
			);
			$this->_getSlugData( $assetVars[ 'assetSlug' ] );
			$this->_getAssetExpandableData( $assetId, $assetVars[ 'assetTypeId' ] );
		}

		private function _getSlugData( $slug, $publicationId = 0 ) {
			$slugsObj = false;
			if( $publicationId > 0 ) {
				$slugsObj = new Network_Slugs( "slug||$slug", "publicationId||$publicationId" );
			} else {
				$slugsObj = new Network_Slugs( "slug||$slug" );
			}
			foreach( $slugsObj() as $slugObj ) {
				$records = $slugsObj->varDump();
				$this->_tableData[ 'cmsSlug' ][] = $records[ 0 ];
			}
		}

		private function _getAssetExpandableData( $assetId, $assetTypeId ) {
			$assetExpandableObj = new Editorial_Db_AssetExpandable();
			$assetExpandableObj->set( "assetId||$assetId", 'quantity||1000' );
			$recordsCount = $assetExpandableObj->getResultCount();
			for( $i = 0; $i < $recordsCount; $i++ ) {
				$record = $assetExpandableObj->getRecord( $i );
				$record[ 'assetTypeId' ] = $assetTypeId;
				$this->_tableData[ 'cmsAssetExpandable' ][] = $record;
				if( $record[ 'xElementId' ] == 3 || $record[ 'xElementId' ] == 7 ) {
					$this->pushImage( $record[ 'xElementValue' ] );
				}
			}

			$assetExpandableObj = new Editorial_Db_AssetExpandableText();
			$assetExpandableObj->set( "assetId||$assetId", 'quantity||1000' );
			$recordsCount = $assetExpandableObj->getResultCount();
			for( $i = 0; $i < $recordsCount; $i++ ) {
				$record = $assetExpandableObj->getRecord( $i );
				$record[ 'assetTypeId' ] = $assetTypeId;
				$this->_tableData[ 'cmsAssetExpandableText' ][] = $record;
			}
		}

		private function _getTagData() {
			$tags = explode( ',', $this->_tableData[ 'cmsStory' ][ 0 ][ 'storyTags' ] );
			$this->_tableData[ 'cmsTag' ] = array();
			$this->_tableData[ 'cmsTagExpandable' ] = array();
			$this->_tableData[ 'cmsTagGroup' ] = array();
			$tagGroups = array();
			$actualTags = array();
			$doneTags = array();
			foreach( $tags as $tag ) {
				$tagSlug = substr( $tag, 0, 1 ) == '#' ? 'ib-spl-' . Helper::sanitizeWithDashes( $tag ) : Helper::sanitizeWithDashes( $tag );
				$tagsObj = new Network_Tags( "tagSlug||$tagSlug" );
				if( $tagsObj->getResultCount() > 0 ) {
					if( array_search( $tagsObj->getTagId(), $doneTags ) !== false ) {
						continue;
					}
					$actualTags[] = $tagsObj->getTagName();
					$doneTags[] = $tagsObj->getTagId();
					$tagObj = $tagsObj->get( 0 );
					$this->_tableData[ 'cmsTag' ][] = $tagObj->varDump();
					if( $tagObj->getTagGroupId() > 0 ) {
						$tagGroups[] = $tagObj->getTagGroupId();
					}
					$this->_getTagExpandableData( $tagObj->getTagId() );
				} else {
					$actualTags[] = $tag;
					$tagDbObj = new Network_Db_Tag( 'add' );
					$tagDbObj->set( "tagName||$tag" );
					$tagId = $tagDbObj->getLastInsertedId();
					$tagObj = new Network_Tag( $tagId );
					$this->_tableData[ 'cmsTag' ][] = $tagObj->varDump();
				}
			}
			$this->_tableData[ 'cmsStory' ][ 0 ][ 'storyTags' ] = implode( ',', $actualTags );
		}

		private function _getTagGroupData( $tagGroups ) {
			foreach( $tagGroups as $tagGroupId ) {
				$tagGroupDbObj = new Network_Db_TagGroup();
				$tagGroupDbObj->set( "tagGroupId||$tagGroupId" );
				if( $tagGroupDbObj->getResultCount() > 0 ) {
					$this->_tableData[ 'cmsTagGroup' ][] = $tagGroupDbObj->getRecord( 0 );
				}
			}
		}

		private function _getTagExpandableData( $tagId ) {
			$tagExpandableDbObj = new Network_Db_TagExpandable();
			$tagExpandableDbObj->set( "tagId||$tagId", 'quantity||1000' );
			$recordsCount = $tagExpandableDbObj->getResultCount();

			for( $i = 0; $i < $recordsCount; $i++ ) {
				$this->_tableData[ 'cmsTagExpandable' ][] = $tagExpandableDbObj->getRecord( $i );
			}
		}

		private function _getListicalData( $dossierId ) {
			$listItems = new Editorial_ListItems( 'dossierId||' . $dossierId, 'quantity||1000' );
			$this->_tableData[ 'cmsListItem' ] = array();
			$this->_tableData[ 'cmsListItemText' ] = array();
			$getImage = true;
			foreach( $listItems() as $listItem ) {
				$this->_tableData[ 'cmsListItem' ][] = array(
					'listItemId' => $listItem->getListItemId(),
					'itemSlug' => $listItem->getItemSlug(),
					'clientId' => $listItem->getClientId(),
					'publicationId' => $listItem->getPublicationId(),
					'storyId' => $listItem->getDossierId(),
					'listitemDossierId' => $listItem->getListitemDossierId(),
					'moderationStatus' => $listItem->getModerationStatus(),
					'moderatorUserId' => $listItem->getModeratorUserId(),
					'moderationTime' => $listItem->getModerationTime(),
					'voteUp' => $listItem->getVoteUp(),
					'voteDown' => $listItem->getVoteDown(),
					'netVote' => $listItem->getNetVote(),
					'title' => $listItem->getTitle(),
					'assetId' => $listItem->getAssetId(),
					'serialNumber' => $listItem->getSerialNumber(),
					'createdUserId' => $listItem->getCreatedUserId(),
					'createdTime' => $listItem->getCreatedTime()
				);
				if( $listItem->getAssetId() > 0 ) {
					$this->_getAssetData( $listItem->getAssetId() );
					if( $getImage ) {
						$asset = new Editorial_Asset( $listItem->getAssetId() );
						$imageUri = $asset->getGalleryImageUri( 1 );
						if( $imageUri ) {
							$dossier = new Editorial_Dossier( $dossierId );
							$dossier->update( "dossierPrimaryImagePath||$imageUri" );
							$story = new Editorial_DossierStory( $dossierId );
							$story->update( "storyPrimaryImagePath||$imageUri" );
							$this->_tableData[ 'cmsStory' ][ 0 ][ 'primaryImagePath' ] = $imageUri;
							$getImage = false;
						}
					}
				}
				$this->_tableData[ 'cmsListItemText' ][] = array(
					'listItemId' => $listItem->getListItemId(),
					'clientId' => $listItem->getClientId(),
					'publicationId' => $listItem->getPublicationId(),
					'storyId' => $listItem->getDossierId(),
					'listDescription' => $listItem->getListDescription(),
					'createdUserId' => $listItem->getCreatedUserId(),
					'createdTime' => $listItem->getCreatedTime()
				);
			}
		}

		private function _getPublicationData( $publicationId ) {
			if( !$publicationId ) {
				return true;
			}
			$publicationObj = new Network_Publication( $publicationId );
			$this->_tableData[ 'cmsPublication' ][] = $publicationObj->varDump();
		}

		private function _getSectionData( $sectionId ) {
			if( !$sectionId ) {
				return true;
			}
			$sectionObj = new Network_Section( $sectionId );
			$this->_tableData[ 'cmsSection' ][] = $sectionObj->varDump();
		}

		private function _getProductClusterData( $productClusterId ) {
			$productClusterObj = new Network_Db_ProductCluster();
			$productClusterObj->set( "productClusterId||$productClusterId" );
			$this->_tableData[ 'cmsProductCluster' ][] = $productClusterObj->getRecord( 0 );
		}

		private function _getProductClassData( $productClassId ) {
			$productClassObj = new Network_Db_ProductClass();
			$productClassObj->set( "productClassId||$productClassId" );
			$this->_tableData[ 'cmsProductClass' ][] = $productClassObj->getRecord( 0 );
		}

		private function _getProductClassRatingParameterData( $productClassRatingParameterId ) {
			$productClassObj = new Network_Db_ProductClassRatingParameter();
			$productClassObj->set( "productClassRatingParameterId||$productClassRatingParameterId" );
			$this->_tableData[ 'cmsProductClassRatingParameter' ][] = $productClassObj->getRecord( 0 );
		}

		private function _getShoppingGuideProductData( $totalProducts, $dossierId ) {
			$productsObj = new Editorial_ShoppingGuideProducts( 'dossierId||'. $dossierId, "quantity||$totalProducts" );
			$getImage = true;
			foreach( $productsObj() as $productObj ) {
				$productData = $productObj->varDump();
				$this->_tableData[ 'cmsShoppingGuideProduct' ][] = array(
					'shoppingGuideProductId' => $productData[ 'shoppingGuideProductId' ],
					'clientId' => $productData[ 'clientId' ],
					'publicationId' => $productData[ 'publicationId' ],
					'storyId' => $productData[ 'dossierId' ],
					'productName' => $productData[ 'productName' ],
					'productBrandName' => $productData[ 'productBrandName' ],
					'assetId' => $productData[ 'assetId' ],
					'productPrice' => $productData[ 'productPrice' ],
					'priceLink' => $productData[ 'priceLink' ],
					'serialNumber' => $productData[ 'serialNumber' ],
					'ratingParameter1' => $productData[ 'ratingParameter1' ],
					'ratingParameter2' => $productData[ 'ratingParameter2' ],
					'ratingParameter3' => $productData[ 'ratingParameter3' ],
					'ratingParameter4' => $productData[ 'ratingParameter4' ],
					'ratingParameter5' => $productData[ 'ratingParameter5' ],
					'averageRating' => $productData[ 'averageRating' ],
					'createdUserId' => $productData[ 'createdUserId' ],
					'createdTime' => $productData[ 'createdTime' ]
				);
				$this->_tableData[ 'cmsShoppingGuideProductText' ][] = array(
					'shoppingGuideProductId' => $productData[ 'shoppingGuideProductId' ],
					'clientId' => $productData[ 'clientId' ],
					'publicationId' => $productData[ 'publicationId' ],
					'storyId' => $productData[ 'dossierId' ],
					'productDescription' => isset( $productData[ 'productDescription' ] ) ? $productData[ 'productDescription' ] : '',
					'technicalSpecification' => isset( $productData[ 'technicalSpecification' ] ) ? $productData[ 'technicalSpecification' ] : '',
					'productUsp' => isset( $productData[ 'productUsp' ] ) ? $productData[ 'productUsp' ] : '',
					'productPros' => isset( $productData[ 'productPros' ] ) ? $productData[ 'productPros' ] : '',
					'productCons' => isset( $productData[ 'productCons' ] ) ? $productData[ 'productCons' ] : '',
					'whereToBuy' => isset( $productData[ 'whereToBuy' ] ) ? $productData[ 'whereToBuy' ] : '',
					'createdUserId' => $productData[ 'createdUserId' ],
					'createdTime' => $productData[ 'createdTime' ],
				);
				if( $productData[ 'assetId' ] > 0 ) {
					$this->_getAssetData( $productData[ 'assetId' ] );
					if( $getImage ) {
						$asset = new Editorial_Asset( $productData[ 'assetId' ] );
						$imageUri = $asset->getGalleryImageUri( 1 );
						if( $imageUri ) {
							$dossier = new Editorial_Dossier( $dossierId );
							$dossier->update( "dossierPrimaryImagePath||$imageUri" );
							$story = new Editorial_DossierStory( $dossierId );
							$story->update( "storyPrimaryImagePath||$imageUri" );
							$this->_tableData[ 'cmsStory' ][ 0 ][ 'primaryImagePath' ] = $imageUri;
							$getImage = false;
						}
					}
				}
			}
		}

		private function _getShoppingGuideData( $dossierId ) {
			$dossierObj = new Editorial_Dossier( $dossierId );
			$dossierData = $dossierObj->varDump();

			$this->_tableData[ 'cmsShoppingGuide' ][] = array(
				'storyId' => $dossierData[ 'dossierId' ],
				'clientId' => $dossierData[ 'clientId' ],
				'publicationId' => $dossierData[ 'publicationId' ],
				'totalProducts' => $dossierData[ 'totalProducts' ],
				'productClassId' => $dossierData[ 'productClassId' ],
				'ratingParameter1' => $dossierData[ 'ratingParameter1' ],
				'ratingParameter2' => $dossierData[ 'ratingParameter2' ],
				'ratingParameter3' => $dossierData[ 'ratingParameter3' ],
				'ratingParameter4' => $dossierData[ 'ratingParameter4' ],
				'ratingParameter5' => $dossierData[ 'ratingParameter5' ],
				'pros' => $dossierData[ 'pros' ],
				'cons' => $dossierData[ 'cons' ],
				'technicalSpecification' => $dossierData[ 'technicalSpecification' ],
				'createdUserId' => $dossierData[ 'createdUserId' ],
				'createdTime' => $dossierData[ 'createdTime' ]
			);

			$this->_getShoppingGuideProductData( $dossierData[ 'totalProducts' ] );
			

			for( $j = 1; $j < 6; $j++ ) {
				if( !$dossierData[ 'ratingParameter' . $j ] ) {
					break;
				} else {
					$this->_getProductClassRatingParameterData( $dossierData[ 'ratingParameter' . $j ] );
				}
			}

			$this->_getProductClassData( $dossierData[ 'productClassId' ] );
		}

		public function __call( $functionName, $argumentsArray ) {
			$lookFor = '_' . lcfirst( substr( $functionName, 3 ) );
			if( isset( $this->$lookFor ) ) {
				return $this->$lookFor;
			}
			throw new Exception( "Call to undefined function '$functionName'!" );
		}
	 }