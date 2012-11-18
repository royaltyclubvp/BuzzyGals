/* Author: Jarrod Placide-Raymond

*/
//Data Structures
function Profile(data) {
	this.about = ko.observable(data.about);
	this.interests = ko.observable(data.interests);
}
function Townhall(data) {
	this.id = data.id;
	this.title = data.Topic.name;
	this.url = townhallBaseUrl + data.Topic.name.toLowerCase();
}

function Bookmark(data) {
	this.id = data.id;
	this.title = data.title
	this.date = data.date;
	this.topic = data.Topic.name;
	this.url = articleBaseUrl + data.uri;
}

function Friend(data) {
	this.id = data.id;
	this.userid = data.Friend.id;
	this.displayName = data.Friend.Profile.displayName;
	this.url = friendProfileBaseUrl + data.Friend.Profile.displayName.toLowerCase();
	this.photo = profileImagesUrl + data.Friend.Profile.photo;
	this.location = "";
	if (data.Friend.Profile.Location.city != '')
		this.location = this.location + data.Friend.Profile.Location.city + ", ";
	if (data.Friend.Profile.Location.stateprov != '')
		this.location = this.location + data.Friend.Profile.Location.stateprov + ", ";
	if (data.Friend.Profile.Location.country != '')
		this.location = this.location + data.Friend.Profile.Location.country;
}

function Comment(data) {
	this.id = data.id;
	this.date = ISODateString(new Date(data.date));
	this.userPhoto = profileImagesUrl + data.User.Profile.photo;
	this.displayName = data.User.Profile.displayName;
	this.content = data.content;
}

function Story(data) {
	var self = this;
	self.id = data.id;
	self.date = ISODateString(new Date(data.date));
	self.contentPreview = data.content.substring(0, 50);
	self.contentFull = data.content.substring(50);
	self.fullStoryVisible = ko.observable(0);
	self.photocount = data.Media.totalphotos;
	if (self.photocount != 0) {
		self.galleryAvailable = true;
		self.previewPhotos = new Array();
		if (self.photocount < 3)
			var len = self.photocount;
		else
			var len = 3;
		for (var i = 0; i < len; i++)
			self.previewPhotos[i] = data.Media.photos[i];
	}
	else {
		self.galleryAvailable = false;
	}
	self.mainPhotos = new Array();
	if (self.photocount > 3) {
		var len = self.photocount;
		for (var i = 3; i < len; i++) {
			self.mainPhotos[i] = data.Media.photos[i]
		}
	}
	self.galleryVisible = ko.observable(0);
	self.commentCount = data.Comments.length;
	self.commentText = (self.commentComment == 1) ? 'Comment' : 'Comments';
	self.comments = ko.observableArray([]);
	for(var i=0; i < self.commentCount; i++) {
		self.comments.push(new Comment(data.Comments[i]));
	}
	self.addComment = function(comment) {
		result = ProfileVM.addComment(comment);
		if(result) //Modify to check type
			self.comments.push(comment);
	}
	self.removeComment = function(comment) {
		result = ProfileVM.removeComment(comment);
		if(result)
			self.comments.remove(comment);
	}
	self.commentsVisible = ko.observable(0);
	self.toggleGallery = function() {
		(self.galleryVisible()) ? self.galleryVisible(0) : self.galleryVisible(1);
	}
	self.toggleFullStory = function() {
		(self.fullStoryVisible()) ? self.fullStoryVisible(0) : self.fullStoryVisible(1);
	}
	self.showComments = function() {
		self.commentsVisible(1);
	}
}

function ResourceType(data) {
	var self = this;
	self.typeid = data.id;
	self.name = data.type;
	self.resources = ko.observableArray(data.Resources);
	self.deleteResource = function(resource) {
		result = ProfileVM.deleteResource(resource);
		if(result)
			self.resources.remove(resource);
	}
}

