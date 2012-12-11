/*
 * Author: Jarrod Placide-Raymond
 */
//Data Structures
function User(data) {
	var self = this;
	self.user = data.user;
	self.displayName = data.displayName;
	self.url = '/' + self.displayName.toLowerCase() + '/view';
	self.photo = profileThumbsUrl + data.photo;
	self.location = "";
	if (data.Location.city != '')
		this.location = this.location + data.Location.city + ", ";
	if (data.Location.stateprov != '')
		this.location = this.location + data.Location.stateprov + ", ";
	if (data.Location.country != '')
		this.location = this.location + data.Location.country;
	self.connections = data.connections;
	self.friend = ko.observable(data.friend);
	self.requested = ko.observable((data.friend) ? false : data.outgoingRequest);
	self.requestreceived = ko.observable((data.requested) ? false : data.incomingRequest);
	self.requestid = ko.observable((self.requested || self.requestreceived) ? data.requestid : 0);
	self.requestFriendship = function() {
		success = UserSearchVM.requestFriendship(self.user);
		if(success) 
			self.requested(true);
	}
	self.acceptRequest = function() {
		success = UserSearchVM.acceptFriendRequest(self.requestid)
		if(success) {
			self.friend(true);
		}
	}
	self.cancelRequest = function() {
		success = UserSearchVM.rejectFriendRequest(self.requestid);
		if(success) {
			self.requested(false);
		}
	}
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
UserSearchVM = new (function() {
	//Data
	var self = this;
	
	self.searchValue = ko.observable("");
	self.showSearchButton = ko.observable(false);
	
	self.users = ko.observableArray([]);
	self.userCount = ko.observable();
	self.currentResults = ko.observableArray([]);
	self.currentPage = ko.observable();
	self.noPerPage = ko.observable();
	self.totalPages = ko.observable();
	self.currentResultsLower = ko.observable();
	self.currentResultsUpper = ko.observable();
	
	self.newMessage = ko.observable(new Message(0,0));
	
	//Behaviours
	self.loadInitialSearchResults = function(users, count, noPerPage, searchTerms) {
		var mapped = $.map(users.root, function(user) {
			return new User(user); 
		});
		self.users(mapped);
		self.currentResults(self.users().slice(0, noPerPage));
		self.searchValue(searchTerms);
		self.userCount(count);
		self.currentPage(1);
		self.noPerPage(noPerPage);
		self.totalPages(Math.ceil(self.userCount()/self.noPerPage()));
		self.currentResultsLower(0);
		self.currentResultsUpper(self.currentResults().length);
	}
	
	self.showPreviousResultsPage = function() {
		self.currentPage(self.currentPage()-1);
		self.currentResultsLower(((self.currentPage()-1)*self.noPerPage())+1);
		self.currentResultsUpper(self.currentPage()*self.noPerPage());
		self.currentResults(self.users().slice(self.currentResultsLower()-1, self.currentResultsUpper()));
	}
	
	self.showNextResultsPage = function() {
		self.currentPage(self.currentPage()+1);
		self.currentResultsLower(((self.currentPage()-1)*self.noPerPage())+1);
		if(self.currentPage() < self.totalPages()) {
			self.currentResultsUpper(self.currentPage()*self.noPerPage());
		}
		else {
			self.currentResultsUpper(self.userCount());
		}
		self.currentResults(self.users().slice(self.currentResultsLower()-1, self.currentResultsUpper()));
	}
	
	self.requestFriendship = function(id) {
		success = false;
		$.ajax({
			url : '/profile/requestfriendship',
			data : {
				user : id
			},
			type : "POST",
			async : false,
			dataType : "text",
			success : function(result) {
				if (result == "1") 
					success = true;
			}
		});
		return success;
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
	
	self.sendNewMessage = function(user) {
		self.newMessage(new Message(user.displayName, user.user));
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
	
	self.loadInitialSearchResults(users, userCount, noPerPage, searchValue);
	
});

ko.applyBindings(UserSearchVM, $("#content")[0]);
