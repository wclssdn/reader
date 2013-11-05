<!DOCTYPE html>
<html>
<head>
<title><?php S($title)?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="<?php echo URL_STATIC?>css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="<?php echo URL_STATIC?>css/public/main.css" rel="stylesheet" media="screen">
</head>
<body>
	<div class="navbar">
		<div class="container">
			<a class="navbar-brand" href="<?php echo URL_ROOT?>">一个PHP框架</a>
			<ul class="nav navbar-nav">
				<li class="col-4 col-sm-4 active"><a href="#">Home</a></li>
				<li class="col-4 col-sm-4"><a href="#">Link</a></li>
				<li class="col-4 col-sm-4"><a href="#">Link</a></li>
			</ul>

		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="text-danger text-center">系统繁忙<?php if ($message) echo ': ', $message;?></div>
			</div>
		</div>
	</div>
	<footer>
		<div class="row">
			<div class="col-lg-12 text-center">copy right wclssdn</div>
		</div>
	</footer>
	<script src="<?php echo URL_STATIC?>js/jquery.min.js"></script>
	<script src="<?php echo URL_STATIC?>js/bootstrap.min.js"></script>
</body>
</html>