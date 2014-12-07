<!DOCTYPE>
<html>
<head>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/styles/monokai_sublime.min.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/highlight.min.js"></script>
	<style type="text/css">
	html, body, .container-fluid, .row 
	{
	    height:100%;
	}

	body
	{
		background-color:#fff;
	}

	pre
	{
		padding:0px;
		margin:0px;
		border:none;
		border-radius:0px;
	}

	.sidebar 
	{
		background-color:#021c35;
	}

	@media (min-width: 992px){
	  .sidebar {
	    position:fixed;
	    top:0;
	    left:0;
	    bottom:0;
	    z-index:1000;
	    display:block;
	    background-color:#021c35;
	  }
	}

	.psx-navigation
	{
		background-color:#021c35;
		overflow:hidden;
	}

	.psx-navigation ul
	{
		list-style-type:none;
		padding:0px;
		margin:0px;
		margin-top:16px;
	}

	.psx-navigation a
	{
		display:block;
		padding:8px;
		color:#fff;
		font-family:monospace;
	}

	.psx-navigation .nav-head
	{
		color:#55acee;
		font-weight:bold;
		font-size:0.8em;
		padding:8px;
		margin-bottom:4px;
		border-bottom:1px solid #55acee;
	}

	.psx-content
	{
		background-color:#fff;
		margin-bottom:64px;
	}

	.psx-content > h3
	{
		margin:12px 8px;
	}

	.psx-content > div > div > h4
	{
		margin:0px;
		margin-top:8px;
		padding:8px;
		font-size:0.9em;
		font-weight:bold;
		background-color:#021c35;
		color:#fff;
	}

	.psx-content > div > div > h5
	{
		text-align:right;
		background-color:#eee;
		margin:0px;
		padding:10px;
		font-weight:bold;
		font-size:0.9em;
		opacity:0.8;
	}

	#api-toolbar
	{
		float:right;
	}

	#api-toolbar button
	{
		margin-left:8px;
	}

	#api-description
	{
		margin:12px 8px
	}

	.form-control, .btn
	{
		border-radius:0px;
	}

	.nav-tabs > li > a
	{
		border-radius:0px;
	}

	.type > h1
	{
		font-family:monospace;
		font-size:1.6em;
		border-bottom:2px solid #222;
		padding:8px;
	}

	.property-required
	{
		font-weight:bold;
	}

	.property-constraint dt
	{
		font-size:0.9em;
		float:left;
		width:100px;
		clear:left;
	}

	.property-constraint dd ul
	{
		list-style-type:none;
		padding:0px;
		margin:0px;
		padding-left:100px;
	}
	</style>
	<script type="text/javascript">
	function loadApi(path, version)
	{
		var html = '<div id="api-toolbar"></div>';
		html+= '<h3 id="api-title"></h3>';
		html+= '<div id="api-description"></div>';
		html+= '<div id="api-nav"></div>';
		html+= '<div id="api-doc" class="tab-content"></div>';
		$('.psx-content').html(html);

		var successCallback = function(resp){
			if (resp.path) {
				$('#api-title').html(resp.path + ' (v' + resp.view.version + ')');
				$('#api-description').html(resp.description);
				$('#api-toolbar').html(getToolbar(resp));

				var html = '';
				var nav = '<ul class="nav nav-tabs">';

				for (var i = 0; i < resp.method.length; i++) {

					var method = resp.method[i].toLowerCase();

					nav+= '<li role="presentation"><a href="#' + method + '">' + resp.method[i] + '</a></li>'

					var requestKey  = method + '_request';
					var responseKey = method + '_response';

					html+= '<div role="tabpanel" class="tab-pane" id="' + method + '">';

					for (var key in resp.view.data) {

						var dataType = resp.view.data[key];
						var row = dataType[method];

						if (row && (row['request'] || row['response'])) {

							html+= '<h4>' + key + '</h4>';

							if (row['request']) {
								html+= '<h5>Request</h5>';
								html+= row['request'];
							}

							if (row['response']) {
								html+= '<h5>Response</h5>';
								html+= row['response'];
							}
						}

					}

					html+= '</div>';
				}

				nav+= '</ul>';

				// nav
				$('#api-nav').html(nav);
				$('#api-nav a').click(function(e){
					e.preventDefault();
					$(this).tab('show');
				});

				// doc
				$('#api-doc').html(html);
				$('#api-doc').find('pre code').each(function(i, block) {
					hljs.highlightBlock(block);
				});
				$('#api-nav a:first').trigger('click');
			} else {
				$('#api-nav').html('');
				$('#api-doc').html('<div class="alert alert-info" role="alert>No API documentation available</div>');
			}
		};

		var errorCallback = function(resp){
			var data = resp.responseJSON;
			if (data.hasOwnProperty('success') && data.success === false) {
				$('#api-doc').html('<div class="alert alert-danger" role="alert">' + data.message + '</div><pre>' + data.trace + '</pre>');
			} else {
				$('#api-doc').html('<div class="alert alert-danger" role="alert">An unknown error occured</div><pre>' + resp.responseText + '</pre>');
			}
		};

		var url = location.pathname + '/' + version + path;

		$.ajax(url, {
			success: successCallback,
			error: errorCallback
		});
	}

	function loadPage(url)
	{
		$.get(url, function(resp){
			$('.psx-content').html(resp);
		});
	}

	function getToolbar(resp, currentVersion)
	{
		var html = '';
		if (resp.see_others) {
			for (var key in resp.see_others) {
				html+= '<button class="btn btn-default" onclick="goToOther(\'' + resp.see_others[key] + '\')">' + key + '</button>';
			}
		}
		return html;
	}

	function goToOther(path)
	{
		window.open(path, '_blank');
	}

	$(document).ready(function(){
		$('.psx-navigation ul li a:first').trigger('click');
	});
	</script>
</head>
<body>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-2 sidebar">
			<nav class="psx-navigation">
				<ul>
					<?php if(!empty($metas)): ?>
					<li class="nav-head">Meta</li>
					<?php foreach($metas as $name => $url): ?>
					<li><a href="#" onclick="loadPage('<?php echo $url; ?>');return false"><?php echo $name; ?></a></li>
					<?php endforeach; ?>
					<?php endif; ?>
					<?php if(!empty($routings)): ?>
					<li class="nav-head">Endpoints</li>
					<?php foreach($routings as $routing): ?>
					<li><a href="#" onclick="loadApi('<?php echo $routing['path']; ?>', <?php echo $routing['version']; ?>);return false"><?php echo $routing['path']; ?></a></li>
					<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</nav>
		</div>
		<div class="col-md-10 col-md-offset-2 content">
			<div class="psx-content">
				<div id="api-toolbar"></div>
				<h3 id="api-title"></h3>
				<div id="api-description"></div>
				<div id="api-nav"></div>
				<div id="api-doc" class="tab-content"></div>
			</div>
		</div>
	</div>
</div>


</body>
</html>
