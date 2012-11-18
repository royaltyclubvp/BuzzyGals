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
	self.bookmarked = 0;
	//Check if Browsing User has Bookmarked the Resource
	self.bookmark = function(resource) {
		result = ResourceVM.bookmarkResource(resource);
		if(result)
			self.bookmarked(1);
	}
	self.removeBookmark = function(resource) {
		result = ResourceVM.removeBookmark(resource);
		if(result)
			self.bookmarked(0);
	}
}

ResourceVM = new (function() {
	//Data
	var self = this;
	
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
	
	self.loadInitialResources = function(newestResources, localResources) {
		var newestMapped = $.map(newestResources.articles, function(resource) {
			return new Resource(resource);
		});
		self.newestResources(newestMapped);
		var localMapped = $.map(localResources.articles, function(resource) {
			return new Resource(resource);
		});
		self.localResources(localMapped);
		self.localResourceTotal(localResources.total);
		self.localCurrentPageRetrieved(5);
	}
	
	//Load Initial Data
	self.loadInitialResources(newestResources, localResources);
});

ko.applyBindings(ResourceVM, $("#content")[0]);
