<?php
  	

      	$paged = (int)get_query_var( 'paged', 1 );

   	   if($paged) {


   	   	   $x = $paged - 1;
   	   	   
		   $start = (50 * $x);
	      
	       $count = $start;


   	   }
   	   else {
   	   		$start = 0;
	  	    $count = 0;
   		}



?>
<section class="bb-member-list">	
	<div class="wrap">
		<div class="wrap-left">
			   <?php echo '<h3>Your members : ' . $total .  '</h3>'; ?>
		</div>
		<div class="wrap-right">
			<?php echo $this->consultant->display_pagination(); ?>
		</div>
	</div>
   	<div class="view-task">
 
   			<div class="wrap-right width-30">
   				  <ul class="members-list" >

					  	<?php 

					  	$first = 1;

					  	foreach($query as $row) :
					  		
					  		$count++;


							$user = get_user_by( 'email', $row['email'] );


								if($first == 1) {
									$f_ID = $row['id'];
				                    $f_firstname = $row['firstname']; 
				                    $f_lastname = $row['lastname']; 
				                    $f_email = $row['email'];
				                    $f_joinedDate = $row['JoinedBlue_174'];	
									
									if( !empty ( $user ) ) {
										$f_userID = $user->ID;
									}
				                    
				                }
				                if( !empty ( $user ) ) {
	 	                        	$userID = $user->ID;
	 	                    	}
								$company = $row['company'];	
								$country = $row['country'];	
								$year_level = $row['BBYearLeve_258'];
								$sms_number = $row['sms_number'];


	                            

								if( $country == 'AU' )
								{
									$country = 'Australia';
								}
								else if ( $country == 'NZ' )
								{
									$country = 'New Zealand';
								}

								if($first == 1) {
									$f_country = $country;
								}

								switch($year_level) {
									case '1204':
									   $bb_level = "Fast-Trask";
									   break;

									case '1205':
										$bb_level = "Elite - VIP";
									   break;   

									case '1206':
										$bb_level = "Elite - All Stars";
									   break; 
									   
									case '1207':
										$bb_level = "Elite - Masters";
									   break;

									default:
										$bb_level = '';
										break;          
								}

							  if($first == 1) {
									$f_bb_level = $bb_level;
								}
								
								$first = $first + 1;
					  		
					  	?>
					  	<li wp-id="<?php echo $user_ID; ?>" class="item index-<?php echo ( $row['BBCustomer_165'] == '800' ) ? 'gold' : 'platinum'; ?>" id="<?php echo $row['id'] ?>">	
						  	<div class="wrap-left index with-s30">
						  		<span class="no"><?php echo $count; ?></span>
						  	</div>

						  	
						  	<div class="wrap-right width-s70">
						  		<div class="name"><?php echo $row['firstname'] . ' ' . $row['lastname']; ?> </div>
						  		  <a href="#" class="show-task">View Task</a>
						  		  <div class="hide user_id"><?php echo $userID; ?></div>
						  		  <div class="hide email"><?php echo $row['email']  ?></div>
						  		  <div class="hide joined_date"><?php echo date('d-m-Y', $row['JoinedBlue_174'])  ?></div>
						  		  <div class="hide year_level"><?php echo $bb_level  ?></div>
						  		  <div class="hide country"><?php echo $country ?></div>
						  		  <div class="hide sms_number"><?php echo $sms_number ?></div>
						  	</div>
						  	<div class="clear"></div>
					  	</li>
					<?php 

					  	endforeach; 

					  	wp_reset_query();
					?>
				</ul>

   			</div>

   			<div class="wrap-left width-70">
   						<div class="loading">
   							<span class="loading-icon">Loading...</span>
   						</div>
				   		<div class="wrap">
				   			<div class="wrap-left">
				   				 <h3>Tasks</h3>
				   			</div>
				   			<div class="wrap-right">
				   				<a href="#" class="btn btn-addTask"><span class="fa fa-plus"></span> Add Task</a>
				   			</div>
						 </div>
						 <div class="popup-add-task">
						 	<h3 class="heading">Add New Task</h3>
						 	<form>
						 		    
							 		<input type="text" id="todo-title" placeholder="Task Name" class="margin-right-20" />
							 		<input type="text" id="todo-link" placeholder="Task Link (Optional)" />
							 		<textarea type="text" id="todo-content" placeholder="Task Notes"></textarea>
							 		<input type="hidden" id="todo-id" value="<?php echo $f_userID; ?>" />
							 		<?php wp_nonce_field( 'task_post_nonce', 'post_nonce_field' ); ?>
							 		<input type="submit" value="Submit" id="postTask" />
						 	</form>
						 </div>
						 <div class="information">
						 	<h4 class="heading">Member Information</h4>

						 	<div class="row">
							 	<div class="column column-left">
							   		 <p><strong>Full Name</strong> : <span class="fullname"><?php echo $f_firstname . ' ' . $f_lastname; ?></span><br>
							 	<strong>Email</strong> : <span class="email"><?php echo $f_email ?></span><br><strong>Joined Date </strong> : <span class="joined_date"><?php echo date('M d, Y', $f_joinedDate) ?></span></p>	
							 	</div>
							 	<div class="column column-right">
							 		<p><strong>Country</strong> : <span class="country"><?php echo $f_country ?></span><br>
							 	<strong>Year Level </strong> : <span class="year_level"><?php echo $f_bb_level  ?></span></p>	
							 	</div>
							</div> 	

						 </div>
						 <div class="view-member-tasks">
						 	 <h4 class="heading">Task List</h4>
							 <table cellspacing="0" class="bb_table_list">
							 	  	<tr>
								  	    <th>Item</th>
								  		<th>Task Name</th>
								  		<th>Status</th>
								  	</tr>
								  	<?php 

					                    /**
					                    * ==================================================
					                    *
					                    * PENDING TASKS
					                    *
					                    * ==================================================
					                    */

					                    $counter = 0;

					                    if( !empty ( $user ) ) {
					                    	$todos =  $this->consultant->get_todos($f_userID);
										}
						                if( !empty ($todos) ) {
						                        foreach ($todos as $page) {

														$has_subtask = get_post_meta($page->ID, 'subtask_from', true);

	                              						if (!$has_subtask) :


						                              	 $counter++;

				                                         $id =  $page->ID;
				                                         $due_date = get_post_meta($id, 'due_date', true);
				                                         $date = date_create($due_date);
				                                         $link = get_post_meta($id, 'my_task_link', true);

				                                         $consultant = get_post_meta($id, 'consultant', true);
								                    

											  	?>
											  	        <tr id="<?php echo $page->ID ?>" class="<?php echo ($consultant) ? 'c-added': '';?>">
													  		<td><span><?php echo $counter; ?></span></td>
													  		<td><div class="toggle-content"><?php echo $page->post_title; ?></div>
													  			<div class="task-content">
								 	 									<?php echo $page->post_content; ?>
																</div>
													  		</td>
													  		<td><?php echo $this->consultant->get_status($id) ?>
													  			<!-- <button class="fa fa-mail-forward"></button> -->
													  		
													  		</td>
													  	</tr>

											  	<?php 

											  			endif;
											  		}
											  	}
											  	else {
											  		echo '<tr><td colspan="3">N/A</td></tr>';
											  	}

								  			wp_reset_postdata(); 
							
								  	 ?> 
							 </table>

							 
						 </div>

   			</div>


   		

	</div>

</section>  




