/* Author: Jarrod Placide-Raymond

*/
/*Hide & Show Login Menu */
$(document).ready(function(){
	$('#single #login_option_button').click(function() {
		$('#login_option').hide(600, function() {
			$('#login').show(200);
		});
	});
	
	$('time.date').timeago();

});





