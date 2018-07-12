<div class="pending-task pending-task group-task">
    <ul id="task-list" class="task-list">
     <div id="bigdate"></div>
        <?php 
                    /**
                    * ==================================================
                    *
                    * PENDING TASKS
                    *
                    * ==================================================
                    */

                    $count = 0;
                    $query =  Mybb_Template::get_todos();


                    // if($query) {

                          foreach ($query as $page) {
                              $count++;
                                         $id =  $page->ID;
                                    
                                         $due_date = get_post_meta($id, 'due_date', true);
                                         $date = date_create($due_date);

                                         $link = get_post_meta($id, 'my_task_link', true);
                             


                                         $has_consultant = get_post_meta($id, 'consultant', true);

                                      
                            ?>

                                  <li class="task-row <?php echo ($has_consultant) ? 'con-task' : '' ?>" id="<?php echo esc_attr( $page->ID ); ?>">
                       
                                      <div class="tick-box">
                                          <span class="btn btn-circle fa fa-check"></span>
                                      </div>

                                        <div class="content-holder">
                                            <div class="task-name">
                                                   <span class="number"><?php echo $count; ?></span>
                                                   <div class="wrap-task">
                                                      <span class="orig_title hide"><?php echo $page->post_title; ?></span>
                                                      <p class="title"><?php echo $page->post_title; ?> 
                                                        <?php if ($has_consultant == '2') {
                                                                  $con = 'Task from Luke';

                                                                   echo ' - <span>' . $con . '</span>';
                                                              }
                                                              else if ($has_consultant == '38'){
                                                                  $con = 'Task from Beau';

                                                                   echo ' - <span>' . $con . '</span>';
                                                              } 
                                                              ?>
                                                      </p>
                                                      <?php if($link) { ?>
                                                              <p class="link"><?php echo $link; ?></p> 
                                                            <?php } ?>
                                                      <?php if(!empty($page->post_content)) { ?>
                                                         <span class="view-more">more...</span>
                                                      <?php } ?>  

                                                          <p class="timer">( <span class="date"><?php echo 'Started ' . date('D M j', strtotime($page->post_date))  ?>  </span> <span class="fa fa-long-arrow-right"></span>

                                                            <?php if($due_date) { ?>
                                                                <span class="date db-date">Due <?php echo date_format($date,"D M j"); ?></span>
                                                                <span class="date due-date hide">Due </span>
                                                                <input type="text" class="update-calendar datepicker select-<?php echo  $id; ?>" dateID="<?php echo  $id; ?>" /><input type="hidden" class="update-button alternate long-format-<?php echo  $id; ?>" /> <button class="submitDate">Submit</button>

                                                                <span class="fa fa-calendar-o changeDate"></span>

                                                          <?php } else { ?>
                                                                <span class="addText due-date">Add due date</span> <span class="date text-date hide">Due </span><input type="text" class="datepicker select-<?php echo  $id; ?>" dateID="<?php echo  $id; ?>" /><input type="hidden" class="alternate long-format-<?php echo  $id; ?>" /> <button class="submitDate">Submit</button>
                                                            <?php  } ?>
                                                          )</p>
                                                        
                                                      <?php if(!empty($page->post_content)) { ?>
                                                         <div class="details ">
                                                            
                                                            <?php echo nl2br( $page->post_content); ?>
                                                          </div>

                                                      <?php } ?>
                                                 </div><!-- wrap-task -->
                                                 <div class="subtasks">
                                                    <div class="heading">Subtasks</div>
                                   
                                                    <div class="subtask-lists">
                                                      <?php wp_nonce_field( 'subtask_post_nonce', 'subtask_nonce_field' ); ?>
                                                    </div>
                            
                                                    <div class="task new-sub-task">
            
                                                       <div class="detail">
                                                          <p class="title pre-subtask"><span class="add-mtask"><span class="fa fa-plus tick-btn add-btn"></span> Add Task</span> | <span class="close-mtask"><span class="fa fa-close"></span> Close</span></p>


                                                          <div class="add-task">
                                                            <form>
                                                                
                                                                  <input type="text" class="input input-text at-name"  placeholder="What's need to be done?" />
                                                                
                                                                
                                                                <!-- Subtask - Textarea -->
                                                                <div class="input-field task-textarea">
                                                                  <span class="fa fa-align-left"></span>  
                                                                   <div class="task-twrap"> 
                                                                        <textarea type="text" class="input input-textarea at-notes" placeholder="Task Notes"></textarea>
                                                                   </div>
                                                                </div>  

                                                                <div class="input-field">
                                                                <?php wp_nonce_field( 'subtask_post_nonce', 'subpost_nonce_field' ); ?>
                                                                  <input type="submit" class="add-subtask" value="Submit"> or
                                                                  <span class="close-form">Cancel</span>
                                                                </div>
                                                            </form>
                                                          </div><!-- ./ end of add-task -->
                                                          
                                                       </div>

                                                    </div> 
                                                   

                                                 </div>                                       
                                                 
                                            </div>
                                            <div class="btn-col">

                                                <span class="btn-add w-tip">
                                                   <i class="tooltip">Subtasks</i> 
                                                   <span class="fa fa-outdent"></span>
                                                </span> 
                                                <span class="btn-edit w-tip">
                                                    <i class="tooltip">Edit Task</i>
                                                    <span class="fa fa-pencil"></span>
                                                </span>
                                                <span class="btn-delete w-tip">
                                                    <i class="tooltip">Delete Task</i>
                                                    <span class="fa fa-trash"></span>
                                                </span>

                                               
                                            </div>

                                          
                                        </div><!-- ./End of Content Holder -->

                                  </li>

                           <?php 
                             
                                } ?>
                 

                    <?php

                      // else {
                      //    Mybb_Template::task_notification('pending');    
                      // }

                      wp_reset_postdata();  
                     ?> 
    </ul>
