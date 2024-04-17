function notice(data, type)
{
	if(data != null)
	{
		var noticeId = Math.floor(Math.random() * (1 - 999999)) + 1;
		$('#notice-list').append('<div class="item ' + type + ' notice' + noticeId + '"> <div class="desc">' + data + '</div> </div>');
		setTimeout(() => {
			$('.notice' + noticeId).fadeOut(500, () => {
				$('.notice' + noticeId).remove();
			});
		}, 3000);
	}

  $('#notice-list .item').each(function(){
    $(this).on('click', '', function(){
      $(this).fadeOut(500, () => $(this).remove());
    });
  });

	return false;
}

function redirect(url, seconds)
{
	var red_seconds = seconds*1000;
	setTimeout(() => {
		return document.location.href = url;
	}, red_seconds);
}

$(document).ready(() => {

	window.onerror = null;
	var History = window.History;

	History.Adapter.bind(window, 'statechange', function()
	{
		var State = History.getState();
		loadPage(State.data.path);
		History.log(State);
	});

	$(document).on('click','a, button, div, b, i, li', function(e)
	{
		if($(this).attr('href') && $(this).attr('href')[0] == '/' && $(this).attr('href')[1] != '/')
    {
      e.preventDefault();
			var urlpath = $(this).attr('href');
			History.pushState({path: urlpath}, tags.title, urlpath);
		}

	});

	var loadpage = false;
	function loadPage(page)
	{
		if(!loadpage)
		{
			loadpage = true;
			$.ajax({
				type: 'GET',
				url: page,
				success: (resp) => {
					resp = JSON.parse(resp);
					$('#app').html(resp.app);
				  loadpage = false;
				}
			});
		}
		return false;
	}

});

var stop_request = false;
function sendRequest(form = null, uri)
{
	if(!stop_request)
	{
		stop_request = true;
		var formData = form != null ? new FormData($('#' + form)[0]) : '';
		$.ajax({
			type: 'POST',
			url: uri,
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: (resp) => {

				if(resp[0] == '{') {
					
					var parse = JSON.parse(resp);
					switch(parse.response)
					{
						case 'alert': notice(parse.data, parse.type); break;

						case 'location': 
							notice(parse.data, parse.type); 
							redirect(parse.uri, 1);
							break;
								
						default: notice('Error response!', 'error');
					}

				} else if(resp === '') {

						notice('Ошибочка;(', 'error');

				} else console.log('Error:', resp);
				stop_request = false;

			}
		});
	}
	return false;
}