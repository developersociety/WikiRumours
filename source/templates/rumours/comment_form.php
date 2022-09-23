<form name="addCommentForm2" method="post" onSubmit="return false;">
<div id='addComment' <?php if ($initially_hidden) echo 'class="collapse"'; ?>>
		<div class='row form-group'>
			<div class='col-md-10'>
				<textarea name="new_comment" placeholder="Add your thoughts..." class="form-control richtext"><?php echo @$_POST['new_comment'] ?></textarea>
			</div>
			<div class='col-md-2'>
				<input type="submit" name="submitComment" value="Submit" class="btn btn-info" onClick="validateAddCommentForm(this.form); return false;" />
			</div>
		</div>
	</div>
</form>
