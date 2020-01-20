<?php
	/**
	 * Role Based Access Control
	 * @category  RBAC Helper
	 */
	defined('ROOT') OR exit('No direct script access allowed');
	class PageAccessManager{
		/**
	     * Array Of User Roles And Page Access 
	     * Use "*" to Grant All Access Right to Particular User Role
	     * @return Html View
	     */
		public static $usersRolePermissions='*';
		
		/**
	     * pages to Exclude From Access Validation Check
	     * @var $excludePageCheck array()
	     */
		public static $excludePageCheck = array("","index","home","account","info","masterdetail","report");
		
		/**
	     * Display About us page
	     * @return string
	     */
		public static function GetPageAccess($path){
			$rp=self::$usersRolePermissions;
			if($rp=="*"){
				return "AUTHORIZED"; // Grant Access To Any User
			}
			else{
				$path = strtolower(trim($path, '/')); 

				$arrPath=explode("/", $path);
				$page=strtolower($arrPath[0]);
				
				//If User Is Accessing Exclude Access Check Page
				if(in_array($page , self :: $excludePageCheck)){
					return "AUTHORIZED";
				}
					
				$userRole = strtolower(USER_ROLE); // Get User Defined Role From Session Value
				if(array_key_exists($userRole,$rp)){
					$action = (!empty($arrPath[1]) ? $arrPath[1] : null);
					if($action == "index" || $action == ""){
						$action="list";
					}
					//Check If User Have Access To All Pages Or User Have Access to All Page Actions
					if($rp[$userRole] == "*" || (!empty($rp[$userRole][$page]) && $rp[$userRole][$page] == "*")){
						return "AUTHORIZED";
					}
					else{
						if(!empty($rp[$userRole][$page]) && in_array($action, $rp[$userRole][$page])){
							return "AUTHORIZED";
						}
					}
					return "NOT_AUTHORIZED";
				}
				else{
					//User Does Not Have Any Role.
					return "NO_ROLE_PERMISSION";
				}
			}
		}
		
		public static function is_allowed($path){
			$access = self::GetPageAccess($path);
			return ($access == 'AUTHORIZED');
		}
	}
?>