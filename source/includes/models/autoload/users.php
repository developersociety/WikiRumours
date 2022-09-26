<?php

	function retrieveUsers($matching = null, $containing = null, $otherCriteria = null, $sortBy = false, $limit = false) {
		
		global $dbConnection;
		global $tablePrefix;
		global $tl;
		
		// build query
			$query = "SELECT " . $tablePrefix . "users.*,";
			$query .= " " . $tablePrefix . "countries.country as country,";
			$query .= " " . $tablePrefix . "regions.region as region,";
			$query .= " TRIM(CONCAT(" . $tablePrefix . "users.first_name, ' ', " . $tablePrefix . "users.last_name)) as full_name,";
			$query .= " (SELECT COUNT(rumour_id) FROM " .$tablePrefix . "rumours LEFT JOIN " .$tablePrefix . "statuses ON " .$tablePrefix . "rumours.status_id = " .$tablePrefix . "statuses.status_id WHERE " .$tablePrefix . "rumours.assigned_to = " .$tablePrefix . "users.user_id AND (" .$tablePrefix . "statuses.is_closed = '0' OR " .$tablePrefix . "statuses.is_closed IS NULL)" . (@$tl->page['domain_alias']['cms_id'] ? " AND " . $tablePrefix . "rumours.domain_alias_id='" . $tl->page['domain_alias']['cms_id'] . "'" : false) . ") AS rumours_assigned,";
			$query .= " (SELECT COUNT(rumour_id) FROM " .$tablePrefix . "rumours WHERE " .$tablePrefix . "rumours.created_by = " .$tablePrefix . "users.user_id) AS rumours_created,";
			$query .= " (SELECT COUNT(sighting_id) FROM " .$tablePrefix . "rumour_sightings WHERE " .$tablePrefix . "rumour_sightings.created_by = " .$tablePrefix . "users.user_id) AS sightings,";
			$query .= " (SELECT COUNT(comment_id) FROM " .$tablePrefix . "comments WHERE " .$tablePrefix . "comments.created_by = " .$tablePrefix . "users.user_id) AS comments_left,";
			$query .= " " . $tablePrefix . "user_permissions.*,";
			$query .= " " . $tablePrefix . "users.user_id AS user_id";
			$query .= " FROM " . $tablePrefix . "users";
			$query .= " LEFT JOIN " . $tablePrefix . "user_permissions ON " . $tablePrefix . "user_permissions.user_id = " . $tablePrefix . "users.user_id";
			$query .= " LEFT JOIN " . $tablePrefix . "countries ON " . $tablePrefix . "countries.country_id = " . $tablePrefix . "users.country_id";
			$query .= " LEFT JOIN " . $tablePrefix . "regions ON " . $tablePrefix . "regions.region_id = " . $tablePrefix . "users.region_id";
			$query .= " WHERE 1=1";
			if ($matching) foreach ($matching as $field => $value) $query .= " AND " . $field . " = '" . $dbConnection->escape_string($value) . "'";
			if ($containing) foreach ($containing as $field => $value) $query .= " AND " . $field . " LIKE '%" . $dbConnection->escape_string($value) . "%'";
			if ($otherCriteria) $query .= " AND (" . $otherCriteria . ")";
			if ($sortBy) $query .= " ORDER BY " . $sortBy;
			if ($limit) $query .= " LIMIT " . $dbConnection->escape_string($limit);
			
			$result = $dbConnection->query($query) or die('Unable to execute ' . __FUNCTION__ . ': ' . $dbConnection->error . '<br /><br />' . $query);

		// create array
			$parser = new parser_TL();
			$items = $parser->mySqliResourceToArray($result);
			
		// clear memory
			$result->free();

		// return array
			return $items;
		
	}
	
	function retrieveUserKeys($matching, $containing = null, $otherCriteria = null, $sortBy = false, $limit = false) {
		
		global $dbConnection;
		global $tablePrefix;
		$expiry = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 1, date('Y')));
		
		// build query
			$query = "SELECT " . $tablePrefix . "user_keys.*,";
			if (@$matching['user_key'] == 'API' && @$matching['hash']) $query .= " (SELECT COUNT(id) FROM " .$tablePrefix . "api_calls_internal WHERE " .$tablePrefix . "api_calls_internal.api_key = '" .$matching['hash'] . "') AS total_internal_api_calls,";
			if (@$matching['user_key'] == 'API' && @$matching['hash']) $query .= " (SELECT COUNT(id) FROM " .$tablePrefix . "api_calls_internal WHERE " .$tablePrefix . "api_calls_internal.api_key = '" .$matching['hash'] . "' AND queried_on > '" . $expiry . "') AS internal_api_calls_today,";
			$query .= " TRIM(CONCAT(" . $tablePrefix . "users.first_name, ' ', " . $tablePrefix . "users.last_name)) as full_name";
			$query .= " FROM " . $tablePrefix . "user_keys";
			$query .= " LEFT JOIN " . $tablePrefix . "users ON " . $tablePrefix . "users.user_id = " . $tablePrefix . "user_keys.user_id";
			$query .= " WHERE 1=1";
			if ($matching) foreach ($matching as $field => $value) $query .= " AND " . $field . " = '" . $dbConnection->escape_string($value) . "'";
			if ($containing) foreach ($containing as $field => $value) $query .= " AND " . $field . " LIKE '%" . $dbConnection->escape_string($value) . "%'";
			if ($otherCriteria) $query .= " AND (" . $otherCriteria . ")";
			if ($sortBy) $query .= " ORDER BY " . $sortBy;
			if ($limit) $query .= " LIMIT " . $dbConnection->escape_string($limit);
			
			$result = $dbConnection->query($query) or die('Unable to execute ' . __FUNCTION__ . ': ' . $dbConnection->error . '<br /><br />' . $query);

		// create array
			$parser = new parser_TL();
			$items = $parser->mySqliResourceToArray($result);
			
		// clear memory
			$result->free();

		// return array
			return $items;
		
	}
	
?>
