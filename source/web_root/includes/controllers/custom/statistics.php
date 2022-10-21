<?php

function date_from_post_or_default($post_name, $default)
{
    /*  Try to get a date from either the current POST data, if it's a valid date,
     *  and fallback to a default value */

    $output = $default;

    try {
        if (array_key_exists($post_name, $_POST) && $_POST[$post_name]) {
            $parsed_date = date_create($_POST[$post_name]);
            $output = date_format($parsed_date, 'Y-m-d') ?: $default;
        }
    } catch (Exception $err) {
        error_log($err);
    };

    return $output;
}

// Individual calculation functions:

function total_rumour_count($stats_from_date, $stats_to_date, $domain_alias_filter)
{
    $matching_query = array('enabled' => '1');

    $result = countInDb(
        'rumours',
        'rumour_id',
        $matching_query,
        null,
        null,
        null,
        "$domain_alias_filter
        date(occurred_on) > '${stats_from_date}' AND date(occurred_on) <= '${stats_to_date}'"
    );
    return floatval(@$result[0]['count']);
}


function total_sighting_count($stats_from_date, $stats_to_date, $domain_alias_filter)
{
	global $tablePrefix;
	$result = directlyQueryDb(
		"
		SELECT
			COUNT(sighting_id) AS count
		FROM
			${tablePrefix}rumour_sightings
		LEFT JOIN ${tablePrefix}rumours
			ON ${tablePrefix}rumour_sightings.rumour_id = ${tablePrefix}rumours.rumour_id
		WHERE
			${domain_alias_filter}
			date(heard_on) >= '${stats_from_date}' AND date(heard_on) <= '${stats_to_date}'"
	);
	return floatval(@$result[0]['count']);
}

function calculate_sightings_for_months(&$rumoursAndSightingsByDateChart, $stats_from_date, $stats_to_date, $domain_alias_filter)
{
    global $tablePrefix;
    // sightings
    $result = directlyQueryDb(
        "SELECT DATE_FORMAT(heard_on, '%M %Y') AS month,
        COUNT(sighting_id) AS count
        FROM ${tablePrefix}rumour_sightings
        LEFT JOIN ${tablePrefix}rumours
             ON ${tablePrefix}rumour_sightings.rumour_id = ${tablePrefix}rumours.rumour_id
        WHERE
            $domain_alias_filter
            heard_on >= '${stats_from_date}' AND heard_on <= '${stats_to_date}'
        GROUP BY month ORDER BY heard_on ASC"
    );

    for ($counter = 0; $counter < count($result); $counter++) {
        $month = $result[$counter]['month'];
        if (!count(@$rumoursAndSightingsByDateChart[$month])) {
            $rumoursAndSightingsByDateChart[$month] = array();
        }
        $rumoursAndSightingsByDateChart[$month]['sightings'] = $result[$counter]['count'];
    }
}

// rumours
function calculate_rumours_for_months(&$rumoursAndSightingsByDateChart, $stats_from_date, $stats_to_date, $domain_alias_filter)
{
    $result = retrieveFromDb(
        'rumours',
        array(
            "DATE_FORMAT(occurred_on, '%M %Y')" => 'month',
            "COUNT(rumour_id)" => 'count'
        ),
        null,
        null,
        null,
        null,
		"$domain_alias_filter
		date(occurred_on) >= '$stats_from_date' AND date(occurred_on) <= '$stats_to_date'",
        'month',
        'occurred_on ASC'
    );
    for ($counter = 0; $counter < count($result); $counter++) {
        $month = $result[$counter]['month'];
        if (!count(@$rumoursAndSightingsByDateChart[$month])) $rumoursAndSightingsByDateChart[$month] = array();
        $rumoursAndSightingsByDateChart[$month]['rumours'] = $result[$counter]['count'];
    }
}

// statuses
function calculate_statuses($stats_from_date, $stats_to_date, $domain_alias_filter)
{
    global $tablePrefix;

    return directlyQueryDb(
        "SELECT
            ${tablePrefix}statuses.status,
            ${tablePrefix}statuses.hex_color,
            COUNT(${tablePrefix}statuses.status) AS count
        FROM ${tablePrefix}rumours
        LEFT JOIN ${tablePrefix}statuses
        ON ${tablePrefix}rumours.status_id = ${tablePrefix}statuses.status_id
        WHERE
            $domain_alias_filter
            date(occurred_on) >= '${stats_from_date}' AND date(occurred_on) <= '${stats_to_date}'
        GROUP BY ${tablePrefix}statuses.status
        ORDER BY position ASC"
    );
}

function calculate_tags($stats_from_date, $stats_to_date, $domain_alias_filter, $numberOfTagsToDisplay = 20)
{
    global $tablePrefix;

    return directlyQueryDb(
        "SELECT
            ${tablePrefix}tags.tag,
            COUNT(${tablePrefix}tags.tag) AS count
        FROM ${tablePrefix}rumours_x_tags
        LEFT JOIN ${tablePrefix}tags
            ON ${tablePrefix}rumours_x_tags.tag_id = ${tablePrefix}tags.tag_id
        LEFT JOIN ${tablePrefix}rumours
            ON ${tablePrefix}rumours_x_tags.rumour_id = ${tablePrefix}rumours.rumour_id
        WHERE
            $domain_alias_filter
            date(occurred_on) >= '${stats_from_date}' AND date(occurred_on) <= '${stats_to_date}'
        GROUP BY ${tablePrefix}tags.tag
        ORDER BY count DESC" . ($numberOfTagsToDisplay ? " LIMIT " . $numberOfTagsToDisplay : false)
    );
}

