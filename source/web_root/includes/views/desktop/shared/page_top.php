<?php

	// specify XHTML compliance and begin document header
		echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n";
		echo "<html lang='en' xmlns='http://www.w3.org/1999/xhtml'>\n";

		echo "<head>\n";

	// refresh
		if (@$refreshInSeconds) echo "<meta http-equiv='refresh' content='" . $refreshInSeconds . "' />\n";
	
	// discourage caching
		echo "  <meta http-equiv='Pragma' content='no-cache'>\n";
		echo "  <meta http-equiv='Expires' content='-1'>\n";
		echo "  <meta http-equiv='CACHE-CONTROL' content='NO-CACHE'>\n";

	// define character set
		echo "  <meta charset='UTF-8'>\n";
		echo "  <meta http-equiv='content-type' content='text/html; charset=UTF-8' />\n";
		
	// set IE compatibility
		echo "  <meta http-equiv='X-UA-Compatible' content='IE=9; IE=8;' />\n";

	// define viewport
		echo "  <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
		
	// define description for search engine indexing
		echo "  <meta name='description' content=" . '"' . $tl->settings['Describe this application'] . '"' . ">\n";

	// Facebook sharing tags
		if (@$tl->page['title']) echo "  <meta property='og:title' content=" . '"' . $tl->page['title'] . '"' . " />\n";
		if (@$tl->page['description']) echo "  <meta property='og:description' content=" . '"' . $tl->page['description'] . '"' . " />\n";
		if (@$pageImage) echo "  <meta property='og:image' content='" . $pageImage . "' />\n";

	// Twitter sharing tags
		if (@$tl->page['title'] || @$tl->page['description'] || @$pageImage) {
			echo "  <meta name='twitter:card' content='summary'>\n";
			if (@$tl->page['title']) echo "  <meta property='twitter:title' content=" . '"' . $tl->page['title'] . '"' . " />\n";
			if (@$tl->page['description']) echo "  <meta property='twitter:description' content=" . '"' . $tl->page['description'] . '"' . " />\n";
			if (@$pageImage) echo "  <meta property='twitter:image' content='" . $pageImage . "' />\n";
		}

	// load third-party CSS
		if (count(@$tl->frontEndLibraries)) {
			foreach ($tl->frontEndLibraries as $key =>$value) {
				if (@$value['local_css_path'] || @$value['remote_css_path']) {
					echo "  <!-- " . $key . (@$value['version'] ? " v." . $value['version'] : false) . " -->\n";
					if (@$value['remote_css_path']) echo "    <link rel='stylesheet' type='text/css' media='screen' href='" . $value['remote_css_path'] . "' />\n";
					elseif (@$value['local_css_path']) echo "    <link rel='stylesheet' type='text/css' media='screen' href='/libraries/" . $value['local_css_path'] . "' />\n";
					echo "\n";
				}
			}
		}

	// load TidalLock stylesheets
		if ($handle = opendir('libraries/tidal_lock/css/.')) {
			while (false !== ($file = readdir($handle))) {
				if (substr_count($file, ".css") > 0) echo "  <!-- Tidal Lock --><link href='/libraries/tidal_lock/css/" . $file . "' rel='stylesheet' media='screen' type='text/css' />\n";
			}
			closedir($handle);
		}

	// load base stylesheets
		if ($handle = opendir('resources/css/desktop/autoload/.')) {
			while (false !== ($file = readdir($handle))) {
				if (substr_count($file, ".css") > 0) echo "  <link href='/resources/css/desktop/autoload/" . $file . "' rel='stylesheet' media='screen' type='text/css' />\n";
			}
			closedir($handle);
		}

		if ($handle = opendir('resources/css/print/autoload/.')) {
			while (false !== ($file = readdir($handle))) {
				if (substr_count($file, ".css") > 0) echo "  <!-- " . ucwords(str_replace('_', ' ', substr($file, 0, -4))) . " -->\n    <link href='/resources/css/print/autoload/" . $file . "?rand=" . rand(10000, 99999) . "' rel='stylesheet' media='print' type='text/css' />\n";
			}
			closedir($handle);
		}

		if (file_exists("resources/css/desktop/" . $tl->page['template'] . ".css") > 0) echo "  <link href='/resources/css/desktop/" . $tl->page['template'] . ".css' rel='stylesheet' media='screen' type='text/css' />\n";
		
	// load page-specific CSS
		if ($tl->page['css']) {
			echo "  <style type='text/css'>\n";
			echo $tl->page['css'] . "\n";
			echo "  </style>\n";
		}

	// specify favicon
		if (file_exists('resources/img/icons/favicon.ico')) echo "  <link href='/resources/img/icons/favicon.ico' rel='SHORTCUT ICON' />\n";
		
	// specify canonical, if provided
		if ($tl->page['canonical_url']) echo "  <link rel='canonical' href='" . $tl->page['canonical_url'] . "' />\n\n";

	// load Google Analytics
		if (@$tl->settings['Enable Google Analytics'] && (@$tl->page['domain_alias']['google_analytics_id'] || @$googleProfiles[$currentGoogleProfile]['Analytics ID'])) {
			echo "<!-- Google Analytics -->\n";
			echo "  <script>\n";
			echo "    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){\n";
			echo "    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),\n";
			echo "    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)\n";
			echo "    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');\n\n";
			echo "    ga('create', '" . (@$tl->page['domain_alias']['google_analytics_id'] ? $tl->page['domain_alias']['google_analytics_id'] : @$googleProfiles[$currentGoogleProfile]['Analytics ID']) . "', 'auto');\n";
			echo "    ga('send', 'pageview');\n";
			echo "  </script>\n\n";
		}

	// specify page title
		echo "  <title>";
		if ($tl->page['title']) echo $tl->page['title'] . " - ";
		if ($tl->page['section']) echo $tl->page['section'] . " - ";
		echo htmlspecialchars($tl->settings['Name of this application'], ENT_QUOTES);
		echo "</title>\n";

		echo "</head>\n\n";

	// start body
		echo "<body" . ($tl->page['events'] ? " onLoad='" . $tl->page['events'] . "'" : false) . ">\n\n";

	if (!$tl->page['hide_page_chrome']) {
		
		// start header content
			echo "  <div id='pageContainer'>\n\n";
				
		// environment
			if (@$tl->settings['Display environment warning'] && (@$currentDatabase == 'dev' || @$currentDatabase == 'staging')) {
				echo "  <div id='environmentWarning' class='collapse in'><center>" . strtoupper($currentDatabase) . "</center></div>\n";
			}

		// maintenance mode
			if (@$tl->settings['Maintenance Mode'] == 'On' && @$logged_in['is_administrator']) {
				echo "  <div id='maintenanceWarning'><center>The website is currently in maintenance mode and is disabled for all users except administrators.</center></div>\n";
			}

		// console
			if (@$logged_in['is_tester'] && @$tl->settings['Enable console for testers']) {
				echo "  <div id='console' class='collapse'>\n";
				echo "  </div><!-- console -->\n";
			}

		echo "    <div class='container'>\n";
		echo "      <div id='header' class='row'>\n";

		// logo
			echo "        <div id='logo' class='col-xs-12 col-sm-3 col-md-3 col-lg-3'>\n";
			echo "          <h1 class='hidden'>" . htmlspecialchars($tl->settings['Name of this application'], ENT_QUOTES) . "</h1>\n";
			echo "          <a href='/'><img src='" . (@$tl->page['domain_alias']['destination_url'] && file_exists($tl->page['domain_alias']['destination_url']) ? '/' . $tl->page['domain_alias']['destination_url'] : $tl->defaultHeaderLogo) . "' border='0' class='img-responsive' alt='" . htmlspecialchars($tl->settings['Name of this application'], ENT_QUOTES) . "' /></a>\n";
			echo "        </div><!-- logo -->\n\n";
	
		// header
			echo "        <div id='userNav' class='col-xs-12 col-sm-9 col-md-9 col-lg-9'>\n";
			
			// non-mobile experience
				echo "          <ul id='userNavNonMobile' class='nav nav-pills pull-right hidden-xs'>\n";
				include __DIR__ . "/user_nav.php";
				echo "          </ul>\n";
			// mobile experience
				echo "          <ul id='userNavMobile' class='visible-xs hideBullets'>\n";
				include __DIR__ . "/user_nav.php";
				echo "            <hr />\n";
				echo "          </ul>\n";
					
			echo "        </div><!-- userNav -->\n";
			echo "      </div><!-- header -->\n";
			echo "    </div>\n";
			
			echo "    <div class='container'>\n";
			
			// begin page content
				echo "      <div id='pageContent' class='row'>\n";
				echo "        <div class='col-xs-12 col-sm-9 col-sm-push-3 col-md-9 col-md-push-3'>\n";
				
		// success, warning or error message
			if (@$tl->page['error']) echo "        <div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" . $tl->page['error'] . "</div>\n";
			elseif (@$tl->page['warning']) echo "        <div class='alert alert-warning alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" . $tl->page['warning'] . "</div>\n";
			elseif (@$tl->page['success']) echo "        <div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" . $tl->page['success'] . "</div>\n";
				
			echo "        <!-- PAGE CONTENT BEGINS -->\n\n";

	}

?>