//View Model
ProfileVM = new (function() {
	//Data
	var self = this;
	
	self.pages = ['About Me','My Townhalls','Friends','My Stories','My Resources'];
	self.currentPage = ko.observable();
	
	//Page Data
	self.profile = ko.observable();
	self.townhalls = ko.observableArray([]);
	self.bookmarkedArticles = ko.observableArray([]);
	self.friends = ko.observableArray([]);
	self.stories = ko.observableArray([]);
	self.resourceTypes = ko.observableArray([]);
	self.newStory = ko.observable();
	
	//Edit Toggles
	self.aboutEdit = ko.observable(false);
	self.interestsEdit = ko.observable(false);

	//Behaviours
	self.goToPage = function(page) {
		page = page.replace(/\s/g, ""); 
		location.hash = page; 
	};
	self.removeBookmark = function(bookmark) {
		$.ajax({
			url : "/profile/removearticlebookmark",
			data : {
				article : bookmark.id
			},
			type : "POST",
			dataType : "text",
			success : function(result) {
				if (result == "1")
					self.bookmarkedArticles.remove(bookmark);
				else
					return;
				//Modify for Error Message
			}
		});
	}
	self.deleteResource = function(resource) {
		success = false;
		$.ajax({
			url : "/profile/removeresourcebookmark",
			async: false,
			data : {
				resource : resource.id
			},
			type : "POST",
			dataType: "text",
			success: function(result) {
				if(result=="1")
					success = true;
			}
		});
		return success;
	}
	self.showAboutEdit = function() {
		self.aboutEdit(true);
		$('.profile_edit.expanding').expandingTextarea();
	}
	self.showInterestsEdit = function() {
		self.interestsEdit(true);
		$('.profile_edit.expanding').expandingTextarea();
	}
	self.saveAbout = function() {
		$.ajax({
			url : "/profile/editprofile",
			data : {
				about : self.profile().about
			},
			type : "POST",
			dataType : "text",
			success : function(result) {
				if (result == "1")
					self.aboutEdit(false);
			}
		});
	}
	self.saveInterests = function() {
		$.ajax({
			url : "/profile/editprofile",
			data : {
				interests : self.profile().interests
			},
			type : "POST",
			dataType : "text",
			success : function(result) {
				if (result == "1")
					self.interestsEdit(false);
			}
		});
	}
	
	//Client-Side Routes
	Sammy(function() {
		//Page Routes
		this.get('#:page', function() {
			self.currentPage(this.params.page);
			if(this.params.page == "AboutMe") {
				$.getJSON("/profile/loadabout", function(allData) {
					self.profile(new Profile(allData));
				});
			}
			else if(this.params.page == "MyTownhalls") {
				$.getJSON("/profile/loadtownhalls", function(allData) {
					var mappedTownhalls = $.map(allData.townhalls, function(townhall) {
						return new Townhall(townhall);
					});
					var mappedBookmarks = $.map(allData.articles, function(bookmark) {
						return new Bookmark(bookmark);
					});
					self.townhalls(mappedTownhalls);
					self.bookmarkedArticles(mappedBookmarks);
				});
			}
			else if(this.params.page == "Friends") {
				$.getJSON("/profile/loadfriends", function(allData) {
					var mappedFriends = $.map(allData.root, function(friend) {
						return new Friend(friend);
					});
					self.friends(mappedFriends);
				});
			}
			else if(this.params.page == "MyStories") {
				$.getJSON("/profile/loadstories", function(allData) {
					var mappedStories = $.map(allData.root, function(story) {
						return new Story(story);
					});
					self.stories(mappedStories);
				});
			}
			else if(this.params.page == "MyResources") {
				$.getJSON("/profile/loadresources", function(allData) {
					var mappedResourceTypes = $.map(allData.root, function(resourceType) {
						return new ResourceType(resourceType);
					});
					self.resourceTypes(mappedResourceTypes);
				});
			}
		});
	}).run();	
	
	//DOM Processing
	self.storiesDOMProcess = function() {
		//Show Comments on Story
		$('.story div.count').on("click.showComments", function() {
			$(this).parent().find('.inner-comment-container').show('fast', function() {
				$(this).parent().find('div.count').off('click');
			}) ;
		});
		//Show Full Story Gallery
		$('span.more_photos').on("click.showGallery", function() {
			$(this).parent().find('.main_gallery').show('fast', function() {
				$(this).parent().find('span.more_photos').addClass('option_less').text('Hide Full Gallery');
				$(this).parent().find('span.more_photos.option_less').on("click.hideGallery", function() {
					$(this).parent().find('.main_gallery').hide('fast', function() {
						$(this).parent().find('span.more_photos').removeClass('option_less').text('Show Full Gallery').off("click.hideGallery");
					});
				});
			});
		});
		//Show Full User Story
		$('.story_body span.option_more').on("click.showStory", function() {
			$(this).parent().find('.main_story_body').show('fast', function() {
				$(this).parent().find('span.option_more').addClass('option_less').text('Hide Story');
				$(this).parent().find('span.option_more.option_less').on("click.hideStory", function() {
					$(this).parent().find('.main_story_body').hide('fast', function() {
						$(this).parent().find('span.option_more').removeClass('option_less').text('Show All').off("click.hideStory");
					});
				});
			});
		});
		//Run Timeago Plugin
		$('.story time.date').timeago();
		
	};
	
	//Load Default Page
	//self.goToPage({title: 'About Me', alias: 'about'});
});

ko.applyBindings(ProfileVM, $("#content")[0]);
