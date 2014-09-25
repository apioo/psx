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
	function doSearch(el, method) {
		var search = $(el).val().toLowerCase();
		var expression = new RegExp(search, 'g');

		$('#routing-table tbody tr td:nth-child(' + method + ')').each(function(){

			if ($(this).text().toLowerCase().search(expression) == -1) {
				$(this).parent().collapse('hide');
			} else {
				$(this).parent().collapse('show');
			}

		});
	}
	</script>
</head>
<body>

<div class="psx-tool-content">
	<h1>Routing</h1>
	<table id="routing-table" class="table">
	<colgroup>
		<col width="20%" />
		<col width="40%" />
		<col width="40%" />
	</colgroup>
	<thead>
	<tr>
		<th>Method</th>
		<th>Path</th>
		<th>Controller</th>
	</tr>
	<tr>
		<td><input type="text" class="form-control" onkeyup="doSearch(this,1)" /></td>
		<td><input type="text" class="form-control" onkeyup="doSearch(this,2)" /></td>
		<td><input type="text" class="form-control" onkeyup="doSearch(this,3)" /></td>
	</tr>
	</thead>
	<tbody>
	<?php foreach($routings as $routing): ?>
		<tr>
			<td><?php echo implode(', ', $routing['methods']); ?></td>
			<td><?php echo $routing['path']; ?></td>
			<td><?php echo $routing['source']; ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
</div>

</body>
</html>
