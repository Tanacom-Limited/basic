<?php 

/**
 * Home Page Controller
 * @category  Controller
 */
class HomeController extends SecureController{
	/**
     * Index Action
     * @return View
     */
	function index(){
		
		$this->view->render("home/index.php" , null , "main_layout.php");

	}
}
