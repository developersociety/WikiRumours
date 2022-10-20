<?php

$title = "Statistics" . (@$tl->page['domain_alias']['title'] ? " for " . $tl->page['domain_alias']['title'] : false);

if (count($_POST)) {
	$title .= " ($stats_from_date - $stats_to_date)";

}

// load Google Charts packages
$tl->page['javascript'] .= "  google.load('visualization', '1.1', {packages:['bar', 'corechart']});\n";

$dateToday = (new DateTime())->format('Y-m-d');

?>

<button type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#filtersModal">Filters</button>
<h2><?php echo $title ?></h2>

<div class="modal" id="filtersModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<form method="POST" id="dateFilterForm" class="form form-horizontal">
					<label>From:
						<input type="date" name="from_date" class="form-control" format="%Y-%m-%d"
							   value="<?php echo substr($stats_from_date, 0, 10); ?>"></input>
					</label>
					<label>To:
						<input type="date" name="to_date" class="form-control" format="%Y-%m-%d"
						       value="<?php echo substr($stats_to_date, 0, 10); ?>"></input>
					</label>

					<script>
						function dateString(daysAgo=0) {
							var now = new Date();
							now.setDate(now.getDate() + daysAgo);
							return now.toISOString().substring(0,10);
						}

						function filterThisWeek() {
							document.getElementsByName("from_date")[0].value = dateString(-7);
							document.getElementsByName("to_date")[0].value = dateString();
						}
						function filterThisMonth() {
							document.getElementsByName("from_date")[0].value = dateString(-30);
							document.getElementsByName("to_date")[0].value = dateString();
						}
						function filterThisYear() {
							document.getElementsByName("from_date")[0].value = dateString(-365);
							document.getElementsByName("to_date")[0].value = dateString();
						}
						function filterAllTime() {
							// document.getElementsByName("from_date")[0].value = '2000-01-01';
							// document.getElementsByName("to_date")[0].value = dateString();

							// Just 'GET' the page, since filters only apply when POST...
							location.replace(location.href); 
						}
					</script>
					<div class="btn-group">
						<a class="btn btn-secondary" onclick="filterThisWeek()">Last 7 days</a>
						<a class="btn btn-secondary" onclick="filterThisMonth()">Last 30 days</a>
						<a class="btn btn-secondary" onclick="filterThisYear()">Last 365 days</a>
						<a class="btn btn-secondary" onclick="filterAllTime()">Clear</a>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<input form="dateFilterForm" class="btn btn-primary pull-left" type="submit" name="action" value="Export">
				<input form="dateFilterForm" class="btn btn-primary" type="submit" name="action" value="Filter">
			</div>
		</div>
	</div>
</div>





