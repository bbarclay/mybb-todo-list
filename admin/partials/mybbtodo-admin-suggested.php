<?php

/**
* Plugin settings page
*/


?>
	<div class="wrap">
		<h2>Suggested List</h2>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<form method="post">
							<?php
							$this->todo_obj->prepare_items();
							$this->todo_obj->display(); ?>
						
						</form>
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
	</div>



