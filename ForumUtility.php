<?php

	/*
	 * Created on Jan 20, 2012
	 *
	 * To change the template for this generated file go to
	 * Window - Preferences - PHPeclipse - PHP - Code Templates
	 */
	 
	 
	class Cms_ForumUtility {

		public static function createAsset( $pageVars ) {
			$assetId = $pageVars[ 'assetId' ];

			$pageVars[ 'imageTitle' ] = 'Forum Gallery Image ' . Helper::randomStringGenerator( 10, true );
			$pageVars[ 'imageDescription' ] = 'Forum gallery description';
			$pageVars[ 'imageKeywords' ] = 'keywords';
			$pageVars[ 'imageCredit' ] = 'credit';
			$pageVars[ 'imageCopyright' ] = 'copyright';
			$imageId = Editorial_AssetUtility::createImage( $pageVars );
			if( !$imageId ) {
				throw new Exception( Editorial_AssetUtility::getMessage() );
			} else {
				if( $pageVars[ 'assetId' ] == 0 ) {
					$assetId = Editorial_AssetUtility::createGallery( 'Question Gallery ' . Helper::randomStringGenerator( 10, true ), 'Question gallery description', 'gallery tags' );
					if( !$assetId ) {
						describe( Editorial_AssetUtility::getMessage(), true );
					}
				}
				$gallery = new Editorial_Asset( $assetId );
				$gallery->addGalleryImage( $imageId );
	
				// push image to cms
				$pushBridge = new Cms_PushUtility();
				$pushBridge->pushImage( $imageId, true );
	
				$libImageObj = new Instapress_Core_Image();
				$imageUri = S3_IMAGE_PUBLIC_LOCATION . $libImageObj->getScaledImage( $imageId, 126, 98 );
				return array(
					'assetId' => $assetId,
					'imageId' => $imageId,
					'imageUri' => $imageUri
				);
			}
		}

		public static function addForum( $pageVars ) {
			$forumType = $pageVars[ 'type' ];
			$title = $pageVars[ 'question' ];
			$description = $pageVars[ 'questionDetails' ];
			$firstCategoryId = $pageVars[ 'categories' ];
			$assetId = $pageVars[ 'assetId' ];
			$tags = $pageVars[ 'tags' ];
			$imagePath = '';
			if( $assetId > 0 ) {
				$asset = new Editorial_Asset( $assetId );
				try {
					$imagePath = $asset->getGalleryImageUri( 1 );
				} catch( Exception $ex ) {
					// do nothing
				}
			}
			$questionId = 0;
			try {
				$questionId = Editorial_Utility::addForum( $forumType, PUBLICATION_ID, $title, $description, $firstCategoryId,
					0, 0, 'M', USER_ID, $imagePath, 0, $assetId, $tags );
			} catch( Exception $ex ) {
				describe( $ex->getMessage(), true );
			}
			$dossier = new Editorial_Dossier( $questionId );
			if( $assetId > 0 ) {
				$dossier->addAsset( $assetId, 5 );
			}

			// push dossier to cms
			$pushBridge = new Cms_PushUtility();
			$pushBridge->pushDossier( $questionId, true );

			return HOME_PATH . $dossier->getStorySlug() . '.html';
		}

		public static function loginUser( $userId = 0 ) {
			$userId = ( !empty( $_COOKIE[ 'userId' ] ) ) ? $_COOKIE[ 'userId' ] : $userId;
			$user = new Instacheckin_User( $userId );

			try {
				$networkUser = new Network_User( $userId );
				Instapress_Core_Login::fillInfo( $networkUser, true );
				Instapress_Core_Login::$loginStatus = 1;
			} catch( Exception $ex ) {
				Instapress_Core_Login::fillInfo( $user, true );
			}
			defined( 'USER_TYPE' ) || define( 'USER_TYPE', $user->isModerator( PUBLICATION_ID )? 'moderator' : 'normal' );
		}

		public static function getUserProfileData( $userId ) {
			try {
				$user = new Instacheckin_User( $userId );
				$response = array();
				$fields = array( 'userId', 'userFirstName', 'userLastName', 'userLogin', 'userImage', 'userGender', 'userBirthDate', 'userEmail' );
				foreach( $fields as $field ) {
					$response[ $field ] = $user->{ 'get' . $field }();
				}
				$response[ 'name' ] = ucwords( $response[ 'userFirstName' ] . ' ' . $response[ 'userLastName' ] );
				$response[ 'isModerator' ] = $user->isModerator( PUBLICATION_ID );

				$userBadges = @new Gamification_Badges( Gamification_Utility::getUserBadges( $userId, PUBLICATION_ID, CLIENT_ID ) );
				$response[ 'userBadges' ] = $userBadges;
				$response = array_merge( $response, Gamification_Utility::getUserLevel( $userId, PUBLICATION_ID, CLIENT_ID ) );
				$response = array_merge( $response, Gamification_Utility::getUserTotalPoints( $userId, PUBLICATION_ID ) );
				return $response;
			} catch( Exception $ex ) {
				return false;
			}
		}
	}
