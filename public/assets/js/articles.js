/* Author: Jarrod Placide-Raymond
 * 
 */
//Data Structures
function Comment(data) {
	this.id = data.id;
	var da = data.date.split(/[- :]/);
	this.date = new Date(da[0], da[1]-1, da[2], da[3], da[4], da[5]);
	this.date = this.Date.toDateString();
	this.userPhoto = profileImagesUrl + data.User.Profile.photo;
	this.displayName = data.User.Profile.Profile.displayName;
	this.content = data.content;
}
