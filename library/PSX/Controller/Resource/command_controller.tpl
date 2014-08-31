<!DOCTYPE>
<html>
<head>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	<style type="text/css">
	.api-navigation
	{
		float:left;
		width:280px;
		background-color:#eee;
		height:100%;
		overflow:hidden;
		border-right:1px solid #222;
	}

	.api-navigation h1
	{
		background-color:#222;
		color:#fff;
		margin:0px;
		padding:12px;
		padding-left:8px;
		font-size:0.8em;
	}

	.api-navigation ul
	{
		list-style-type:none;
		padding:0px;
	}

	.api-navigation li
	{
		padding:8px;
	}

	.api-navigation a
	{
		font-size:1.4em;
	}

	.api-navigation .odd
	{
		background-color:#fff;
	}

	.api-content
	{
		height:100%;
		overflow:auto;
	}

	.api-content h1
	{
		background-color:#222;
		color:#fff;
		margin:0px;
		padding:12px;
		padding-left:8px;
		font-size:0.8em;
	}

	.api-content h2
	{
		margin:0px;
		padding:8px;
		background-color:#eee;
		font-size:1.4em;
	}

	.api-content h3
	{
		margin:0px;
		padding:8px;
		background-color:#e7f0f7;
		border-top:1px solid #0f6ab4;
		font-size:1.4em;
	}

	.api-content h4
	{
		margin:0px;
		padding:8px;
		background-color:#222;
		color:#fff;
		font-size:1.2em;
		text-align:right;
	}

	.command-input
	{
		margin:8px;
	}

	#command-output
	{
		white-space:pre;
		font-family:monospace;
		margin:8px;
	}

	.form-control, .btn
	{
		border-radius:0px;
	}
	</style>
	<script type="text/javascript">

	function loadCommand(command)
	{
		$.get('?format=json&command=' + encodeURIComponent(command), function(resp){

			var html = '<h2>' + resp.command + '</h2>';
			html+= '<h3>Input</h3>';
			html+= '<div class="command-input">';
			html+= '<p>' + resp.description + '</p>';

			if (resp.parameters) {
				html+= '<form role="form" id="form">';

				for (var i = 0; i < resp.parameters.length; i++) {

					html+= '<div class="form-group">';
					html+= '<label for="' + resp.parameters[i].name + '">' + resp.parameters[i].description + '</label>';
					html+= '<input type="text" class="form-control" name="' + resp.parameters[i].name + '" id="' + resp.parameters[i].name + '" />';
					html+= '</div>';

				}

				html+= '<button type="button" class="btn btn-default" onclick="submitCommand(\'' + command.replace(/\\/g, '\\\\') + '\')">Submit</button>';
				html+= '</form>';
			}

			html+= '</div>';
			html+= '<h3>Output</h3>';
			html+= '<div id="command-output"></div>';

			$('#body').html(html);

		});
	}

	function submitCommand(command)
	{
		$.post('?format=json&command=' + encodeURIComponent(command), $('#form').serialize(), function(resp){

			$('#command-output').html(resp.output);

		});
	}

	$(document).ready(function(){

		$('.api-navigation').find('a:first').trigger('click');

	});
	</script>
</head>
<body>

<div class="api-navigation">
	<h1>Navigation</h1>
	<ul>
	<?php $i = 0; foreach($commands as $alias => $command): ?>
		<li class="<?php echo $i % 2 == 0 ? 'even' : 'odd' ?>">
			<a href="#content" onclick="loadCommand('<?php echo addslashes($command); ?>');"><?php echo $alias; ?></a>
		</li>
	<?php $i++; endforeach; ?>
	</ul>
</div>

<div class="api-content">
	<a name="content"></a>
	<h1>Content</h1>
	<div id="body"></div>
</div>

</body>
</html>
