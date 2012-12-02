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

function registration() {
	var self = this;
	self.username = ko.observable("");
	self.password = ko.observable("");
	self.passwordConfirm = ko.observable("");
	self.fName = ko.observable("");
	self.lName = ko.observable("");
	self.displayName = ko.observable("");
	self.gender = ko.observable("");
	self.dob_month = ko.observable("");
	self.dob_day = ko.observable("");
	self.dob_year = ko.observable("");
	self.countryid = ko.observable("");
	self.stateprovid = ko.observable("");
	self.cityid = ko.observable("");
	self.country = ko.observable("");
	self.stateprov = ko.observable("");
	self.city = ko.observable("");
}

RegistrationVM = new (function() {
	//Data
	var self = this;
	
	self.countryList = ko.observableArray([]);
	self.stateList = ko.observableArray([]);
	self.cityList = ko.observableArray([]);
	
	self.processState = ko.observable("New");
	
	self.registration = ko.observable(new registration());
	
	//Behaviours
	self.loadLocations = function() {
		$.ajax({
			url : "/user/getlocations",
			type : "GET",
			dataType : "json",
			success : function(result) {
				if(result.length) {
					var mappedLocations = $.map(result, function(country) {
						return new country(country);
					});
					self.countryList(mappedLocations);
				}
			}
		});
	}
	
	self.submitRegistration = function(registration) {
		$.ajax({
			url : "/user/register",
			data : ko.toJS(registration),
			type : "POST",
			dataType : "json",
			success : function(result) {
				if(result.id) {
					self.processState("Success");
				}
			}
		});
	}
	
	//Load Locations By Default
	self.loadLocations();
	
});

ko.applyBindings(RegistrationVM, $("#right-column")[0]);
