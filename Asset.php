<?php

	/**
	 * Singular Asset class.
	 *
	 * @author Rashid Moahamd <rashid.mohamad@instamedia.com>
	 * @version 1.0
	 * @copyright Instamedia
	 */
	class Cms_Asset extends Cms_AbstractSingular {

		protected $_foreignKey = 'assetId';
		protected $_dbClass = 'Asset';

		public function getPermalink() {
			switch( $this->getAssetTypeId() ) {
				case 5 : { // gallery
					if( Cms_Url::getUrlFileUri() == 'error_404.php' ) {
						return HOME_PATH . 'gallery/' . $this->getAssetSlug() . '/';
					} else {
						if( defined( 'OLD_ASSETS_UPTO' ) ) {
							if( $this->getAssetId() > OLD_ASSETS_UPTO ) {
								return HOME_PATH . $this->getAssetSlug() . '.html';
							} else {
								return HOME_PATH . 'gallery/' . $this->getAssetSlug() . '/';
							}
						} else {
							return HOME_PATH . $this->getAssetSlug() . '.html';
						}
					}
				} case 4 : { // slideshow
					if( Cms_Url::getUrlFileUri() == 'error_404.php' ) {
						return HOME_PATH . 'slideshow/' . $this->getAssetSlug() . '/';
					} else {
						if( defined( 'OLD_ASSETS_UPTO' ) ) {
							if( $this->getAssetId() > OLD_ASSETS_UPTO ) {
								return HOME_PATH . $this->getAssetSlug() . '.html';
							} else {
								return HOME_PATH . 'slideshow/' . $this->getAssetSlug() . '/';
							}
						} else {
							return HOME_PATH . $this->getAssetSlug() . '.html';
						}
					}
					break;
				} default : {
					return 'javascript:void(0);';
				}
			}
		}

		public function getContainingStory() {
			$storyAssetDbObj = new Cms_Db_StoryAsset();
			$storyAssetDbObj->set( 'assetId||' . $this->getAssetId() );
			if( $storyAssetDbObj->getResultCount() > 0 ) {
				return new Cms_Story( $storyAssetDbObj->getStoryId() );
			} else {
				return false;
			}
		}

		public function getVideos() {
			return new Cms_Videos( 'assetId||' . $this->getAssetId() );
		}
	}
