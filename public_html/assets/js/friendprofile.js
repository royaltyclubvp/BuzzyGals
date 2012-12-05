/* 
 * Author: Jarrod Placide-Raymond
 */

//Data Structures
function Profile(data) {
	this.about = ko.observable(data.about);
	this.interests = ko.observable(data.interests);
}

function Comment(data) {
	this.id = data.id;
	var da = data.date.split(/[- :]/);
	this.date = new Date(da[0], da[1]-1, da[2], da[3], da[4], da[5]);
	this.date = ISODateString(this.date);
	this.userPhoto = profileImagesUrl + data.User.Profile.photo;
	this.displayName = data.User.Profile.displayName;
	this.content = data.content;
	this.owned = (data.user = userId) ? true : false;
}

function GalleryPhoto(data) {
	var self = this;
	self.thumbnail = userGalleryThumbsUrl + data;
	self.full = userGalleryImagesUrl + data;
}

function Story(data) {
	var self = this;
	self.id = data.id;
	var da = data.date.split(/[- :]/);
	self.date = new Date(da[0], da[1]-1, da[2], da[3], da[4], da[5]);
	self.date = ISODateString(self.date);
	self.contentPreview = data.content.substring(0, 150);
	self.contentFull = data.content.substring(150);
	self.fullStoryVisible = ko.observable(0);
	self.photocount = (typeof data.Media != 'undefined') ? data.Media.totalphotos : 0;
	if (self.photocount != 0) {
		self.galleryAvailable = true;
		self.previewPhotos = new Array();
		if (self.photocount < 3)
			var len = self.photocount;
		else
			var len = 3;
		for (var i = 0; i < len; i++) {
			self.previewPhotos.push(new GalleryPhoto(data.Media.photos[i]));
		}
	}
	else {
		self.galleryAvailable = false;
	}
	self.mainPhotos = new Array();
	if (self.photocount > 3) {
		var len = self.photocount;
		for (var i = 3; i < len; i++) {
			self.mainPhotos.push(new GalleryPhoto(data.Media.photos[i]));
		}
	}
	self.galleryVisible = ko.observable(0);
	self.commentCount = ko.observable(data.Comments.length);
	self.commentText = (self.commentCount() == 1) ? 'Comment' : 'Comments';
	self.comments = ko.observableArray([]);
	for(var i=0; i < self.commentCount(); i++) {
		self.comments.push(new Comment(data.Comments[i]));
	}
	self.addComment = function() {
		result = ProfileVM.addComment(self.id, self.newComment);
		if(result.id) {
			self.newComment("");
			self.comments.push(new Comment(result));
			self.commentCount(self.comments().length);
			setTimeout(ProfileVM.timeAgo, 2000);
		}
	}
	self.removeComment = function(comment) {
		result = ProfileVM.removeComment(comment.id);
		if(result) {
			self.comments.remove(comment);
			self.commentCount(self.comments().length);
		}
	}
	self.commentsVisible = ko.observable(0);
	self.newComment = ko.observable("");
	self.showAddCommentButton = ko.observable(false);
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

//View Model
FriendProfileVM = new (function() {
	//Data
	var self = this;
	
	self.pages = ['About','Stories'];
	self.currentPage = ko.observable();
	
	//Page Data
	self.profile = ko.observable();
	self.stories = ko.observableArray([]);
	self.displayName = ko.observable("");
	
	//Toggles
	
	
	//Behaviours
	self.goToPage = function(page) {
		page = page.replace(/\s/g, ""); 
		location.hash = page; 
	};
	
	self.addComment = function(storyid, comment) {
		success = "";
		$.ajax({
			url : "/profile/addstorycomment",
			data : {
				story : storyid,
				comment : comment
			},
			type : "POST",
			async : false,
			dataType : "json",
			success : function(result) {
				if(result.root.id)
					success = result.root;				
			}
		});
		return success;
	}
	
	self.removeComment = function(id) {
		success = false;
		$.ajax({
			url : "/profile/deleteownedstorycomment",
			data : {
				comment : id
			},
			type: "POST",
			async : false,
			dataType : "text",
			success : function(result) {
				if(result == "1") {
					success = true;
				}
			}
		});
		return success;
	}
	
	//Client-Side Routes
	Sammy(function() {
		//Page Routes
		this.get('#page', function() {
			self.currentPage(this.params.page);
			if(this.params.page == "About") {
				$.getJSON("/profile/loadfriendabout", {username: self.displayName()}, function(allData) {
					self.profile(new Profile(allData));
				});
			}
			else if(this.params.page == "Stories") {
				$.getJSON("/profile/loadfriendstories", {username: self.displayName()}, function(allData) {
					var mappedStories = $.map(allData.root, function(story) {
						return new Story(story);
					});
					self.stories(mappedStories);
				});
				setTimeout(self.timeAgo, 2000);
			}
		});
	}).run();
	
	if(location.hash == "") {
		self.displayName(displayName);
		self.goToPage("About");
	}
});
