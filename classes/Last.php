<?php
	// A Last.fm API wrapper class used to generate and send requests.
	class Last {
		// Adds session and API keys to the parameter list, sorts (required by Last.fm).
		public static function prepareRequest($params) {
			$defaults = array(
				"api_key" => API_KEY,
				"sk" => SESSION_KEY
			);
			
			$params = array_merge($defaults, $params);
			ksort($params, SORT_STRING);
			
			return $params;
		}
		
		
		// Sends a request using context for either a GET or a POST method.
		public static function sendRequest($params, $method = "GET") {
			global $context;
			
			$params["api_sig"] = self::generateSignature($params);
			$query = http_build_query($params, "", "&");
			
			$content = file_get_contents(SCROBBLE_URL . "?" . $query, false, $context);
			$xml = simplexml_load_string($content);
			
			return $xml;
		}
		
		
		// Generates a request signature based on sorted request parameters.
		public static function generateSignature($params) {
			$signature = "";
			
			foreach ($params as $key => $value) {
				$signature .= $key . $value;
			}
			
			return md5($signature . API_SECRET);
		}
	}