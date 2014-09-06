<!DOCTYPE>
<html>
<head>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	<style type="text/css">
	<?php include __DIR__ . '/tool.css'; ?>
	.api-version-select
	{
		float:right;
		width:160px;
		padding:4px;
		margin:6px
	}

	.api-info
	{
		padding:12px;
		background-color:#e7f0f7;
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

			var html = '';
			if (resp.versions && resp.versions.length > 0) {
				html+= getVersionSelect(resp.versions);
				for (var i = 0; i < resp.versions.length; i++) {
					var version = resp.versions[i];
					if (version.status != 2) {
						html+= '<div id="api-version-' + version.version + '" class="api-version" ' + (i > 0 ? 'style="display:none"' : '') + '>';
						html+= '<h2>' + (version.status == 1 ? '<s>' + resp.path + '</s>' : resp.path) + ' (v' + version.version + ')</h2>';
						html+= '<div class="api-version-view">' + getApiBody(version.view) + '</div>';
						html+= '</div>';
					}
				}
			} else {
				html+= '<div class="api-info">No API documentation available</div>';
			}

			$('#body').html(html);
			$('#body .api-version-view').find('div:first').slideToggle();

		});
	}

	function getApiBody(resp)
	{
		var html = '';
		if (resp.get_response) {
			html+= '<h3><a href="#" onclick="$(this).parent().next().slideToggle();return false;">GET</a></h3>';
			html+= '<div style="display:none;">';
			html+= '<h4>Response</h4>';
			html+= resp.get_response;
			html+= '</div>';
		}

		if (resp.post_request || resp.post_response) {
			html+= '<h3><a href="#" onclick="$(this).parent().next().slideToggle();return false;">POST</a></h3>';
			html+= '<div style="display:none;">';
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
			html+= '<h3><a href="#" onclick="$(this).parent().next().slideToggle();return false;">PUT</a></h3>';
			html+= '<div style="display:none;">';
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
			html+= '<h3><a href="#" onclick="$(this).parent().next().slideToggle();return false;">DELETE</a></h3>';
			html+= '<div style="display:none;">';
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
		return html;
	}

	function getVersionSelect(versions)
	{
		var html = '';
		html+= '<select class="api-version-select" onchange="changeVersion(this)">';

		for (var i = 0; i < versions.length; i++) {
			var version = versions[i];
			if (version.status != 2) {
				html+= '<option value="' + version.version + '">v' + version.version + (version.status == 1 ? ' (Deprecated)' : '') + '</option>';
			}
		}
		
		html+= '</select>';

		return html;
	}

	function changeVersion(el)
	{
		$('.api-version').fadeOut(0);
		$('#api-version-' + $(el).val()).fadeIn(0);
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
		<li class="<?php echo $i % 2 == 0 ? 'even' : 'odd' ?>">
			<a href="#content" onclick="loadController('<?php echo $routing['path']; ?>');"><?php echo $routing['path']; ?></a>
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
