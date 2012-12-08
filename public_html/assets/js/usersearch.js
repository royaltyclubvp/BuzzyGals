/*
 * Author: Jarrod Placide-Raymond
 */
//Data Structures
function User(data) {
	var self = this;
	self.id = data.id;
	self.displayName = data.Profile.displayName;
	self.url = '/' + self.displayName.toLowerCase() + '/view';
	self.photo = profileImagesUrl + data.Profile.photo;
	self.location = "";
	if (data.Friend.Profile.Location.city != '')
		this.location = this.location + data.Friend.Profile.Location.city + ", ";
	if (data.Friend.Profile.Location.stateprov != '')
		this.location = this.location + data.Friend.Profile.Location.stateprov + ", ";
	if (data.Friend.Profile.Location.country != '')
		this.location = this.location + data.Friend.Profile.Location.country;
	self.connections = data.connections;
	self.friend = ko.observable(data.friend);
	self.requested = ko.observable((data.friend) ? "" : data.requested);
	self.requestFriendship = function() {
		success = UserSearchVM.requestFriendship(self.id);
		if(success) 
			self.requested(true);
	}
}

//View Model
UserSearchVM = new (function() {
	//Data
	var self = this;
	
	self.currentResults = ko.observableArray([]);
	self.noPerPage = ko.observable();
	self.total = ko.observable();
	self.currentPage = ko.observable();
	self.currentResultsLower = ko.observable();
	self.currentResultsUpper = ko.observable();
	self.searchTerms = ko.observable();
	
	//Behaviours
	self.requestFriendship = function(id) {
		success = false;
		$.ajax({
			url : '/profile/requestfriendship',
			data : {
				user : id
			},
			type : "POST",
			dataType : "text",
			success : function(result) {
				if (result == "1") 
					success = true;
			}
		});
		return success;
	}
	
	self.loadPreviousPage = function() {
		$.ajax({
			url : '/profile/loadsearchresults',
			data : {
				terms : self.searchTerms(),
				page : self.currentPage()-1,
				pageAmount : self.noPerPage()
			},
			type : "POST",
			dataType : "json",
			success : function(result) {
				if(result.length) {
					self.currentResults(result);
					self.currentPage(self.currentPage()-1);
					self.currentResultsLower(((self.currentPage()-1)*self.noPerPage()) + 1);
					self.currentResultsUpper((self.currentPage()-1)*self.noPerPage() + self.currentResults().length);
				}
			}
		});
	}
	
	self.loadNextPage = function() {
		$.ajax({
			url : '/user/loadsearchresults',
			data : {
				terms : self.searchTerms(),
				page : self.currentPage()+1,
				pageAmount : self.noPerPage()
			}
			type : "POST",
			dataType : "json",
			success : function(result) {
				if(result.length) {
					self.currentResults(result);
					self.currentPage(self.currentPage()+1);
					self.currentResultsLower(((self.currentPage()-1)*self.noPerPage()) + 1);
					self.currentResultsUpper((self.currentPage()-1)*self.noPerPage() + self.currentResults().length);
				}
			}
		});
	}
	
})
