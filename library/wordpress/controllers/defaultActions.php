<?php
class defaultActions extends wv43v_action {
	/*******************************************************************
	 * Default actions of all types 
	 *******************************************************************/
	/*******************************************************************
	 * Routines used by the default actions
	 *******************************************************************/
	public function plugins_loadedWPaction() {
		load_plugin_textdomain ( get_class ( $this ), false, dirname ( plugin_basename ( $this->application()->filename ) ) . "/languages/" );
	}
	public function initWPaction() {
		wp_register_style ( 'jquery-ui_smoothness', $this->application ()->pluginuri () . '/library/public/css/smoothness/jquery-ui-1.8.13.custom.css', null, $this->application ()->version () );
		wp_register_style ( 'v43v_style_css', $this->application ()->pluginuri () . '/library/public/css/style.css', null, $this->application ()->version () );
		wp_register_script ( 'v43v_script_js', $this->application ()->pluginuri () . '/library/public/js/script.js', null, $this->application ()->version () );
		wv43v_data_settings::setup ( ($this->application ()->data('_debug_settings')->debug_settings['settings']!="" || $this->dodebug()) );
	}
	public function admin_enqueue_scriptsWPaction() {
		$this->wp_enqueue_scriptsWPaction ();
		$this->wp_enqueue_scriptsWPaction ();
	}
	public function wp_enqueue_scriptsWPaction() {
		wp_enqueue_style ( 'jquery-ui_smoothness' );
		wp_enqueue_style ( 'v43v_style_css' );
		wp_enqueue_script ( 'jquery' );
		wp_enqueue_script ( 'jquery-ui-sortable' );
		wp_enqueue_script ( 'jquery-form' );
		wp_enqueue_script ( 'jquery-ui-dialog' );
		wp_enqueue_script ( 'v43v_script_js' );
		$data = array ('dodebug' => $this->dodebug () );
		wp_localize_script ( 'v43v_script_js', 'v43v_data', $data );
	}
	public function user_can_richeditWPaction($value) {
		if (get_post_type () == 'dcoda_settings') {
			$value = false;
		}
		return $value;
	}
}