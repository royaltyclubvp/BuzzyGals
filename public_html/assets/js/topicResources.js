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
	
	self.loadInitialResources = function(resources, count) {
		var mapped = $.map(resources, function(resource) {
			return new Resource(resource);
		});
		self.resources(mapped);
		self.resourceCount(count);
	}
	
	//Load Resources on Page Load
	self.loadInitialResources(resources, resourceCount);
	
});

ko.applyBindings(ResourceVM, $("#content")[0]);
