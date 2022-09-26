<?php

/*	--------------------------------------
	Execute immediately upon load
	-------------------------------------- */
	
/*	--------------------------------------
	Execute only if a form post
	-------------------------------------- */
			
	if (count(@$_POST) > 0 && @$_POST['formName'] == 'searchForm') {

		// clean input
			$parser = new parser_TL();
			$_POST = $parser->trimAll($_POST);

		// redirect
			$filters = '';
			if ($_POST['search_keywords']) $filters .= "|keywords=" . $_POST['search_keywords'];
			if ($_POST['search_country']) $filters .= "|country_id=" . $_POST['search_country'];
			if ($_POST['search_priority']) $filters .= "|priority_id=" . $_POST['search_priority'];
			if ($_POST['search_status']) $filters .= "|status_id=" . $_POST['search_status'];
			if ($_POST['search_tag']) $filters .= "|tag_id=" . $_POST['search_tag'];

			$filters = trim($filters, '|');
			
			$authentication_manager->forceRedirect ('/search_results/' . urlencode($filters));
	}
		
/*	--------------------------------------
	Execute only if not a form post
	-------------------------------------- */
		
	else {
	}
		
?>