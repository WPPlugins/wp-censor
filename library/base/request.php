<?php
class bv43v_request extends bv43v_base {
	public function is_post()
	{
		return ($_SERVER ['REQUEST_METHOD'] == 'POST');
	}
}