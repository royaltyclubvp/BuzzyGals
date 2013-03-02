/*
 * Author: Jarrod Placide-Raymond
 */

//Data Structures
function Story(data) {
	this.id = data.id;
	var da = data.date.split(/[- :]/);
	this.date = new Date(da[0], da[1]-1, da[2], da[3], da[4], da[5]);
	this.date = this.date.toDateString() + " " + ((this.date.getHours() > 12) ? this.date.getHours()-12 : this.date.getHours()) + ":" + this.date.getMinutes() + ((this.date.getHours() > 12) ? " p.m." : "");
	this.photo = profileThumbsUrl + data.Profile.photo;
	this.displayName = data.Profile.displayName;
	this.url = storyLinkPrefix + this.id;
	this.contentPreview = data.content.substr(0, 50);
};

function FriendRequest(data) {
	var self = this;
	self.isLoading = ko.observable(false);
	self.id = data.id;
	self.photo = profileThumbsUrl + data.Requestor.Profile.photo;
	self.displayName = data.Requestor.Profile.displayName;
	self.responded = ko.observable(false);
	self.accepted = ko.observable(false);
	self.acceptFriendRequest = function() {
		self.isLoading(true);
		success = NotificationsVM.acceptFriendRequest(self.id);
		self.isLoading(false);
		if(success) {
			self.responded(true);
			self.accepted(true);
		}
	}
	self.rejectFriendRequest = function() {
		self.isLoading(true);
		success = NotificationsVM.rejectFriendRequest(self.id);
		self.isLoading(false);
		if(success) {
			self.responded(true);
		}
	}
}


//View Model
NotificationsVM = new (function() {
	var self = this;
	
	self.isLoading = ko.observable(false);
	
	//Data
	self.section = ['New Stories', 'New Articles', 'Friend Requests'];
	self.currentSection = ko.observable();
	
	self.stories = ko.observableArray([]);
	self.friendRequests = ko.observableArray([]);
	
	//Behaviours
	self.goToSection = function(section) {
		self.isLoading(true);
		section = section.replace(/\s/g, "");
		location.hash = section;
	}
	
	self.acceptFriendRequest = function(id) {
		success = false;
		$.ajax({
			url : "/profile/acceptrequest",
			data : {
				requestid : id
			},
			type : "GET",
			async : false,
			dataType : "text",
			success : function(result) {
				if(result = "1")
					success = true;
			}
		});
		return success;
	}
	
	self.rejectFriendRequest = function(id) {
		success = false;
		$.ajax({
			url : "/profile/rejectrequest",
			data : {
				requestid : id
			},
			type : "GET",
			async : false,
			dataType : "text",
			success : function(result) {
				if(result = "1")
					success = true;
			}
		});
		return success;
	}
	
	//Client-Side Routes
	Sammy(function() {
		this.get('#:section', function() {
			self.currentSection(this.params.section);
			if(this.params.section == "NewStories") {
				$.getJSON("/messages/loadnewstories", function(allData) {
					var mappedStories = $.map(allData.root, function(story) {
						return new Story(story);
					});
					self.stories(mappedStories);
				});
			}
			else if(this.params.section == "FriendRequests") {
				$.getJSON("/messages/loadreceivedrequests", function(allData) {
					var mappedRequests = $.map(allData.root, function(request) {
						return new FriendRequest(request);
					});
					self.friendRequests(mappedRequests);
				});
			}
			self.isLoading(false);
		});
	}).run();
	
	//Load Default Page
	if(location.hash == "") {
		self.goToSection("NewStories");
	}
});

ko.applyBindings(NotificationsVM, $("#content")[0]);
