$(document).ready(function(){

		( function($) {

			 var sortList = $('#task-list');
			 var animation = $('.loading');
			    sortList.sortable({
					    update: function(event, ui) { 
					                 animation.addClass('show'); 
							        $('#task-list li').each(function() {
							            $(this).find('span.number').html($(this).index())
							        });
								
							        console.log($(this).index());

					            	$.ajax({
									 		url: sortable_ajax.ajaxurl,
									        type: 'post',
									        dataType: 'json',
									        data: {
									            action: 'mybbtodo_updateMenuOrder',
									            order: sortList.sortable( 'toArray'),
									            security: sortable_ajax.security
									        },
							 
									        success: function(data, textStatus, XMLHttpRequest) {
									        	animation.removeClass('show');
									        
									        },
									        error: function(MLHttpRequest, textStatus, errorThrown) {
									            alert(errorThrown);
									          
									        }
									 
								    });

									
					    },
					    //revert: true,
  				       cancel: '#task-list li .details.show, #task-list li .title.open, #task-list li .datepicker, #task-list li .add-task, #task-list li .subtasks, #task-list li .btn-col, .wrap-task .btn-cancel'
 					
			    });
			    //.disableSelection();
				// var notsortList = $('#task-list');
			 //    notsortList.sortable({
			 //    	revert: true,
			 //    	cancel: '#task-list li .details.show, #task-list li .title.open'
			 //    });


			} )(jQuery);

	

});
