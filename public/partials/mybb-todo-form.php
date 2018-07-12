<div class="editor">
	<div class="wp-editor todo-editor">
		<form>
		    <?php wp_nonce_field( 'task_post_nonce', 'post_nonce_field' ); ?>
		    <input type="text" id="todo-title" name="todoTitle" required placeholder="Task name *" />
		    <input type="text" id="todo-link" name="todoLink" placeholder="Task link (e.g http://my.businessblueprint.com)" />
		    <textarea type="text" id="todo-content" name="todoContent" placeholder="Task notes"></textarea>

		    <div class="input-field">
		    	<span class="close"><span class="fa fa-close"></span></span><button id="postTodo">Submit</button>
		    </div>
	    </form>
	</div>
</div>