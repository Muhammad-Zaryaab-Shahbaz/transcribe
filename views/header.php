<html class="no-js" lang="en-US">

<head>
	<!-- META TAGS -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta charset="UTF-8" />
	<meta name="author" content="Muhammad Zaryaab Shahbaz">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="description" content="">

	<!-- LINK TAGS -->
	<link rel="icon" type="image/gif" sizes="128x128" href="./assets/favicon.gif">
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

	<!-- INCLUDE FILES -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js" integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="./assets/css/style.css" type="text/css" media="all" />

	<script src="https://kit.fontawesome.com/02dca0d8e3.js" crossorigin="anonymous"></script>
	<title>Transcribe</title>
</head>

<?php $navigation = array(
	array("title" => "home page", "icon" => "homepage", "url" => HOME_URL),
	array("title" => "message", "icon" => "message", "url" => ROUTE_HOME),
	array("title" => "mail", "icon" => "mail", "url" => "mailto:" . EMAIL),
	array("title" => "admin", "icon" => "admin", "url" => "admin")
); ?>

<body>
	<div class="container-fluid">
		<div class="row align-items-center top-bar">
			<div class="d-flex justify-content-between align-items-center w-100">
				<img class="logo" src="./assets/img/bird.png">
				<div class="d-flex">
					<?php foreach ($navigation as $item) { ?>
						<a href="<?= $item["url"] ?>">
							<img class="p-1" src="./assets/img/<?= $item["icon"] ?>.png">
						</a>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>