<?php

	class Cms_CmsPullManager {

		private $_data;
		private $_storyId;
		private $_yearMonth;
		private $_storyTime;
		private $_authorId;
		private $_storyType = 'story';
		private $_sendToFacebook = false;

		private function _publishToFacebook() {
			/*if(PUBLICATION_ID == 793)
				return true;*/
			
			$storyObj = new Cms_Story( $this->_storyId );
			$link = urlencode( $storyObj->getPermalink() );
			$description = urlencode( strip_tags( $storyObj->getShortContent() ) );
			$publicationId = urlencode( PUBLICATION_ID );
			$picture = urlencode( S3_IMAGE_LOCATION . $storyObj->getPrimaryImagePath() );
			$message = urlencode( $storyObj->getStoryTitle() );

			//http://instacheckin.com/facebookpagepost
			//http://0xee1.info/postpage
			$url = "http://instacheckin.com/facebookpagepost?publicationId=$publicationId&link=$link&description=$description&picture=$picture&message=$message";
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			$result = curl_exec( $ch );
			curl_close( $ch );

			/*
			$mail = new Instapress_Core_Mail();
			$mail->setFrom( 'prashant@instablogs.com', "Prashant Thakur" )->addRecipient( 'prashant.thakur@instamedia.com', 'Prashant Thakur' );
			$mail->bcc( 'rashid.mohamad@instamedia.com', 'Rashid Mohamad' );
			$mail->buildMail( 'After Publishing "' . $storyObj->getStoryTitle() . '"' , "$url\n\n$result", "<p>$url</p><br /><br /><p>$result</p>" );
			$mail->sendMail();
			*/
		}

		function __construct( $data )  {
			ob_start();
			$this->_data = unserialize( $data );
			//describe( $this->_data, true );
			if( !$this->_data ) {
				ob_end_clean();
				throw new Exception( 'Unable to decode generated data! Error: ' . $data );
			} else {
				$this->_storyType = isset( $this->_data[ 'cmsStory' ] ) ? 'story' : 'question';
				$this->_storyId = $this->_storyType == 'story' ? $this->_data[ 'cmsStory' ][ 0 ][ 'storyId' ] : $this->_data[ 'cmsQuestion' ][ 0 ][ 'questionId' ];
				if( isset( $this->_data[ 'cmsStory' ][ 0 ][ 'unpublishDossier' ] ) ) {
					$this->_unpublishStory();
				} else if( isset( $this->_data[ 'cmsStory' ] ) ) {
					$this->_yearMonth = $this->_data[ 'cmsStory' ][ 0 ][ 'yearmonth' ];
					$this->_storyTime = $this->_data[ 'cmsStory' ][ 0 ][ 'createdTime' ];
					$this->_authorId = $this->_data[ 'cmsStory' ][ 0 ][ 'authorId' ];
					$latestStoryByAuthor = new Cms_Stories( 'authorId||' . $this->_authorId, 'quantity||1' );
					$totalStories = $latestStoryByAuthor->getTotalCount();
					if( $latestStoryByAuthor->getResultCount() > 0 ) {
						if( $latestStoryByAuthor->getAuthorByLine( 0 ) != $this->_data[ 'cmsStory' ][ 0 ][ 'authorByLine' ] ) {
							$authorStories = new Cms_Stories( 'authorId||' . $this->_authorId, "quantity||$totalStories" );
							foreach( $authorStories() as $authorStory ) {
								if( !$authorStory->update( 'authorByLine||' . $this->_data[ 'cmsStory' ][ 0 ][ 'authorByLine' ] ) ) {
									describe( $authorStory->getMessage(), true );
								}
							}
						}
					}
					$this->_init();
					$storyObj = new Cms_Story( $this->_storyId );
					$duringThis = ob_get_contents();
					ob_end_clean();
					die( serialize( array( 'dossierId' => $this->_storyId, 'authorId' => $this->_authorId, 'permalink' => $storyObj->getPermalink(), 'sendMail' => $this->_sendToFacebook, 'inBetween' => $duringThis ) ) );
				} else if( isset( $this->_data[ 'cmsQuestion' ][ 0 ][ 'unpublishDossier' ] ) ) {
					$this->_unpublishQuestion();
				} else if( isset( $this->_data[ 'cmsQuestion' ] ) ) {
					$this->_init();
					$duringThis = ob_get_contents();
					ob_end_clean();
					$questionObj = new Cms_Question( $this->_storyId );
					$permalink = HOME_PATH . $questionObj->getQuestionSlug() . '.html';
					die( serialize( array( 'dossierId' => $this->_storyId, 'authorId' => 0, 'permalink' => $permalink, 'sendMail' => false, 'inBetween' => $duringThis ) ) );
				} else if( isset( $this->_data[ 'cmsDeeplink' ] ) ) {
					$this->_init();
					$duringThis = ob_get_contents();
					ob_end_clean();
					die( serialize( array( 'deeplinkId' => $this->_data[ 'cmsDeeplink' ][ 0 ][ 'deeplinkId' ], 'inBetween' => $duringThis ) ) );
				}
			}
			ob_end_clean();
		}

		private function _unpublishStory() {
			$storyObj = new Cms_Story( $this->_storyId );
			$storyObj->unpublish();
		}

		private function _unpublishQuestion() {
			$questionObj = new Cms_Question( $this->_storyId );
			$questionObj->unpublish();
		}

		private function _init() {
			foreach( $this->_data as $classInfo => $records ) {
				if( count( $records ) == 0 ) {
					continue;
				}
				$class = 'Cms_Db_' . str_ireplace( 'cms', '', $classInfo );
				$classObj = false;
				try {
					$classObj = new $class();
				} catch( Exception $ex ) {
					describe( $ex, true );
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
					} case 'Cms_Db_ClusterStory' : {
						$slusterStoryObj = new Cms_Db_ClusterStory();
						$slusterStoryObj->emptyTable();
						break;
					} case 'Cms_Db_StoryAsset' : {
						$storyAssetDbObj = new Cms_Db_StoryAsset( 'delete' );
						$storyId = $this->_storyId;
						$storyAssetDbObj->set( "storyId||$storyId" );
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
					} case 'Cms_Db_ListItem' :
					case 'Cms_Db_ListItemText' : {
						$classDeleteObj = new $class( 'delete' );
						$classDeleteObj->set( 'storyId||' . $this->_storyId );
						break;
					}
				}
				foreach( $records as $record ) {
					if( $class == 'Cms_Db_Story' ) {
						$this->_sendToFacebook = $classObj->fillData( $record ) == 'add';
					} else {
						$classObj->fillData( $record );
					}
				}
			}
			if( $this->_storyType == 'story' ) {
				$archiveDbObj = new Cms_Db_Archive( 'add' );
				$archiveDbObj->set( 'publicationId||' . PUBLICATION_ID, 'yearmonth||' . $this->_yearMonth );
				//$this->_addTagRecords();
				if( $this->_sendToFacebook ) {
					$this->_publishToFacebook();
				}
			}
		}

		private function _addTagRecords() {
			$storyTagDbObj = new Cms_Db_StoryTag( 'delete' );
			$storyTagDbObj->set( 'storyId||' . $this->_storyId );
			$storyTagDbObj = new Cms_Db_StoryTag( 'add' );
			foreach( $this->_data[ 'cmsTag' ] as $record ) {
				$storyTagDbObj->set( 'publicationId||' . PUBLICATION_ID, 'storyId||'. $this->_storyId, 'storyTime||' . $this->_storyTime, 'tagId||' . $record[ 'tagId' ] );
				$tagRelationDbObj = new Cms_Db_TagRelation( 'add' );
				foreach( $this->_data[ 'cmsTag' ] as $relRecord ) {
					if( $record[ 'tagId' ] == $relRecord[ 'tagId' ] ) {
						continue;
					}
					$tagRelationDbObj->set( 'tagId||' . $record[ 'tagId' ], 'relatedTagId||' . $relRecord[ 'tagId' ], 'publicationId||' . PUBLICATION_ID );
				}
			}
		}
	}
