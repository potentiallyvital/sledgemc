doInputs();
doPanels();
doModals();

$(document).ready(function()
{

});

function doInputs()
{
	$(document).ready(function()
	{
		$('input').attr('autocomplete', 'off');
	});
	$(document).on('focus', 'input', function()
	{
		$(this).attr('autocomplete', 'off');
	});
}

function doPanels()
{
	$(document).on('click', '.panel-heading', function()
	{
		$(this).parents('.panel:first').children('.panel-body').slideToggle(100);
	});
}

function doModals()
{
	$(document).on('click', '.modal-close', function()
	{
		$(this).parents('.modal-dialog').addClass('closing');
		$(this).parents('.modal-dialog').fadeOut(100);
		setTimeout(function()
		{
			$('.modal-dialog.closing').remove();
			if ($('.modal-dialog').length == 0)
			{
				$('.modal').fadeOut(100);
			}
		}, 250);
	});
}
