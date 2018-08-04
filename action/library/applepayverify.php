<?php
	/*
	 * @desc  苹果小票验证
	 */
	class applepayverify extends basicsingleton{

		public function verify($identifier,$product_item,$bundle_id,$release_url,$test_url,basicmodelimpl $goods_model) {
			$result = array();
			$result['code'] = 1;
			$result['desc'] = 'fail';
			$result['data'] = null;

			if(is_null($identifier) || is_null($release_url)) return $result;
	
			$identifier = str_replace('_', '+', $identifier);
			BASIC_LOG_WARNING('applepayverify', '%s', 'identifier='.$identifier);

			$json_data = array('receipt-data'=>$identifier);//客户端返回服务器端之前，已经作加密处理
	    	$json_value = json_encode($json_data);

	    	$response = $this->http_post_data($release_url,$json_value);
			$status = $response["status"];
			if($status==21007){
				$response = $this->http_post_data($test_url,$json_value);
				$status = $response["status"];
			}

			BASIC_LOG_WARNING('applepayverify', '%s', 'status='.$status);
			if($status!=0) {
				$result['code'] = 2;
				$result['desc'] = '$status!=0';
				return $result;
			}

			BASIC_LOG_WARNING('applepayverify', '%s', 'response='.json_encode($response));
			$result['data'] = $response;
			$bundle_id_value = $this->get_response_bundle_id($response);//$response["receipt"]["bundle_id"];//$response["receipt"]["bid"];

			if($bundle_id_value!=$bundle_id){
				$result['code'] = 3;
				$result['desc'] = 'bind_id check fail';
				return $result;
			}

			/*$item_id = $response["receipt"]["item_id"]?$response["receipt"]["item_id"]:0;
			//$product_item = $good_info->get('goods_product_item',0);
			if($item_id != $product_item) {
				$result['code'] = 4;
				$result['desc'] = 'item_id check fail';
				return $result;
			}
			*/
			$goods_product_id = $goods_model->get('goods_product_id',0);
			$product_id = $this->get_response_product_id($response);
			if($product_id != $goods_product_id) {
				$result['code'] = 4;
				$result['desc'] = 'item_id check fail';
				return $result;
			}
			$result['code'] = 0;
			$result['desc'] = 'ok';
			return $result;
		}

		protected function http_post_data($url, $data_string) {
	
			$curl_handle=curl_init();
			curl_setopt($curl_handle,CURLOPT_URL, $url);
			curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_handle,CURLOPT_HEADER, 0);
			curl_setopt($curl_handle,CURLOPT_POST, true);
			curl_setopt($curl_handle,CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($curl_handle,CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl_handle,CURLOPT_SSL_VERIFYPEER, 0);
			$response_json =curl_exec($curl_handle);
			$response =json_decode($response_json,true);
			curl_close($curl_handle);
			return $response;
	    }

	    protected function get_response_product_id($response) {
	    	if(!isset($response["receipt"])) {
	    		return '';
	    	}
	    	if(isset($response["receipt"]['product_id'])) {
	    		return $response["receipt"]['product_id'];
	    	}else if(isset($response["receipt"]["in_app"]) && isset($response["receipt"]["in_app"]['product_id'])) {
	    		return $response["receipt"]["in_app"]['product_id'];
	    	}else if(isset($response["receipt"]["in_app"]) && isset($response["receipt"]["in_app"][0]['product_id'])) {
	    		return $response["receipt"]["in_app"][0]['product_id'];
	    	}else {
	    		return '';
	    	}
	    }

	    protected function get_response_bundle_id($response) {
	    	if(!isset($response["receipt"])) {
	    		return '';
	    	}
	    	if(isset($response["receipt"]['bid'])) {
	    		return $response["receipt"]['bid'];
	    	}else if(isset($response["receipt"]["bundle_id"])) {
	    		return $response["receipt"]["bundle_id"];
	    	}else {
	    		return '';
	    	}
	    }
	}
?>