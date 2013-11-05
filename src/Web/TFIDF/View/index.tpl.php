<?php use BootsPHP\Util\TemplateHelper as H;?>
<!DOCTYPE html>
<html>
<head>
<title><?php H::S($title)?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="<?php echo URL_STATIC?>css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="<?php echo URL_STATIC?>css/bootstrap-glyphicons.css" rel="stylesheet" media="screen">
<link href="<?php echo URL_STATIC?>css/public/main.css" rel="stylesheet" media="screen">
<script src="<?php echo URL_STATIC?>js/jquery.min.js"></script>
<script src="<?php echo URL_STATIC?>js/bootstrap.min.js"></script>
</head>
<body>
	<form method="post" action="/Home/learn" class="form-horizontal" role="form">
		<div class="form-group">
			<label for="title" class="col-lg-2 control-label">标题</label>
			<div class="col-lg-3">
				<input type="text" name="title" class="form-control" id="title" placeholder="标题" value="暂无用">
			</div>
		</div>
		<div class="form-group">
			<label for="inputPassword1" class="col-lg-2 control-label">正文</label>
			<div class="col-lg-3">
				<textarea name="content" placeholder="正文" class="form-control" rows="5"></textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-offset-2 col-lg-10">
				<button type="submit" class="btn btn-default">学习</button>
			</div>
		</div>
	</form>

</body>
</html>