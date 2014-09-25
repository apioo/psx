<!DOCTYPE>
<html>
<head>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.2/styles/github.min.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ace.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/mode-text.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/theme-eclipse.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.2/highlight.min.js"></script>
	<style type="text/css">
	<?php include __DIR__ . '/tool.css'; ?>

	#rest-response-header
	{
		white-space:pre;
		font-family:monospace;
		padding:8px;
		border-bottom:1px solid #aaa;
	}

	#rest-response
	{
		white-space:pre;
		font-family:monospace;
		margin:0px;
	}
	</style>
	<script type="text/javascript">
	function loadRoutes(){

		$('#available-paths option').remove();

		$.get('<?php echo $router->getAbsolutePath('PSX\Controller\Tool\RoutingController'); ?>', function(resp){

			if (resp.routings) {
				for (var i = 0; i < resp.routings.length; i++) {
					$('#available-paths').append('<option value="<?php echo rtrim($url, '/'); ?>' + resp.routings[i].path + '" />');
				}
			}

		});

	}

	function sendRequest(){

		var method = $('#request-method').val();
		var path = $('#request-path').val();
		var body = method != 'GET' ? ace.edit("body").getSession().getValue() : undefined;

		$.ajax(path, {
			type: method,
			data: body,
			headers: undefined,
			processData: false,
			error: function(xhr, status, err){
				var statusLine = '<b>' + xhr.status + ' ' + xhr.statusText + '</b>';
				$('#rest-response-header').html(statusLine + "\n" + xhr.getAllResponseHeaders());
				$('#rest-response').html(document.createTextNode(xhr.responseText));
				$('#rest-response').each(function(i, el){
					hljs.highlightBlock(el);
				});
			},
			success: function(data, status, xhr){
				var statusLine = '<b>' + xhr.status + ' ' + xhr.statusText + '</b>';
				$('#rest-response-header').html(statusLine + "\n" + xhr.getAllResponseHeaders());
				$('#rest-response').html(document.createTextNode(xhr.responseText));
				$('#rest-response').each(function(i, el){
					hljs.highlightBlock(el);
				});
			}
		});

	}

	$(document).ready(function(){

		var editor = ace.edit("body");
		editor.setTheme("ace/theme/eclipse");
		editor.getSession().setMode("ace/mode/text");

		loadRoutes();

	});
	</script>
</head>
<body>

<div class="psx-tool-content">
	<h1>Request</h1>
	<div id="rest-request" style="width:95%;margin:8px">
		
		<form class="form-inline" role="form">
			<div class="form-group">
				<label class="sr-only" for="request-method">Method</label>
				<select class="form-control" id="request-method">
					<option>GET</option>
					<option>POST</option>
					<option>PUT</option>
					<option>DELETE</option>
				</select>
			</div>

			<div class="form-group">
				<label class="sr-only" for="request-path">Path</label>
				<input type="text" class="form-control" list="available-paths" id="request-path" style="width:429px" />
				<datalist id="available-paths">
				</datalist>
			</div>

			<button type="button" onclick="sendRequest();return false;" class="btn btn-default">Send</button>

			<div id="body" style="width:600px;height:200px;margin-top:8px;border:1px solid #999;"></div>

		</form>

	</div>

	<h1>Response</h1>
	<div id="rest-response-header"></div>
	<div id="rest-response"></div>
</div>

</body>
</html>
