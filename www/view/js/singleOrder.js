$(function() {
/*
	var paramsArray = window.location.search.substring(1).split('&');
	var event_id;
	for( var i = 0; i < paramsArray.length; i++ ) {
		params = paramsArray[i].split('=');
		if (params[0] == 'event_id') {
			event_id = params[1];
		}
		console.log('event_id: ' + event_id);
	}
*/

	$.ajax( {
		url: '../logic/htdocs/getProduct.php',
		dataType: 'json',
		success:function(data) {
console.log('ajax success');



/*
			var event_name = '';
			var html = '<tr><th width="30%"><font size="3">Rank</font></th><th width="40%"><font size="3">User</font></th><th width="30%"><font size="3">Score</font></th></tr>';
			for ( var i = 0; i < data.length; i++ ) {
				if ( data[i]["event_name"] ){
					event_name = data[i]["event_name"];
				} else {
					var user_id = data[i]["user_id"];
					var user_name = data[i]["user_name"];
					var user_score = data[i]["user_score"];
					var user_rank = i + 1;
					html += '<tr style="font-size: 2; text-align: center;"><td>'
									+ user_rank
									+ '位</td><td>'
									+ user_name
									+ '</td><td>'
									+ user_score
									+ '</td></tr>';
					// console.log('user id: ' + user_id + ', user name: ' + user_name);
				}
			}
			$('#eventName').append(event_name);
			$('#userRankingTable').append(html);
*/
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
console.log('ajax error');
                    $("#XMLHttpRequest").html("XMLHttpRequest : " + XMLHttpRequest.status);
                    $("#textStatus").html("textStatus : " + textStatus);
                    $("#errorThrown").html("errorThrown : " + errorThrown.message);
		}
	} );
});
