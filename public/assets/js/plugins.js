// usage: log('inside coolFunc', this, arguments);
// paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
window.log = function f() {
	log.history = log.history || [];
	log.history.push(arguments);
	if (this.console) {
		var args = arguments, newarr;
		args.callee = args.callee.caller;
		newarr = [].slice.call(args);
		if ( typeof console.log === 'object')
			log.apply.call(console.log, console, newarr);
		else
			console.log.apply(console, newarr);
	}
};

// make it safe to use console.log always
(function(a) {
	function b() {
	}

	for (var c = "assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","), d; !!( d = c.pop()); ) {
		a[d] = a[d] || b;
	}
})( function() {
	try {
		console.log();
		return window.console;
	} catch(a) {
		return (window.console = {});
	}
}());

//Custom Javascript Functions
function ISODateString(d) {
	function pad(n) {
		return n < 10 ? '0' + n : n
	}

	return d.getUTCFullYear() + '-' + pad(d.getUTCMonth() + 1) + '-' + pad(d.getUTCDate()) + 'T' + pad(d.getUTCHours()) + ':' + pad(d.getUTCMinutes()) + ':' + pad(d.getUTCSeconds()) + 'Z'
}

// place any jQuery/helper plugins in here, instead of separate, slower script files.
jQuery.fn.exists = function() {
	return this.length > 0;
};

// Knockout Custom Bindings

//AUTOCOMPLETE Binding
ko.bindingHandlers.autoComplete = {
    findSelectedItem: function (dataSource, binding, selectedValue) {
        var unwrap = ko.utils.unwrapObservable;
        //Go through the source and find the id, and use its label to set the autocomplete
        var source = unwrap(dataSource);
        var valueProp = unwrap(binding.optionsValue);

        var selectedItem = ko.utils.arrayFirst(source, function (item) {
            if (unwrap(item) === selectedValue)
                return true;
        }, this);

        return selectedItem;
    },
    buildDataSource: function (dataSource, labelProp, valueProp) {
        var unwrap = ko.utils.unwrapObservable;
        var source = unwrap(dataSource);
        var mapped = ko.utils.arrayMap(source, function (item) {
            var result = {};
            result.label = labelProp ? unwrap(item[labelProp]) : unwrap(item).toString();  //show in pop-up choices
            result.value = valueProp ? unwrap(item[valueProp]) : unwrap(item).toString();  //value
            return result;
        });
        return mapped;
    },
    init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
        var unwrap = ko.utils.unwrapObservable;
        var dataSource = valueAccessor();
        var binding = allBindingsAccessor();
        var valueProp = unwrap(binding.optionsValue);
        var labelProp = unwrap(binding.optionsText) || valueProp;
        var displayId = $(element).attr('id') + '-display';
        var displayElement;
        var options = {};

        if (binding.autoCompleteOptions) {
            options = $.extend(options, binding.autoCompleteOptions);
        }

        //Create a new input to be the autocomplete so that the label shows
        // also hide the original control since it will be used for the value binding
        $(element).hide();
        $(element).after('<input type="text" id="' + displayId + '" />')
        displayElement = $('#' + displayId);

        //handle value changing
        var modelValue = binding.value;
        if (modelValue) {
            var handleValueChange = function (event, ui) {
                var labelToWrite = ui.item ? ui.item.label : null
                var valueToWrite = ui.item ? ui.item.value : null;
                //The Label and Value should not be null, if it is
                // then they did not make a selection so do not update the
                // ko model
                if (labelToWrite && valueToWrite) {
                    if (ko.isWriteableObservable(modelValue)) {
                        //Since this is an observable, the update part will fire and select the
                        //  appropriate display values in the controls
                        modelValue(valueToWrite);
                    } else {  //write to non-observable
                        if (binding['_ko_property_writers'] && binding['_ko_property_writers']['value']) {
                            binding['_ko_property_writers']['value'](valueToWrite);
                            //Because this is not an observable, we have to manually change the controls values
                            // since update will not do it for us (it will not fire since it is not observable)
                            displayElement.val(labelToWrite);
                            $(element).val(valueToWrite);
                        }
                    }
                }
                //They did not make a valid selection so change the autoComplete box back to the previous selection
                else {
                    var currentModelValue = unwrap(modelValue);
                    //If the currentModelValue exists and is not nothing, then find out the display
                    // otherwise just blank it out since it is an invalid value
                    if (!currentModelValue)
                        displayElement.val('');
                    else {
                        //Go through the source and find the id, and use its label to set the autocomplete
                        var selectedItem = ko.bindingHandlers.autoComplete.findSelectedItem(dataSource, binding, currentModelValue);           

                  //If we found the item then update the display
                        if (selectedItem) {
                            var displayText = labelProp ? unwrap(selectedItem[labelProp]) : unwrap(selectedItem).toString();
                            displayElement.val(displayText);
                        }
                        //if we did not find the item, then just blank it out, because it is an invalid value
                        else {
                            displayElement.val('');
                        }
                    }
                }

                return false;
            };

            var handleFocus = function (event, ui) {
                $(displayElement).val(ui.item.label);
                return false;
            };

            options.change = handleValueChange;
            options.select = handleValueChange;
            options.focus = handleFocus;
            //options.close = handleValueChange;
        }

        //handle the choices being updated in a Dependant Observable (DO), so the update function doesn't
        // have to do it each time the value is updated. Since we are passing the dataSource in DO, if it is
        // an observable, when you change the dataSource, the dependentObservable will be re-evaluated
        // and its subscribe event will fire allowing us to update the autocomplete datasource
        var mappedSource = ko.dependentObservable(function () {
            return ko.bindingHandlers.autoComplete.buildDataSource(dataSource, labelProp, valueProp);
        }, viewModel);
        //Subscribe to the knockout observable array to get new/remove items
        mappedSource.subscribe(function (newValue) {
            displayElement.autocomplete("option", "source", newValue);
        });

        options.source = mappedSource();

        displayElement.autocomplete(options);
    },
    update: function (element, valueAccessor, allBindingsAccessor, viewModel) {
        //update value based on a model change
        var unwrap = ko.utils.unwrapObservable;
        var dataSource = valueAccessor();
        var binding = allBindingsAccessor();
        var valueProp = unwrap(binding.optionsValue);
        var labelProp = unwrap(binding.optionsText) || valueProp;
        var displayId = $(element).attr('id') + '-display';
        var displayElement = $('#' + displayId);
        var modelValue = binding.value;

        if (modelValue) {
            var currentModelValue = unwrap(modelValue);
            //Set the hidden box to be the same as the viewModels Bound property
            $(element).val(currentModelValue);
            //Go through the source and find the id, and use its label to set the autocomplete
            var selectedItem = ko.bindingHandlers.autoComplete.findSelectedItem(dataSource, binding, currentModelValue);
            if (selectedItem) {
                var displayText = labelProp ? unwrap(selectedItem[labelProp]) : unwrap(selectedItem).toString();
                displayElement.val(displayText);
            }
        }
    }
};

