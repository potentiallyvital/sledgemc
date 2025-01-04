<div class="panel panel-error">
	<div class="panel-heading">
		<h3>Type: <span class="text-orange"><?=$type;?></span></h3>
	</div>
	<div class="panel-body">
		<div class="text-green">
			<?php
			switch (strtolower($type))
			{
				case 'string':
					echo '"<span style="white-space:pre;">'.trim(htmlspecialchars($what)).'</span>"';
					echo '<hr />';
					break;

				default:
					$what = trim(print_r($what, true));
					$what = str_replace(' ','&nbsp;',$what);
					$what = str_replace(PHP_EOL, '<br>', $what);
					$what = str_replace('[', '[<b class="text-orange copy-text">', $what);
					$what = str_replace(']', '</b>]', $what);
					echo '<pre>'.$what.'</pre>';
					echo '<hr />';
					break;
			}
			?>
		</div>
		<?=$caller;?>
		<hr />
		<ul>
			<li><?=implode('</li><li>', $backtrace);?></li>
		</ul>
	</div>
</div>
<script>
$(document).ready(function()
{
	setTimeout(function()
	{
		$('#bg-hide').stop().remove();
	}, 1000);
});
</script>
