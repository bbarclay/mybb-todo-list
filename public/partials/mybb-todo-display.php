<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public/partials
 */
?>
    <?php 
    	$message = get_option('top_notification');	

    if(!empty($message)) {
    	 ?>
    		<span class="toggleIntro"><span class="fa fa-close"></span></span>

    	<?php
    	echo '<p class="text-primary welcome-note">' . nl2br($message) . '<span class="hide"><span class="fa fa-close"></span></span></p>';
    }

    ?>
    <div class="wrap-task">
		<div class="response-wrap"></div>

			<div class="extra-header">
	             <div class="left nomargin">
		             	<div class="tab-content">
		  				 	<ul>
		  				 	    <li id="suggested" class="active"><span class="fa fa-tasks"></span> <span class="name"><?php echo get_option('suggestedtask_option_name') ?></span></li>
		  				 		<li id="pending"><span class="fa fa-list-ol"></span><span class="name"> <?php echo get_option('mytask_option_name') ?></span></li>
		  				 		<li id="completed"><span class="fa fa-check"></span><span class="name"><?php echo get_option('completedtask_option_name') ?></span></li>
		  				 	</ul>
		  				 	<div class="loading"><img src="<?php echo plugins_url( '../images/loader.gif', __FILE__ ) ?>" /></div>
		  				 </div>
	             </div>
					 <div class="right btn-add nomargin">
					 	 <button id="addTodo"><div class="name"><?php echo get_option('addtask_option_name') ?></div></button>
	  			</div>
	  		</div>
	  		 	<?php  $this->get_form()  ?> 
		  	<div class="todo-list">
	     	 	<?php $this->get_lists() ?>    
	    	</div> 	 
    </div> 	     	  		   
	  	 