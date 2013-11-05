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
	<div class="container">
		<div class="alert alert-success">学习成功 <a href="/" class="btn btn-default">返回</a></div>
		<table class="table table-bordered table-hover table-condensed">
			<thead>
				<tr>
					<th>词</th>
					<th>TF-IDF</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
		<?php foreach ($result as $word => $tfidf):?>
			<tr>
				<td><?php H::S($word)?></td>
				<td><?php H::S($tfidf)?></td>
				<td><a href="/Home/ban?word=<?php H::S($word)?>" class="ajax">屏蔽</a></td>
			</tr>
		<?php endforeach;?>
		</tbody>
		</table>
	</div>
	<script>
$('.ajax').click(function(){
	var t = $(this);
	$.getJSON(t.prop('href'), function(data){
		if (data.code == 0){
			t.parents('tr').remove();
		}else{
			alert(data.message);
		}
	});
	return false;
});

	</script>
</body>
</html>