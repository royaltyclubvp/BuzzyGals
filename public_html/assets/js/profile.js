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
	this.url = '/' + data.Friend.Profile.displayName.toLowerCase() + '/view';
	this.photo = profileThumbsUrl + data.Friend.Profile.photo;
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
	var da = data.date.split(/[- :]/);
	this.date = new Date(da[0], da[1]-1, da[2], da[3], da[4], da[5]);
	this.date = ISODateString(this.date);
	this.userPhoto = profileThumbsUrl + data.User.Profile.photo;
	this.displayName = data.User.Profile.displayName;
	this.content = data.content;
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

function newStory() {
	var self = this;
	self.content = ko.observable("");
	self.gallery = ko.observableArray([]);
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
	self.listVisible = ko.observable(1);
	self.toggleVisibility = function() {
		if(self.listVisible()) {
			self.listVisible(0);
		}
		else {
			self.listVisible(1);
		}
	}
}

function newProfileImage() {
	var self = this;
	self.fullSizeImage = ko.observable("");
	self.filename = ko.observable();
	self.x = ko.observable("");
	self.x2 = ko.observable("");
	self.y = ko.observable("");
	self.y2 = ko.observable("");
	self.width = ko.observable("");
	self.height = ko.observable("");
}

function newMessageRecipient(id, displayName) {
	this.id = id;
	this.displayName = displayName;
}

function Message(recipientDisplayName, recipientId) {
	this.subject = "";
	this.content = "";
	this.recipients = ko.observableArray([]);
	this.recipients.push(new newMessageRecipient(recipientId, recipientDisplayName));
	this.type = 'n';
	this.ref = "";
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
	self.newProfileImage = ko.observable(new newProfileImage());
	self.newMessage = ko.observable(new Message(0,0));
	
	//Edit Toggles
	self.aboutEdit = ko.observable(false);
	self.interestsEdit = ko.observable(false);
	self.galleryInterfaceVisible = ko.observable(0);
	self.profileUploadVisible = ko.observable(0);

	//Behaviours
	self.goToPage = function(page) {
		page = page.replace(/\s/g, ""); 
		location.hash = page; 
	};
	
	self.addNewStory = function() { self.currentPage('NewStory') }
	
	self.addStoryPhoto = function(file) {
		self.newStory().gallery.push(file);
	}
	
	self.addStory = function() {
		$.ajax({
			url : "/profile/addstory",
			data : ko.toJS(self.newStory),
			type : "POST",
			dataType : "text",
			success : function(result) {
				if (result == "1") {
					location.hash = "AboutMe";
					self.goToPage("MyStories");	
				}
				else 
					return; //Modify for Error Handling
			}
		})
	}
	
	self.cancelNewStory = function() {
		self.currentPage('MyStories');
		self.newStory("");
	}
	
	self.showAddGalleryInterface = function() {
		self.galleryInterfaceVisible(1);
	}
	
	self.showProfileUpload = function() {
		self.profileUploadVisible(1);
	}
	
	self.submitImageCrop = function() {
		$.ajax({
			url : '/profile/cropprofilepic',
			data : {
				filename : self.newProfileImage().filename(),
				x1 : self.newProfileImage().x(),
				x2 : self.newProfileImage().x2(),
				y1 : self.newProfileImage().y(),
				y2: self.newProfileImage().y2(),
				width : self.newProfileImage().width(),
				height : self.newProfileImage().height()
			},
			type : "POST",
			dataType : "text",
			success : function(result) {
				if(result == "1")
					location.reload();
			}
		});
	}
	
	self.setCoordinates = function(c) {
		self.newProfileImage().x(c.x);
		self.newProfileImage().y(c.y);
		self.newProfileImage().x2(c.x2);
		self.newProfileImage().y2(c.y2);
		self.newProfileImage().width(c.w);
		self.newProfileImage().height(c.h);
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
	
	self.timeAgo = function() {
		$('time.date').timeago();
	}
	
	self.sendNewMessage = function(friend) {
		self.newMessage(new Message(friend.displayName, friend.userid));
		$.fancybox.open(
			{
				href : '#send_message'
			},
			[{
				width: '500px',
				maxHeight: 600 
			}]
		);
	}
	
	self.sendMessage = function() {
		$.ajax({
			url : "/messages/send",
			data : ko.toJS(self.newMessage),
			type : "POST",
			dataType: "json",
			success: function(result) {
				if(result.length === 0) {
					//Implement Failure Handler
				}
				else {
					$.fancybox.close();
					self.newMessage(new Message(0,0));
				}
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
				setTimeout(self.timeAgo, 2000);
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
		
		this.defaultCheckFormSubmission = this._checkFormSubmission;
		this._checkFormSubmission = function (form){
    		var $form, path, verb;
    		$form = $(form);
    		path = $form.attr("action");
    		verb = this._getFormVerb($form);
    		if (verb === "get" && !path.startsWith("#")) {
         		return false;
    		}
    		else {
        		return this.defaultCheckFormSubmission(form);
   			}
		}; 
		
	}).run();	
	
	
	//Load Default Page
	if(location.hash == "") {
		self.goToPage("My Stories");
	}
});

ko.applyBindings(ProfileVM, $("#content")[0]);