function calculate_rumours_and_sightings_by_domain($stats_from_date, $stats_to_date)
{
    global $tablePrefix;
    return directlyQueryDb("
    SELECT
        ${tablePrefix}cms.title,
        (SELECT 
            COUNT(*)
            FROM ${tablePrefix}rumours
			WHERE
				${tablePrefix}rumours.domain_alias_id = ${tablePrefix}cms.cms_id
				AND date(occurred_on) >= '${stats_from_date}' AND date(occurred_on) <= '${stats_to_date}'
        ) AS number_of_rumours,
        (SELECT
            COUNT(*) FROM ${tablePrefix}rumour_sightings
            LEFT JOIN ${tablePrefix}rumours
            ON ${tablePrefix}rumours.rumour_id = ${tablePrefix}rumour_sightings.rumour_id
			WHERE
				${tablePrefix}rumours.domain_alias_id = ${tablePrefix}cms.cms_id
				AND date(occurred_on) >= '${stats_from_date}' AND date(occurred_on) <= '${stats_to_date}'
        ) AS number_of_sightings
    FROM ${tablePrefix}cms
    WHERE ${tablePrefix}cms.content_type = 'd'
    ORDER BY ${tablePrefix}cms.title ASC");
}

/* *******************************************************************
 * Now all the individual calcualtions are defined as functions,
 * calculate them all using the appropriate filters, etc,
 * and store them as variables that are used in includes/views/desktop/custom/statistics.php
 ********************************************************************* */

$stats_from_date = date_from_post_or_default('from_date', '2000-01-01');
$stats_to_date = date_from_post_or_default('to_date', (new DateTime())->format('Y-m-d'));

// Domain Alias filter:

// generate a chunk of SQL which can go between WHERE and the dates filtering,
// with an AND at the end of it - if the current view is being rendered on a subsite,
// to limit results to just that subsite
if (@$tl->page['domain_alias']['cms_id']) {
    $domain_alias_filter = "${tablePrefix}rumours.domain_alias_id = '" . $tl->page['domain_alias']['cms_id'] . "' AND";
} else {
    $domain_alias_filter = '';
}


// Get results of calculations
$numberOfSightings = total_sighting_count($stats_from_date, $stats_to_date, $domain_alias_filter);
$numberOfRumours = total_rumour_count($stats_from_date, $stats_to_date, $domain_alias_filter);

// rumours and sightings over time
$rumoursAndSightingsByDateChart = array();
calculate_sightings_for_months($rumoursAndSightingsByDateChart, $stats_from_date, $stats_to_date, $domain_alias_filter);
calculate_rumours_for_months($rumoursAndSightingsByDateChart, $stats_from_date, $stats_to_date, $domain_alias_filter);
// And generate the table - which should be most-recent-first:
$rumoursAndSightingsByDateTable = array_reverse($rumoursAndSightingsByDateChart);

$statuses = calculate_statuses($stats_from_date, $stats_to_date, $domain_alias_filter);
$tags = calculate_tags($stats_from_date, $stats_to_date, $domain_alias_filter);

// rumours and sightings by domain alias
if (!@$tl->page['domain_alias']['cms_id']) {
    $rumoursAndSightingsByDomain = calculate_rumours_and_sightings_by_domain($stats_from_date, $stats_to_date);
}


if (array_key_exists('action', $_POST) && $_POST['action'] == 'Export') {
	// If the user requested an export, then return a CSV File download, rather than
	// regular page rendering:

	$filename = "statistics_${stats_from_date}-to-${stats_to_date}";
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=$filename.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	/* echo "<pre>"; */

	$data = array(
		"New Rumours" => $numberOfRumours,
		"New Sightings" => $numberOfSightings,
	);

	foreach (array_values($statuses) as $status_option) {
		$data['Status:' . $status_option['status']] = $status_option['count'];
	}

	foreach (array_values($tags) as $tag_option) {
		$data['Tag:' . $tag_option['tag']] = $tag_option['count'];
	}


	foreach (array_values($tags) as $tag_option) {
		$data['Tag:' . $tag_option['tag']] = $tag_option['count'];
	}

	if (isset($rumoursAndSightingsByDomain)) {
		foreach (array_values($rumoursAndSightingsByDomain) as $domain_option) {
			$data[$domain_option['title'] . ' Rumours'] = $domain_option['number_of_rumours'];
			$data[$domain_option['title'] . ' Sightings'] = $domain_option['number_of_sightings'];
		}
	}

	# Produce the CSV from the key => value data array...
	echo implode(',', array_keys($data));
	echo "\n";
	echo implode(',', array_values($data));
	echo "\n";

	/* echo "</pre>"; */

	// And Don't attempt any further page rendering:
	exit(0);
}
