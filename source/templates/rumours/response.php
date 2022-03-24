<?php
$label_class = 'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label';
$control_class = 'col-lg-9 col-md-9 col-sm-8 col-xs-12';
?>
<form class="form-horizontal">
	<div class="form-group">
		<label class="<?php echo $label_class ?>" for="who">Who:</label>
		<div class="<?php echo $control_class ?>">
			<select class="form-control" name="who"></select>
		</div>
	</div>
	<div class="form-group">
		<label class="<?php echo $label_class ?>" for="start_date">Start Date:</label>
		<div class="<?php echo $control_class ?>">
			<input type="date" name="start_date" />
		</div>
	</div>
	<div class="form-group">
		<label class="<?php echo $label_class ?>" for="duration_weeks">Proposed Duration: (in weeks)</label>
		<div class="<?php echo $control_class ?>">
			<input type="number" name="duration_weeks">
		</div>
	</div>
	<div class="form-group">
		<label class="<?php echo $label_class ?>" for="completion_date">Completion Date:</label>
		<div class="<?php echo $control_class ?>">
			<input type="date" name="completion_date" />
		</div>
	</div>
	<div class="form-group">
		<label class="<?php echo $label_class ?>" for="completed">Completed</label>
		<div class="<?php echo $control_class ?>">
			<input type="checkbox" name="completed">
		</div>
	</div>
	<div class="form-group">
		<label class="<?php echo $label_class ?>" for="outcomes">Outcomes:</label>
		<div class="<?php echo $control_class ?>">
			<textarea name="outcomes" class="form-control"></textarea>
		</div>
	</div>
	<button class="btn btn-info">Save</button>
	<h3>Discussion</h3>
</form>
