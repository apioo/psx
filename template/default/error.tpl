<!DOCTYPE html>
<html>
<head>
	<title>Exception</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
</head>
<body>
	<h1>Internal Server Error</h1>
	<p><?php echo $message; ?></p>
	<?php if(!empty($trace)): ?>
		<p><pre><?php echo $trace; ?></pre></p>
	<?php endif; ?>
</body>
</html>
