<?php 
class HTTPClient{
	public $error = "";
	
	//$url : url to get data
	//$type : request type - get, post, delete, put
	//$data : used for request post, put
	//$data_type: specifty what data will be sent in request, json or raw
	public function send($url, $type, $data = null, $data_type = null){
		
		if(isset($data_type)){
			$data_type = strtolower($data_type);
			$data = $this->_convert($data, $data_type);
		}
		
		$type = strtolower($type);

		switch($type){
			case "get":
				return $this->_get($url);
				break;
			case "post":
				return $this->_post($url, $data, $data_type);
				break;
			case "put":
				return $this->_put($url, $data, $data_type);
				break;
			case "delete":
				return $this->_delete($url);
				break;
		}
	}
	
	private function _post($url, $data, $data_type){

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);   
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 		if($data_type == "json"){
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
																			'Content-Type: application/json',                                                                                
																			'Content-Length: ' . strlen($data)
																		)                                                                       
																	);
		}else{
			curl_setopt($ch, CURLOPT_POST, count($data));	
		} 
		return curl_exec($ch);
	}
	
	private function _get($url){

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 
		return curl_exec($ch);	
	}
	
	private function _put($url, $data, $data_type){

 		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);    
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if(!empty($data)){
			if($data_type == "json"){
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
															'Content-Type: application/json',                                                                                
															'Content-Length: ' . strlen($data)
														)                                                                       
													);
			}else{
				curl_setopt($ch, CURLOPT_POST, count($data));	
			} 
		}

		return curl_exec($ch);
	}
	
	private function _delete($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE'); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		return curl_exec($ch);	
	}
	
	private function _convert($data, $data_type){
		switch($data_type){
			case "raw":
				return urldecode(http_build_query($data, '&'));
				break;
			case "json":
				return json_encode($data);
				break;
		}
	}
}
