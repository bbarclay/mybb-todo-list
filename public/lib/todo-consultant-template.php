<?php


class Mybb_Consultant {

	public $client;
	private $display_total;

	public function __construct($client = "") {

		$this->client = $client;
	}
	//5.4
	function get_members() 
	{

	  $client = $this->client;



   	   $paged = (int)get_query_var( 'paged', 1 );

   	   if($paged) {

   	   	   $x = $paged - 1;
		   $start = (50 * $x);
	       $range = ($start + 50) - 1;
   	   }
   	   else {

	  	   $start = 0;
	       $range = 50;
   		}

	    $queryParams = array(
	          "condition"     => 
	                             '[

	                             {
	                              "field":{"field":"owner"},
	                              "op":"=",
	                              "value":{"value":"' . self::getID() .'"}
	                             },
	                             "AND",
	                             {
	                              "field":{"field":"BBCustomer_165"},
	                              "op":"IN",
								  "value":{"list":[{"value":802},{"value":800}]}
	                             }
	                             

	                            ]',

	         "listFields" => "id, firstname, lastname, email, BBCustomer_165, JoinedBlue_174, title, country, sms_number,BBYearLeve_258",
	         "start" => $start,
        	 "range" => $range,
        	 'sort' => 'firstname',
        	 'sortDir' => 'asc'                  
	    );


	  $response = $client->contact()->retrieveMultiple($queryParams);
	  $response = json_decode($response, true);
	  $response = $response['data'];
	  

	   return $response;

	}
	public function getID() {

	  $current_user = wp_get_current_user();

	  $user_email   = $current_user->user_email;

	   if ($user_email == 'luke@businessblueprint.com') {
	   	  return $ontra_id = '22';
	   }
	   else if ($user_email == 'beau@businessblueprint.com') {
	   	   return $ontra_id = '13';
	   }
	   else if ($user_email == 'july@businessblueprint.com') {
	   	   return $ontra_id = '22';
	   }
	   else if ($user_email == 'josh@businessblueprint.com') {
	   	   return $ontra_id = '22';
	   }
	   else if ($user_email == 'dale@businessblueprint.com') {
	   	   return $ontra_id = '22';
	   }
        
	}

	public function get_totalItems($html = false) {

		$client = $this->client;
	    $queryParams = array(
	          "condition"     => 
	                             '[

	                             {
	                              "field":{"field":"owner"},
	                              "op":"=",
	                              "value":{"value":"' . self::getID() .'"}
	                             },
	                             "AND",
	                             {
	                              "field":{"field":"BBCustomer_165"},
	                              "op":"IN",
								  "value":{"list":[{"value":802},{"value":800}]}
	                             }
	                             

	                            ]'                
	    );

	    $response = $client->contact()->retrieveCollectionInfo($queryParams);
	    $response = json_decode($response, true);
	    $count = $response["data"]["count"];


		if(!$html) {
			return $count;
		}
		else {
	   		return self::display_total($count);
	   }

	}



	public static function get_todos($author_id) {

 		global $wpdb;


		$query = $wpdb->get_results(

			$wpdb->prepare(
			"

				 SELECT 	posts.* 
				 FROM 		$wpdb->posts posts
				 WHERE      posts.post_author = %s
				            AND posts.post_type = 'mybb_todo'
				            AND posts.post_status = 'publish'
				    

			"
			, $author_id )

			);

		return $query;
	}

	public function get_status($id) 
	{
		$task_id = $id;

		$status = get_post_meta($task_id, 'status', true);

		if ($status == 'pending')
		{
			$status = 'pending';
		}
		else if ( $status == 'completed' ) 
		{
			$status = 'completed';
		}
		else {
			$status = 'pending';
		}

		return $status;
	}



	/** ========================
	*
	*	TEMPLATING BLOCK
	* 
	========================== */
	
	public function display_total($total) 
	{
		return '<p class="total">Total Members you managed : ' . $total . '</p>';
	}

	public function display_pagination( $max = 50) 
	{
		$output = '';

		$total = self::get_totalItems();

		if( $total % $max ) {
	        $lists =  ( $total / $max ) + 1;
	    }
	    else {
	        $lists =  ( $total / $max );
	    }


	    if ( $lists > 1 ){

	        $current_page = max(1, get_query_var('paged'));

	        $output .= paginate_links(array(
	            'base' => get_pagenum_link(1) . '%_%',
	            'format' => '/page/%#%',
	            'current' => $current_page,
	            'total' => $lists,
	            'before_page_number' => '',
			    'after_page_number'  => '',
			    'type'               => 'list',
	        ));

	    }
   	
	    return $output;
        


	}

}