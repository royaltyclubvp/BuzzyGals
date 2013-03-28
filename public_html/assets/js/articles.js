/* Author: Jarrod Placide-Raymond
 * 
 */
//Data Structures
function Comment(data) {
	this.id = data.id;
	var da = data.date.split(/[- :]/);
	this.date = new Date(da[0], da[1]-1, da[2], da[3], da[4], da[5]);
	this.date = ISODateString(this.date);
	this.userPhoto = profileImagesUrl + data.UserProfile.photo;
	this.displayName = data.UserProfile.displayName;
	this.content = data.content;
	this.owned = (data.user == userId) ? true : false;
}

function newComment(data) {
	this.id = data.id;
	var da = data.date.split(/[- :]/);
	this.date = new Date(da[0], da[1]-1, da[2], da[3], da[4], da[5]);
	this.date = ISODateString(this.date);
	this.userPhoto = profileImagesUrl + data.User.Profile.photo;
	this.displayName = data.User.Profile.displayName;
	this.content = data.content;
	this.owned = (data.user == userId) ? true : false;
}

function Article(data, bookmarked)	{
	var self = this;
	self.id = data.id;
	self.domID = "article_id_" + self.id;
	self.titleImageUrl = articleImagesUrl + data.title_image;
	self.title = data.title;
	var da = data.date.split(/[- :]/);
	self.date = new Date(da[0], da[1]-1, da[2]);
	self.date = self.date.toDateString();
	self.authorName = data.Author.name;
	self.bookmarked = ko.observable(bookmarked);
	self.content = data.content;
	self.commentCount = ko.observable(data.Comments.length);
	self.commentText = (self.commentCount() == 1) ? 'Comment' : 'Comments';
	self.comments = ko.observableArray([]);
	for(var i=0; i < self.commentCount(); i++) {
		self.comments.push(new Comment(data.Comments[i]));
	}
	self.addComment = function() {
		result = ArticleVM.addComment(self.id, self.newComment);
		if(result.id) {
			self.newComment("");
			self.comments.push(new newComment(result));
			self.commentCount(self.comments().length);
			setTimeout(ArticleVM.timeAgo, 2000);
		}
	}
	self.removeComment = function(comment) {
		result = ArticleVM.removeComment(self.id, comment.id);
		if(result) {
			self.comments.remove(comment);
			self.commentCount(self.comments().length);
		}
	}
	self.bookmark = function() {
		result = ArticleVM.bookmark(self.id);
		if(result)
			self.bookmarked(1);
	}
	self.removebookmark = function() {
		result = ArticleVM.removebookmark(self.id);
		if(result)
			self.bookmarked(0);
	}
	self.commentsVisible = ko.observable(0);
	self.newComment = ko.observable("");
	self.showAddCommentButton = ko.observable(false);
	self.showComments = function() {
		self.commentsVisible(1);
	}
	self.flag = function() {
		result = ArticleVM.flag(self.id, ArticleVM.chosenReason().reason);
		if(result=="-1") {
			
		}
		else if(result=="1") {
			ArticleVM.showFlagResponse(true, ArticleVM.chosenReason().reason);
			ArticleVM.reasons(false);
			ArticleVM.reported(true);
			ArticleVM.chosenReason(false);
		}
	}
}

function flagResponse(reason, success) {
	var self = this;
	self.success = ko.observable(success);
	self.reason = ko.observable(reason);
}

//View model
ArticleVM = new (function() {
	//Data
	var self = this;
	
	self.article = ko.observable();
	self.reportReasons = [
		{ title: "Sexual content", reason: "Includes graphic sexual activity, nudity, and other sexual content" },
		{ title: "Violent or repulsive content", reason: "Violent or graphic content" },
		{ title: "Hateful or abusive content", reason: "Content that promotes hatred against protected groups" },
		{ title: "Harmful dangerous acts", reason: "Content that includes acts that may result in physical harm" },
		{ title: "Child abuse", reason: "Content that includes sexual, predatory or abusive communications towards minors" },
		{ title: "Spam or misleading", reason: "Content that is massively posted or otherwise misleading in nature" },
		{ title: "Infringes my rights", reason: "Privacy, copyright and other legal complaints" },
	];
	self.chosenReason = ko.observable(false);
	self.reported = ko.observable(false);
	self.reasons = ko.observable(false);
	self.flagResponseMessage = ko.observable(new flagResponse("", false));
	
	//Behaviours
	
	self.timeAgo = function() {
		$('time.date').timeago();
	}
	
	self.showReasons = function() {
		self.reasons(true);
	}
	
	self.addComment = function(articleid, comment) {
		success = "";
		$.ajax({
			url : "/townhalls/addarticlecomment",
			data : {
				article : articleid,
				comment : comment
			},
			type : "POST",
			async : false,
			dataType : 'json',
			success : function(result) {
				if(result.root.id)
					success = result.root;
			}
		});
		return success;
	}
	
	self.removeComment = function(articleid, commentid) {
		success = false,
		$.ajax({
			url : "/townhalls/deletestoryownedcomment",
			data : {
				article : articleid,
				comment : commentid
			},
			type : "POST",
			async : false,
			dataType : 'text',
			success : function(result) {
				if(result ==  "1") {
					success = true;
				}
			}
		});
		return success;
	}
	
	self.bookmark = function(id) {
		success = false;
		$.ajax({
			url : "/profile/addarticlebookmark",
			data : {
				article : id
			},
			type : "POST",
			async : false,
			dataType : "text",
			success : function(result) {
				if(result == "1")
					success = true;
			}
		});
		return success;
	}
	
	self.removebookmark = function(id) {
		success = false;
		$.ajax({
			url : "/profile/removearticlebookmark",
			data : {
				article : id
			},
			type : "POST",
			async : false,
			dataType : "text",
			success : function(result) {
				if (result == "1")
					success = true;
			}
		});
		return success;	
	}
	
	self.flag = function(id, reason) {
		success = false;
		$.ajax({
			url : "/townhalls/flagarticle",
			data : {
				article : id,
				reason: reason
			},
			type : "POST",
			async : false,
			dataType : "text",
			success : function(result) {
				success = result;
			}
		});
		return success;
	}
	
	self.showFlagResponse = function(response, reason) {
		self.flagResponseMessage(new flagResponse(reason, response));
		$.fancybox.open(
			{
				href : '#flag_response'
			},
			[{
				width : '500px',
				maxHeight : 300
			}]
		);
	}
	
	self.closeModal = function() {
		$.fancybox.close();
	}
	
	self.loadArticle = function(article, bookmarked) {
		self.article(new Article(article, bookmarked));
	}
	
	self.loadArticle(article, bookmarked);
});

ko.applyBindings(ArticleVM, $("#content")[0]);