</div>
<div class="completed-task group-task">
    <ul id="task-completed" class="task-list">
    <?php 
       $count = 0;
       $query =  Mybb_Template::get_completedTodos();

       if( $query->have_posts() ) : ?>

        
           <?php  

                while ( $query->have_posts() ) : $query->the_post();
                   $count++; 

                   $date = get_post_meta(GET_THE_ID(), "date_completed", true);
                   $date = date_create($date);

                   $link = get_post_meta(GET_THE_ID(), 'my_task_link', true);
                   $link = '<a href="'. esc_url( $link ) . '" target="_blank">' . esc_url( $link ) . '</a>';
           ?>
            
                  <li class="task-row" id="<?php esc_attr( the_id() ); ?>">


                    <div class="content-holder">
                        <div class="task-name">
                            <p class="title"><?php esc_html( the_title() ); ?></p>
                              <?php if($link) { ?>
                                     <p class="link"><?php echo $link; ?></p> 
                             <?php } ?>
                            <?php if(!empty(get_the_content())) { ?>
                               <span class="view-more">more details...</span>
                            <?php } ?>
                            <span class="date">( <?php echo 'Completed: ' . date_format($date,"D M j"); ?> )</span>
                            <?php if(!empty(get_the_content())) { ?>
                              <div class="details"><?php echo nl2br(get_the_content()); ?></div>
                            <?php } ?>
                        </div>
                        <div class="btn-col">
                             <span class="btn-undo fa fa-undo"></span>
                            <span class="btn-delete fa fa-trash"></span>
                        </div>
                    </div>

                  </li>

           <?php 
                 endwhile;
              else:

                Mybb_Template::task_notification('completed'); 
            ?>     
        
      <?php
       endif;

        wp_reset_postdata();  
     ?> 
  </ul> 
</div> 
<div class="suggested-task group-task active">
    
    <?php 
       $count = 0;
       $total_hide = 0;
       $query =  Mybb_Template::get_suggestedTodos(); 

       
       ?>


           <?php  

            if( $query->have_posts() ) : ?>

            <ul id="task-suggested" class="task-list">
              <?php

                  while ( $query->have_posts() ) : $query->the_post();
                     $count++; 

                    $id       = GET_THE_ID();
                    $user_id  = get_current_user_id();
                    $link     = get_post_meta($id, 'my_task_link', true);
                    $is_added = get_post_meta($id, '', true);


                  if( !empty(get_post_meta($id, '_user_id', true)) && in_array($user_id, $is_added['_user_id']) || 
                      !empty(get_post_meta($id, 'removed_by_user_id', true)) && in_array($user_id, $is_added['removed_by_user_id']) )  {
                        $hide = 'hide';
                        $total_hide++;
                  }
                  else {
                      $hide = '';
                  }  ?>
            
                  <li class="task-row <?php echo $hide; ?>" id="<?php esc_attr( the_id() ); ?>">

                      <div class="tick-box">
                          <span class="btn btn-circle fa fa-plus"></span>
                      </div>
                      <div class="content-holder">
                          <div class="task-name">

                              <p class="title"><?php esc_html( the_title() ); ?></p>
                              <?php if($link) { ?>
                                <p class="link"><a href="<?php echo $link; ?>" target="_blank"><?php echo $link; ?></a></p> 
                              <?php } ?>                              
                              <?php if(!empty(get_the_content())) { ?>
                                <span class="view-more">more</span>
                              <?php } ?>
                              <span class="date">( <?php echo 'Added : ' . get_the_date( 'D M j' ); ?> )</span>
                              <?php if(!empty(get_the_content())) { ?>
                                <div class="details"><?php echo nl2br(get_the_content()); ?></div>
                              <?php } ?>
                          </div>
                          <div class="btn-col">
                              
                              <span class="btn-delete fa fa-trash"></span>
                          </div>
                      </div>

                  </li>

           <?php 
              
              
                 endwhile; ?>
            </ul>
           <?php 
                    if($total_hide == $count) {
                      Mybb_Template::task_notification('suggested');
                    } ?>
           <?php  else:

              Mybb_Template::task_notification('suggested'); 
            ?>     
        
      <?php
       endif;

        wp_reset_postdata();  
     ?> 
  
</div>      