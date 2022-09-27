<?php

// authenticate user
if (!$logged_in['is_administrator']) $authentication_manager->forceLoginThenRedirectHere();

if ($tl->page['parameter1']) {
	$filters = $keyvalue_array->keyValueToArray(urldecode($tl->page['parameter1']), '|');
}

// retrieve data				
if (@$filters['keywords']) {
	$otherCriteria = '';
	$keywordExplode = explode(' ', $filters['keywords']);
	foreach ($keywordExplode as $keyword) {
		if ($keyword) {
			if ($otherCriteria) $otherCriteria .= " AND";
			$otherCriteria .= " " . $tablePrefix . "rumours.description LIKE '%" . addSlashes($keyword) . "%'";
		}
	}
	$otherCriteria = trim($otherCriteria);
}

$matching = array();

// status
if (@$filters['rumour_status']) $matching += array($tablePrefix . 'rumours.status_id' => $filters['rumour_status']);
// country
if (@$filters['rumour_country']) $matching += array($tablePrefix . 'rumours.country_id' => $filters['rumour_country']);
// sort by
if (@$filters['sort_by'] == 'rumour') $sortBy = 'description ' . $operators->firstTrue(@$filters['sort_by_direction'], 'ASC');
elseif (@$filters['sort_by'] == 'date') $sortBy = 'updated_on ' . $operators->firstTrue(@$filters['sort_by_direction'], 'ASC') . ', city ' . $operators->firstTrue(@$filters['sort_by_direction'], 'ASC');
elseif (@$filters['sort_by'] == 'location') $sortBy = $tablePrefix . 'countries.country ' . $operators->firstTrue(@$filters['sort_by_direction'], 'ASC') . ', city ' . $operators->firstTrue(@$filters['sort_by_direction'], 'ASC');
elseif (@$filters['sort_by'] == 'status') $sortBy = $tablePrefix . 'statuses.status ' . $operators->firstTrue(@$filters['sort_by_direction'], 'ASC');
elseif (@$filters['sort_by'] == 'assigned_to') $sortBy = 'assigned_to ' . $operators->firstTrue(@$filters['sort_by_direction'], 'ASC');
else $sortBy = 'description ASC';

$rumours = $database_manager->retrieve('rumours', ['rumour_id' => 'rumour_id', 'domain_alias_id' => 'domain_alias_id'], $matching);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	// We're displaying the rumours & form in the view.
	$subsites = $database_manager->retrieve('cms', ['cms_id' => 'cms_id', 'title' => 'title'], ['content_type' => 'd']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// This would be more efficient as a single update query. Oh well.
	$new_id = $_POST['domain_id'];
	$already_set = 0;
	$changed = 0;
	foreach ($rumours as $rumour) {
		if ($rumour['domain_alias_id'] !== $new_id) {
			$rumour['domain_alias_id'] = $new_id;
			$database_manager->updateSingle('rumours',
				// SET:
				['domain_alias_id' => $new_id],
				// WHERE:
				['rumour_id' => $rumour['rumour_id']]
			);
			$changed++;
		} else {
			$already_set++;
		}
	}
}
