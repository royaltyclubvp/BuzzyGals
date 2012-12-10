//Data Structures

function Townhall(data) {
	var self = this;
	self.id = data.id;
	self.name = data.name;
	self.description = data.description;
	self.following = ko.observable(data.following);
	self.url = '/townhall/' + data.name.replace(/\s/g, "").toLowerCase();
	self.follow = function() {
		success = TownhallVM.follow(self.id);
		if(success) {
			self.following(true);
		}
	}
	self.unfollow = function() {
		success = TownhallVM.unfollow(self.id);
		if(success)
			self.following(false);
	}
}

TownhallVM = new (function() {
	//Data
	var self = this;
	
	self.townhalls = ko.observableArray([]);
	
	//Behaviours
	self.follow = function(id) {
		success = false;
		$.ajax({
			url : '/profile/followtownhall',
			data : {
				townhall : id
			},
			type : "POST",
			dataType : "text",
			async : false,
			success : function(result) {
				if(result == "1")
					success = true;
			}
		});
		return success;
	}
	
	self.unfollow = function(id) {
		success = false;
		$.ajax({
			url : '/profile/removetownhall',
			data : {
				townhall : id
			},
			type : "POST",
			dataType : "text",
			async : false,
			success : function(result) {
				if(result == "1")
					success = true;
			}
		});
		return success;
	}
	
	self.loadTownhalls = function(townhalls) {
		var mapped = $.map(townhalls.root, function(townhall) {
			return new Townhall(townhall);
		});
		self.townhalls(mapped);
	}
	
	self.loadTownhalls(townhalls);
});

ko.applyBindings(TownhallVM, $("#content")[0]);
