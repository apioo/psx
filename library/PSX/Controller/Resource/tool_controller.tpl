<!DOCTYPE>
<html>
<head>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	<style type="text/css">
	<?php include __DIR__ . '/tool.css'; ?>
	</style>
	<script type="text/javascript">
	</script>
</head>
<body>

<div class="psx-tool-navigation">
	<h1>Navigation</h1>
	<?php if(!empty($paths)): ?>
	<ul>
		<?php foreach($paths as $i => $path): ?>
		<li class="<?php echo $i % 2 == 0 ? 'even' : 'odd' ?>">
			<a href="<?php echo $path['path']; ?>" target="psx-tool-content-frame"><?php echo $path['title']; ?></a>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>

<div class="psx-tool-content">
	<iframe name="psx-tool-content-frame" src="<?php echo $current['path']; ?>" style="width:100%;height:100%;border:none"></iframe>
</div>

</body>
</html>
