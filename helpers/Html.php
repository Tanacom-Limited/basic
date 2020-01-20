<?php
/**
 * Html Helper Class
 * Use To Display Customisable Html Page Component
 * Better used for small html reusable html components
 * @category  View Helper
 */
class Html{
	/**
     * Display Html Head Meta Tag
     * @return Html
     */
	public static function page_meta($name,$val=null){
		?>
		<meta name="<?php echo $name; ?>" content="<?php echo $val ?>" />
		<?php
	}

	/**
     * Link To Css File From Css Dir
     * NB -- Pass only The Css File Nam-- (eg. style.css) 
     * @return Html
     */
	public static function page_css($arg){
		?>
		<link rel="stylesheet" href="<?php print_link(CSS_DIR.$arg); ?>" />
		<?php
	}

	/**
     * Link To Js File From JS Dir
     * NB -- Pass only The Js File Name-- (eg. script.js) 
     * @return Html
     */
	public static function page_js($arg){
		?>
		<script type="text/javascript" src="<?php print_link(JS_DIR.$arg); ?>"></script>
		<?php
	}

	/**
	 * Build Menu List From Array
	 * Support Multi Level Dropdown Menu Tree
	 * Set Active Menu Base on The Current Page || url
	 * @return  HTML
	 */
	public static function render_menu($arrMenu,$menu_class='nav navbar-nav',$submenu_class='dropdown-menu'){
		$page_name=Router::$page_name;
		$page_url=Router::$page_url;
		if(!empty($arrMenu)){
			?>
			<ul class="<?php echo $menu_class; ?>">
				<?php
					foreach($arrMenu as $menuobj){
						$path = $menuobj['path'];
						if(PageAccessManager::GetPageAccess($path)=='AUTHORIZED'){
							$active_class=null;
							
							$menu_url = parse_url($path , PHP_URL_PATH);
							
							if($page_name == $menu_url || urldecode($page_url) == $menu_url){
								$active_class="active";
							}
							if(empty($menuobj['submenu'])){
								?>
								<li class="nav-item">
									<a class="nav-link <?php echo ($active_class) ?>" href="<?php print_link($path); ?>">
										<?php echo (!empty($menuobj['icon']) ? $menuobj['icon'] : null); ?> 
										<?php echo $menuobj['label']; ?>
									</a>
								</li>
								<?php
							}
							else{
							?>
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="#" data-boundary="viewport" data-toggle="dropdown">
										<?php echo (!empty($menuobj['icon']) ? $menuobj['icon'] : null); ?> 
										<?php echo $menuobj['label']; ?>
									</a>
									<?php self :: render_submenu($menuobj['submenu'] , $submenu_class);?>
								</li>
							<?php 
							}
						}
					}
				?>
			</ul>
			<?php
		}
	}
	
