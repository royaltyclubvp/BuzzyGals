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
}

function newComment(data) {
	this.id = data.id;
	var da = data.date.split(/[- :]/);
	this.date = new Date(da[0], da[1]-1, da[2], da[3], da[4], da[5]);
	this.date = ISODateString(this.date);
	this.userPhoto = profileImagesUrl + data.User.Profile.photo;
	this.displayName = data.User.Profile.displayName;
	this.content = data.content;
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
			self.commentCount(self.comments().length)
		}
	}
	self.removeComment = function(comment) {
		result = ArticleVM.removeComment(comment.id);
		if(result)
			self.comments.remove(comment);
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
}

//View model
ArticleVM = new (function() {
	//Data
	var self = this;
	
	self.article = ko.observable();
	
	//Behaviours
	//
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
	
	self.loadArticle = function(article, bookmarked) {
		self.article(new Article(article, bookmarked));
	}
	
	self.loadArticle(article, bookmarked);
});

ko.applyBindings(ArticleVM, $("#content")[0]);
