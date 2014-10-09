<!DOCTYPE>
<html>
<head>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	<style type="text/css">
	<?php include __DIR__ . '/tool.css'; ?>
	.api-schema-download
	{
		float:left;
		padding:4px;
	}

	.api-schema-download select
	{
		width:120px;
		padding:3px;
		margin:0px;
		margin-right:8px
	}

	.api-schema-download a
	{
		padding:0px;
		margin:0px;
		color:#fff;
		text-decoration:underline;
	}

	.api-info
	{
		padding:12px;
		background-color:#e7f0f7;
	}

	.api-toolbar
	{
		float:right;
		padding:4px;
		width:340px;
		text-align:right;
	}

	.api-toolbar button
	{
		margin-left:4px;
		height:30px;
	}

	.api-toolbar select
	{
		margin-left:4px;
		height:30px;
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
	function loadController(path, version)
	{
		var absolutePath = location.pathname + '/' + version + path;

		$.get(absolutePath, function(resp){

			var html = '';
			if (resp.versions && resp.versions.length > 0) {
				var view = resp.view;
				html+= getToolbar(resp, view.version);
				if (view.status != 2) {
					html+= '<div id="api-version-' + view.version + '" class="api-version">';
					html+= '<h2>' + (view.status == 1 ? '<s><span id="api-path">' + resp.path + '</span></s>' : '<span id="api-path">' + resp.path + '</span>') + ' (v' + view.version + ')</h2>';
					html+= '<div class="api-version-view">' + getApiBody(view.data, resp.path, view.version) + '</div>';
					html+= '</div>';
				}
			} else {
				html+= '<div class="api-info">No API documentation available</div>';
			}

			$('#body').html(html);
			$('#body .api-version-view').find('div:first').slideToggle();

		});
	}

	function getApiBody(resp, path, version)
	{
		var html = '';
		if (resp.get_response) {
			html+= '<h3><a href="#" onclick="$(this).parent().next().slideToggle();return false;">GET</a></h3>';
			html+= '<div style="display:none;">';
			html+= getSchemaDownload(path, version, 'GET', 1);
			html+= '<h4>Response</h4>';
			html+= resp.get_response;
			html+= '</div>';
		}

		if (resp.post_request || resp.post_response) {
			html+= '<h3><a href="#" onclick="$(this).parent().next().slideToggle();return false;">POST</a></h3>';
			html+= '<div style="display:none;">';
			if (resp.post_request) {
				html+= getSchemaDownload(path, version, 'POST', 0);
				html+= '<h4>Request</h4>';
				html+= resp.post_request;
			}
			if (resp.post_response) {
				html+= getSchemaDownload(path, version, 'POST', 1);
				html+= '<h4>Response</h4>';
				html+= resp.post_response;
			}
			html+= '</div>';
		}

		if (resp.put_request || resp.put_response) {
			html+= '<h3><a href="#" onclick="$(this).parent().next().slideToggle();return false;">PUT</a></h3>';
			html+= '<div style="display:none;">';
			if (resp.put_request) {
				html+= getSchemaDownload(path, version, 'PUT', 0);
				html+= '<h4>Request</h4>';
				html+= resp.put_request;
			}
			if (resp.put_response) {
				html+= getSchemaDownload(path, version, 'PUT', 1);
				html+= '<h4>Response</h4>';
				html+= resp.put_response;
			}
			html+= '</div>';
		}

		if (resp.delete_request || resp.delete_response) {
			html+= '<h3><a href="#" onclick="$(this).parent().next().slideToggle();return false;">DELETE</a></h3>';
			html+= '<div style="display:none;">';
			if (resp.delete_request) {
				html+= getSchemaDownload(path, version, 'DELETE', 0);
				html+= '<h4>Request</h4>';
				html+= resp.delete_request;
			}
			if (resp.delete_response) {
				html+= getSchemaDownload(path, version, 'DELETE', 1);
				html+= '<h4>Response</h4>';
				html+= resp.delete_response;
			}
			html+= '</div>';
		}
		return html;
	}

	function getToolbar(resp, currentVersion)
	{
		var html = '';
		html+= '<div class="api-toolbar">';

		if (resp.see_others) {
			for (var key in resp.see_others) {
				html+= '<button onclick="goToOther(\'' + resp.see_others[key] + '\')">' + key + '</button>';
			}
		}

		html+= '<select onchange="changeVersion(this)">';
		for (var i = 0; i < resp.versions.length; i++) {
			var version = resp.versions[i];
			if (version.status != 2) {
				html+= '<option value="' + version.version + '" ' + (currentVersion == version.version ? 'selected="selected"' : '') + '>v' + version.version + (version.status == 1 ? ' (Deprecated)' : '') + '</option>';
			}
		}
		
		html+= '</select>';
		html+= '</div>';

		return html;
	}

	function getSchemaDownload(path, version, method, type)
	{
		var html = '<div class="api-schema-download">';
		html+= '<select id="' + path.replace(/\//g, '_') + '-' + version + '-' + method + '-' + type + '"><option value="1">XSD</option><option value="2">JsonSchema</option><option value="3">HTML</option></select>';
		html+= '<a href="#" onclick="downloadSchema(\'' + path + '\',' + version + ',\'' + method + '\',' + type + ');return false">Download</a>';
		html+= '</div>';

		return html;
	}

	function downloadSchema(path, version, method, type)
	{
		var exportType = $('#' + path.replace(/\//g, '_') + '-' + version + '-' + method + '-' + type).val();
		var absoluteUrl = location.pathname + '/' + version + path + '?export=' + exportType + '&method=' + method + '&type=' + type;

		window.open(absoluteUrl, '_blank');
	}

	function goToOther(path)
	{
		window.open(path, '_blank');
	}

	function changeVersion(el)
	{
		var path = $('#api-path').text();
		var version = $(el).val();

		loadController(path, version);
	}

	$(document).ready(function(){

		$('.psx-tool-navigation').find('a:first').trigger('click');

	});
	</script>
</head>
<body>

<div class="psx-tool-navigation">
	<h1>Navigation</h1>
	<?php if(!empty($routings)): ?>
	<ul>
		<?php foreach($routings as $i => $routing): ?>
		<li class="psx-tool-navigation-item <?php echo $i % 2 == 0 ? 'even' : 'odd' ?>">
			<a href="#content" onclick="loadController('<?php echo $routing['path']; ?>',<?php echo $routing['version']; ?>);"><?php echo $routing['path']; ?></a>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>

<div class="psx-tool-content">
	<a name="content"></a>
	<h1>Content</h1>
	<div id="body"></div>
</div>

</body>
</html>