	/**
	 * Render Multi Level Dropdown menu 
	 * Recursive Function
	 * @return  HTML
	 */
	public static function render_submenu($arrMenu,$menu_class="dropdown-menu"){
		$page_name=Router::$page_name;
		$page_url=Router::$page_url;
		if(!empty($arrMenu)){
			?>
			<ul class="<?php echo $menu_class ?>">
				<?php
					foreach($arrMenu as $key=>$menuobj){
						$path=$menuobj['path'];
						if(PageAccessManager::GetPageAccess($path)=='AUTHORIZED'){
							$active_class=null;
							$menu_url=parse_url($path,PHP_URL_PATH);
							if($page_url==$menu_url){
								$active_class="active";
							}
							if(!empty($menuobj['submenu'])){
								?>
								<li class="nav-item dropdown-submenu">
									<a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">
										<?php echo (!empty($menuobj['icon']) ? $menuobj['icon'] : null); ?> 
										<?php echo $menuobj['label']; ?>
									</a>
									<?php self :: render_submenu($menuobj['submenu'] , "dropdown-menu");?>
								</li>
								<?php
							}
							else{
								?>
								<li class="nav-item">
									<a class="dropdown-item <?php echo ($active_class) ?>" href="<?php print_link($path); ?>">
										<?php echo (!empty($menuobj['icon']) ? $menuobj['icon'] : null); ?> 
										<?php echo $menuobj['label']; ?>
									</a>
								</li>
								<?php
							}
						}
					}
				?>
			</ul>
			<?php
		}
	}

	
	/**
     * Display Html Image Tag
     * Can be Use to Display Multiple Images Separated By Comma
     * Also Can Be Use To Resize Image Via Url
     * @return Html
     */
	public static function page_img($imgsrc,$resizewidth=null,$resizeheight=null,$max=1,$link=null,$imgclass=null){
		if(!empty($imgsrc)){

			$arrsrc = explode(",",$imgsrc);
			if($max >= 1){
				$arrsrc = array_slice($arrsrc, 0, min(count($arrsrc), $max));
			}
			foreach($arrsrc as $src){
				$src = str_ireplace(SITE_ADDR , "", $src );
				$imgpath="helpers/timthumb.php?src=$src";
				$imgpath.=($resizeheight!=null ? "&h=$resizeheight" : null);
				$imgpath.=($resizewidth!=null ? "&w=$resizewidth" : null);
				$previewlink=$link;
				$previewattr=null;
				if($link==null){
					$previewlink="helpers/timthumb.php?src=$src&w=760&h=520";
					$previewattr="data-gallery";
				}
				?>
				<a <?php echo $previewattr; ?> href="<?php print_link($previewlink) ?>">
					<img src="<?php print_link($imgpath); ?>" alt="<?php echo $src; ?>" class="<?php echo $imgclass ?>"  />
				</a>
				<?php
			}
		}
	}
	
	/**
     * display multiple file link (files can be separated by comma)
     * @return Html
     */
	public static function page_link_file($src,$btnclass="btn btn-info btn-sm",$target="_blank"){
		if(!empty($src)){
			$arrpath=explode(",",$src);
			foreach($arrpath as $path){
				if(!empty($path)){
					?>
					<a class="<?php echo $btnclass ?>" target="<?php echo $target ?>" href="<?php print_link($path); ?>">
						<i class="fa fa-paperclip"></i>
						<?php echo basename($path); ?>
					</a>
					<?php
				}
			}
		}
	}
	
	/**
     * Display html Hyper Link Tag
     * If User is Allowed to Assess That Particular Resource Or link
     * @return Html
     */
	public static function secured_page_link($path,$label="",$class=null,$attrs=null){
		$acl=new ResourceAccessManager();
		$access_condition=$acl->GetPathAccessCondition($path);
		if($access_condition=='AUTHORIZED'){
			?>
			<a href="<?php print_link($path); ?>" class="<?php echo($class) ?>" <?php echo $attrs; ?>><?php echo($label) ?></a>
			<?php	
		}
	}
	
	/**
     * Display html Hyper Link Tag
     * @return Html
     */
	public static function page_link($path,$label="",$classes=null,$attrs=null){
		?>
		<a href="<?php print_link($path); ?>" class="<?php echo($classes) ?>" <?php echo $attrs; ?>><?php echo($label) ?></a>
		<?php	
	}
	
	/**
     * Display import data form
     * @return Html
     */
	public static function import_form($form_path , $button_text="", $format_text="csv, json"){
		?>
		<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#-import-data">
		<i class="fa fa-file"></i> <?php echo $button_text; ?>
		</button>	
		
		<form method="post" action="<?php print_link($form_path) ?>" enctype="multipart/form-data" id="-import-data" class="modal fade" role="dialog" tabindex="-1" data-backdrop="false" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog modal-dialog-centered modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Import Data</h4>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<label>Select a file to import <input required="required" class="form-control form-control-sm" type="file" name="file" /> </label>
						<small class="text-muted">Supported file types(csv , json)</small>
						
					</div>
					<div class="modal-footer">
						<button type="reset" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Import Data</button>
					</div>
				</div>
			</div>
		</form>
		<?php	
	}
	


