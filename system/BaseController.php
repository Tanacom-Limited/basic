<?php 
defined('ROOT') OR exit('No direct script access allowed');

/**
* Application Base Controller.
* Other Controllers must extend to the class 
* Controllers which do not need page authentication and resource authorization can extend to this class
*/
class BaseController{
	/**
	 * Instance of the base view class. Use for rendering pages
	 * @var [BaseView class]
	 */
	public $view = null;


	/**
	 * Instance of the PDODB class
	 * Provide Data access laeyer to the database
	 * @var [PDODB class]
	 */
	public $db = null;


	/**
	 * Current page number from $_GET['page_num']
	 * @var [int]
	 */
	public $page_num = 1;


	/**
	 * Current page number from $_GET['limit_start']
	 * @var [int]
	 */
	public $limit_start = 1;


	/**
	 * Maximum number of item per page from $_GET['limit_count']
	 * @var [int]
	 */
	public $limit_count = MAX_RECORD_COUNT;


	/**
	 * order query based on field name from $_GET['orderby']
	 * @var [string]
	 */
	public $orderby = null;


	/**
	 * order query type desc|asc from $_GET['ordertype']
	 * @var [string]
	 */
	public $ordertype = ORDER_TYPE;


	/**
	 * current page record id from router url pagename/[rec_id]
	 * @var [string]
	 */
	public $rec_id = null;


	/**
	 * the search query for the page from $_GET['search]
	 * @var [string]
	 */
	public $search = null;


	/**
	 * the table name associated to the current page
	 * @var [string]
	 */
	public $tablename = null;

	
	/**
	 * POST Data after sanitization and validation from $_POST
	 * @var [array]
	 */
	public $modeldata = array();
	

	/**
	 * File upload settings if their should be any upload on any page
	 * @var [array]
	 */
	public $file_upload_settings = array();


	/**
	 * Table fields associated with the current page
	 * @var [array]
	 */
	public $fields = array();
	


	function __construct(){
		$this->view = new BaseView;
		$q=$_GET;
		if(!empty($q)){
			//pass each request data to the current page as class property
			foreach($q as $obj => $val){
				$this->$obj = $val;
			}	
		}
		if(empty($this -> limit_start)){
			$this -> limit_start = 1;
		}
		$this -> page_num = $this -> limit_start;
		$this -> limit_start = ($this -> limit_start-1) * $this -> limit_count;
		
		
		$this->file_upload_settings['user_dp'] = array(
			"title" => "{{random}}",
			"extensions" => ".jpg,.png,.gif,.jpeg",
			"limit" => "1",
			"filesize" => "3",
			"returnfullpath" => false,
			"filenameprefix" => "",
			"uploadDir" => "uploads/files/"
		);
	

		
	}
	/**
     * validate post array using gump library
     * sanitize input array based on the page sanitize rule
     * validate data based on the set of defined rules
     * @var $filter_rules = true: will validate post data only for posted array data if field name is not set in the postdata
     * @return  Array
     */
	function validate_form($postdata, $filter_rules = false){
		$modeldata = GUMP::filter_input($postdata, $this->sanitize_array);
		$rules = $this->rules_array;
		
		if($filter_rules){
			$rules = array(); //reset rules
			//set rules for only fields in the posted data
			foreach($postdata as $key => $val){
				if(in_array($key, $this->rules_array)){
					$rules[$key] =  $this->rules_array[$key];
				}
			}
		}
		
		//accept posted fields if they are part of the page fields
		/* foreach($postdata as $key => $val){
			if(!in_array($key, $this->fields)){
				$this->view->page_error[] = "$key field is not allowed";
			}
		} */
		
		$is_valid = GUMP::is_valid($modeldata, $rules);
		if( $is_valid !== true) {
			if(is_array($is_valid)){
				foreach($is_valid as  $error_msg){
					$this->view->page_error[] = strip_tags($error_msg);
				}
			}
			else{
				$this->view->page_error[] = $is_valid;
			}
		}
		return $modeldata;
	}
	
	/**
     * Concat Array  Values With Comma if REQUEST Value is a simple Array
     * Specific for this Framework Only
     * @arr $_POST || $_GET data
     * @return  Array
     */
	function transform_request_data($arr){
		foreach($arr as $key=>$val){
			if(is_array($val)){
				$arr[$key]=implode(',',$val);
			}
		}
		return $arr;
	}
	
