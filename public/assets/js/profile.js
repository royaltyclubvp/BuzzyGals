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
	var da = data.date.split(/[- :]/);
	self.date = new Date(da[0], da[1]-1, da[2], da[3], da[4], da[5]);
	self.date = self.date.toDateString();
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
			self.previewPhotos[i] = userGalleryImagesUrl + data.Media.photos[i];
	}
	else {
		self.galleryAvailable = false;
	}
	self.mainPhotos = new Array();
	if (self.photocount > 3) {
		var len = self.photocount;
		for (var i = 3; i < len; i++) {
			self.mainPhotos[i] = userGalleryImagesUrl + data.Media.photos[i]
		}
	}
	self.galleryVisible = ko.observable(0);
	self.commentCount = data.Comments.length;
	self.commentText = (self.commentComment == 1) ? 'Comment' : 'Comments';
	self.comments = ko.observableArray([]);
	for(var i=0; i < self.commentCount; i++) {
		self.comments.push(new Comment(data.Comments[i]));
	}
	self.addComment = function(story) {
		result = ProfileVM.addComment(story.id, story.newComment);
		if(result.id) //Check for Existence of ID Value On Object
			self.comments.push(new Comment(result));
	}
	self.removeComment = function(comment) {
		result = ProfileVM.removeComment(comment);
		if(result)
			self.comments.remove(comment);
	}
	self.commentsVisible = ko.observable(0);
	self.newComment = ko.observable();
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

function newStory() {
	var self = this;
	self.content = ko.observable("");
	self.photos = ko.observableArray([]);
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
	self.newStory = ko.observable(new newStory());
	
	//Edit Toggles
	self.aboutEdit = ko.observable(false);
	self.interestsEdit = ko.observable(false);
	self.galleryInterfaceVisible = ko.observable(0);

	//Behaviours
	self.goToPage = function(page) {
		page = page.replace(/\s/g, ""); 
		location.hash = page; 
	};
	
	self.addNewStory = function() { self.currentPage('NewStory') }
	
	self.addStoryPhoto = function(file) {
		self.newStory().photos.push(file);
	}
	
	self.cancelNewStory = function() {
		self.currentPage('MyStories');
		self.newStory("");
	}
	
	self.showAddGalleryInterface = function() {
		self.galleryInterfaceVisible(1);
	}
	
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
	
	self.addComment = function(storyid, comment) {
		success = "";
		$.ajax({
			url : "/profile/addstorycomment",
			data : {
				story : storyid,
				comment : comment
			},
			type : "POST",
			dataType : "json",
			success : function(result) {
				if(result.root.id)
					success = result.root;
			}
		});
		return success;
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
	
	
	//Load Default Page
	//self.goToPage({title: 'About Me', alias: 'about'});
});

ko.applyBindings(ProfileVM, $("#content")[0]);
