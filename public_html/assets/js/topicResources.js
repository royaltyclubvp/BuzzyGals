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
	
	self.resources = ko.observableArray([]);
	self.resourceCount = ko.observable();
	
	//Behaviours
	self.bookmarkResource = function(resource) {
		success = false;
		$.ajax({
			url : "/resources/bookmark",
			async : false,
			data : {
				resource : resource.id
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
	
	self.removeBookmark = function(resource) {
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
	
	self.loadResources = function(resources, count) {
		var mapped = $.map(resources, function(resource) {
			return new Resource(resource);
		});
		self.resources(mapped);
		self.resourceCount(count);
	}
	
	//Load Resources on Page Load
	self.loadResources(resources, resourceCount);
	
});

ko.applyBindings(ResourceVM, $("#content")[0]);
