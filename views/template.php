<html>
	<head>
		<title><?=$title;?></title>
		<meta http-equiv="Content-Type" content="text/html,charset=utf-8" />
		<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0"> 
		<script type='text/javascript'>
			var BASE_URL = '<?=BASE_URL;?>';
			var AJAX_URL = '<?=BASE_URL;?>/ajax';
		</script>
		<script src="<?=BASE_URL;?>/js/resources.js"></script>
		<script src="<?=BASE_URL;?>/js/main.js"></script>
		<link rel="stylesheet" href="<?=BASE_URL;?>/css/resources.css" />
		<link rel="stylesheet" href="<?=BASE_URL;?>/css/main.css" />
		<?php
		if ($this->data['controller'] != 'main' && file_exists(SLEDGEMC_PATH.'/js/'.$this->data['controller'].'.js'))
		{
			?>
			<script src="<?=BASE_URL;?>/js/<?=$this->data['controller'];?>.js"></script>
			<?php
		}
		?>
		<?php
		if ($this->data['controller'] != 'main' && file_exists(SLEDGEMC_PATH.'/css/'.$this->data['controller'].'.css'))
		{
			?>
			<link rel="stylesheet" href="<?=BASE_URL;?>/css/<?=$this->data['controller'];?>.css" />
			<?php
		}
		?>
	</head>
	<body class="<?=$this->data['controller'];?>">
		<div class="container-wide grid">
			<?php include SLEDGEMC_PATH.'/views/main/flash.php'; ?>
			<div id="header">
				<?=SLEDGEMC_APP;?>
			</div>
			<div id="left">
				<?php
				if ($this->account)
				{
					include SLEDGEMC_PATH.'/views/main/nav_user.php';
				}
				else
				{
					include SLEDGEMC_PATH.'/views/main/nav_session.php';
				}
				?>
			</div>
			<div id="body">
				<div id="page">
					<?=$innards;?>
				</div>
			</div>
			<div id="footer">
				Copyright &copy; <?=date('Y');?> <?=SLEDGEMC_APP;?>.<br />
				All rights reserved.
			</div>
		</div>
	</body>
</html>
