/* Author: Jarrod Placide-Raymond
 * 
 */

//Data Structures

//View Model
function TopMenuViewModel() {
	//Data
	var self = this;
	
	self.messageCount = ko.observable(0);
	
	//Behaviours
	self.checkNewMessages = function() {
		$.get("/messages/countnewmessages", function(count) {
			if(count != self.messageCount()) self.messageCount(count);
		});
	};
	
	setInterval(self.checkNewMessages, 30000);
	
	//Initiate Default Functions
	self.checkNewMessages();
};

ko.applyBindings(new TopMenuViewModel(), $("#body_header")[0]);
