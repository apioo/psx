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

	.type h1
	{
		background-color:#eee;
		color:#222;
		font-size:1.2em;
		font-weight:bold;
		border-bottom:1px solid #666;
	}

	.property-required
	{
		padding:2px 4px;
		font-size:90%;
		font-weight:bold;
		color:#c7254e;
		background-color:#f9f2f4;
		border-radius:4px;
		font-family:Menlo, Monaco, Consolas, "Courier New", monospace;
	}

	.property-optional
	{
		padding:2px 4px;
		font-size:90%;
		font-weight:normal;
		color:#c7254e;
		background-color:#f9f2f4;
		border-radius:4px;
		font-family:Menlo, Monaco, Consolas, "Courier New", monospace;
	}

	</style>
	<script type="text/javascript">

	function loadController(path)
	{
		$.get('?format=json&path=' + encodeURIComponent(path), function(resp){

			var html = '<h2>' + resp.path + '</h2>';

			if (resp.get_response) {
				html+= '<h3><a href="#" onclick="$(\'#api-get\').slideToggle();return false;">GET</a></h3>';
				html+= '<div id="api-get" style="display:none;">';
				html+= '<h4>Response</h4>';
				html+= resp.get_response;
				html+= '</div>';
			}

			if (resp.post_request || resp.post_response) {
				html+= '<h3><a href="#" onclick="$(\'#api-post\').slideToggle();return false;">POST</a></h3>';
				html+= '<div id="api-post" style="display:none;">';
				if (resp.post_request) {
					html+= '<h4>Request</h4>';
					html+= resp.post_request;
				}
				if (resp.post_response) {
					html+= '<h4>Response</h4>';
					html+= resp.post_response;
				}
				html+= '</div>';
			}

			if (resp.put_request || resp.put_response) {
				html+= '<h3><a href="#" onclick="$(\'#api-put\').slideToggle();return false;">PUT</a></h3>';
				html+= '<div id="api-put" style="display:none;">';
				if (resp.put_request) {
					html+= '<h4>Request</h4>';
					html+= resp.put_request;
				}
				if (resp.put_response) {
					html+= '<h4>Response</h4>';
					html+= resp.put_response;
				}
				html+= '</div>';
			}

			if (resp.delete_request || resp.delete_response) {
				html+= '<h3><a href="#" onclick="$(\'#api-delete\').slideToggle();return false;">DELETE</a></h3>';
				html+= '<div id="api-delete" style="display:none;">';
				if (resp.delete_request) {
					html+= '<h4>Request</h4>';
					html+= resp.delete_request;
				}
				if (resp.delete_response) {
					html+= '<h4>Response</h4>';
					html+= resp.delete_response;
				}
				html+= '</div>';
			}

			$('#body').html(html);
			$('#body').find('div:first').slideToggle();

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
	<?php foreach($routings as $i => $routing): ?>
		<?php list($methods, $path, $className) = $routing; ?>
		<li class="<?php echo $i % 2 == 0 ? 'even' : 'odd' ?>">
			<a href="#content" onclick="loadController('<?php echo $path; ?>');"><?php echo $path; ?></a>
		</li>
	<?php endforeach; ?>
	</ul>
</div>

<div class="api-content">
	<a name="content"></a>
	<h1>Content</h1>
	<div id="body"></div>
</div>

</body>
</html>
