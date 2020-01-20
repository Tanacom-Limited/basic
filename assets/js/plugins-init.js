$('.has-tooltip').tooltip();

$(function () { 
  $('[data-toggle="tooltip"]').tooltip({trigger: 'manual'}).tooltip('show');
});

$(function() {
	$(".switch-checkbox").bootstrapSwitch();
});

$('.open-page-popover').popover({
	title : '<div>-<a class="close" data-dismiss="alert">&times;</button></a>',
	template: '<div class="popover inline-page" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
	html: true,
	container: 'body',
    content: function(){
        var divID =  "tmp-id-" + $.now();
        return loadPopOverContent($(this).attr('href'), divID);
    }
});

function loadPopOverContent(link, divID){
    $.ajax({
        url: link,
        success: function(response){
            $('#' + divID).html(response);
        }
	});
	//var footer = '<div class="card-footer text-right"><a class="btn btn-sm btn-secondary close-btn">&times;</a></div>';
    return '<div class="reset-grids" id="'+ divID +'">' + pageLoadingIndicator + '</div>';// + footer;
}

(function() {
	var forms = document.getElementsByClassName('needs-validation');
	// Loop over them and prevent submission
	var validation = Array.prototype.filter.call(forms, function(form) {
		form.addEventListener('submit', function(event) {
			if (form.checkValidity() === false) {
				event.preventDefault();
				event.stopPropagation();
			}
			form.classList.add('was-validated');
			$("input:required:invalid").parents('.dropzone').css("borderColor", "red");
			$("input:required:invalid").parents('.custom-file').find('.custom-file-label').css("borderColor", "red");
			$("textarea:required:invalid").parents('.form-group').find('.note-editor').css("borderColor", "red");
		}, false);
	});
})();


$.fn.editableform.buttons = '<button type="submit" class="btn btn-sm btn-primary editable-submit">&check;</button><button type="button" class="btn btn-sm btn-secondary editable-cancel">&times;</button>';
$(function(){
	$.fn.editable.defaults.ajaxOptions = {type: "post"};
	$.fn.editable.defaults.params = {csrf_token : csrfToken};
	$.fn.editable.defaults.emptytext = '...';
	$.fn.editable.defaults.textFieldName = 'label';
	
	$('.is-editable').editable();
	
	$(document).on('click', '.inline-edit-btn', function(e){
		e.stopPropagation();
		$(this).closest('td').find('.make-editable').editable('toggle');
	});
});





	
	$('.datepicker').flatpickr({
		altInput: true, 
		allowInput:true,
		onReady: function(dateObj, dateStr, instance) {
			var $cal = $(instance.calendarContainer);
			if ($cal.find('.flatpickr-clear').length < 1) {
				$cal.append('<button class="btn btn-light my-2 flatpickr-clear">Clear</button>');
				$cal.find('.flatpickr-clear').on('click', function() {
					instance.clear();
					instance.close();
				});
			}
		}
	});




Dropzone.autoDiscover = false;
$(function(){
	$('.dropzone').each(function(){
		var uploadUrl = $(this).attr('path') || setPathLink('filehelper/uploadfile/');
		var multiple = $(this).data('multiple') || false;
		var limit = $(this).attr('maximum') || 1;
		var size = $(this).attr('filesize') || 10;
		var extensions = $(this).attr('extensions') || "";
		var resizewidth = $(this).attr('resizewidth') || null;
		var resizeheight = $(this).attr('resizeheight') || null;
		var resizequality = $(this).attr('resizequality') || null;
		var resizemethod = $(this).attr('resizemethod') || null;
		var resizemimetype = $(this).attr('resizemimetype') || null;
		var dropmsg = $(this).attr('dropmsg') || 'Choose files or drag and drop files to upload';
		var autoSubmit = $(this).attr('autosubmit') || true;
		var btntext = $(this).attr('btntext') || 'Choose file';
		var fieldname = $(this).attr('fieldname') || "";
		var input = $(this).attr('input');
		$(this).dropzone({
			url: uploadUrl ,
			maxFilesize:size,
			uploadMultiple: multiple,
			parallelUploads:1,
			paramName:'file',
			maxFiles:limit,
			resizeWidth: resizewidth,
			resizeHeight: resizeheight,
			resizeQuality: resizequality,
			resizeMethod: resizemethod,
			resizeMimeType: resizemimetype,
			acceptedFiles: extensions,
			addRemoveLinks:true,
			params:{
				csrf_token : csrfToken,
				fieldname : fieldname,
			},
			init: function() {
				this.on('addedfile', function(file) {
					//if allow multiple file upload is allowed, then validate maximum number of files
					var inputFiles = $(input).val();
					var inputFilesLen = 0;
					if(inputFiles){
						inputFilesLen = inputFiles.split(',').length;
					}
					var totalFiles = this.files.length + inputFilesLen;
					if ( totalFiles  > limit) {
						if(multiple){
							$(file.previewElement).closest('.dropzone').find('.dz-file-limit').text('Maximum upload limit reached');
							this.removeFile(file);
						}
						else if(limit == 1){
							if(!inputFiles){
								this.removeFile(this.files[0]);
							}
						}
					}
				});
				
				this.on("success", function(file, responseText) {
					if(responseText){
						if(limit == 1){
							$(input).val(responseText);
						}
						else{
							var files = $(input).val();
							files = files + ',' + responseText;
							files = files.trim().trimLeft(',')
							$(input).val(files);
						}
					}
				});
				
				this.on("removedfile", function(file) {
					if(file.xhr){
						var filename = file.xhr.responseText;
						var files = $(input).val();
						var arrFiles = files.split(',');
						while (arrFiles.indexOf(filename) !== -1) {
							arrFiles.splice(arrFiles.indexOf(filename), 1);
						}
						
						$(input).val(arrFiles.toString());
						var remUrl = setPathLink('filehelper/removefile/')
						$.ajax({
							type:'POST',
							url: remUrl,
							data : {filepath: filename, csrf_token: csrfToken},
							success : function (data) {
								console.log(data);
							}
						});
					}
					var inputFiles = $(input).val();
					if(inputFiles){
						var inputFilesLen = inputFiles.split(',').length;
						if (  limit > inputFilesLen){
							$(input).closest('.dropzone').find('.dz-file-limit').text('');
						}
					}
				});
				
				this.on("complete", function (file) {
					
					//do something all files uploaded
				});
			},
			dictDefaultMessage: dropmsg,
			/* dictRemoveFile:'' */
		});
	});
});