	/**
     * Convinient Function For Diisplaying Field Order By
     * Uses The Current Page URL and Modify Only The orderby and ordertype query string Parameter
     * @return Html
     */
	public static function get_field_order_link($fieldname,$fieldlabel){
		$currentordertype=strtoupper((array_key_exists("ordertype", $_GET) ? $_GET['ordertype'] : "ASC"));
		$newordertype=($currentordertype=='ASC' ? 'DESC' : 'ASC');
		$orderlink=set_current_page_link(array("orderby"=>$fieldname,"ordertype"=>$newordertype));
		$linkbtnclass=(get_query_str_value('orderby')==$fieldname ? 'btn-success' : 'btn-default');
		?>
		<a class="btn btn-xs <?php echo $linkbtnclass; ?>" href="<?php print_link($orderlink); ?>">
			<?php 
				echo $fieldlabel;
				if($currentordertype=='DESC' && get_query_str_value('orderby')==$fieldname){
					?>
					<i class="fa fa-arrow-up"></i>
					<?php
				}
				else{
					?>
					<i class="fa fa-arrow-down"></i>
					<?php
				}
			?>
		</a>
		<?php
	}
	
	
	/**
     * Convinient Function For Diisplaying Field Order By
     * Uses The Current Page URL and Modify Only The orderby and ordertype query string Parameter
     * @return Html
     */
	public static function uploaded_files_list($files, $inputid, $delete_file="false"){
		?>
		<div class="uploaded-file-holder clearfix">
			<?php	
				if(!empty($files)){
					$arrsrc=explode(",",$files);
					$i=0;
					$img_exts =  array('gif','png' ,'jpg');
					
					foreach($arrsrc as $src){
						$i++;
						$previewattr = "";
						$is_img = false;
						$ext = pathinfo($src, PATHINFO_EXTENSION);
						if(in_array($ext,$img_exts) ) {
						
							$is_img = true;
						}
						
						?>
						<div class="d-inline-block p-2 card m-1" id="file-holder-<?php echo $i; ?>">
							<?php 
								if($is_img){
									self :: page_img($src,50,50); 
									echo basename($src);
								}
								else{
									?>
									<a class="btn btn-sm btn-light" target="_blank" href="<?php print_link($src) ?>">
										<?php echo basename($src); ?>
									</a>
									<?php
								}
							?>
							<button data-input="<?php echo $inputid; ?>" data-delete-file="<?php echo $delete_file; ?>" type="button" data-file="<?php echo $src ?>" data-file-num="<?php echo $i; ?>" class="btn btn-sm btn-danger removeEditUploadFile">
								&times;
							</button>
						</div>
						<?php
					}
				}
			?>
		</div>
		<?php
	}
	
	public static function display_form_errors($formerror){
		if(!empty($formerror)){
			if(!is_array($formerror)){
				?>
					<div class="alert alert-danger animated shake">
						<?php echo $formerror; ?>
					</div>
				<?php
			}
			else{
				?>
				<script>
					$(document).ready(function(){
						<?php 
							foreach($formerror as $key=>$value){
								echo "$('[name=$key]').parent().addClass('has-error').append('<span class=\"help-block\">$value</span>');";
							}
						?>
					});
				</script>
				<?php
			}
		}
	}
	
	public static function list_options($arr_options, $selected_value=null){
		if(!empty($arr_options)){
			foreach($arr_options as $label=>$value){
				$val = array_values($arr);
				?>
				<option <?php echo $selected; ?> value="<?php echo $value; ?>">
					<?php echo $label; ?>
				</option>
				<?php
			}
		}
		elseif(!empty($selected_value)){
			?>
			<option>
				<?php echo $selected_value; ?>
			</option>
			<?php
		}
	}
}