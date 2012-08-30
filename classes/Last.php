<?php
	class Last {
		public static function prepareRequest($params) {
			$defaults = array(
				"api_key" => API_KEY,
				"sk" => SESSION_KEY
			);
			
			$params = array_merge($defaults, $params);
			ksort($params, SORT_STRING);
			
			return $params;
		}
		
		
		public static function sendRequest($params, $method = "GET") {
			$params["api_sig"] = self::generateSecret($params);
			$query = http_build_query($params, "", "&");
			
			$context = stream_context_create(array(
				"http" => array(
					"timeout" => 10,
					"method" => $method)
				)
			);
			
			$content = file_get_contents(SCROBBLE_URL . "?" . $query, false, $context);
			return simplexml_load_string($content);
		}
		
		
		public static function generateSecret($params) {
			$signature = "";
			
			foreach ($params as $key => $value) {
				$signature .= $key . $value;
			}
			
			return md5($signature . API_SECRET);
		}
	}