//EXPANDING TEXTAREA Binding
ko.bindingHandlers.expandingTextarea = {
	init: function(element) {
		$(element).expandingTextarea();
	}
};

//GALLERY MODAL Binding
ko.bindingHandlers.galleryModal = {
	init: function(element) {
		$(element).fancybox({
			openEffect: 'none',
			closeEffect: 'none'
		});
	}
};

//UPLOADER Binding
ko.bindingHandlers.uploader = {
	completeHandler : function(event, id, filename, response) {
		file = $(this).fineUploader('getItemByFileId', id);
		imageUrl = userGalleryImagesUrl + response.filename;
		$(this).find(file).find('.image_uploaded').html('<img src="'+imageUrl+'" width="140" height="140"/>');
		ProfileVM.addStoryPhoto(response.filename);
	},
	init: function(element) {
		$(element).fineUploader({
			debug: true,
			request: {
				endpoint : '/profile/uploadstoryphotos',
				inputName : 'filename'
			},
			validation : {
				sizeLimit : 5242880
			},
			text : {
				uploadButton : 'Upload Photos'
			},
			template: '<div class="drop_zone"><span>{dragZoneText}</span></div>' + 
				'<input name="upload_photos" type="button" class="submit_button upload_button" value="{uploadButtonText}"/>' +
				'<div class="images_uploaded"></div>',
				
			fileTemplate: '<div class="image_upload">' + 
				'<div class="loading"></div>' + 
				'<div class="progress_bar"></div>' +
				'<div class="image_uploaded"></div>' +
				'<div class="image_failed"></div>' + 
				'<div class="retry">{retryButtonText}</div>' +
				'<div class="upload_size"></div>' +
				'<div class="upload_cancel">{cancelButtonText}</div>' + 
				'<div class="upload_status_text">{statusText}</span>' +
				'</div>',
				
			classes: {
				button: 'upload_button',
				drop: 'drop_zone',
				list: 'images_uploaded',
				progressBar: 'progress_bar',
				file: 'image_uploaded',
				spinner: 'loading',
				retry: 'retry',
				success: 'upload_success',
				fail: 'upload_fail',
				retrying: 'retrying',
				retryable: 'retryable',
				size: 'upload_size',
				cancel: 'upload_cancel',
				statusText: 'upload_status_text'
			}
		}).on('complete', ko.bindingHandlers.uploader.completeHandler);
	}
};
