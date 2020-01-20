<?php
	// Set url Variable From Router Class
	$page_name = Router :: get_page_name();
	$page_action = Router :: get_page_action();
	$page_id = Router :: get_page_id();

	$this->render_body();

	//rebind jquery plugins init with ajax loaded html content
	Html :: page_js("plugins-init.js");
?>
