<?php

	echo "        <div id='siteNav' class='col-xs-12 col-sm-3 col-sm-pull-9 col-md-3 col-md-pull-9'>\n";
	echo "          <div class='visible-xs'><hr></div>\n";
	echo "          <div id='siteNavLiner'>\n";

	// side navigation
		if (@$logged_in) {
			echo "            <div id='salutation'>\n";
			echo "              Welcome, <strong><a href='/profile/" . $logged_in['username'] . "'>" . $logged_in['full_name'] . "</a></strong>\n";
			echo "            </div><!-- salutation -->\n";
			if (@$logged_in['rumours_assigned'] > 0) {
				echo "            <div id='rumourAlert'>\n";
				echo "              <span class='label label-danger pull-right'><a href='/my_rumours' class='inverse'>" . floatval($logged_in['rumours_assigned']) . "</a></span>\n";
				echo "              <a href='/my_rumours'>Rumours requiring your attention</a>\n";
				echo "            </div><!-- rumourAlert -->\n";
				
			}
		}
		echo "            <div class='siteNavItem'><a href='/search_results/report%3Drecent'>Recent rumours</a></div>\n";
		echo "            <div class='siteNavItem'><a href='/search_results/report%3Dcommon'>Most common rumours</a></div>\n";
		echo "            <div class='siteNavItem'><a href='/statistics'>Statistics</a></div>\n";
		echo "            <div class='siteNavItem'>" . $form->input('button', 'add_rumour', null, false, 'Report a Rumour', 'btn btn-info btn-block', null, null, null, null, array('onClick'=>'document.location.href="/rumour_add"; return false;')) . "</div>\n";
		echo "            " . $form->start('searchForm', '', 'post', 'form-horizontal') . "\n";
		// Search
			echo "              <div id='siteNavSearch' class='container-fluid'>\n";
			echo "                <div class='form-group'>" . $form->input('search', 'search_keywords', @$keywords, false, null, 'form-control') . "</div>\n";
			/* echo "                </div><!-- siteNavSearchAdvancedToggle -->\n"; */
			echo "              </div><!-- siteNavSearch -->\n";
		echo "            " . $form->end() . "<!-- searchForm -->\n";
		// API
			echo "            <div>\n";
			echo "              <span class='pull-left transluscent'><span class='glyphicon glyphicon-new-window'></span>&nbsp;&nbsp;</span>\n";
			// Explore the API Link hidden by default https://app.forecast.it/project/P-211/workflow/T14801
			// echo "              <div class='siteNavItemLabel'><a href='/explore_api'>Explore the API</a></div>\n";
			echo "            </div>\n";
		// About
			echo "            <div>\n";
			echo "              <span class='pull-left transluscent'><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;&nbsp;</span>\n";
			echo "              <div class='siteNavItemLabel'><a href='/about'>About " . htmlspecialchars($tl->settings['Name of this application'], ENT_QUOTES) . "</a></div>\n";
			echo "            </div>\n";
		// Help
			/* echo "            <div>\n"; */
			/* echo "              <span class='pull-left transluscent'><span class='glyphicon glyphicon-question-sign'></span>&nbsp;&nbsp;</span>\n"; */
			/* echo "              <div class='siteNavItemLabel'><a href='/help'>Help</a></div>\n"; */
			/* echo "            </div>\n"; */
		/* // Contact */
			/* echo "            <div>\n"; */
			/* echo "              <span class='pull-left transluscent'><span class='glyphicon glyphicon-envelope'></span>&nbsp;&nbsp;</span>\n"; */
			/* echo "              <div class='siteNavItemLabel'><a href='/contact'>Contact us</a></div>\n"; */
			/* echo "            </div>\n"; */
		
	echo "          </div><!-- siteNavLiner -->\n";
	
	echo "        </div><!-- siteNav -->\n";

?>
