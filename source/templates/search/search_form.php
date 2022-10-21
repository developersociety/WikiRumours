<?php

// Only display advanced filters un-collapsed if they're already selected:
$advancedFiltersCSS = '';
if (@$filters['country_id'] || @$filters['priority_id'] || @$filters['status_id'] || @$filters['tag_id']) {
	$advancedFiltersCSS = ' in';
}

echo $form->start('searchForm', '', 'post', ''); // 'form-horizontal');
?>
<div id='siteNavSearch' class='container-fluid'>
	<div class="input-group">
		<?php echo $form->input('search', 'search_keywords', @$keywords, false, null, 'form-control'); ?>
		<div class="input-group-btn">
			<a href='javascript:void(0)' class="btn btn-default" onClick='return false' id='advancedSearchButton' data-toggle='collapse' data-target='#siteNavSearchAdvancedToggle'><span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filters</a>
			<input type="submit" value="Search" class="btn btn-info"></input>
		</div>

	</div>
	<div id="siteNavSearchAdvancedToggle" class="collapse <?php echo $advancedFiltersCSS; ?>">
		<div id=" siteNavSearchAdvanced" class="form-horizontal">
			<div class="row container-fluid">
				<div class="col-md-6 panel">
					<div class="form-group">
						<label for="search_country">Country</label>
						<?php echo $form->input('country', 'search_country', @$filters['country_id'], false, 'All countries', 'form-control'); ?>
					</div>
					<div class="form-group ">
						<label for="search_priority">Priority</label>
						<?php echo $form->input('select', 'search_priority', @$filters['priority_id'], false, 'All priorities', 'form-control', $rumourPriorities); ?>
					</div>
				</div>
				<div class="col-md-6 panel">

					<div class="form-group">
						<label for="search_status">Status</label>
						<?php echo $form->input('select', 'search_status', @$filters['status_id'], false, 'All statuses', 'form-control', $rumourStatuses); ?>
					</div>
					<div class="form-group">
						<label for="search_tag">Tags</label>
						<?php echo $form->input('select', 'search_tag', @$filters['tag_id'], false, 'All tags', 'form-control', $rumourTags); ?>
					</div>
				</div>
			</div>

		</div><!-- siteNavSearchAdvanced -->
	</div><!-- siteNavSearchAdvancedToggle -->
</div><!-- siteNavSearch -->

<?php
echo $form->end() . "<!-- searchForm -->";
