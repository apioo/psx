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

	#rest-header
	{
		width:100%;
		margin-top:4px;
	}

	#rest-header-heading td
	{
		background-color:#eee;
		color:#222;
		border-bottom:1px solid #222;
		font-size:0.7em;
	}

	#rest-header td
	{
		padding:4px 0px;
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

		var headers = {};
		$('#rest-header tbody tr').each(function(){

			var key = $(this).find('.key').val();
			var value = $(this).find('.value').val();

			if (key && value) {
				headers[key] = value;
			}

		});

		var method = $('#request-method').val();
		var path = $('#request-path').val();
		var body = method != 'GET' ? ace.edit("body").getSession().getValue() : undefined;

		$.ajax(path, {
			type: method,
			data: body,
			headers: headers,
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

	function addHeader(){

		var html = '';
		html+= '<tr>';
		html+= '<td><input type="text" class="form-control key" list="available-header" style="width:246px" /></td>';
		html+= '<td><input type="text" class="form-control value" style="width:100%" /></td>';
		html+= '<td><button type="button" onclick="removeHeader(this);return false;" class="btn btn-default" style="margin-left:6px;">&times;</button></td>';
		html+= '</tr>';
		$('#rest-header tbody').append(html);

	}

	function removeHeader(el){

		$(el).parent().parent().fadeOut(20);

	}

	$(document).ready(function(){

		var editor = ace.edit("body");
		editor.setTheme("ace/theme/eclipse");
		editor.getSession().setMode("ace/mode/text");

		loadRoutes();

		addHeader();
		addHeader();

	});
	</script>
</head>
<body>

<div class="psx-tool-content">
	<h1>Request</h1>
	<div id="rest-request" style="margin:8px">
		
		<form role="form" onsubmit="sendRequest();return false;">

			<table style="width:100%">
			<colgroup>
				<col width="100" />
				<col width="*" />
				<col width="20" />
				<col width="20" />
			</colgroup>
			<tr>
				<td>
					<select class="form-control" id="request-method" style="width:94px;">
						<option>GET</option>
						<option>POST</option>
						<option>PUT</option>
						<option>DELETE</option>
					</select>
				</td>
				<td>
					<input type="text" class="form-control" list="available-paths" id="request-path" style="width:100%;" />
				</td>
				<td>
					<button type="button" onclick="sendRequest();return false;" class="btn btn-default" style="margin-left:6px;">Send</button>
				</td>
				<td>
					<button type="button" onclick="addHeader();return false;" class="btn btn-default" style="margin-left:6px;">+</button>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<table id="rest-header">
					<colgroup>
						<col width="250" />
						<col width="*" />
						<col width="20" />
					</colgroup>
					<tbody>
					</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<div id="body" style="width:100%;height:200px;margin-top:8px;border:1px solid #999;"></div>
				</td>
			</tr>
			</table>

			<datalist id="available-paths">
			</datalist>

			<datalist id="available-header">
				<option value="Accept" />
				<option value="Accept-Charset" />
				<option value="Accept-Encoding" />
				<option value="Accept-Language" />
				<option value="Accept-Ranges" />
				<option value="Age" />
				<option value="Allow" />
				<option value="Authorization" />
				<option value="Cache-Control" />
				<option value="Cookie" />
				<option value="Content-MD5" />
				<option value="Content-Type" />
				<option value="Date" />
				<option value="Expect" />
				<option value="From" />
				<option value="If-Match" />
				<option value="If-Modified-Since" />
				<option value="If-None-Match" />
				<option value="If-Range" />
				<option value="If-Unmodified-Since" />
				<option value="Pragma" />
				<option value="Referer" />
				<option value="User-Agent" />
				<option value="Via" />
			</datalist>
		</form>

	</div>

	<h1>Response</h1>
	<div id="rest-response-header"></div>
	<div id="rest-response"></div>
</div>

</body>
</html>
