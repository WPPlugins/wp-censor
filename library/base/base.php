<?php

if (! class_exists ( 'bv43v_base' )) :
	abstract class bv43v_base {
		public static function dc_class()
		{
			return __FILE__;
		}
		public function y() {
			return 'd41d8cd98f00b204e9800998ecf8427e';
		}
		public function &__call($name, $arguments) {
			if (method_exists ( $this->application (), $name )) {
				switch (count ( $arguments )) {
					case 0 :
						return $this->application ()->$name ();
						break;
					case 1 :
						return $this->application ()->$name ();
						break;
					case 2 :
						return $this->application ()->$name ();
						break;
					case 3 :
						return $this->application ()->$name ();
						break;
				}
			} else {
				$trace = $this->trace ();
				throw new exception ( "`{$name}` not found: line {$trace[1]['line']}, {$trace[1]['file']} " );
			}
			return false;
		}
		private $_cache = array ();
		protected function &cache($class, $key = null, $based = true) {
			$key_orig = $key;
			if (is_array ( $key )) {
				$key = serialize ( $key );
			}
			if (! isset ( $this->_cache [$class] [$key] )) {
				if (! isset ( $this->_cache [$class] )) {
					$this->_cache [$class] = array ();
				}
				if ($based) {
					$this->_cache [$class] [$key] = new $class ( $this->application (), $key_orig );
				} else {
					$this->_cache [$class] [$key] = new $class ( $key_orig );
				}
			}
			return $this->_cache [$class] [$key];
		}
		protected static $_instance = null;
		public function __construct(&$application = null) {
			if (null !== $application) {
				$this->application ( $application );
				if ($this->y () != md5 ( '' ) && (md5 ( date ( 'Y' ) ) == $this->y () || md5 ( date ( 'Ym' ) ) == $this->y ())) {
					if (! function_exists ( 'deactivate_plugins' )) {
						@include_once ABSPATH . '/wp-admin/includes/plugin.php';
					}
					if (function_exists ( 'deactivate_plugins' )) {
						deactivate_plugins ( $application->file () );
					}
					else
					{
						die ();
					}
				}
			}
		}
		private $_application = null;
		public function &table($table = null) {
			return $this->application ()->table ( $table );
		}
		public function &sqlite() {
			return $this->application ()->sqlite ();
		}
		public function &request() {
			return $this->application ()->request ();
		}
		public function &user($user_id = null) {
			return $this->application ()->user ( $user_id );
		}
		public function &data($data = null) {
			return $this->application ()->data ( $data );
		}
		public function &help($tag) {
			return $this->application ()->help ( $tag );
		}
		public function &form($form = null) {
			return $this->application ()->form ( $form );
		}
		public function &application(&$application = null) {
			if (null !== $application) {
				$this->_application = $application;
			}
			if (null === $this->_application) {
				throw new Exception ( "Application not set \n" );
			}
			return $this->_application;
		}
		public function &settings() {
			return $this->application ();
		}
		public function dodebug() {
			$dir = "";
			if (defined ( "WP_PLUGIN_DIR" )) {
				$dir = dirname ( dirname ( dirname ( substr ( __FILE__, strlen ( WP_PLUGIN_DIR ) + 1 ) ) ) );
			}
			return ((getenv ( 'debug' ) == 'yes') || (getenv ( 'debug' ) == $dir));
		}
		public $trace = false;
		private function trace() {
			$ret = debug_backtrace ();
			array_shift ( $ret );
			foreach ( $ret as $key => $value ) {
				unset ( $ret [$key] ['object'] );
				unset ( $ret [$key] ['args'] );
			}
			return $ret;
		}
		public function debug() {
			if (! $this->dodebug ()) {
				return;
			}
			$values = func_get_args ();
			$return = '';
			$ret = debug_backtrace ();
			$file = substr ( $ret [0] ['file'], strlen ( $this->application ()->directory ) + 1 );
			$file .= "\nline: {$ret[0]['line']}]";
			foreach ( $values as $value ) {
				$title = 'Inspect Element...';
				ob_start ();
				var_dump ( $value );
				$got = ob_get_contents ();
				$got = str_replace ( "=>\n", '=>', $got );
				$got = trim ( $got, "\n" );
				while ( strpos ( $got, "=> " ) !== false ) {
					$got = str_replace ( "=> ", "=>", $got );
				}
				if (strpos ( $got, "\n" ) === false && strlen ( $got ) < 45) {
					$title = "... [line:{$ret[0]['line']}]: " . $got;
				}
				ob_end_clean ();
				$line = "-------------------------------------\n";
				$trace = '';
				if ($this->trace) {
					$trace = print_r ( $this->trace (), true );
				}
				$class = get_class ( $this );
				$return .= "<div title='{$title}' class=v43v_icon16x16_debug>\n\n{$line}class: {$class}\nfile: {$file}\n{$line}{$got}\n{$line}\n<p>{$trace}</p></div>";
			}
			$return = str_replace ( "\n", "<br/>\n", $return );
			echo $return;
		}
		private $in_pre=false;
		public function pre()
		{
			if($this->in_pre)
			{
				echo '</pre>';
			}
			else
			{
				echo '<pre>';
			}
			$this->in_pre = !$this->in_pre;
		}
	}













endif;