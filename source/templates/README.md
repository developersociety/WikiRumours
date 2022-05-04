# Templates

This folder is for new HTML templates.

Rather than import any new dependencies or anything, we'll just create .php
files here, and try to be discipined in how we use them.

Probably it'll end up being HTML snippets rather than whole pages, and rather
than a big refactoring task, just creating the new bits of templating as those
sections of the codebase are touched.

## Using Templates

For a template in this directory named `my_template.php`:

```php
<h1><?php echo $variable; ?></h1>
```

You can then use that template in another location in the codebase:

```php
<?php
   ...

	renderTemplate('my_template.php', [
		'variable' => 'whatever'
	]);

?>
```

resulting in:

```html
<h1>whatever</h1>
```

Try to do as much of your logic and pre-processing in your main PHP files, rather than
in the template - and have the template just do rendering.  The intention is to get to
templates being editable by FE developers with HTML experience, but not a lot of PHP needed.
