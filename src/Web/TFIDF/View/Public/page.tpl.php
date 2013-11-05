
<div class="pagination pull-right">
	<ul>
	<?php if ($pager->isFirstPage()):?>
		<li class="disabled"><a href="#">首页</a></li>
	<?php else:?>
		<li><a href="<?php echo $pager->getFirstPage()?>">首页</a></li>
	<?php endif;?>
	<?php foreach($pager->getPages() as $p):?>
		<?php if ($pager->isCurrentPage($p['page'])):?>
		<li class="active"><a href="#"><?php echo $p['page']?></a></li>
		<?php else:?>
		<li><a href="<?php echo $p['url']?>"><?php echo $p['page']?></a></li>
		<?php endif;?>
	<?php endforeach;?>
	<?php if ($pager->isLastPage()):?>
		<li class="disabled"><a href="#">尾页</a></li>
	<?php else:?>
		<li><a href="<?php echo $pager->getLastPage()?>">尾页</a></li>
	<?php endif;?>
	</ul>
</div>