<?php

	echo "<div class='pageModule row container-fluid'>\n";

	echo "  <div class='row'>\n";
	echo "    <div class='col-lg-6 col-md-6 col-sm-6 col-xs-12'>\n";
	// rumours
		echo "      <div class='panel panel-default'>\n";
	 	echo "        <div class='panel-heading'>\n";
	 	echo "          <h3 class='panel-title'>Rumours</h3>\n";
	 	echo "        </div>\n";
	  	echo "        <div class='panel-body'>" . floatval($numberOfRumours) . "</div>\n";
		echo "      </div>\n";
	echo "    </div>\n";
	echo "    <div class='col-lg-6 col-md-6 col-sm-6 col-xs-12'>\n";
	// sightings			
		echo "      <div class='panel panel-default'>\n";
	 	echo "        <div class='panel-heading'>\n";
	 	echo "          <h3 class='panel-title'>Sightings</h3>\n";
	 	echo "        </div>\n";
	  	echo "        <div class='panel-body'>" . floatval($numberOfSightings) . "</div>\n";
		echo "      </div>\n";
	echo "    </div>\n";
	echo "  </div>\n";

	// statuses
		if (count($statuses)) {
			echo "  <div class='row'>\n";
			echo "    <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
			echo "      <div class='panel panel-default'>\n";
		 	echo "        <div class='panel-heading'>\n";
		 	echo "          <h3 class='panel-title'>Statuses</h3>\n";
		 	echo "        </div>\n";
		  	echo "        <div class='panel-body'>\n";
			echo "          <ul class='nav nav-pills mutedPills'>\n";
			echo "            <li class='active'><a href='#statusChart' data-toggle='tab'>View as chart</a></li>\n";
			echo "            <li><a href='#statusTable' data-toggle='tab'>View as table</a></li>\n";
			echo "          </ul>\n";
			echo "          <div class='tab-content'>\n";
			echo "            <div class='tab-pane active' id='statusChart'>\n";
		  	echo "              <div id='statusPie' style='width: 100%; height: auto;'></div>\n";
		  	echo "            </div>\n";
			echo "            <div class='tab-pane' id='statusTable'>\n";
			echo "              <table class='table table-condensed'>\n";
			echo "              <thead>\n";
			echo "              <tr>\n";
			echo "              <th>Status</th>\n";
			echo "              <th>Rumours</th>\n";
			echo "              <th>Percentage</th>\n";
			echo "              </tr>\n";
			echo "              </thead>\n";
			echo "              <tbody>\n";
			for ($counter = 0; $counter < count($statuses); $counter++) {
				echo "              <tr>\n";
				echo "              <td>" . htmlspecialchars($statuses[$counter]['status'], ENT_QUOTES). "</td>\n";
				echo "              <td>" . $statuses[$counter]['count'] . "</td>\n";
				echo "              <td>" . number_format($statuses[$counter]['count'] / $numberOfRumours * 100, 1) . "%</td>\n";
				echo "              </tr>\n";
			}
			echo "              </tbody>\n";
			echo "              </table>\n";
		  	echo "            </div>\n";
		  	echo "          </div>\n";
		  	echo "        </div>\n";
			echo "      </div>\n";
			echo "    </div>\n";
			echo "  </div>\n";

			$tl->page['javascript'] .= "// status chart\n";
			$tl->page['javascript'] .= "  google.setOnLoadCallback(drawStatusPie);\n\n";
			$tl->page['javascript'] .= "  function drawStatusPie() {\n";
			$tl->page['javascript'] .= "    var data = new google.visualization.arrayToDataTable([\n";
			$tl->page['javascript'] .= "      ['Status', 'Rumours'],\n";
			for ($counter = 0; $counter < count($statuses); $counter++) {
				$tl->page['javascript'] .= "      ['" . htmlspecialchars($statuses[$counter]['status'], ENT_QUOTES). "', " . $statuses[$counter]['count'] . "]";
				if ($counter < count($statuses) - 1) $tl->page['javascript'] .= ",";
				$tl->page['javascript'] .= "\n";
			}
			$tl->page['javascript'] .= "    ]);\n\n";
			$tl->page['javascript'] .= "    var options = {\n";
			$tl->page['javascript'] .= "      is3D: true,\n";
			$tl->page['javascript'] .= "      chartArea: { left: 0, top: 0, width: '100%', height: '100%' },\n";
			$tl->page['javascript'] .= "      colors: [";
			for ($counter = 0; $counter < count($statuses); $counter++) {
				$tl->page['javascript'] .= "'#" . $statuses[$counter]['hex_color']. "'";
				if ($counter < count($statuses) - 1) $tl->page['javascript'] .= ",";
			}
			$tl->page['javascript'] .= "],\n";
			$tl->page['javascript'] .= "      pieSliceText: 'value'\n";
			$tl->page['javascript'] .= "    };\n\n";
			$tl->page['javascript'] .= "    var chart = new google.visualization.PieChart(document.getElementById('statusPie'));\n";
			$tl->page['javascript'] .= "    chart.draw(data, options);\n";
			$tl->page['javascript'] .= "  };\n\n";
		}

	// tags
		if (count($tags)) {
			echo "  <div class='row'>\n";
			echo "    <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
			echo "      <div class='panel panel-default'>\n";
		 	echo "        <div class='panel-heading'>\n";
		 	echo "          <h3 class='panel-title'>Tags</h3>\n";
		 	echo "        </div>\n";
		  	echo "        <div class='panel-body'>\n";
			echo "          <ul class='nav nav-pills mutedPills'>\n";
			echo "            <li class='active'><a href='#tagChart' data-toggle='tab'>View as chart</a></li>\n";
			echo "            <li><a href='#tagTable' data-toggle='tab'>View as table</a></li>\n";
			echo "          </ul>\n";
			echo "          <div class='tab-content'>\n";
			echo "            <div class='tab-pane active' id='tagChart'>\n";
		  	echo "              <div id='tagPie' style='width: 100%;'></div>\n";
		  	echo "            </div>\n";
			echo "            <div class='tab-pane' id='tagTable'>\n";
			echo "              <table class='table table-condensed'>\n";
			echo "              <thead>\n";
			echo "              <tr>\n";
			echo "              <th>Tag</th>\n";
			echo "              <th>Rumours</th>\n";
			echo "              <th>Percentage</th>\n";
			echo "              </tr>\n";
			echo "              </thead>\n";
			echo "              <tbody>\n";
			for ($counter = 0; $counter < count($tags); $counter++) {
				echo "              <tr>\n";
				echo "              <td>" . htmlspecialchars($tags[$counter]['tag'], ENT_QUOTES). "</td>\n";
				echo "              <td>" . $tags[$counter]['count'] . "</td>\n";
				echo "              <td>" . number_format($tags[$counter]['count'] / $numberOfRumours * 100, 1) . "%</td>\n";
				echo "              </tr>\n";
			}
			echo "              </tbody>\n";
			echo "              </table>\n";
		  	echo "            </div>\n";
		  	echo "          </div>\n";
		  	echo "        </div>\n";
			echo "      </div>\n";
			echo "    </div>\n";
			echo "  </div>\n";

			$tl->page['javascript'] .= "// tag chart\n";
			$tl->page['javascript'] .= "  google.setOnLoadCallback(drawTagPie);\n\n";
			$tl->page['javascript'] .= "  function drawTagPie() {\n";
			$tl->page['javascript'] .= "    var data = new google.visualization.arrayToDataTable([\n";
			$tl->page['javascript'] .= "      ['Tags', 'Rumours'],\n";
			for ($counter = 0; $counter < count($tags); $counter++) {
				$tl->page['javascript'] .= "      ['" . htmlspecialchars($tags[$counter]['tag'], ENT_QUOTES). "', " . $tags[$counter]['count'] . "]";
				if ($counter < count($tags) - 1) $tl->page['javascript'] .= ",";
				$tl->page['javascript'] .= "\n";
			}
			$tl->page['javascript'] .= "    ]);\n\n";
			$tl->page['javascript'] .= "    var options = {\n";
			$tl->page['javascript'] .= "      is3D: true,\n";
			$tl->page['javascript'] .= "      chartArea: { left: 0, top: 0, width: '100%', height: '100%' },\n";
			$tl->page['javascript'] .= "      pieSliceText: 'value'\n";
			$tl->page['javascript'] .= "    };\n\n";
			$tl->page['javascript'] .= "    var chart = new google.visualization.PieChart(document.getElementById('tagPie'));\n";
			$tl->page['javascript'] .= "    chart.draw(data, options);\n";
			$tl->page['javascript'] .= "  };\n\n";
		}

	echo "</div>\n";

	// rumours and sightings over time
		echo "<div class='pageModule row'>\n";
		echo "  <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
		echo "    <h3>Rumours and sightings by date</h3>\n";
		echo "    <ul class='nav nav-pills mutedPills'>\n";
		echo "      <li class='active'><a href='#rumoursAndSightingsByDateChartTab' data-toggle='tab'>View as chart</a></li>\n";
		echo "      <li><a href='#rumoursAndSightingsByDateTableTab' data-toggle='tab'>View as table</a></li>\n";
		echo "    </ul>\n";
		echo "    <div class='tab-content'>\n";
		echo "      <div class='tab-pane active' id='rumoursAndSightingsByDateChartTab'>\n";
		echo "        <div id='rumoursAndSightingsByDateChart' style='width: 100%; height: auto;''></div>\n";
		echo "      </div>\n";
		echo "      <div class='tab-pane' id='rumoursAndSightingsByDateTableTab'>\n";
		echo "        <table class='table table-condensed'>\n";
		echo "        <thead>\n";
		echo "        <tr>\n";
		echo "        <th>Month</th>\n";
		echo "        <th>Rumours</th>\n";
		echo "        <th>Sightings</th>\n";
		echo "        </tr>\n";
		echo "        </thead>\n";
		echo "        <tbody>\n";
		foreach ($rumoursAndSightingsByDateTable as $month=>$counts) {
			echo "        <tr>\n";
			echo "        <td>" . htmlspecialchars($month, ENT_QUOTES). "</td>\n";
			echo "        <td>" . floatval(@$counts['rumours']) . "</td>\n";
			echo "        <td>" . floatval(@$counts['sightings']) . "</td>\n";
			echo "        </tr>\n";
		}
		echo "        </tbody>\n";
		echo "        </table>\n";
		echo "      </div>\n";
		echo "    </div>\n";
		echo "  </div>\n";
		echo "</div>\n";

		$tl->page['javascript'] .= "// rumours by instance chart\n";
		$tl->page['javascript'] .= "  google.setOnLoadCallback(drawRumoursAndSightingsByDateChart);\n\n";
		$tl->page['javascript'] .= "  function drawRumoursAndSightingsByDateChart() {\n";
		$tl->page['javascript'] .= "    var data = new google.visualization.arrayToDataTable([\n";
		$tl->page['javascript'] .= "      ['Month', 'Rumours', 'Sightings'],\n";
		$counter = 0;
		foreach ($rumoursAndSightingsByDateChart as $month=>$counts) {
			$tl->page['javascript'] .= "      ['" . htmlspecialchars($month, ENT_QUOTES). "', " . floatval(@$counts['rumours']) . ", " . floatval(@$counts['sightings']) . "]";
			if ($counter < count($rumoursAndSightingsByDateChart) - 1) $tl->page['javascript'] .= ",";
			$tl->page['javascript'] .= "\n";
			$counter++;
		}
		$tl->page['javascript'] .= "    ]);\n\n";
		$tl->page['javascript'] .= "    var options = {\n";
		$tl->page['javascript'] .= "      curveType: 'function',\n";
		$tl->page['javascript'] .= "      width: '100%',\n";
		$tl->page['javascript'] .= "      vAxis: { viewWindow: { min: 0 }},\n";
		$tl->page['javascript'] .= "      hAxis: { slantedText: true },\n";
		$tl->page['javascript'] .= "      legend: { position: 'bottom' }\n";
		$tl->page['javascript'] .= "    };\n\n";
		$tl->page['javascript'] .= "    var chart = new google.visualization.LineChart(document.getElementById('rumoursAndSightingsByDateChart'));\n";
		$tl->page['javascript'] .= "    chart.draw(data, options);\n";
		$tl->page['javascript'] .= "  };\n\n";

	if (count(@$rumoursAndSightingsByDomain)) {
		// rumours and sightings per domain
			echo "<div class='pageModule row'>\n";
			echo "  <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\n";
			echo "    <h3>Deployments of MSF-Listen</h3>\n";
			echo "    <div id='rumoursAndSightingsByDomainChart' style='width: 100%; height: auto;''></div>\n";
			echo "  </div>\n";
			echo "</div>\n";

			$tl->page['javascript'] .= "// rumours by instance chart\n";
			$tl->page['javascript'] .= "  google.setOnLoadCallback(drawRumoursAndSightingsByDomainChart);\n\n";
			$tl->page['javascript'] .= "  function drawRumoursAndSightingsByDomainChart() {\n";
			$tl->page['javascript'] .= "    var data = new google.visualization.arrayToDataTable([\n";
			$tl->page['javascript'] .= "      ['Deployment', 'Rumours', 'Sightings'],\n";
			for ($counter = 0; $counter < count($rumoursAndSightingsByDomain); $counter++) {
				$tl->page['javascript'] .= "      ['" . htmlspecialchars($rumoursAndSightingsByDomain[$counter]['title'], ENT_QUOTES). "', " . $rumoursAndSightingsByDomain[$counter]['number_of_rumours'] . ", " . $rumoursAndSightingsByDomain[$counter]['number_of_sightings'] . "]";
				if ($counter < count($rumoursAndSightingsByDomain) - 1) $tl->page['javascript'] .= ",";
				$tl->page['javascript'] .= "\n";
			}
			$tl->page['javascript'] .= "    ]);\n\n";
			$tl->page['javascript'] .= "    var options = {\n";
			$tl->page['javascript'] .= "      colors:['#5bc0de','#addcea'],\n";
			$tl->page['javascript'] .= "      bars: 'horizontal' // Required for Material Bar Charts.\n";
			$tl->page['javascript'] .= "    };\n\n";
			$tl->page['javascript'] .= "    var chart = new google.charts.Bar(document.getElementById('rumoursAndSightingsByDomainChart'));\n";
			$tl->page['javascript'] .= "    chart.draw(data, options);\n";
			$tl->page['javascript'] .= "  };\n";
	}

?>
