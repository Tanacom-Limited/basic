<?php
/**
 * File Helper Controller
 *
 * @category  File Helper
 */

class FilehelperController extends BaseController{
	/**
     * Upload A file to the server
     * @return JSON String
     */
	function uploadfile(){
		Csrf :: cross_check();
		$uploader = new Uploader;
		$upload_settings = array();
		if(!empty($_POST['fieldname'])){ // Get Upload field name from post request
			$fieldname = $_POST['fieldname'];
			if(!empty($this->file_upload_settings[$fieldname])){
				$upload_settings = $this->file_upload_settings[$fieldname];
			}
		}
		$upload_data = $uploader->upload($_FILES['file'], $upload_settings);
		if($upload_data['hasErrors']){
			$errors = $upload_data['errors'];
			render_error( json_encode($errors));
		}
		if($upload_data['isComplete']){
			$arr_files = $upload_data['data']['files'];
			if(!empty($upload_settings['returnfullpath'])){
				$arr_files = array_map( "set_url", $arr_files ); // set files with complete url of the website
			}
			$uploaded_files = implode("," , $arr_files);
			echo $uploaded_files;
		}
	}
	
	function removefile(){
		Csrf :: cross_check();
		if(!empty($_POST['filepath'])){
			try{
				$filepath = $_POST['filepath'];
				$file_dir = str_ireplace(SITE_ADDR , "" , $filepath);
				echo unlink($file_dir);
			}
			catch(Exception $e) {
			  echo 'Message: ' .$e->getMessage();
			}
		}
	}
}
