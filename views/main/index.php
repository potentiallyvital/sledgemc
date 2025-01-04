<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-heading">
				Login
			</div>
			<div class="panel-body">
				<form action="<?=BASE_URL;?>/login" method="post">
					<?=textbox('sledge_login_email', ['label'=>'Email']);?>
					<?=textbox('sledge_login_keyword', ['label'=>'Password','type'=>'password']);?>
					<?=button('login');?>
				</form>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-heading">
				Sign Up
			</div>
			<div class="panel-body">
				<form action="<?=BASE_URL;?>/register" method="post">
					<?=textbox('sledge_register_email', ['label'=>'Email']);?>
					<?=textbox('sledge_register_username', ['label'=>'Username']);?>
					<?=textbox('sledge_register_keyword', ['label'=>'Password','type'=>'password']);?>
					<?=textbox('sledge_register_confirm', ['label'=>'Confirm Password','type'=>'password']);?>
					<?=button('register');?>
				</form>
			</div>
		</div>
	</div>
</div>
