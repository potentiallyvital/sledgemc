<?php
$flash = [];

foreach ($this->session->data['flash'] as $message)
{
	$item = new Flash();
	$item->setBody($message);

	$flash[] = $item;
}

if ($this->account)
{
	foreach ($this->account->getChildren('Flash') as $message)
	{
		$flash[] = $message;
	}
}

if ($flash)
{
	?>
	<div class="modal" style="display:block;">
		<div class="modal-dialog">
			<i class="fa fa-trash-can modal-close float-right"></i>
			<ul class="flash-messages">
				<?php
				foreach ($flash as $item)
				{
					echo '<li>'.$item->body.'</li>';

					$item->delete();
				}
				?>
			</ul>
		</div>
	</div>
	<?php

	$this->session->data['flash'] = [];
}
