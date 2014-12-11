<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>
	<body>

		<h1>Clients</h1>

		<?php foreach($clients as $client): ?>
			<div>
				<img src="<?= Sys::$b ?>images/icon.jpg">
				<?= $client->name ?> : $&nbsp;<?= $client->fortune ?> / €&nbsp;<?= convertToEuros($client->fortune) ?>
			</div>
		<?php endforeach; ?>

	</body>
</html>


