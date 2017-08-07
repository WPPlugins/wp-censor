<?php
class wv43v_blogs extends bv43v_base {
	private $_old_id = array();
	public function swap($id = null,$validate=false) {
		if(is_multisite())
		{
			if (null !== $id) {
				if($id!='')
				{
					switch_to_blog($id,$validate);
				}
			} else {
				restore_current_blog();
			}
		}
	}
}