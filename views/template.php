<!DOCTYPE html>
<!--






<?php
include SLEDGEMC_PATH.'/views/main/troll.txt';
?>






-->
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
                <?php
		// load special js for this controller/page
                if ($this->data['controller'] != 'main' && file_exists(SLEDGEMC_PATH.'/js/'.$this->data['controller'].'.js'))
                {
                        ?>
                        <script src="<?=BASE_URL;?>/js/<?=$this->data['controller'];?>.js"></script>
                        <?php
                }
                ?>
                <link rel="stylesheet" href="<?=BASE_URL;?>/css/resources.css" />
                <link rel="stylesheet" href="<?=BASE_URL;?>/css/main.css" />
                <?php
		// load special css for this controller/page
                if ($this->data['controller'] != 'main' && file_exists(SLEDGEMC_PATH.'/css/'.$this->data['controller'].'.css'))
                {
                        ?>
                        <link rel="stylesheet" href="<?=BASE_URL;?>/css/<?=$this->data['controller'];?>.css" />
                        <?php
                }
                ?>
        </head>
        <body>
                <div id="bg-hide" style="position:fixed;top:0;bottom:0;left:0;right:0;z-index:2;">
                </div>
                <div id="one-hundred-vh">
                        <div id="header">
				[header]
                        </div>
                        <div id="main-content" class="container-wide grid show-left-nav">
                                <div id="main-page" class="row">
                                        <div id="left-box" class="col-md-6 col-lg-4 col-xl-3 pull-left">
                                                <div id="left-box-contents">
							[left box]
                                                </div>
                                        </div>
                                        <div id="middle-box" class="col-md-12 col-lg-8 col-xl-6">
                                                <div class="page-content">
							[page content]
							<?=$innards;?>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
                <div id="footer">
			[footer]
                </div>
        </body>
</html>
