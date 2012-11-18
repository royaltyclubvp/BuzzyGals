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
	
	//Show Comments on Article
	$('div.count').click(function() {
		var commentCount = '';
		commentCount = $(this).attr('data-count');
		$(this).text(commentCount+" comments");
		$(this).parent().find('.inner-comment-container').show('fast', function() {
			$(this).parent().find('div.count').off('click');
		});
	});
	
	//Show Full Photo Gallery
	$('span.more_photos').click(function() {
		$(this).parent().find('.main_gallery').show('fast', function() {
			$(this).parent().find('span.more_photos').addClass('option_less').text('hide photos...');
			$(this).parent().find('span.more_photos.option_less').on("click.HidePhotos", function() {
				$(this).parent().find('.main_gallery').hide('fast', function() {
					$(this).parent().find('span.more_photos').removeClass('option_less').text('see all...').off("click.HidePhotos");
				});
			});
		});
	});
	
	//Show Full User Story
	$('.story_body span.option_more').click(function() {
		$(this).parent().find('.main_story_body').show('fast', function() {
			$(this).parent().find('span.option_more').addClass('option_less').text('Hide Story');
			$(this).parent().find('span.option_more.option_less').on("click.HideStory", function() {
				$(this).parent().find('.main_story_body').hide('fast', function() {
					$(this).parent().find('span.option_more').removeClass('option_less').text('Show All').off("click.HideStory");
				});
			});
		});
	});


});





