/* Author: Jarrod Placide-Raymond
 * 
 */
//Data Structures
function Comment(data) {
	this.id = data.id;
	var da = data.date.split(/[- :]/);
	this.date = new Date(da[0], da[1]-1, da[2], da[3], da[4], da[5]);
	this.date = this.Date.toDateString();
	this.userPhoto = profileImagesUrl + data.UserProfile.photo;
	this.displayName = data.UserProfile.displayName;
	this.content = data.content;
}

function Article(data, bookmarked)	{
	var self = this;
	self.id = data.id;
	self.domID = "article_id_" + self.id;
	self.titleImageUrl = articleImagesUrl + data.title_image;
	self.title = data.title;
	var da = data.date.split(/[- :]/);
	self.date = new Date(da[0], da[1]-1, da[2], da[3], da[4], da[5]);
	self.authorName = data.Author.name;
	self.bookmarked = ko.observable(bookmarked);
	self.content = data.content;
	self.commentCount = ko.observable(data.Comments.length);
	self.commentText = (self.commentComment() == 1) ? 'Comment' : 'Comments';
	self.comments = ko.observableArray([]);
	for(var i=0; i < self.commentCount; i++) {
		self.comments.push(new Comment(data.Comments[i]));
	}
	self.addComment = function(story) {
		result = ArticleVM.addComment(story.newComment);
		if(result.id) {
			self.comments.push(new Comment(result));
			self.commentCount(self.comments().length)
		}
		self.newComment("");	
	}
	self.removeComment = function(comment) {
		result = ArticleVM.removeComment(comment);
		if(result)
			self.comments.remove(comment);
	}
	self.commentsVisible = ko.observable(0);
	self.newComment = ko.observable();
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
	self.addComment = function(storyid, comment) {
		success = "";
		$.ajax({
			url : "/profile/addstorycomment",
			data : {
				story : storyid,
				comment : comment
			},
			type : "POST",
			dataType : 'json',
			success : function(result) {
				if(result.root.id)
					success = result.root;
			}
		});
		return success;
	}
	
	self.bookmark = function(article) {
		success = false;
		$.ajax({
			url : "/profile/addarticlebookmark",
			data : {
				article : article.id
			},
			type : "POST",
			dataType : "text",
			success : function(result) {
				if(result == "1")
					success = true;
			}
		});
		return success;
	}
	
	self.removebookmark = function(article) {
		success = false;
		$.ajax({
			url : "/profile/removearticlebookmark",
			data : {
				article : bookmark.id
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
	
	self.loadArticle = function(article, bookmarked) {
		self.article(new Article(article, bookmarked));
	}
	
	self.loadArticle(article, bookmarked);
});

ko.applyBindings(ArticleVM, $("#content")[0]);
