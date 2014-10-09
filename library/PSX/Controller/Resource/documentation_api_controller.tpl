<!DOCTYPE>
<html>
<head>
	<title><?php echo $path; ?></title>
	<style type="text/css">
	body
	{
		font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;
	}

	h1
	{
		padding:10px;
		background-color:#222;
		color:#fff;
		font-size:1.4em;
	}

	dl
	{
		padding:4px 8px;
	}

	.table
	{
		width:100%;
	}

	.table th
	{
		border-bottom:2px solid #ccc;
		text-align:left;
		padding:6px;
	}

	.table td
	{
		padding:6px;
		border-bottom:1px solid #eee;
	}

	.property-required
	{
		font-weight:bold;
	}
	</style>
</head>
<body>

<h1><?php echo $path; ?></h1>

<dl>
	<dt>Methods</dt>
	<dd><?php echo implode(', ', $method); ?></dd>
	<dt>Version</dt>
	<dd><?php echo $view['version']; ?></dd>
	<dt>Status</dt>
	<dd><?php echo $view['status'] == \PSX\Api\View::STATUS_ACTIVE ? 'Active' : ($view['status'] == \PSX\Api\View::STATUS_DEPRECATED ? 'Deprecated' : 'Closed'); ?></dd>
</dl>

<?php foreach($view['data'] as $key => $html): ?>
	<h2 name="<?php echo $key; ?>"><?php echo strtoupper(strstr($key, '_', true)) . ' ' . ucfirst(substr(strstr($key, '_'), 1)); ?></h2>
	<?php echo $html; ?>
<?php endforeach; ?>

</body>
</html>
