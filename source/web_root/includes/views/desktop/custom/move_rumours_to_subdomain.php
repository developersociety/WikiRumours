<h2>Move rumours to subdomain</h2>
<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
?>
<?php echo count($rumours);  ?> Rumours Selected.
	<form method="POST">
		<select name="domain_id">
			<?php
			foreach ($subsites as $subsite) {
				echo '<option value="' . $subsite['cms_id'] . '">' . $subsite['title'] . '</option>';
			}
			?>
		</select>
		<input type="submit">Move</input>
	</form>
<?php


} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
	echo 'Setting to Site ID:' . $new_id;
	echo '<hr>';
	print_r('Unchanged:' . $already_set);
	echo '<hr>';
	print_r('Changed:' . $changed);
	echo '<hr>';
}
