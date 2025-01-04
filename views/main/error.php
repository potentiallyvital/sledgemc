<div class="panel panel-error">
	<div class="panel-heading">
		<h1 class="text-red"><?=(!empty($error_type) ? $error_type : 'ERROR');?></h1>
	</div>
	<div class="panel-body">
		<?php
		if (allow_dump())
		{
			if (empty($error_info))
			{
				dump('no error info available', false);
			}
			else
			{
				?>
				<h3 class="text-orange"><?=$error_info['message'];?></h3>
				<hr />
				<ul><li><span class="copy-text"><?=$error_info['file'];?></span> (<?=$error_info['line'];?>)</li></ul>
				<?php
				if (!empty($error_info['backtrace']))
				{
					?>
					<hr />
					<ul>
						<li><?=implode('</li><li>', $error_info['backtrace']);?></li>
					</ul>
					<?php
				}
			}
		}
		else
		{
			?>
			Something went wrong, please try again or contact tech support
			<?php
		}
		?>
	</div>
</div>
