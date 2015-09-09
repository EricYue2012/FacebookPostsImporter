<?php 
include 'HTTPClient.php';

class NCImporter{
	
	//init
	var $posts = array();
	var $message  = '';
	var $url = '';
	var $author_id = 0;
	var $status_id = 0;
	var $counter_imported = 0;
	var $counter_skipped = 0;
	
	function __construct($author_id, $status_id) {
		$this->author_id = $author_id;
		$this->status_id = $status_id;
	}

	function __destruct() {
	
	}
	
	function	print_message(){
		//echo $this->message;
	}
	
	// check if url is valid
	function validate_url($arg_url){
		if(strpos($arg_url, 'http://') ===0 || strpos($arg_url, 'https://') ===0){
 			return true;
		}else{
			$this->message = 'Invalid URL, please entry full url begin with http or https';
			return false;
		}
		
	}
	
	// extract posts from target source
	function extract_posts($arg_url){
		
		$httpclient = new HTTPClient();
		$json_data = $httpclient->send($arg_url, 'get');
		
		if(empty($json_data)){
			$this->message = 'Empty content returned';
			return false;
		}
		
		// convert json to php array
		$array_data = json_decode($json_data, true);
		// get data
		$facebook_posts = $array_data['data'];
		
		if(count($facebook_posts)>0){
			
			foreach($facebook_posts as $facebook_post){
				
				$post_content = $facebook_post['message'];
				$post_title = $facebook_post['name'];	
				$post_author = $this->author_id;
				$post_status = $this->status_id;
				$post_date_gmt = gmdate( 'Y-m-d H:i:s', strtotime( $facebook_post['created_time']));
				$post_date = get_date_from_gmt( $post_date_gmt );
				$facebook_id    = $facebook_post['id'];
				
				// build the post array
				$this->posts[] = compact(
					'post_content',
					'post_title',
					'post_author',
					'post_status',
					'post_date',
					'post_date_gmt',
					'facebook_id'
				);
			} // end of foreach
		} // end of if
		return true;	
	}
	
	// import post into wordpress
	function insert_posts(){
		global $wpdb;
		$this->counter_imported = 0;
		$this->counter_skipped = 0;
		
		if(count($this->posts)>0){
			
			foreach($this->posts as $post){
				
				// check if it is existing in wp database already
				$facebook_id = $post['facebook_id'];
				if (	$wpdb->get_var( $wpdb->prepare( "SELECT meta_id FROM {$wpdb->postmeta} WHERE meta_key = 'facebook_id' AND meta_value = %s", $facebook_id))
					|| post_exists( $post['post_title'], $post['post_content'], $post['post_date'])
				){	
					// already in database, skip
					$this->counter_skipped++;
					continue;
				}else{
					// insert into wp database
					$post_id = wp_insert_post($post);	
					
					//if failed to insert, keep going to try next post.
					if ( !$post_id )
						continue;
						
					add_post_meta($post_id, 'facebook_id', $facebook_id);
					$this->counter_imported++;
				}// end of if
			} // end of foreach
		}// end of if
		return true;
	}
	
	function start($url){
		//valid url
		if(!$this->validate_url($url)){
			$this->print_message();
			return false;
		}
		
		// extract posts and saved into $this->posts
		if(!$this->extract_posts($url)){
			$this->print_message();
			return false;
		}
		
		// insert $this->posts into wordpress database
		if(!$this->insert_posts()){
			$this->print_message();
			return false;
		}
		return true;
	}
}