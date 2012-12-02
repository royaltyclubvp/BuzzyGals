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
	self.bookmarked = ko.observable(data.bookmarked)
	//Check if Browsing User has Bookmarked the Resource
	self.bookmark = function() {
		result = ResourceVM.bookmark(self.id);
		if(result)
			self.bookmarked(1);
	}
	self.removeBookmark = function() {
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
	
	self.newestResources = ko.observableArray([]);
	self.localResources = ko.observableArray([]);
	self.localResourceTotal = ko.observable();
	self.localCurrentPageRetrieved = ko.observable();
	
	//Behaviours
	self.loadMoreLocalResources = function() {
		var newPage = self.localCurrentPageRetrieved() + 1;
		$.ajax({
			url : "/resources/loadmorelocal",
			data : {
				page : newPage
			},
			type : "GET",
			dataType : "json",
			success : function(result) {
				if(result.total != self.localResourceTotal()) {
					$.ajax({
						url : "/resources/loadmorelocal",
						data : {
							page : 1,
							noPerPage : newPage * 5
						},
						type : "GET", 
						dataType : "json",
						success : function(newResult) {
							var mappedResources = $.map(newResult.resources, function(resource) {
								return new Resource(resource);
							});
							self.localResources(mappedResources);
							self.localResourceTotal(newResult.total);
							self.localCurrentPageRetrieved(Math.ceil(self.localResources().length/5));
						}
					});
				}
				else {
					var mappedResources = $.map(result.resources, function(resource) {
						return new Resource(resource);
					});
					self.localResources.push(mappedResources);
					self.localCurrentPageRetrieved(Math.ceil(self.localResources().length/5));
				}
			}	
		});
	}
	
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
	
	//Add Check for Data Redundancy in Two Categories on Screen after Bookmark modification
	
	self.loadInitialResources = function(newestResources, localResources) {
		var newestMapped = $.map(newestResources, function(resource) {
			return new Resource(resource);
		});
		self.newestResources(newestMapped);
		var localMapped = $.map(localResources.resources, function(resource) {
			return new Resource(resource);
		});
		self.localResources(localMapped);
		self.localResourceTotal(localResources.total);
		self.localCurrentPageRetrieved(1);
	}
	
	//Load Initial Data
	self.loadInitialResources(newestResources, localResources);
});

ko.applyBindings(ResourceVM, $("#content")[0]);
