<?php 

/**
 * SharedController Controller
 * @category  Controller / Model
 */
class SharedController extends BaseController{
	
	/**
     * users_user_name_value_exist Model Action
     * @return array
     */
	function users_user_name_value_exist($val){
		$db = $this->GetModel();
		$db->where('user_name', $val);
		$exist = $db->has('users');
		return $exist;
	}

	/**
     * users_email_value_exist Model Action
     * @return array
     */
	function users_email_value_exist($val){
		$db = $this->GetModel();
		$db->where('email', $val);
		$exist = $db->has('users');
		return $exist;
	}

}
