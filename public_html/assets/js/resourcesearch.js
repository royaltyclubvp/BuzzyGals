/**
 * Author: Jarrod Placide-Raymond
 * 
 */

//Data Structures
function Resource(data) {
	var self = this;
	self.id = data.id;
	self.name = data.name;
	self.contact = data.contact;
	self.address = data.address;
	self.bookmarked = ko.observable(data.bookmarked);
	//Check if Browsing User has Bookmarked the Resource
	self.bookmark = function() {
		result = ResourceVM.bookmark(self.id);
		if(result)
			self.bookmarked(1);
	}
	self.removebookmark = function() {
		result = ResourceVM.removebookmark(self.id);
		if(result)
			self.bookmarked(0);
	}
}

ResourceVM = new (function() {
	//Data
	var self = this;
	
	self.searchValue = ko.observable("");
	self.showSearchButton = ko.observable(false);
	
	self.resources = ko.observableArray([]);
	self.resourceCount = ko.observable();
	self.currentResources = ko.observableArray([]);
	self.currentPage = ko.observable();
	self.noPerPage = ko.observable();
	self.currentResultsLower = ko.observable();
	self.currentResultsUpper = ko.observable();
	
	//Behaviours
	self.bookmark = function(id) {
		success = false;
		$.ajax({
			url : '/resources/bookmark',
			async : false,
			data : {
				resource : id
			},
			type: "POST",
			dataType : "text",
			success: function(result) {
				if(result = "1")
					success = true;
			}
		});
		return success;
	}
	
	self.removebookmark = function(id) {
		success = false;
		$.ajax({
			url : "/profile/removeresourcebookmark",
			async: false,
			data : {
				resource : id
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
	
	self.loadInitialSearchResults = function(resources, count, noPerPage, searchTerms) {
		var mapped = $.map(resources.root, function(resource) {
			return new Resource(resource); 
		});
		self.resources(mapped);
		self.currentResources(self.resources().slice(0, noPerPage));
		self.searchValue(searchTerms);
		self.resourceCount(count);
		self.currentPage(1);
		self.noPerPage(noPerPage);
		self.currentResultsLower(0);
		self.currentResultsUpper(self.currentResources().length);
	}
	
	self.showPreviousResultsPage = function() {
		self.currentPage(self.currentPage()-1);
		self.currentResultsLower(((self.currentPage()-1)*self.noPerPage())+1);
		self.currentResultsUpper(self.currentPage()*self.noPerPage());
		self.currentResources(self.resources().slice(self.currentResultsLower()-1, self.currentResultsUpper()));
	}
	
	self.showNextResultsPage = function() {
		self.currentPage(self.currentPage()+1);
		self.currentResultsLower(((self.currentPage()-1)*self.noPerPage())+1);
		self.currentResultsUpper(self.currentPage()*self.noPerPage());
		self.currentResources(self.resources().slice(self.currentResultsLower()-1, self.currentResultsUpper()));
	}
	
})
