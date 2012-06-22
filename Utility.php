<?php

	class Cms_Utility {

		private static $_errorMessage = '';
		private static $_storyObj = false;

		public static function getMessage() {
			return self::$_errorMessage;
		}

		private function __construct() {}

		public static function submitFeedback( $pageVars ) {
			$url = ENTN_PUSH_URL . '/feedbackSubmit/true/';
			$fields = array(
				'jobProfile' => urlencode( $pageVars[ 'jobProfile' ] ),
				'personName' => urlencode( $pageVars[ 'personName' ] ),
				'personEmail' => urlencode( $pageVars[ 'personEmail' ] ),
				'personTwitter' => urlencode( $pageVars[ 'personTwitter' ] ),
				'personExperience' => urlencode( $pageVars[ 'personExperience' ] ),
				'clientId' => urlencode( CLIENT_ID ),
				'publicationId' => urlencode( PUBLICATION_ID )
			);

			//url-ify the data for the POST
			$fieldsString = array();
			foreach( $fields as $key => $value ) {
				$fieldsString[] = "$key=$value";
			}
			$fieldsString = implode( '&', $fieldsString );

			//open connection
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_POST, count( $fields ) );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $fieldsString );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			$result = curl_exec( $ch );
			curl_close( $ch );
			return $result > 0;
		}
	}
