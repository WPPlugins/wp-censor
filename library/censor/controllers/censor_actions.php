<?php
class censor_actions extends wv43v_action {
	public function __construct(&$application)
	{
		if($application->slug!='sandbox')
		{
			parent::__construct($application);
		}
	}
	public function censor_write_phrasesWPfilter($data) {
		return $this->redactor_write_phrasesWPfilter($data);
	}
	public function spoiler_write_phrasesWPfilter($data) {
		return $this->redactor_write_phrasesWPfilter($data);
	}
	public function redactor_write_phrasesWPfilter($data) {
		if($this->application()->slug=='sandbox')
		{
			return $data;
		}
		$return = array ();
		foreach ( $data as $value ) {
			if(!isset($value['style']))
			{
				$value['style']=$this->application()->slug;
			}
			if(!isset($value['replacement']))
			{
				$value['replacement']='';
			}
			if ($value ['phrase'] != "" && ! isset ( $value ['delete'] )) {
				$return [$value ['phrase']] = $value;
				if (! is_numeric ( $value ['priority'] )) {
					$return [$value ['phrase']] ['priority'] = 0;
				}
				if ($value ['style'] == 'swap' && $value ['replacement'] == $value ['phrase']) {
					$return [$value ['phrase']] ['style'] = 'blackout';
				}
				if ($value ['style'] != 'swap' && $value ['replacement'] != "") {
					$return [$value ['phrase']] ['replacement'] = '';
				}
			}
		}
		uasort ( $return, array ($this, 'sort_phrases' ) );
		return $return;
	}
	public function sort_phrases($a, $b) {
		$al = strtolower ( $a ['phrase'] );
		$bl = strtolower ( $b ['phrase'] );
		if ($a ['priority'] == $b ['priority']) {
			if ($al == $bl) {
				return 0;
			}
			return ($al > $bl) ? + 1 : - 1;
		} else {
			return ($a ['priority'] < $b ['priority']) ? + 1 : - 1;
		}
	}
	public function initWPaction() {
		wp_enqueue_style ( 'censor_style', $this->application ()->pluginuri () . '/library/censor/public/css/style.css', null, $this->application ()->version () );
	}
	public function commonWPmenuMeta($return) {
		$return ['menu'] = 'Settings';
		$return ['slug'] = $this->application ()->slug;
		$return ['title'] = $this->application ()->name;
		return $return;
	}
	public function settingsActionMeta($return) {
		$return ['link_name'] = $return ['title'];
		$return ['classes'] [] = 'v43v_icon16x16';
		$return ['classes'] [] = 'v43v_icon16x16_settings';
		$return ['priority'] = - 1;
		return $return;
	}
	public function settingsAction() {
		$this->view->title = $this->help('settings')->render('Settings');
		$this->view->column_count=5;
		if($this->application()->slug=='redactor')
		{
			$this->view->column_count=7;
		}
		$this->view->table_type=$this->application()->slug.'_list';
		$search_replace=$this->data()->post('search_replace');
		$this->view->columns = $this->render_script('common/columns.phtml');
		$phrases = $this->application ()->data ()->post ( 'phrases' );
		$this->view->cnt=0;
		foreach($phrases as $value)
		{
			
			$this->view->phrase=$value['phrase'];
			$this->view->style=$value['style'];
			$this->view->replacement=$value['replacement'];
			$this->view->priority=$value['priority'];
			$this->view->rows[] = $this->render_script('common/row.phtml');
			$this->view->cnt++;
		}		

		$this->view->footer = $this->render_script('common/footer.phtml');
		$page = $this->render_table();
		return $page;
	}
	public function comment_textWPfilter($content) {
		return $this->the_contentWPfilter ( $content );
	}
	public function the_contentWPfilter($content) {
		if($this->application()->slug=='sandbox')
		{
			return $content;
		}
		$settings = $this->application ()->data ()->phrases;
		if (! is_array ( $settings )) {
			return $content;
		}
		$tag = bv43v_tag::instance ();
		$content = ' ' . $content . ' ';
		// check for my bbcode tags
		if($this->application()->slug!='censor')
		{
			$content = $this->marker ( 'Spoiler', $content );
		}
		if($this->application()->slug!='spoiler')
		{
			$content = $this->marker ( 'censor', $content );
		}
		// now tokenise all the tags or bbcode tags to stop search and replace breaking anything.
		$tag->tokenise ( $content );
		$cnt = count ( $content->tokens );
		// 2 pass to allow for people trying to swap in bad words 
		for($i = 0; $i < 2; $i ++) {
			foreach ( $settings as $setting ) {
				$token = "#" . str_pad ( $cnt, 4, "0", STR_PAD_LEFT ) . "#";
				$type = $setting ['style'];
				$phrase = $this->preg_safe ( $setting ['phrase'] );
				$pattern = '|[\W](' . $phrase . ')[\W]|Ui';
				preg_match_all ( $pattern, $content->text, $matches, PREG_SET_ORDER );
				foreach ( $matches as $match ) {
					//$this->debug($match);
					$pattern = null;
					switch ($type) {
						case 'blackout' :
							$new_content = $match [1];
							$pwords = $this->words ( $new_content );
							foreach ( $pwords as $pword ) {
								$new_content = preg_replace ( '/\b(' . $pword . ')\b/Ui', $this->blackout ( $pword ), $new_content );
							}
							$pattern = '|' . $match [0] . '|Ui';
							break;
						case 'censor' :
							$new_content = $this->$type ( $phrase );
							$pattern = '|\b(' . $phrase . ')\b|Ui';
							break;
						case 'spoiler' :
							if($this->application()->slug!='censor')
							{	
								$new_content = $this->$type ( $phrase );
								$pattern = '|\b(' . $phrase . ')\b|Ui';
							}
							break;
						case 'swap' :
							$new_content = $setting ['replacement'];
							$pattern = '|\b(' . $phrase . ')\b|Ui';
							break;
					}
					if (null !== $pattern) {
						$content->tokens [$cnt] = $new_content;
						$content->text = preg_replace ( $pattern, ' ' . $token . ' ', $content->text );
						$cnt ++;
					}
				}
			}
		}
		if ($content->text [0] == ' ') {
			$content->text = substr ( $content->text, 1 );
		}
		$tag->detokenise ( $content );
		return $content;
	}
	
