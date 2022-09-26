<?php

/*	--------------------------------------
	Execute immediately upon load
	-------------------------------------- */

	// authenticate user
		if (!$logged_in['is_administrator']) $authentication_manager->forceLoginThenRedirectHere(true);
		
	// parse query string
		$query_string = urldecode(@$tl->page['parameter1']);

	// query
		$logs = new logs_widget_TL();
		$logs->initialize(['filterable'=>true, 'connection_type_filter'=>true, 'exportable'=>true, 'paginate'=>true, 'rows_per_page'=>50, 'template_name'=>$tl->page['template'], 'query_string'=>$query_string, 'resortable'=>true, 'columns'=>['connected_on'=>'', 'connection_type'=>'', 'activity'=>'', 'is_error'=>'warning-sign', 'is_resolved'=>'thumbs-up', 'connection_length_in_seconds'=>'time']]);

	$tl->page['title'] = 'Logs';
	$tl->page['section'] = "Administration";
		
/*	--------------------------------------
	Execute only if a form post
	-------------------------------------- */
			
	if (count($_POST) > 0) {
	}
		
/*	--------------------------------------
	Execute only if not a form post
	-------------------------------------- */
		
	else {
	}
		
?>