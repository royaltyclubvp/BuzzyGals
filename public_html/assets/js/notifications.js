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

//View Model
NotificationsVM = new (function() {
	var self = this;
	//Data
	self.section = ['New Stories', 'New Articles'];
	self.currentSection = ko.observable();
	
	self.stories = ko.observableArray([]);
	
	//Behaviours
	self.goToSection = function(section) {
		section = section.replace(/\s/g, "");
		location.hash = section;
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
		});
	}).run();
	
	//Load Default Page
	if(location.hash == "") {
		self.goToSection("NewStories");
	}
});

ko.applyBindings(NotificationsVM, $("#content")[0]);