	protected function preg_safe($text) {
		$text = str_replace ( ',', '\W', $text );
		return $text;
	}
	
	public function Censor_Marker($match) {
		$this->view->text = $match ['innerhtml'];
		$page = $this->render_script ( 'front/' . strtolower ( $match ['tag'] ) . '.phtml' );
		return $page;
	}
	
	public function Spoiler_Marker($match) {
		return $this->Censor_Marker ( $match );
	}
	
	protected function words($text) {
		// find just words
		$return = array ();
		$words = preg_split ( '|\W|Ui', $text );
		// remove dupes to avaoid problems
		foreach ( $words as $word ) {
			$lword = strtolower ( $word );
			$return [$lword] = $lword;
		}
		return $return;
	}
	
	public function blackout($text) {
		if (strlen ( $text ) == 0)
			return $text;
		$this->view->text = str_repeat ( '&nbsp;&nbsp;', strlen ( $text ) );
		$text = $this->render_script ( 'front/blackout.phtml' );
		return $text;
	}
	
	protected function block($text, $tag) {
		if (strlen ( $text ) == 0)
			return $text;
		return $this->censor_Marker ( array ('tag' => $tag, 'innerhtml' => $text ) );
	}
	
	public function censor($text) {
		return $this->block ( $text, 'censor' );
	}
	
	public function spoiler($text) {
		return $this->block ( $text, 'spoiler' );
	}
}
		