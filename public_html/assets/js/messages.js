/* Author: Jarrod Placide-Raymond
 * 
 */

//Data Structures
function Message(data) {
	this.id = data.id;
	this.sender = data.Sender;
	this.recipients = data.Recipients;
	var da = data.date.split(/[- :]/);
	this.date = new Date(da[0], da[1]-1, da[2], da[3], da[4], da[5]);
	this.date = this.date.toDateString() + " " + ((this.date.getHours() > 12) ? this.date.getHours()-12 : this.date.getHours()) + ":" + this.date.getMinutes() + ((this.date.getHours() > 12) ? " p.m." : "");
	this.content = data.content; 
	this.subject = data.subject;
}

function InboxMessage(data) {
	this.id = data.id;
	this.sender = data.SenderProfile;
	var da = data.date.split(/[- :]/);
	this.date = new Date(da[0], da[1]-1, da[2], da[3], da[4], da[5]);
	this.date = this.date.toDateString();
	this.subject = data.subject;
	this.read = ko.observable(data.Recipients[0].seen);
}

function SentMessage(data) {
	this.id = data.id;
	this.subject = data.subject;
	var da = data.date.split(/[- :]/);
	this.date = new Date(da[0], da[1]-1, da[2], da[3], da[4], da[5]);
	this.date = this.date.toDateString();
	this.recipients = data.Recipients[0].User.Profile.displayName;
	this.len = data.Recipients.length;
	if(this.len > 1) {
		for(var i=1; i < this.len; i++) {
			this.recipients = this.recipients + ', ' + data.Recipients[i].User.Profile.displayName; 
		}
	}
}

function newMessageRecipient(data) {
	this.id = data.id;
	this.displayName = data.displayName;
}

function NewMessage(subject, recipient, type, reference) {
	this.id = "";
	this.subject = subject;
	this.content = "";
	this.recipients = ko.observableArray([]);
	if(recipient instanceof Array) {
		this.recipientCount = recipient.length;
		for(var i=0; i < this.recipientCount; i++) {
			this.recipients.push(new newMessageRecipient(recipient[i]));
		}
	}
	this.type = type;
	this.ref = reference;
}

function Friend(data) {
	this.id = data.id;
	this.userid = data.Friend.id;
	this.displayName = data.Friend.Profile.displayName;
}

function Recipient(data) {
	this.id = data.Friend.id;
	this.displayName = data.Friend.Profile.displayName;
}

//View Model
function MessagesViewModel() {
	//Data
	var self = this;
	
	self.folder = ['New','Inbox','Sent'];
	self.currentFolder = ko.observable();
	
	self.newMessage = ko.observable(new NewMessage("","","n",""));
	self.message = ko.observable();
	self.inboxMessages = ko.observableArray([]);
	self.sentMessages = ko.observableArray([]);
	self.recipientList = ko.observableArray([]);
	
	self.recipientSelect = ko.observable(0);
	
	//Subscriptions
	self.recipientSelect.subscribe(function(newValue) {
		if(self.currentFolder() != "New")
			return;
		var selected = ko.utils.arrayFirst(self.recipientList(), function(recipient) {
			if(recipient.id==newValue)
				return true;
		});
		if(selected instanceof Recipient) {
			self.newMessage().recipients.push(new newMessageRecipient(selected));
			self.recipientList.remove(selected);
		}
	});
	
	//Behaviours 
	self.goToFolder = function(folder) { location.hash = folder };
	self.goToMessage = function(message) { location.hash = self.currentFolder() + '/' + message.id };
	self.replyToMessage = function(message) {
		var subject = "Re: " + message.subject;
		var recipients = new Array();
		var jsontext = { 'id' : message.sender.id, 'displayName': message.sender.Profile.displayName};
		recipients[0] = jsontext;
		var type = 'r';
		var ref = message.id;
		self.newMessage(new NewMessage(subject, recipients, type, ref));
		self.currentFolder("New");
		location.hash = "New";
	};
	self.forwardMessage = function(message) {
		var subject = "Fw: " + message.subject;
		var type = 'f';
		var ref = message.id;
		self.newMessage(new NewMessage(subject, "", type, ref));
		self.currentFolder("New");
		location.hash = "New";
	}
	self.deleteMessage = function(message) {
		$.ajax({
			url : "/messages/deletemsg",
			data : {
				mid : message.id
			},
			type : "POST",
			dataType : "text",
			success : function(result) {
				if(result == "1") {
					self.goToFolder("Inbox");
				}
			}
		});
	}
	self.removeMessageRecipient = function(recipient) {
		self.recipientList.push({id: recipient.id, displayName: recipient.displayName});
		self.newMessage().recipients.remove(recipient);
	}
	self.sendMessage = function(message) {
		if(message.recipients().length && message.subject != "" && message.content != "") {
			$.ajax({
				url : "/messages/send",
				data : ko.toJS(message),
				type : "POST",
				dataType: "json",
				success: function(result) {
					if(result.length === 0) {
						//Implement Failure Handler
					}
					else {
						self.goToFolder("Sent");
						self.newMessage(new NewMessage("","","n",""));
					}
				}
			});
		}
		else 
			return;
	}
	
	self.toggleRead = function(message) {
		var newStatus;
		if(message.read() == 1) newStatus = 0;
		else newStatus = 1;
		$.ajax({
			url : "/messages/toggleread",
			data : {
				mid : message.id,
				status : newStatus
			},
			type : "POST",
			dataType : "text",
			success : function(result) {
				if(result == "1") {
					message.read(newStatus);
				}
			}
		});
	}
	
	//Client-Side Routes
	Sammy(function() {
		this.get('#:folder', function() {
			self.currentFolder(this.params.folder);
			if(this.params.folder == "Inbox") {
				$.getJSON("/messages/loadinbox", function(allData) {
					var mappedMessages = $.map(allData.root, function(message) {
						return new InboxMessage(message);
					});
					self.inboxMessages(mappedMessages);
				});
			}
			else if(this.params.folder == "Sent") {
				$.getJSON("/messages/loadsent", function(allData) {
					var mappedMessages = $.map(allData.root, function(message) {
						return new SentMessage(message);
					});
					self.sentMessages(mappedMessages);
				});
			}
			else if(this.params.folder == "New") {
				$.getJSON("/profile/loadfriends", function(allData) {
					var mappedRecipients = $.map(allData.root, function(friend) {
						return new Recipient(friend);
					});
					self.recipientList(mappedRecipients);
				});
				$('.new-message .fields .expanding').expandingTextarea();
			}
		});
		
		this.get('#:folder/:messageId', function() {
			if(this.params.folder == "Inbox") self.currentFolder("ReceivedMessage");
			else if(this.params.folder == "Sent") self.currentFolder("SentMessage");
			$.getJSON("/messages/loadmessage?mid=" + this.params.messageId, function(data) {
				self.message(new Message(data));
			});
		});
	}).run();
	
	//Load Default Page
	self.goToFolder("Inbox");
	
};

ko.applyBindings(new MessagesViewModel(), $("#content")[0]);


