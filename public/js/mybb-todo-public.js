(function( $ ) {
	'use strict';


    jQuery(document).ready(function(){

			var animation = $('.loading'),
				response_msg = $('#msg-response'),
				task_list	= $('#task-list'),
				completed_list = $('#task-completed'),
				suggested_list = $('#task-suggested'),
				Ulrow = '',
				isDoing = false;
	

			$('#addTodo').on("click", function(){
				var icon = $(this).find('span');

				$(this).toggleClass('click');

				if(icon.hasClass('fa-plus')) {

					icon.removeClass('fa-plus').addClass('fa-minus');
				}
				else if (icon.hasClass('fa-minus')) {
				
					icon.removeClass('fa-minus').addClass('fa-plus');
				}
				$('.wp-editor').toggleClass('active');
			});

			/** ===========================================
			*   Slide Toggle Show add Task for member
			*   ===========================================
			*/
			$('.toggleIntro').on('click', function(){
	
				if($(this).find('span').hasClass('fa-close'))
				{
					$(this).find('span').removeClass('fa-close').addClass('fa-sticky-note-o');
				}	
				else {
					$(this).find('span').removeClass('fa-sticky-note-o').addClass('fa-close');
				}
				$('.welcome-note').slideToggle();
			});

			$('.wp-editor .close').on('click', function(){
				$('.wp-editor').toggleClass('active');
			});


			$('.text-primary.welcome-note .hide').on("click", function(){
				  $('.text-primary.welcome-note').addClass('hide');
			});	

			/** ===========================================
			*   Slide Toggle Show add Task for member
			*   ===========================================
			*/
			$('.bb-member-list .btn-addTask').on('click', function(e){

				e.preventDefault();

				$('.bb-member-list .popup-add-task').slideToggle();

				if( $('.bb-member-list .fa').hasClass('fa-plus') )
				{
					$('.bb-member-list .fa').removeClass('fa-plus').addClass('fa-minus');
				}
				else {
					$('.bb-member-list .fa').removeClass('fa-minus').addClass('fa-plus');
				}

			});



            $('.tab-content ul li').on('click', function(){
      	
      			var task = $(this).attr('id'),
      			    active = $('.tab-content ul li').removeClass('active');

      			$(this).addClass('active');

      			$('.group-task').removeClass('active');

      			$('.' + task + '-task').addClass('active');
            })

	/* ==============================================

		(SUBTASKS) Add Sub Task

	============================================== */
	$('#task-list').on('click', '.task-row .close-mtask', function(){
		 var id = findParentID(this);
		 console.log('test');


			$('#' + id).find('.subtasks').toggleClass('show');
		 

	});


    $('#task-list').on('click', 'li .btn-add', function(){

    		var id = findParentID(this);
console.log('test');

		    if(!isDoing ) {
		    	isDoing = true;
			
			}

    	if ( !$('#' + id).find('.subtasks').hasClass('is_show') )	{

    		$(this).addClass('noclick');

			$.ajax({
			 		url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
			        data: {
			            action: 'mybbtodo_getposts',
			            id: id,
			            security: mybb_ajax.security
			        },
	 
			        success: function(data, textStatus, XMLHttpRequest) {
	
						var tasks = data.data.tasks,
						listing = [],
						list = [];

						if( data.data.has_subtask == true ) {

							var obj = JSON.stringify(tasks);
								obj = JSON.parse(obj);


							var count = 0;

							$.each( obj,function( x, task ) 
							{
				

								// Generate Subtask Lists
								var list = create_subtask_lists( task['title'], task['content'], task['date'], task['ID'], task['is_complete'] );
								
								$('#' + id).find('.subtask-lists').append(list);


							});	

						}	


						$('#' + id).find('.subtasks').addClass('is_show');
						$('#' + id + ' .btn-col .btn-add').removeClass('noclick');
						isDoing = false;
			 
			          
			        },
			 
			        error: function(MLHttpRequest, textStatus, errorThrown) {
			            alert(errorThrown);
			        }
			 
			});	

		}

    	togglingShow(this, '.subtasks');  
        //togglingHide(this, '.btn-col');         	  
    });

    $('#task-list').on('click', ' li .add-mtask', function(){
    	 togglingHide(this, '.pre-subtask');
    	 togglingShow(this, '.add-task');  
    });

    $('#task-list').on('click', ' li .close-form', function(){
    	togglingHide(this, '.pre-subtask');
    	togglingShow(this, '.add-task'); 
 

    });

     function togglingShow(event, subject) {
			$(event).closest('li').find(subject).toggleClass('show');
     }
     function togglingHide(event, subject) 
     {
     	$(event).closest('li').find(subject).toggleClass('hide');
     }
     function findParentID(event) {
     	var id;
     	id = $(event).closest('li').attr('id');

     	return id;
     }	
     /*  Creating subtask lists
      *
      */
     function create_subtask_lists(title, content, date, id, is_completed = false) {

     	      var html = '<div class="task '+ is_completed + '" id="' + id +'">' +
                 			'<div class="checker">' +
                    			'<span class="fa fa-check tick-btn"></span>' +
                 			'</div>' +
                 			'<div class="detail">' +
                    			'<p class="title">' + 
                     				title +
                        			'<span>' + content + '</span>' + 
                       
                    			'</p><span class="fa fa-trash delete-subtask"></span>' +
                 			'</div>' +
                 			'<div class="clear"></div>' +
              			  '</div>'; 

              			  return html;
     }

	/* ==============================================

		Tick Box to complete task

	============================================== */


	task_list.on('click', '.tick-box .btn', function(){
			var menu_order = $(this).attr('task-id'),
				    row = $(this).closest('.task-row'),
				    id = row.attr('id'),
				    title = $('#' + id  + ' .wrap-task').find('.title').text(),
				    link = $('#' + id).find('.link').text(),
				    content = $('#' + id).find('.details').text(),
				    only_list = '';

  			

			animation.addClass('show'); 
			
			$('#task-list' + ' li#' +  id ).find('p.title').addClass('crash-out').after(' <span class="btn btn-danger">completing..</span>');
			$('#' + id).addClass('noclick');



            if( $('#task-list li').length <= 1 ) {

            	only_list = mybb_ajax.completed_msg;
            }
            else {
            	only_list = '';
            }
			$.ajax({
			 		url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
			        data: {
			            action: 'mybbtodo_completedpost',
			            id: id,
			            only_list: only_list,
			            title: title,
			            link: link,
			            content: content,
			            security: mybb_ajax.security
			        },
	 
			        success: function(data, textStatus, XMLHttpRequest) {
						
			        	if(data.success) {
			        		animation.removeClass('show'); 
			                 
				
							var removeLi =	row.remove();


							if($('#task-list').has('li')) {
								$(this).show();
							}
							
							if(removeLi.length > 0 ) {
								 $('#task-list li').each(function() {
							            $(this).find('span.number').html($(this).index() + 1)
							      });
							}
							
							if( $('.completed-task').has('ul').length < 1 ) {
								$('.completed-task').html('<ul id="task-completed" class="task-list"></ul>');
							}
							else {
								$('.completed-task ul').show();
								remove_notification('completed-task');
							}


								if(data.data.only_list == '') {
									//show_action_message(data.data.message);
									

									remove_notification('completed-task');


									if(data.data.content.length < 1 && data.data.link.length > 1) {
										$('#task-completed').append('<li class="task-row" id="' + data.data.id + '"><div class="content-holder"><div class="task-name"><p class="title">' +  data.data.title + ' </p><p class="link">' + data.data.link + '</p> <span class="date">( Completed: Today )</span></div><div class="btn-col"><span class="btn-undo fa fa-undo"></span><span class="btn-delete fa fa-trash"></span></div></div></li>');
									}
									else if(data.data.content.length < 1 && data.data.link.length < 1) {
										$('#task-completed').append('<li class="task-row" id="' + data.data.id + '"><div class="content-holder"><div class="task-name"><p class="title">' +  data.data.title + ' </p> <span class="date">( Completed: Today )</span></div><div class="btn-col"><span class="btn-undo fa fa-undo"></span><span class="btn-delete fa fa-trash"></span></div></div></li>');
									}
									else {
										var text = nl2br(data.data.content);
										$('#task-completed').append('<li class="task-row" id="' + data.data.id + '"><div class="content-holder"><div class="task-name"><p class="title">' +  data.data.title + ' </p><p class="link">' + data.data.link + '</p> <span class="view-more">more details...</span><span class="date">( Completed: Today )</span><div class="details">'+ text + '</div></div><div class="btn-col"><span class="btn-undo fa fa-undo"></span><span class="btn-delete fa fa-trash"></span></div></div></li>');
									}

									
								}
								else {
									remove_notification('completed-task');
				

									if(data.data.content.length < 1) {
										$('#task-completed').append('<li class="task-row" id="' + data.data.id + '"><div class="content-holder"><div class="task-name"><p class="title">' +  data.data.title + ' </p> <span class="date">( Completed: Today )</span></div><div class="btn-col"><span class="btn-undo fa fa-undo"></span><span class="btn-delete fa fa-trash"></span></div></div></li>');
									}
									else {
										$('#task-completed').append('<li class="task-row" id="' + data.data.id + '"><div class="content-holder"><div class="task-name"><p class="title">' +  data.data.title + ' </p> <span class="view-more">more...</span><span class="date">( Completed: Today )</span><div class="details">'+ data.data.content + '</div></div><div class="btn-col"><span class="btn-undo fa fa-undo"></span><span class="btn-delete fa fa-trash"></span></div></div></li>');
									}

									//show_action_message(data.data.message);
									$('.pending-task').append('<p class="notification-msg">' + data.data.only_list + '</p>');
									hide_list('#task-list');
									show_list('#task-completed');
								}

							do_indexing();

				         } else {
				            //$(response_msg).html('');
			                //$(response_msg).append(data.data.message).show(500).delay(4000).hide(500);
				         }
			 
			          
			        },
			 
			        error: function(MLHttpRequest, textStatus, errorThrown) {
			            alert(errorThrown);
			        }
			 
			});	
				
	});

	/* ==============================================
		For consultant page
		Post Member Task to the database

	============================================== */


    $('#postTask').on('click', function(e){
    		e.preventDefault();
  
    		var title   = $('#todo-title').val(),
    			link   = $('#todo-link').val(),
    			id   = $('#todo-id').val(),
    		    content = $('#todo-content').val(),
    		    post_nonce_field = $('#post_nonce_field').val(),
    		    last_list = false;


    		    if(title.length < 1) {
    		    	 alert('Please add Task title');
    		    	 return false;
    		    }
    		 
    		
    			
    			$('.loading').addClass('show');

    			if( (task_list + ' li').length <= 1) {
    				$('.notification-msg').hide();
    				last_list = true;
    			}

    		    $.ajax({
			 		url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
			        data: {
			            action: 'add_member_task',
			            title: title,
			            content: content,
			            id : id,
			            link: link,
			            post_nonce_field: post_nonce_field,

			        },
	 
			        success: function(data, textStatus, XMLHttpRequest) {
						
			        	if(data.success) {
				            $('.loading').removeClass('show');
							resetvaluesx(); 
							var tbl_data = [];

							tbl_data['title'] = title;
							tbl_data['content'] = content;
							tbl_data['item'] = $('.bb_table_list tbody').find('tr:last-child td:first-child').text();

							add_data_row(tbl_data);
				        }
						
			        },
			 
			        error: function(MLHttpRequest, textStatus, errorThrown) {
			            alert(errorThrown);
			        }
			 
		    });	

    		function resetvaluesx() {
	 
			    var title = document.getElementById("todo-title"),
			    	content = document.getElementById("todo-content"),
			    	link = document.getElementById("todo-link");
			    	title.value = '';
			    	content.value = '';
			    	link.value = '';
			 
			}
    });





	/* ==============================================

		Post To Do to the database

	============================================== */


    $('#postTodo').on('click', function(e){

  			e.preventDefault();
    		var title   = $('#todo-title').val(),
    			link   = $('#todo-link').val(),
    		    content = $('#todo-content').val(),
    		    post_nonce_field = $('#post_nonce_field').val(),
    		    last_list = false;


    		    if(title.length < 1) {

		  			if(!$(this).closest('form').find('#todo-title').hasClass('input-error')) {
		  				$(this).closest('form').find('#todo-title').addClass('input-error');
		  				$(this).closest('form').prepend('<div class="error">Please enter task name</div>');
		  			}
    		  			
    		  			return false;
    	
    		    }


    		    if( link.length > 0) {
    		  		var validURL = isURL(link);

    		  		if(validURL != true) {
    		  			console.log('url test');
    		  			if(!$(this).closest('form').find('#todo-link').hasClass('input-error')) {
    		  				$(this).closest('form').find('#todo-link').addClass('input-error');
    		  				$(this).closest('form').prepend('<div class="error">Please use a valid Link</div>');
    		  			}
    		  			
    		  			return false;
    		  		}
    		  		else {
    		  			link = link;

    		  			if($(this).closest('form').find('#todo-link').hasClass('input-error')) {
							$(this).closest('form').find('.input-error').remove();
    		  				$(this).closest('form').find('#todo-link').removeClass('input-error');
    		  				$(this).closest('form').find('#todo-link').val('');
    		  			}
    		  		}
    			}
		
    			var last_menu_no = $('#task-list li:last-child .number').text();
    			

    			if(!isDoing) {
    		    	isDoing = true;
					$(this).addClass('noclick');
				}
	
    			if(!last_menu_no) {
    				last_menu_no = 0;
    			} 
    			
    			$('.loading').addClass('show');

    			if( (task_list + ' li').length <= 1) {
    				$('.notification-msg').hide();
    				last_list = true;
    			}

    		    $.ajax({
			 		url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
			        data: {
			            action: 'mybbtodo_addpost',
			            title: title,
			            content: content,
			            link: link,
			            post_nonce_field: post_nonce_field,
			            menu_order: last_menu_no
			        },
	 
			        success: function(data, textStatus, XMLHttpRequest) {
						
			        	if(data.success) {
				            $('.loading').removeClass('show');
							

			                if ($('#task-list').length != 0) {
			                	var totalLi = document.getElementById("task-list").getElementsByTagName("li").length;
			            	}

			                if ( $('#task-list').length > 0 && totalLi >= 1 ) {

			                	show_action_message(data.data.message);

			                	if(data.data.content.length < 1) {
								
								$('#task-list').append(li_template(data.data.id, data.data.menu_order, data.data.title, data.data.date, '', '', true, data.data.link)); 
				                  	 console.log('hey');
			                	}
			                	else {
			                		$('#task-list').append(li_template(data.data.id, data.data.menu_order, data.data.title, data.data.date, '', data.data.content, true, data.data.link)); 
				                  	  
			                	
				            	}
				                do_indexing();

			            


			            	}
			            	else if ( $('#task-list').length > 0 && totalLi == 0 ) {
			            	  show_action_message(data.data.message);

					                
									remove_notification('pending-task');

					              	if(data.data.content.length < 1) {
											//$('#task-list').append(
				                  	  //'<li class="task-row test ui-sortable-handle" id="'+ data.data.id +'""><div class="tick-box"><span  class="btn btn-circle fa fa-check"></span></div><div class="content-holder"><div class="task-name"><span class="number">' + data.data.menu_order + '</span><div class="wrap-task"><p class="title">' +  data.data.title + '</p><span class="date">('+ data.data.date +')</span></div></div><div class="btn-col"><span class="btn-edit fa fa-pencil"></span><span class="btn-delete fa fa-trash"></span></div></div></li>');
											$('#task-list').append(li_template(data.data.id, data.data.menu_order, data.data.title, data.data.date, '', data.data.content, true)); 
				                  	  		
					                	}
					                	else {

						                	$('#task-list').append(li_template(data.data.id, data.data.menu_order, data.data.title, data.data.date, '', data.data.content, true)); 
				                  	  		
						            	}
				                do_indexing();
			                    task_list.show();
			               

			            	}
			            	else if ($('#task-list').length == 0) {

			            	  $(response_msg).html('');
			                  $(response_msg).append('Please wait, we are currently loading your list').show(300);

			            		
			            	}


							
				         } else {

				            $(response_msg).html('');
			                $(response_msg).append(data.data.message).show(500).delay(4000).hide(500);
			                console.log(data.data.message + 'failed');
				         }

			 			isDoing = false;
						$('#postTodo').removeClass('noclick');
						$('.wp-editor .error').remove();
						$('.wp-editor input').removeClass('input-error');
			            resetvalues();

			            			    $( ".datepicker").datepicker({
								              		dateFormat: 'D M d',
								              		altField:  ".alternate",
							      					altFormat: "yy-mm-dd",
							      					minDate: 0,
								              		onSelect: function(dateText) {
								              			 $(this).closest('li').find('.submitDate').addClass('show');
								              		}
								        });
			        },
			 
			        error: function(MLHttpRequest, textStatus, errorThrown) {
			            alert(errorThrown);
			        }
			 
		    });	

    		function resetvalues() {
	 
			    var title = document.getElementById("todo-title"),
			    	content = document.getElementById("todo-content"),
			    	link = document.getElementById("todo-link");

			    title.value = '';
			    content.value = '';
			    link.value = '';
			 
			 
			}
    });







	/* ==============================================

		(SUBTASKS)  - Post To Do to the database

	============================================== */


    $('#task-list').on('click', '.subtasks .add-task .add-subtask', function(e){

    	e.preventDefault();

			var id = findParentID(this);
    

  
    		var title   = $(this).closest('form').find('.at-name').val(),
    			link   = $(this).closest('form').find('.at-link').val(),
    		    content = $(this).closest('form').find('.at-notes').val(),
    		    post_nonce_field = $('.add-task #subpost_nonce_field').val(),
    		    parent_id   = $(this).closest('.task-row').attr('id'),
    		    last_list = false;


    		    if(title.length < 1) {
    		    	 alert('Please add Task title');
    		     	 return false;
    		    }
    		 

    			var last_menu_no = $('#task-list li:last-child .number').text();
    			

    			if(!isDoing) {
    		    	isDoing = true;
					$(this).addClass('noclick');
				}
	
    			if(!last_menu_no) {
    				last_menu_no = 0;
    			} 
    			
    			$('.loading').addClass('show');

    		    $.ajax({
			 		url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
			        data: {
			            action: 'mybbtodo_addsubpost',
			            title: title,
			            content: content,
			            link: link,
			            post_nonce_field: post_nonce_field,
			            id : parent_id
			  
			        },
	 
			        success: function(data, textStatus, XMLHttpRequest) {
						
			        	if(data.success) {
				            $('.loading').removeClass('show');

								var tasks = data.data.tasks,
								listing = [],
								list = [];
						
							
								//generate subtask lists
								var list = create_subtask_lists(data.data.title, data.data.content,data.data.date, data.data.id,

									);
								
								$('#' + parent_id + ' .subtask-lists').append(list);
									
								clear_input();	

								function clear_input() {
	

									var form = $('#' + parent_id + ' .pre-subtask .add-task').toggleClass('show');
								
									$('#' + parent_id + ' .pre-subtask').toggleClass('hide');
									
								}	
								isDoing = false;

								var  tag = $('#' + id + ' .subtasks ');
									 tag.find('input.at-name').val('');
									 tag.find('input.at-notes').val('');
									 tag.find('.task .add-task').removeClass('show');


								$('#postTodo').removeClass('noclick');	
								$('.add-subtask').removeClass('noclick');


							
							
				         } else {

				            $(response_msg).html('');
			                console.log(data.data.message + 'failed');
				         }
			 			
						

			        },
			 
			        error: function(MLHttpRequest, textStatus, errorThrown) {
			            alert(errorThrown);
			        }

			        
			 
		    });	



    });









	/* ==============================================

		(SUBTASKS)  - Tick to complete  

	============================================== */


    $('#task-list').on('click', '.subtask-lists .tick-btn', function(e){

    		e.preventDefault();


    		var id = $(this).closest('.task').attr('id'),
    		  	post_nonce_field = $('.subtask-lists #subtask_nonce_field').val();
		

    			if(!isDoing) {
    		    	isDoing = true;
					//$(this).addClass('noclick');
				}
    				
    			console.log(id);	

    			$('.loading').addClass('show');

    		    $.ajax({	

			 		url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
			        data: {
			            action: 'mybbtodo_completedsubpost',
			            security: post_nonce_field,
			            id: id

			        },
	 
			        success: function(data, textStatus, XMLHttpRequest) {

						console.log('clic2k');

			        	if( data.success ) {

				            $('.loading').removeClass('show');

				            $('#' + id).closest('.task').addClass('completed');
							
							isDoing = false;
							
				        } 

			        },
			 
			        error: function(MLHttpRequest, textStatus, errorThrown) {
			            alert(errorThrown);
			        }

			        
			 
		    });	

		    isDoing = false;

    });



















/* ==============================================

	Click to show edit TO Do

============================================== */


  $('#task-list').on('click', '.btn-edit', function(){

   			var post_id = $(this).closest('li.task-row').attr('id'),
   	    		edit_title = $('#'+ post_id + ' .content-holder .wrap-task .title'),
   	    		source = $('#' + post_id  + ' .wrap-task').find('.title'),
			    title = $('#'+ post_id + ' .content-holder .title').text(),
			    response = '', 
			    loader = $(this).closest('li').find('.btn-col .loader');

			    var getTitle = $('#'+ post_id + ' .wrap-task .title').val();

	
			    if( !isDoing ) {
                     isDoing = true;
                     $(this).addClass('active');
                     $('#' + post_id + ' .btn-delete').addClass('noclick');
                      
			    } 	
			    $('#' + post_id + ' .tick-box .btn').addClass('noclick');

				if(('#' + post_id + ' .fa-close').length > 0) {
						isDoing = true;
						$(this).addClass('active');
						$('#' + post_id).addClass('noclick');
				}
			 
			    if(loader.length < 1) {
					var Ulrow = $(this).closest('li').find('.btn-col').prepend('<span class="loader"></span>');
				}
			   
			  	$.ajax({

			        url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
			        data: {
			            action: 'mybbtodo_getPostDetails',
			            id: post_id,
			            security: mybb_ajax.security

			        },

			        success: function(data, textStatus, XMLHttpRequest) {
						
			        	if(data.success) {
			        		var  view_button = $('li#' + post_id).find('.view-more');

							$('li#' + post_id).find('.loader').remove();


								view_button.removeClass('show-less').text('more...');
							
								$('li#' + post_id).find('.view-more').toggleClass('hide');
								$('li#' + post_id).find('.date').toggleClass('hide');
								$('li#' + post_id).find('.timer').toggleClass('hide');
								$('li#' + post_id).find('.link').toggleClass('hide');
								$('li#' + post_id).find('.details').removeClass('show').addClass('hide');

							
			                       response = data.data.content;   

			                   		if(source.hasClass('open') != true) {


										$('#' + post_id + ' .btn-edit .fa-pencil').removeClass('fa-pencil active').addClass('fa-close');
										edit_title.html('<input id="edit-title" value="' + data.data.title + '" /><span class="note">Note: link should start with http or https (e.g http://my.businessblueprint.com )</span><input id="edit-link" value="'+ data.data.link  +'" /><textarea id="edit-content">'+ response +'</textarea><input type="submit" class="btn edit-button" value="Save Changes"/> or <span class="btn-cancel">Cancel</span>');
										
										$('#' + post_id).removeClass('noclick');
										$('#' + post_id + ' .btn-delete').removeClass('noclick');
										source.addClass('open');
							
									}
									else {
										if($('#edit-title').val()) {
											title = $('#edit-title').val();
										}
										source.html(title);
										source.removeClass('open');
										
										$('#' + post_id + ' .btn-edit .fa-pencil').removeClass('fa-close ').addClass('fa-pencil');
										isDoing = false;
										if(('#' + post_id + ' .fa-close').length > 0) {
												isDoing = false;
												$('#' + post_id + ' .btn-edit').removeClass('active');
												$('#' + post_id).removeClass('noclick');
										}
										
									}
									
									
								
			          
				         } else {
				         	response = data.data;
				         
				         }

			  			
			          
			        },
			 
			        error: function(MLHttpRequest, textStatus, errorThrown) {
			            alert(errorThrown + 'error');
			        }
			 
		    });

			   	
   });


/* ==============================================

	When BUTTON view more is click

============================================== */
task_list.on('click', '.view-more', function(){
    var detail = $(this).closest('li').find('.details');

    if(!$(this).hasClass('show-less')) {
   		$(this).text('').addClass('show-less').text('less...'); 

   		$('ul#task-list').addClass('open-content');
   		if ($(this).closest('li').find('.details').text() === '')
		 {	
		 	detail.html('<div class="text-warning">' + mybb_ajax.content_msg +'</div>');
		 }
   		$(this).closest('li').find('.details').removeClass('hide').addClass('show');
   	}
   	else if($(this).hasClass('show-less')) { 
   		$(this).text('').removeClass('show-less').text('more...');

   		$('ul#task-list').removeClass('open-content');
   		$(this).closest('li').find('.details').removeClass('show');

   	}



});

completed_list.on('click', '.view-more', function(){
    var detail = $(this).closest('li').find('.details');

    if(!$(this).hasClass('show-less')) {
   		$(this).text('').addClass('show-less').text('less...'); 
   		if ($(this).closest('li').find('.details').text() === '')
		 {	
		 	detail.html('<div class="text-warning">' + mybb_ajax.content_msg +'</div>');
		 }
   		$(this).closest('li').find('.details').removeClass('hide').addClass('show');
   	}
   	else if($(this).hasClass('show-less')) { 
   		$(this).text('').removeClass('show-less').text('more details..');
   		$(this).closest('li').find('.details').removeClass('show');

   	}



});

suggested_list.on('click', '.view-more', function(){
    var detail = $(this).closest('li').find('.details');

    if(!$(this).hasClass('show-less')) {
   		$(this).text('').addClass('show-less').text('less...'); 
   		if ($(this).closest('li').find('.details').text() === '')
		 {	
		 	detail.html('<div class="text-warning">' + mybb_ajax.content_msg + '</div>');
		 }
   		$(this).closest('li').find('.details').removeClass('hide').addClass('show');
   	}
   	else if($(this).hasClass('show-less')) { 
   		$(this).text('').removeClass('show-less').text('more...');
   		$(this).closest('li').find('.details').removeClass('show');

   	}



});

/* ==============================================

	Cancel Editing post

============================================== */

task_list.on('click', '.btn-cancel', function(event){

	var post_id = $(this).closest('li.task-row').attr('id'),
		orig_title = $('li#' + post_id).find('.orig_title').text();

 		$('li#' + post_id).find('.view-more').toggleClass('hide');
        $('li#' + post_id).find('.date').toggleClass('hide');
        $('li#' + post_id).find('.timer').toggleClass('hide');
        $('li#' + post_id).find('.link').toggleClass('hide');

		$('li#' + post_id).find('.btn-col .btn-edit').toggleClass('active');
		$('li#' + post_id).find('.btn-col .fa-close').removeClass('fa-close').addClass('fa-pencil');

		if( $('#' + post_id + ' .wrap-task').find('.title').hasClass('open') ) {
			 $('#' + post_id + ' .wrap-task').find('.title').removeClass('open'); 
		}
		console.log('test ' + post_id);
         $('#' + post_id + ' .wrap-task').find('.title').html(orig_title);
         $('#' + post_id + ' .tick-box .btn').removeClass('noclick');
         $('#' + post_id).removeClass('error');
		

});



/* ==============================================

	Edit Button and Save Changes

============================================== */

   task_list.on('click', '.edit-button', function(event){

		
    		var post_id = $(this).closest('li.task-row').attr('id'),
    			btn_edit = $('#' + post_id).find('.btn-edit'),
    			source = $('#' + post_id).find('.title'),
			    title = $('#'+ post_id).find('#edit-title').val(),
			    link = $('#'+ post_id).find('#edit-link').val(),
			    content = $('#'+ post_id).find('#edit-content').val(),
			    convert_link = '',
			    is_click = 0;
			
    		    if(title.length < 1) {

		  			if(!$(this).closest('.wrap-task').find('#edit-title').hasClass('input-error')) {

		  				$(this).closest('.wrap-task').find('#edit-title').addClass('input-error');
		  				$(this).closest('.wrap-task').prepend('<div class="error">Please enter task name</div>');
		  			

		  			}
    		  		return false;	
    		  			
    	
    		    }


    		    if( link.length > 0) {
    		  		var validURL = isURL(link);

    		  		if(validURL != true) {
    		  			console.log('url test');
    		  			if(!$(this).closest('.wrap-task').find('#edit-link').hasClass('input-error')) {
    		  				$(this).closest('.wrap-task').find('#edit-link').addClass('input-error');
    		  				$(this).closest('.wrap-task').prepend('<div class="error">Please enter a valid link</div>');
    		  			}
    		  			
    		  			return false;
    		  		}
    		  		else {
    		  			link = link;

    		  			if($(this).closest('.wrap-task').find('#edit-link').hasClass('input-error')) {
							$(this).closest('.wrap-task').find('.input-error').remove();
    		  				$(this).closest('.wrap-task').find('#edit-link').removeClass('input-error');
    		  				$(this).closest('.wrap-task').find('#edit-link').val('');
    		  			}
    		  		}
    			}

    			source.removeClass('open')
				animation.addClass('show');

				console.log(event.detail);
				 if(!event.detail || event.detail == 1 && is_click <= 0 ){
				   var Ulrow = $(this).after(' <span class="text text-primary">Please wait...</span>');
				   $(this).closest('li').addClass('noclick');
				  }


				
				//console.log(content)
				//convert_link = convert_link(content);
			
    		    $.ajax({
			 		url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
			        data: {
			            action: 'mybbtodo_editpost',
			            title: title,
			            content: content,
			            id: post_id,
			            link: link,
			            security: mybb_ajax.security
			        },
	 
			        success: function(data, textStatus, XMLHttpRequest) {
						
			        	if(data.success) {
				      		animation.removeClass('show');
				      		var content_wp = data.data.content;
				   
				      		 var text = nl2br(content_wp);
				      		 $('#' + post_id + ' .wrap-task').find('.error').remove();
	


				      		 if( $('#' + post_id).find('.details').length > 0 ) {

							 	    $('#' + post_id).find('.details').html('').html(text);
			

							 	    //div 1 and data 1
								 	if($('li#' + post_id + ' .wrap-task').find('.link').length > 0   &&  data.data.link.length > 0) {
								 		$('li#' + post_id + ' .wrap-task').find('.link.hide').html('').html(data.data.link);
								 	}
								 	else if($('li#' + post_id + ' .wrap-task').find('.link').length < 1  &&  data.data.link.length > 0) {
								 		//div 0 and data 1
								 		$('li#' + post_id + ' .wrap-task').find('.title').after('<p class="link hide">'+ data.data.link +'</p>')
								 	}
								 	else if($('li#' + post_id + ' .wrap-task').find('.link').length > 0  &&  data.data.link.length < 1) {
								 		//div 1 and data 0
								 		$('li#' + post_id + ' .wrap-task').find('.link').remove();
								 	} else {
								 		//$('li#' + post_id).find('.title').after('<p class="link hide">'+ data.data.link +'</p>');
								 	}

							 } else {
							 	
							 	$('#' + post_id).find('.timer').before('<span class="view-more hide">more...</span> ');
							 	$('#' + post_id).find('.timer').after('<div class="details">' + text + '</div>');
 								

	 								 	    //div 1 and data 1
								 	if($('li#' + post_id + ' .wrap-task').find('.link').length > 0   &&  data.data.link.length > 0) {
								 		$('li#' + post_id + ' .wrap-task').find('.link.hide').html('').html(data.data.link);
								 	}
								 	else if($('li#' + post_id + ' .wrap-task').find('.link').length < 1  &&  data.data.link.length > 0) {
								 		//div 0 and data 1
								 		$('li#' + post_id + ' .wrap-task').find('.title').after('<p class="link hide">'+ data.data.link +'</p>')
								 	}
								 	else if($('li#' + post_id + ' .wrap-task').find('.link').length > 0  &&  data.data.link.length < 1) {
								 		//div 1 and data 0
								 		$('li#' + post_id + ' .wrap-task').find('.link').remove();
								 	} else {
								 		//$('li#' + post_id).find('.title').after('<p class="link hide">'+ data.data.link +'</p>');
								 	}

								if($('li#' + post_id).find('.view-more').hasClass('hide').length > 0) {
 									$('li#' + post_id).find('.view-more').toggleClass('hide');
 								}
							 } 
						

							 if(text.length < 1) {
							 	$('li#' + post_id).find('.view-more').remove();
							 }
							 else {
							 	if( $('li#' + post_id).find('.view-more').length < 1 ) {
							 		$('li#' + post_id).find('.timer').before('<span class="view-more hide">more...</span>');
							 	}
							 }

			                $('#' + post_id  + ' .wrap-task').find('.title').html('').text(data.data.title);
			                btn_edit.removeClass('fa-undo').find('.fa-close').removeClass('.fa-close').addClass('fa-pencil');
			                $('#' + post_id).removeClass('noclick');
 						    

				         } else {
				         
				            //$(response_msg).html('');
			                //$(response_msg).append(data.data.message).show(500).delay(4000).hide(500);
				         }
			 		   $('li#' + post_id).find('.view-more').toggleClass('hide');
			           $('li#' + post_id).find('.date').toggleClass('hide');
			           $('li#' + post_id).find('.timer').toggleClass('hide');
			           $('li#' + post_id).find('.link').toggleClass('hide');
			           $('li#' + post_id).find('.btn-col .btn-edit').toggleClass('active');


			           event.detail = 0;
			           $('#' + post_id + ' .tick-box .btn').removeClass('noclick');
			        },
			 
			        error: function(MLHttpRequest, textStatus, errorThrown) {
			            alert(errorThrown);
			        }
			 
		    });	


    });




  $(document).on('click', '.due-date', function(){
          var datepicker = $(this).closest('li').find('.hasDatepicker'),
          		id = $(this).closest('li').attr('id');


          datepicker.focus().addClass('reveal');
		//datepicker.datepicker();
          $(this).text('Due ');

         //  	$( ".select-" + id).datepicker({
	        //       		dateFormat: 'D M d',
	        //       		altField:  ".alternate",
      			// 		altFormat: "yy-mm-dd",
      			// 		minDate: 0,
	        //       		onSelect: function(dateText) {
	        //       			 $(this).closest('li').find('.submitDate').addClass('show');
	        //       		}
	        // });

  });

 $('body').on('click', '.auto-date', function(){
          var datepicker = $(this).closest('li').find('.hasDatepicker'),
          		id = $(this).closest('li').attr('id');


          datepicker.focus().addClass('reveal');
		//datepicker.datepicker();
          $(this).text('Due ');

          	$( ".datepicker").datepicker({
	              		dateFormat: 'D M d',
	              		altField:  ".alternate",
      					altFormat: "yy-mm-dd",
      					minDate: 0,
	              		onSelect: function(dateText) {
	              			 $(this).closest('li').find('.submitDate').addClass('show');
	              		}
	        });

  });
  /* ==============================================

	Update Due Date

============================================== */

   task_list.on('click', '.changeDate', function(event){
   	console.log('update date');

   	$(this).closest('li').find('.db-date').addClass('hide');
   	$(this).closest('li').find('.due-date').removeClass('hide');

   	$(this).closest('li').find('.update-calendar').addClass('reveal');

   	var datepicker = $(this).closest('li').find('.hasDatepicker');
   	datepicker.focus().addClass('reveal');
  

   });

   /* ==============================================

	Add Due Date

============================================== */
   task_list.on('click', '.submitDate', function(event){
			

    		var post_id = $(this).closest('li.task-row').attr('id'),
    			btn_edit = $('#' + post_id).find('.btn-edit'),
    			main = $('#' + post_id),
			    title = $('#'+ post_id).find('#edit-title').val(),
			    content = $('#'+ post_id).find('#edit-content').val(),
			    convert_link = '',
			    is_click = 0,
			    datepicker = $(this).closest('li').find('.alternate'),
			    due = $(this).closest('li').find('.submitDate'),
			    text_due_default = $(this).closest('p.timer').find('.addText'),
			    text_due = $(this).closest('p.timer').find('.text-date');
			
			   
				if(datepicker.length > 0) {
					var due_date = datepicker.val();

					console.log(due_date);
				}	
				due.addClass('show');
				

				
				 if(!event.detail || event.detail == 1 && is_click <= 0 ){
				   var Ulrow = $(this).after(' <span class="loader"></span>');
				   $(this).closest('li').addClass('noclick');
				  }

                
			
    		    $.ajax({
			 		url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
			        data: {
			            action: 'mybbtodo_duedate',
			            id: post_id,
			            due: due_date,
			            security: mybb_ajax.security
			        },
	 
			        success: function(data, textStatus, XMLHttpRequest) {
						
			        	if(data.success) {
				      		due.removeClass('show');
			
     						
			               
 						    $('#'+ post_id  + '').removeClass('noclick');
 						    $('#'+ post_id  + '').find('.loader').remove();

 						    text_due_default.remove();
							text_due.removeClass('hide');
								if($('#' + post_id + ' .changeDate').length != 1) {
									$('#' + post_id).find('button').after('<span class="fa fa-calendar-o changeDate"></span>');
								}
				         } else {
				         
				            //sucdess is true but doing nothing
				         }

			           
			        },
			 
			        error: function(MLHttpRequest, textStatus, errorThrown) {
			            alert(errorThrown);
			        }
			 
		    });	


    });
    
 /* ==============================================

	Click to delete To Do

============================================== */   

    task_list.on('click', '.btn-delete', function(){
   
  			var post_id = $(this).closest('li.task-row').attr('id');
  

    		    animation.addClass('show');
		
				var Ulrow = $('#task-list' + ' li#' +  post_id ).find('p.title').addClass('crash-out').after(' <span class="btn btn-danger">deleting..</span>');
    		    $('#' + post_id).addClass('noclick');
    
 			
    		    $.ajax({
			 		url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
			        data: {
			            action: 'mybbtodo_deletepost',
			            id: post_id,
			            security: mybb_ajax.security
			        },
	 
			        success: function(data, textStatus, XMLHttpRequest) {
					
			        	if(data.success) {
				            animation.removeClass('show');

					            $('#task-list li#' + post_id).remove();

					            if( $('#task-list').has('li').length < 1 ) {
									$('#task-list').hide();
								}
								do_indexing();
					            
							
								

				         } else {
				            
				            //$(response_msg).html('');
			                //$(response_msg).append(data.data.message).show(500).delay(4000).hide(500);
				         }
			 

			        },
			 
			        error: function(MLHttpRequest, textStatus, errorThrown) {
			            alert(errorThrown);
			        }
			 
		    });	

    });


    completed_list.on('click', '.btn-delete', function(){
   
  			var post_id = $(this).closest('li.task-row').attr('id');
  

    		    animation.addClass('show');

				var Ulrow = $('#task-completed' + ' li#' +  post_id ).find('p.title').addClass('crash-out').after(' <span class="btn btn-danger">deleting..</span>');
				$('#' + post_id).addClass('noclick');

    		    $.ajax({
			 		url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
			        data: {
			            action: 'mybbtodo_deletepost',
			            id: post_id,
			            security: mybb_ajax.security
			        },
	 
			        success: function(data, textStatus, XMLHttpRequest) {
					
			        	if(data.success) {
				            animation.removeClass('show');

		            			$('#task-completed li#' + post_id).remove();
									
								hide_list('#task-completed');
								if( $('#task-completed').has('li').length < 1 ) {
									$('.completed-task').append('<p class="notification-msg">'+ mybb_ajax.content_msg + '</p>');
								}

				         } else {
				            
				            $(response_msg).html('');
			                $(response_msg).append(data.data.message).show(500).delay(4000).hide(500);
				         }
			 			isDoing = false;

			        },
			 
			        error: function(MLHttpRequest, textStatus, errorThrown) {
			            alert(errorThrown);
			        }
			 
		    });	

    });


    suggested_list.on('click', '.btn-delete', function(){
   
  			var post_id = $(this).closest('li.task-row').attr('id');
  

    		    animation.addClass('show');

                  var Ulrow = $('#task-suggested' + ' li#' +  post_id ).find('p.title').addClass('crash-out').after(' <span class="btn btn-danger">removing..</span>');
				$('#' + post_id).addClass('noclick');


    		    $.ajax({
			 		url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
			        data: {
			            action: 'mybbtodo_removeSuggestedpost',
			            id: post_id,
			            security: mybb_ajax.security
			        },
	 
			        success: function(data, textStatus, XMLHttpRequest) {
					
			        	if(data.success) {
				            animation.removeClass('show');

		            			$('#task-suggested li#' + post_id).remove();

		            			if( $('#task-suggested').has('li').length < 1 ) {
									$('#task-suggested').hide();

								}
								

								 if( $('#task-suggested li').length < 1 ) {
				                	//show_action_message(data.data.message); 
				                	$('#task-suggested').before('<p class="notification-msg">' + mybb_ajax.empty_suggest + '</p>');
				                
				                }
				                else {
				                		//show_action_message(data.data.message); 
				                }


				         } else {
				            
				            $(response_msg).html('');
			                show_action_message(data.data.message); 
				         }
			 			isDoing = false;

			        },
			 
			        error: function(MLHttpRequest, textStatus, errorThrown) {
			            alert(errorThrown);
			        }
			 
		    });	

    });



   /* ==============================================

	Tick Box to add Suggested Task

============================================== */


	suggested_list.on('click', '.tick-box .btn', function(){


			var menu_order = $(this).attr('task-id'),
				    row = $(this).closest('.task-row'),
				    id = row.attr('id'),
				    title = $('#' + id).find('.title').text(),
				    link = $('#' + id).find('.link').text(),
				    content = $('#' + id).find('.details').text(),
				    only_list = '',
				    last_menu_no = $('#task-list li:last-child .number').text();
			

			if(!last_menu_no) {
				last_menu_no = 0;
			}
			else {
				last_menu_no + 1;
			}

			animation.addClass('show'); 
			$('#task-suggested' + ' li#' +  id ).find('p.title').addClass('crash-out').after(' <span class="btn btn-danger">Adding task to your list...</span>');
			$('#' + id).addClass('noclick');
			

            if( $('#task-suggested li').length <= 1 ) {
            	only_list = mybb_ajax.completed_suggest;
            }
            else {
            	only_list = '';
            }
			$.ajax({
			 		url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
			        data: {
			            action: 'mybbtodo_suggestedpost',
			            id: id,
			            only_list: only_list,
			            title: title,
			            link: link,
			            content: content,
			            menu_order : last_menu_no,
			            security: mybb_ajax.security
			        },
	 
			        success: function(data, textStatus, XMLHttpRequest) {
						
			        	if(data.success) {
			        		animation.removeClass('show'); 
			                 

								$('#task-suggested li#' + id).remove();

								hide_list('#task-suggested');

 								//$('#task-list').append(
		                  	  //'<li class="task-row ui-sortable-handle" id="'+ data.data.id +'""><div class="tick-box"><span class="btn btn-circle fa fa-check"></span></div><div class="content-holder"><div class="task-name"><span class="number">' + data.data.menu_order + '</span><div class="wrap-task"><p class="title">' +  data.data.title + '</p><span class="view-more">more...</span><p class="timer">( <span class="date">Date Started: '+ data.data.date +'</span><span class="fa fa-long-arrow-right"></span><span class="addText due-date">Add due date</span><span class="date text-date hide">Due </span><input type="text" class="datepicker select-'+ data.data.id +' hasDatepicker" dateid="'+ data.data.id +'"><input type="hidden" class="alternate long-format-'+ data.data.id +'"><button class="submitDate">Submit</button> )</p><div class="details">'+ nl2br(data.data.content) +'</div></div></div><div class="btn-col"><span class="btn-edit fa fa-pencil"></span><span class="btn-delete fa fa-trash"></span></div></div></li>');
								$('#task-list').append(li_template(data.data.id, '', data.data.title, data.data.date, '', data.data.content, false, data.data.link));
	 									
				                do_indexing();

				                remove_notification('pending-task'); 


				                if( $('#task-suggested li').length < 1 ) {
				                	show_action_message(data.data.only_list); 
				                	$('#task-suggested').before('<p class="notification-msg">' + mybb_ajax.empty_suggest + '</p>');
				                }
				                else {
				                	var text = data.data.title + ' ' +  data.data.message;
				                	//$('.response-wrap').append('<div id="msg-response" class="alert" style="display:block;">' + text + ' <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span></div>');
				                	//show_action_message(text); 
				                }
							isDoing = false;
								$( ".datepicker").datepicker({
					              		dateFormat: 'D M d',
					              		altField:  ".alternate",
				      					altFormat: "yy-mm-dd",
				      					minDate: 0,
					              		onSelect: function(dateText) {
					              			 $(this).closest('li').find('.submitDate').addClass('show');
					              		}
								});
				         } else {
				            $(response_msg).html('');
			                //show_action_message(data.data.message); 
				         }
			 		
			          
			        },
			 
			        error: function(MLHttpRequest, textStatus, errorThrown) {
			            alert(errorThrown);
			        }
			 
			});	
				
	});

/* ==============================================

	Do indexing or sort the list

============================================== */

   completed_list.on('click', '.btn-undo', function(){
   
  			var post_id = $(this).closest('li.task-row').attr('id'),
  				title = $('#task-completed #'+ post_id + ' .content-holder .title').text(),
  				link = $('#task-completed #'+ post_id + ' .content-holder .link').text(),
  				last_menu_no = $('#task-list li:last-child .number').text();

    		    animation.addClass('show');

 				 $('#task-completed' + ' li#' +  post_id ).find('p.title').addClass('crash-out').after(' <span class="btn btn-danger">Adding back to your list..</span>');
    		     $('#' + post_id).addClass('noclick');

				if(!last_menu_no) {
    				last_menu_no = 0;
    			}
    			else {
    				last_menu_no + 1;
    			}

    		    animation.addClass('show');

    		    $.ajax({
			 		url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
			        data: {
			            action: 'mybbtodo_undoCompletedpost',
			            id: post_id,
			            title: title,
			            link: link,
			            last_menu: last_menu_no,
			            security: mybb_ajax.security
			        },
	 
			        success: function(data, textStatus, XMLHttpRequest) {
					
			        	if(data.success) {
				            animation.removeClass('show');

		            			$('#task-completed li#' + post_id).remove();
	
								hide_list('#task-completed');
								show_list('#task-list');

									if(data.data.content.length < 1) {
										$('#task-list').append(li_template(data.data.id, '', data.data.title, data.data.date, '', data.data.content, false, data.data.link));
									}
									else {
										
										$('#task-list').append(li_template(data.data.id, '', data.data.title, data.data.date, '', data.data.content, false, data.data.link));
	 									//$('#task-list').append('<li class="task-row ui-sortable-handle" id="'+ data.data.id +'""><div class="tick-box"><span class="btn btn-circle fa fa-check"></span></div><div class="content-holder"><div class="task-name"><span class="number">' + data.data.menu_order + '</span><div class="wrap-task"><p class="title">' +  data.data.title + '</p><span class="view-more">more...</span><p class="timer">( <span class="date">Date Started: '+ data.data.date +'</span><span class="fa fa-long-arrow-right"></span><span class="addText due-date">Refresh to add due date</span><span class="date text-date hide">Due </span><input type="text" class="datepicker select-'+ data.data.id +' hasDatepicker" dateid="'+ data.data.id +'"><input type="hidden" class="alternate long-format-'+ data.data.id +'"><button class="submitDate">Submit</button> )</p><div class="details">'+ nl2br(data.data.content) +'</div></div></div><div class="btn-col"><span class="btn-edit fa fa-pencil"></span><span class="btn-delete fa fa-trash"></span></div></div></li>');

			                  	  		
	 								}
				                do_indexing();

				                remove_notification('pending-task');

				                if( $('#task-completed li').length < 1 ) {
				                	$('.completed-task').append('<p class="notification-msg">Start completing tasks</p>');
				                	show_action_message(data.data.message); 
				                }

										$( ".datepicker").datepicker({
								              		dateFormat: 'D M d',
								              		altField:  ".alternate",
							      					altFormat: "yy-mm-dd",
							      					minDate: 0,
								              		onSelect: function(dateText) {
								              			 $(this).closest('li').find('.submitDate').addClass('show');
								              		}
								        });
				         } else {
				            
				            //$(response_msg).html('');
			                //$(response_msg).append(data.data.message).show(500).delay(4000).hide(500);
				         }
			 

			        },
			 
			        error: function(MLHttpRequest, textStatus, errorThrown) {
			            alert(errorThrown);
			        }
			 
		    });	

    });

   /**
   *   Display Task list of the member
   * 	
   */
   $('.bb-member-list .members-list').on('click','.show-task', function(e){

   		e.preventDefault();

	  		var user_id = $(this).closest('li').find('div.user_id').text(),
	  		    name = $(this).closest('li').find('div.name').text(),
	  			email = $(this).closest('li').find('div.email').text(),
	  			joined_date = $(this).closest('li').find('div.joined_date').text(),
				year_level = $(this).closest('li').find('div.year_level').text(),
				country = $(this).closest('li').find('div.country').text(),
				sms_number = $(this).closest('li').find('div.sms_number').text();

		  		$('.bb-member-list .information .fullname').text(name);
		  		$('.bb-member-list .information .email').text(email);
		  		$('.bb-member-list .information .joined_date').text(joined_date);
		  		$('.bb-member-list .popup-add-task #todo-id').val(user_id);
		  		$('.bb-member-list .information .country').text(country);
		  		$('.bb-member-list .information .year_level').text(year_level);

		  		
		
		  		$.ajax({
			 		url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
		  			data : {
		  					action: 'getMember_posts',
		  					user_id : user_id,
		  					security: mybb_ajax.security
		  			},
		  			success: function(data, textStatus, XMLHttpRequest){
		  				
		  			

		  				if(data.data) {
		  					
		  					var tbl;

		  					tbl = regenerate_table_list('.bb_table_list', data.data);

		  					$('.view-member-tasks').html(tbl);
		  				}
		  			},
		  			error: function(MLHttpRequest, textStatus, errorThrown) {
		  				alert(errorThrown);
		  			}

		  		});


   });

   	/**
   	*  Add table row to member task
   	*/
   	function add_data_row(data)
   	{
   		var tbl = '';
   		var item;

   		if( data['item'] != 'N/A' )
   		{
			item = parseInt(data['item']) + 1;

   		} 
   		else
   		{
   			item = 1;
   			$('.bb_table_list tr:nth-child(2)').remove();
   		}

   		tbl = '<tr class="c-added"><td><span>'+ item +'</span></td><td>'+ data['title'] +'<div class="task-content">'+ data['content'] +'</div></td><td>Pending</td></tr>';

   		$('.bb_table_list tbody').append(tbl);
   	}


   	/**===========================================
   	* (Subtasks Delete)
   	* 
   	=========================================== */
    $('#task-list').on('click', '.task-row .delete-subtask', function(){
   			console.log('delete');

  			var post_id = $(this).closest('.task').attr('id');
  

    		    animation.addClass('show');
		
				if(!isDoing) {	

    		    	$('#' + post_id).addClass('noclick');
    		    	$('#' + post_id).find('.btn-col').addClass('noclick');
    			}
    			

    		    $.ajax({
			 		url: mybb_ajax.ajaxurl,
			        type: 'post',
			        dataType: 'json',
			        data: {
			            action: 'mybbtodo_deletesubtask',
			            id: post_id,
			            security: mybb_ajax.security
			        },
	 
			        success: function(data, textStatus, XMLHttpRequest) {
					
			        	if(data.success) {
				            animation.removeClass('show');

					            $('.subtasks #' + post_id).remove();
					            
							
								isDoing = false;

				         } 

			        },
			 
			        error: function(MLHttpRequest, textStatus, errorThrown) {
			            alert(errorThrown);
			        }
			 
		    });	

    });





    function regenerate_table_list($tablename = '', data = false) 
    {
    	$($tablename).remove();

    	return display_list_table(data);
    }

    function display_list_table(data) 
    {
            var tbl = '';
            tbl += '<h4 class="heading">Task List</h4>';
            // Table
            tbl += '<table cellspacing="0" class="bb_table_list">';
            // Heading
            tbl += '<tr><th>Item</th><th>Task Name</th><th>Status</th></tr>'; 

            if(data.query) {
            		var counter = 0;
		            for( var x = 1; x <= data.total; x++) {
		            	counter++;
		            	tbl += '<tr id="' + data.query[x]['ID'] + '" class="'+ data.query[x]['is_consultant'] +'">';
		            	tbl += '<td><span>'+ counter + '</span></td>';
		            	tbl += '<td>'+ data.query[x]['post_title'] + '<div class="task-content">' + data.query[x]['post_content']  + '</div></td>';
		            	tbl += '<td>'+ data.query[x]['status'] + '</td>';
						tbl += '</tr>';
		            }

        	}
        	else {
        		tbl += '<tr><td colspan="3">N/A</td></tr>';
	
        	}
            	
            
            tbl += '</table>';
            

            return tbl;
        
    }



   	function add_to_list(id) {
   		var output = '';

   		output = '<span class="date due-date hide">Due </span><input type="text" class="update-calendar datepicker select-' + id + ' dateID="'+ id +'" /><input type="hidden" class="update-button alternate long-format-'+ id +'" /> <button class="submitDate">Submit</button><span class="fa fa-calendar-o changeDate"></span>';
   	}

			/* ==============================================

				Do indexing or sort the list

			============================================== */

		    function do_indexing() {

		    	$('#task-list li').each(function() {
		    	
					$(this).find('span.number').html($(this).index())
					});
		    }


			function remove_notification(task) {

			  	$('.' + task + ' .notification-msg').remove();
			}


			/* ==============================
				HIDE LIST IF NO LI CHILDREN AVAILABLE
			*/
			 function hide_list(taskID) {

			  	if( $(taskID).has('li').length < 1 ) {
						$(taskID).hide();
				}

			}
			function show_list(taskID) {

				$(taskID).show();
			
			}
			function notify_tab_suggest($task) {
					var notify = '';

				switch ($task) {

					case 'suggest':
							notify = 'We will be adding suggestion soon.';
					     break;
					default:
							notify = 'Information coming soon';

				}
			}

			function show_action_message(message) {
				var response_msg = $('#msg-response');

					$(response_msg).html('');
                	$(response_msg).append(message);
					$(response_msg).show().fadeOut(4000)

			}
			function nl2br(str, is_xhtml) {
			    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
			    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
			}
				
			$( function() {
	              $( ".datepicker").datepicker({
	              		dateFormat: 'D M d',
	              		altField:  ".alternate",
      					altFormat: "yy-mm-dd",
      					minDate: 0,
	              		onSelect: function(dateText) {
	              			 $(this).closest('li').find('.submitDate').addClass('show');
	              		}
	              });
            } ); 

			/**
			*  Create a template that display a links
			*
			*/
            function li_template(id, menu_order = '', title, start_date, due_date = '', content = '', view_more = false, link = '') {
		    	var output = '', tick_btn = '', holder_start = '',
				    task_start = '', menu_order = '', start_wrap = '', title_text = '',
					start_time = '', date_start = '', task_notes = '', end_time = '',
					view_more = '', end_wrap = '', task_end = '', holder_end = '', end = '',
					button_start = '', btn_edit = '', btn_delete = '', button_end = '', 
					holder_end = '', end = '', arrow = '', add_option = '', start, btn_add = '', subtasks = '';
		    	

		    	 start = '<li class="task-row test ui-sortable-handle" id="'+ id +'"">';
		    	      tick_btn = '<div class="tick-box"><span  class="btn btn-circle fa fa-check"></span></div>';
		    	   		holder_start = '<div class="content-holder">';
		                      task_start = '<div class="task-name">';
		                      
		    	   			   menu_order = '<span class="number">' + menu_order + '</span>';	
		    	   			      start_wrap = '<div class="wrap-task">'; 
		    	   			   	     title_text = '<span class="orig_title hide">'+ title + '</span><p class="title">' +  title + '</p>'; 
		    	   			   	     view_more = '<span class="view-more">more...</span> ';	

		    	   			   	        start_time = '<p class="timer"> ( ';
		    	   			   	        	date_start = '<span class="date">Started '+ start_date +'</span>';
											arrow = ' <span class="fa fa-long-arrow-right"></span> ';
		    	   			   	        end_time = ' )</p>';
		    	   			   	        task_notes = '<div class="details">'+ nl2br(content) +'</div>';
		    	   			   	    
		    	   			   	  end_wrap = '</div>';	

		    	   			   	  subtasks = generate_subtasks();
		    	   			   task_end = '</div>'


		    	   			   button_start = '<div class="btn-col">';
		    	   			   		btn_add = '<span class="btn-add w-tip"><i class="tooltip">Subtasks</i><span class="fa fa-outdent"></span></span>';
		    	   			        btn_edit = '<span class="btn-edit w-tip"><i class="tooltip">Edit Task</i><span class="fa fa-pencil"></span></span>';
		    	   			        btn_delete = '<span class="btn-delete w-tip"><i class="tooltip">Delete Task</i><span class="fa fa-trash"></span></span>';
		    	   			   button_end = '</div>';
		    	   		holder_end = '</div>';    	   		
		    	 end = '</li>';

		    	 function add_link () {
			    	 if( link != '' ) {
			    	 	add_link = '<p class="link">' + link + '</p>'
			    	 }
			    	 else {
			    	 	add_link = '';
			    	 }
			    	 return add_link;
		    	}

		    	 if( due_date == '' ) {
		    	 	add_option = '<span class="addText auto-date d">Add due date</span>';
		    	 	add_option += '<span class="date text-date hide">Due </span>';
		    	 	add_option += '<input type="text" class="datepicker select-' + id + ' " dateid="' + id + '">';
		    	 	add_option += '<input type="hidden" class="alternate long-format-' + id + '"> <button class="submitDate">Submit</button>';
		    	 
		    	 } else {

		    	 }


		    	 function add_view_more() {
		    	 	if(content != '') {
		    	 		view_more = '<span class="view-more">more...</span> ';
		    	 	} else {
		    	 		view_more = '';
		    	 	}
		    	 	return view_more;
		    	 }
		    	function add_notes() {
		    	 	if(content != '') {
		    	 		task_notes = '<div class="details">'+ nl2br(content) +'</div>';
		    	 	} else {
		    	 		task_notes = '';
		    	 	}

		    	 	return task_notes;
		    	 }
		


		 		output = start + tick_btn + holder_start + task_start + menu_order + start_wrap; 
		 		  output += title_text + add_link() + add_view_more() + start_time;
		 		   	  output += date_start + arrow;
			 		      if(due_date == '') {
			 		      	output += add_option;
			 		      }
		 		      output += end_time;
		 		   output += add_notes();
		 		  output +=  end_wrap + subtasks + task_end;

		 		  output += button_start + btn_add + btn_edit + btn_delete + button_end;
		 		output += holder_end + end;
		 		
		 		return output;  

		    }  
             function generate_subtasks() {
             	var subtask = '';

				subtask += '<div class="subtasks">';
                       subtask += '<div class="heading">Subtasks</div>';
                               subtask += '<div class="subtask-lists"></div> <div class="task new-sub-task">';
            						subtask += '<div class="detail"> <p class="title pre-subtask"><span class="add-mtask"><span class="fa fa-plus tick-btn add-btn"></span> Add Task</span> | <span class="close-mtask"><span class="fa fa-close"></span> Close</span></p>';
										subtask += '<div class="add-task">';
                                             subtask += '<form><input type="text" class="input input-text at-name" placeholder="What\'s need to be done?">';
                                                                
                                                    subtask += '<div class="input-field task-textarea"><span class="fa fa-align-left"></span><div class="task-twrap"><textarea type="text" class="input input-textarea at-notes" placeholder="Task Notes"></textarea></div></div>';  

                                                        subtask += '<div class="input-field"> <input type="submit" class="add-subtask" value="Submit"> or<span class="close-form">Cancel</span></div></form></div>';
                                                          
                                                       subtask += '</div>';

                                                    subtask += '</div>'; 
                                                   
                                                 subtask += '</div>';

                       return subtask;

             }


			/** =============================================
			*  Create if input is a string
			*  @param string
			*/
		    function isURL(str) {
			  var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
			  '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name and extension
			  '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
			  '(\\:\\d+)?'+ // port
			  '(\\/[-a-z\\d%@_.~+&:]*)*'+ // path
			  '(\\?[;&a-z\\d%@_.,~+&:=-]*)?'+ // query string
			  '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
			  return pattern.test(str);
			}   
  });
})( jQuery );


