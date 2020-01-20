<?php 
/**
 * Index Page Controller
 * @category  Controller
 */
class IndexController extends BaseController{
	/**
     * Index Action 
     * @return View
     */
	function index(){
		if(user_login_status() == true){
			redirect_to_page(HOME_PAGE);
		}
		else{
			$this->view->render("index/index.php" , null , "main_layout.php");
		}
	}
	private function login_user($username , $password_text, $rememberme = false){
		$db = $this->GetModel();
		$username = filter_var($username, FILTER_SANITIZE_STRING);
		$db->where("user_name", $username)->orWhere("email", $username);
		$tablename = $this->tablename = 'users';
		$user = $db->getOne($tablename);
		if(!empty($user)){
			//Verify User Password Text With DB Password Hash Value.
			//Uses PHP password_verify() function with default options
			$password_hash = $user['user_pass'];
			if(password_verify($password_text,$password_hash)){
        		unset($user['user_pass']); //Remove user password as it's not needed.
				set_session('user_data',$user); // Set Active User Data in A Sessions
				//if Remeber Me, Set Cookie
				if($rememberme == true){
					$sessionkey = time().random_str(20);// Generate a Session Key for the User
					//Update user detail in database with the session key
					$db->where('id' , $user['id']);
					$res = $db -> update($tablename, array("login_session_key"=>hash_value($sessionkey)));
					if(!empty($res)){
						set_cookie("login_session_key",$sessionkey);// save user login_session_key in a Cookie
					}
				}
				else{
					clear_cookie("login_session_key");// Clear any Previous Set Cookie
				}
				$redirect_url = get_session("login_redirect_url");// Redirect to user active page
				if(!empty($redirect_url)){
					redirect_to_page($redirect_url);
					clear_session("login_redirect_url");
				}
				else{
					redirect_to_page(HOME_PAGE);
				}
			}
			else{
				//password not correct
				$page_error =  "Username or password not correct";
				$this->view->page_error = $page_error;
				$this->view->render("index/login.php" ,null,"main_layout.php");
			}
		}
		else{
			$page_error =  "Username or password not correct";
			//user is not registered
			$this->view->page_error = $page_error;
			$this->view->render("index/login.php" ,null,"main_layout.php");
		}
	}
	/**
     * Login Action
     * If Not $_POST Request, Display Login Form View
     * @return View
     */
	function login(){
		if(is_post_request()){
			Csrf :: cross_check();
			$form_collection=$_POST;
			$username = trim($form_collection['username']);
			$password = $form_collection['password'];
			$rememberme = (!empty($form_collection['rememberme']) ? $form_collection['rememberme'] : false);
			$this->login_user($username , $password, $rememberme = false);
		}
		else{
			$this->view->page_error = "Invalid request";
			$this->view->render("index/login.php" ,null,"main_layout.php");
		}
	}
	/**
     * Register User Action 
     * If Not $_POST Request, Display Register Form View
     * @return View
     */
	function register(){
		if(is_post_request()){
			Csrf :: cross_check();
			$db = $this->GetModel();
			$tablename = $this->tablename = 'users';
			$fields = $this->fields = array('user_name','user_pass','user_dp','phone','date','email'); //registration fields
			$postdata = $this->transform_request_data($_POST);
			$this->rules_array = array(
				'user_name' => 'required',
				'user_pass' => 'required',
				'user_dp' => 'required',
				'phone' => 'required',
				'date' => 'required',
				'email' => 'required|valid_email',
			);
			$this->sanitize_array = array(
				'user_name' => 'sanitize_string',
				'user_dp' => 'sanitize_string',
				'phone' => 'sanitize_string',
				'date' => 'sanitize_string',
				'email' => 'sanitize_string',
			);
			$modeldata = $this -> modeldata = $this->validate_form($postdata);
			$cpassword = $modeldata['confirm_password'];
			$password = $modeldata['user_pass'];
			if($cpassword != $password){
				$this->view->page_error[] = "Your password confirmation is not consistent";
			}
			unset($modeldata['confirm_password']);
			$password_text = $modeldata['user_pass'];
			$modeldata['user_pass'] = password_hash($password_text , PASSWORD_DEFAULT);
			//Check if Duplicate Record Already Exit In The Database
			$db->where('user_name',$modeldata['user_name']);
			if($db->has($tablename)){
				$this->view->page_error[] = $modeldata['user_name']." Already exist!";
			}
			//Check if Duplicate Record Already Exit In The Database
			$db->where('email',$modeldata['email']);
			if($db->has($tablename)){
				$this->view->page_error[] = $modeldata['email']." Already exist!";
			}
			if(empty($this->view->page_error)){
				$rec_id = $this->rec_id = $db->insert($tablename, $modeldata);
				if(!empty($rec_id)){
					$this->login_user($modeldata['email'] , $password_text);
					return;
				}
				else{
					$page_error = null;
					if($db->getLastError()){
						$page_error = $db->getLastError();
					}
					else{
						$page_error = "Error registering user";
					}
					$this->view->page_error = $page_error;
				}
			}
		}
		$this->view->page_title ="Add New Users";
		$this->view->render('index/register.php' , null ,"main_layout.php");
	}
	/**
     * Logout Action
     * Destroy All Sessions And Cookies
     * @return View
     */
	function logout($arg=null){
		Csrf :: cross_check();
		session_destroy();
		clear_cookie("login_session_key");
		redirect_to_page("");
	}
}
