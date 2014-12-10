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

	.api-loader
	{
		margin:8px;
		margin-top:32px;
		text-align:center;
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
		html+= '<div id="api-doc" class="tab-content"><div class="api-loader"><img src="data:image/gif;base64,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" /></div></div>';
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
