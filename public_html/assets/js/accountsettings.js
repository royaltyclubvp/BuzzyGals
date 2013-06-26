/**
 * Author: Jarrod Placide-Raymond
 */

//Data Structures
function city(data) {
	var self = this;
	self.id = data.id;
	self.name = data.name;
}

function state(data) {
	var self = this;
	self.id = data.id;
	self.name = data.name;
	self.cities = ko.observableArray([]);
	for(var i=0; i < data.Cities.length; i++) {
		self.cities.push(new city(data.Cities[i]));
	}
}

function country(data) {
	var self = this
	self.id = data.id;
	self.name = data.name;
	self.states = ko.observableArray([]);
	for(var i=0; i < data.States.length; i++) {
		self.states.push(new state(data.States[i]));
	}
}

function settings(data) {
	self.displayName = ko.observable(data.displayName);
	self.fName = ko.observable(data.fName);
	self.lName = ko.observable(data.lName);
	if(data.usergroup == 1) {
		self.gender = ko.observable(data.gender);
		self.storyNotificationPeriod = ko.observable(data.storyNotificationPeriod);
	}
	self.countryid = ko.observable(data.countryid);
	self.stateprovid = ko.observable(data.stateprovid);
	self.cityid = ko.observable(data.cityid);
	self.country = ko.observable(data.country);
	self.stateprov = ko.observable(data.stateprov);
	self.city = ko.observable(data.city);
}

SettingsVM = new (function() {
	//Data
	var self = this;
	
	self.isLoading = ko.observable(true);
	
	self.countryList = ko.observableArray([]);
	self.stateList = ko.observableArray([]);
	self.cityList = ko.observableArray([]);
	
	self.settings = ko.observable("");
	
	
	//Behaviours
	self.loadLocations = function() {
		$.ajax({
			url : "/user/getlocations",
			type : "GET",
			dataType : "json",
			success : function(result) {
				if(result.length) {
					var mappedLocations = $.map(result, function(location) {
						return new country(location);
					});
					self.countryList(mappedLocations);
				}
			}
		});
	}
	
	self.loadSettings = function() {
		$.ajax({
			url : "/profile/getsettings",
			type: "GET",
			dataType : "json",
			success : function(result) {
				if(result.root.length != 0) {
					self.settings(new settings(result.root));
					self.isLoading(false);
				}
				if(result != "") {
					self.settings(new settings(result.root));
					self.isLoading(false);
				}
			}
		});
	}
	
	//Load Locations By Default
	self.loadLocations();
	
	//Load Settings
	self.loadSettings();
	
	//Subscriptions
	self.settings().countryid.subscribe(function(newValue) {
		var selected = ko.utils.arrayFirst(self.countryList(), function(country) {
			if(country.id==newValue) return true;
		});
		self.settings().country(selected.name);
		self.stateList(selected.states());
	});
	
	self.settings().stateprovid.subscribe(function(newValue) {
		var selected = ko.utils.arrayFirst(self.stateList(), function(state) {
			if(state.id==newValue) return true;
		});
		self.settings().stateprov(selected.name);
		self.cityList(selected.cities());
	});
	
	self.settings().cityid.subscribe(function(newValue) {
		var selected = ko.utils.arrayFirst(self.cityList(), function(city) {
			if(city.id==newValue) return true;
		});
		self.settings().city(selected.name);
	});
});

ko.applyBindings(SettingsVM, $("#right-column")[0]);
