<?php


class Mybb_Todo_Activator {


	public static function activate() {


			global $wp_version;

			$exit_msg = __( 'MYBB To-Do List requires WordPress 3.8 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update.</a>', 'cleverness-to-do-list' );
			if ( version_compare( $wp_version, '3.8', '<' ) ) {
				exit( $exit_msg );
			}


			
	}


}