	/**
     * Concat Array  Values With Comma for multiple post data
     * Specific for this Framework Only
     * @arr $_POST || $_GET data
     * @return  Array
     */
	function transform_multi_request_data($arr){
		$alldata = array();
		foreach($arr as $key=>$value){
			$combine_vals = recursive_implode($value, "");
			//merge all values of each input into one string and check if each post data contains value
			if(!empty($combine_vals)){
				$alldata[] = $this -> transform_request_data($value);
			}
		}
		return $alldata;
	}
	
	/**
     * Init DB Connection 
     * Which can be use to perform DB queries
     * @return  PDO Object
     */
	function GetModel(){
		//Initialse New Database Connection
		$this->db = new PDODb(DB_TYPE, DB_HOST , DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT , DB_CHARSET);
		return $this->db;
	}
	
	
	/**
     * Delete files
	 * split file path if they are separated by comma
     * @files Array
     * @return  nul
     */
	function delete_record_files($files, $field){
		foreach($files as $file_path){
			$comma_paths = explode( ',', $file_path[$field] );
			foreach($comma_paths as $file_url){
				try{
					$file_dir_path = str_ireplace( SITE_ADDR , "" , $file_url ) ;
					@unlink($file_dir_path);
				}
				catch(Exception $e) {
				  // error_log('Message: ' .$e->getMessage());
				}
			}
		}
	}
	
	
	/**
     * upload files and return file paths
	 * @var $fieldname File upload filed name
     * @return  nul
     */
	function get_uploaded_file_paths($fieldname){
		$uploaded_files = "";
		if(!empty($_FILES[$fieldname])){
			$uploader = new Uploader;
			$upload_settings = $this->file_upload_settings[$fieldname];
			$upload_data = $uploader->upload($_FILES[$fieldname], $upload_settings );
			if($upload_data['isComplete']){
				$arr_files = $upload_data['data']['files'];
				if(!empty($arr_files)){
					if(!empty($upload_settings['returnfullpath'])){
						$arr_files = array_map( "set_url", $arr_files ); // set files with complete url of the website
					}
					$uploaded_files = implode("," , $arr_files);
				}
			}
			if($upload_data['hasErrors']){
				$errors = $upload_data['errors'];
				foreach($errors as $key=>$val){
					$this->view->page_error[] = "$key : $val[$key]";
				}
			}
		}
		return $uploaded_files;
	}
	
	/**
     * For getting uploaded file as Blob type
     * can be use to insert blob data into the databased
	 * @var $fieldname File upload filed name
     * @return  Blob object
     */
	function get_uploaded_file_data($fieldname){
		if(!empty($_FILES[$fieldname])){
			$name = $_FILES[$fieldname]['name'];
			$extension = strtolower(substr($name, strpos($name, '.') + 1));
			$tmp_name = $_FILES[$fieldname]['tmp_name'];
			$type = $_FILES[$fieldname]['type'];
			$size = $_FILES[$fieldname]['size'];
			return file_get_contents($tmp_name);
		}
		return null;
	}
	
	/**
	 * Set Current Page Start and Page Count
	 * $page_count Set Max Record to retrive per page
	 * $fieldvalue Table Field Value 
	 * @return array(limit_start,limit_count)
	 */
	function get_page_limit($page_count = MAX_RECORD_COUNT){
		
		if(!empty($_GET['limit_count'])){ //Get page limit from query string request if available
		
			 /*Set limit to high number to get all records. starting from the current position */
			 
			if($_GET['limit_count'] == -1){
				$this->limit_count=1000000000;
			}
			else{
				$this->limit_count=$_GET['limit_count'];
			}
		}
		else{
			$this->limit_count=$page_count;
			//$_GET['limit_count']=$page_count;
		}
		
		return array($this->limit_start,$this->limit_count);
	}
	
	
	
}

/**
* Extends to Application Base Controller.
* Page Controllers which need page authentication and authorization can extend to this class 
*/
class SecureController extends BaseController{
	function __construct(){
		parent::__construct();
		// Page actions which do not require authentication.
		$exclude_pages = array();
		$url = Router :: $page_url;
		$url = str_ireplace('/index','/list',$url);
		if(!empty($url)){
			$url_segment =$url_segment = explode("/" , rtrim($url , "/")) ;
			$controller = strtolower(!empty($url_segment[0]) ? $url_segment[0] : null);
			$action = strtolower((!empty($url_segment[1]) ? $url_segment[1] : 'list'));
			$page = "$controller/$action";
			if( !in_array($page , $exclude_pages )){
				authenticate_user(); // Authenticate user And Authorise User
			}
		}
	}
}