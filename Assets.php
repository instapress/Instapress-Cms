<?php

	class Cms_Assets extends Cms_AbstractPlural {

		protected $_dbClass = 'Asset';

		function getPaging( $currentPageNumber = 1, $pagePath = 'galleries/' ) {
			$tempPaging=array();
			if( $this->getTotalPages() > 1 ) {
				$p_start = 1;
	
				if( $currentPageNumber > 5 ) {
					$p_start=$currentPageNumber-5;
				}
	
				$p_end = $p_start + 9;

				if( $p_end > $this->getTotalPages() ) {
					$p_end=$this->getTotalPages();
				}
	
				$i = 0;
				if( $currentPageNumber > 1 ) {
					$temp_paging_link = HOME_PATH . $pagePath . ( $currentPageNumber - 1 ) . "/";
					if( 2 == $currentPageNumber ) {
						$temp_paging_link = HOME_PATH;
					}
					$temp_paging[ $i ][ 'numbers' ] = "Prev";
					$temp_paging[ $i ][ 'link' ] = $temp_paging_link;
					$temp_paging[ $i ][ 'isActive' ] = false;
					$i++;
				}

				for( $x = $p_start; $x <= $p_end; $x++ ) {
					$temp_paging_link = HOME_PATH . $pagePath . $x . "/";
					if( 1 == $x ) {
						$temp_paging_link = HOME_PATH;
					}

					if( $currentPageNumber != $x ) {
						$temp_paging[ $i ][ 'numbers' ] = $x;
						$temp_paging[ $i ][ 'link' ] = $temp_paging_link;
						$temp_paging[ $i ][ 'isActive' ] = false;
					} else {
						$temp_paging[ $i ][ 'numbers' ] = $x;
						$temp_paging[ $i ][ 'link' ] = "";
						$temp_paging[ $i ][ 'isActive' ] = true;
					}
					$i++;
				}

				if( $currentPageNumber < $p_end ) {
					$temp_paging_link = HOME_PATH . $pagePath . ( $currentPageNumber + 1 ) . "/";
					$temp_paging[ $i ][ 'numbers' ] = "Next";
					$temp_paging[ $i ][ 'link' ] = $temp_paging_link;
					$temp_paging[ $i ][ 'isActive' ] = false;
				}
			}
			return $temp_paging;
		}
	}