<?php



class Mybb_Todo_i18n {



	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'mybb-todo',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
