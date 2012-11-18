/**
 * Author: Jarrod Placide-Raymond
 * 
 */

//Data Structures
function Article(data) {
	this.id = data.id;
	this.avatarImage = articleImagesUrl + data.avatar_image;
	this.title = data.title;
	this.url = articleBaseUrl + data.uri;
	this.author = data.Author.name;
	this.description = data.description;
}

//View Model
function TownhallViewModel() {
	//Data
	var self = this;
	
	self.topicid;
	self.newestArticles = ko.observableArray([]);
	self.popularArticles = ko.observableArray([]);
	self.articleTotal = ko.observable();
	self.currentPageRetrieved = ko.observable();
	
	//Behaviours
	self.loadMoreNewArticles = function() {
		var newPage = self.currentPageRetrieved() + 1;
		$.ajax({
			url : "/townhalls/loadmorenewest",
			data : {
				topic : self.topicid,
				page : newPage
			},
			type: "GET",
			dataType : "json",
			success: function(result) {
				if(result.total != self.articleTotal()) {
					$.ajax({
						url: "/townhalls/loadmorenewest",
						data : {
							topic : self.topicid,
							page : 1,
							noPerPage : newPage * 5 
						},
						type : "GET",
						dataType : "json",
						success: function(newResult) {
							var mappedArticles = $.map(newResult.articles, function(article) {
								return new Article(article);
							});
							self.newestArticles(mappedArticles);
							self.articleTotal(newResult.total);
							self.currentPageRetrieved(Math.ceil(self.newestArticles().length/5));
						}
					});
				}
				else {
					var mappedArticles = $.map(result.articles, function(article) {
						return new Article(article);
					});
					self.newestArticles.push(mappedArticles);
					self.currentPageRetrieved(Math.ceil(self.newestArticles().length/5));
				}
			}
		});
	}

	self.loadInitialArticles = function(newestArticles, popularArticles, topicid) {
		var newestMapped = $.map(newestArticles.articles, function(article) {
			return new Article(article);
		});
		self.newestArticles(newestMapped);
		self.articleTotal(newestArticles.total);
		var popularMapped = $.map(popularArticles, function(article) {
			return new Article(article);
		});
		self.popularArticles(popularMapped);
		self.currentPageRetrieved(1);
		self.topicid = topicid;
	}
	
	//Load Initial Data
	self.loadInitialArticles(newestArticles, popularArticles, topicid);
};

ko.applyBindings(new TownhallViewModel(), $("#content")[0]);
