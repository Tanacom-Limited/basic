String.prototype.trimLeft = function(charlist) {
  if (charlist === undefined)
	charlist = "\s";
  return this.replace(new RegExp("^[" + charlist + "]+"), "");
};

String.prototype.trimRight = function(charlist) {
  if (charlist === undefined)
	charlist = "\s";
  return this.replace(new RegExp("[" + charlist + "]+$"), "");
};

function valToArray(val) {
	if(val){
		if(Array.isArray(val)){
			return val;
		}
		else{
			return val.split(",");
		}
	}
	else{
		return [];
	}
};

function debounce(fn, delay) {
  var timer = null;
  return function () {
	var context = this, args = arguments;
	clearTimeout(timer);
	timer = setTimeout(function () {
	  fn.apply(context, args);
	}, delay);
  };
}

function extend(obj, src) {
	for (var key in src) {
		if (src.hasOwnProperty(key)) obj[key] = src[key];
	}
	return obj;
}

function setPathLink(path , queryObj){
	var url;
	if(queryObj){
		var str = [];
		for(var k in queryObj){
			var v = queryObj[k]
			if (queryObj.hasOwnProperty(k) && v !== '') {
				str.push(encodeURIComponent(k) + "=" + encodeURIComponent(v));
			} 
		}
		var qs = str.join("&");
		if(path.indexOf('?') > 0){
			url = path + '&' + qs;  
		}
		else{
			url = path + '?' + qs;  
		}
	}
	else{
		url = siteAddr + path;
	}
	return url;
}

function randomColor() {
	var letters = '0123456789ABCDEF';
	var color = '#';
	for (var i = 0; i < 6; i++) {
		color += letters[Math.floor(Math.random() * 16)];
	}
	return color;
}

function hideFlashMsg(){
	var elem=$('#flashmsgholder');
	if(elem.length>0){
		var duration=elem.attr("data-show-duration");
		if(duration>0){
			window.setTimeout(function(){
				elem.fadeOut();
			},duration)
		}
	}
}

var pageLoadingIndicator = $('#page-loading-indicator').html(); //loding indicator used for ajax load content
var pageSavingIndicator = $('#page-saving-indicator').html(); //saving indicator used for ajax submit form
var inlineLoadingIndicator = $('#inline-loading-indicator').html(); //inline loading indicator

$(document).ready(function() {
	hideFlashMsg();//hides page flash msg after page navigate.
	
});

$(document).on('click', '.toggle-check-all', function(){
	var p = $(this).closest('table').find('.optioncheck');
	p.prop("checked",$(this).prop("checked"));
});

$(document).on('click', '.optioncheck, .toggle-check-all', function(){
	var sel_ids =$(this).closest('.page').find("input.optioncheck:checkbox:checked").map(function(){
	  return $(this).val();
	}).get();
	if(sel_ids.length>0){
		 $(this).closest('.page').find('.btn-delete-selected').removeClass('d-none');
	}
	else{
		$(this).closest('.page').find('.btn-delete-selected').addClass('d-none');
	}
});

$(document).on('click', '.btn-delete-selected', function(){
	var recordDeleteMsg = $(this).data("prompt-msg");
	var displayStyle = $(this).data("display-style");
	if(!recordDeleteMsg){
		recordDeleteMsg="Are you sure you want to delete selected records?";
	}
	var sel_ids =$(this).closest('.page').find("input.optioncheck:checkbox:checked").map(function(){
	  return $(this).val();
	}).get();
	if(sel_ids.length>0){
		var url = $(this).data('url');
		url = url.replace("{sel_ids}",sel_ids);
		if(displayStyle == 'confirm'){
			if(confirm(recordDeleteMsg)){
				window.location = url;
			}
		}
		else if(displayStyle == 'modal'){
			$('#delete-record-modal-msg').html(recordDeleteMsg);
			$('#delete-record-modal-confirm').modal('show');
			$('#delete-record-modal-btn').attr('href', url);
			e.preventDefault();
		}
	}
	else{
		alert('No Record Selected');
	}
});

$(document).on('click', '.record-delete-btn', function(e){
	var recordDeleteMsg = $(this).data("prompt-msg");
	var displayStyle = $(this).data("display-style");

	if(!recordDeleteMsg){
		recordDeleteMsg="Are you sure you want to delete this record?";
	}
	if(displayStyle == 'confirm'){
		if(!confirm(recordDeleteMsg)){
			e.preventDefault();
		}
	}
	else if(displayStyle == 'modal'){
		$('#delete-record-modal-msg').html(recordDeleteMsg);
		$('#delete-record-modal-confirm').modal('show');
		$('#delete-record-modal-btn').attr('href', $(this).attr('href'));
		e.preventDefault();
	}
});

$( document ).on( "click", "#delete-record-modal-btn", function() {
	$('#delete-record-modal-confirm').modal('hide');
});

$(document).on('click', '.removeEditUploadFile', function(e){
	 // hidden input that contains all the file
	var holder = $(this).closest(".uploaded-file-holder");
	var inputid = $(this).attr("data-input");
	var inputControl = $(inputid);
	var filepath = $(this).attr('data-file');
	var filenum = $(this).attr('data-file-num');
	var srcTxt = inputControl.val();
	if(srcTxt){
		var arrSrc = srcTxt.split(",");
		arrSrc.forEach(function(src,index){
			if(src == filepath){
				arrSrc.splice(index,1);
			}
		});
	}
	holder.find("#file-holder-"+filenum).remove();
	var ty = arrSrc.join(",");
	inputControl.val(ty);
});

$(document).on('click', '.open-page-modal', function(e){
	e.preventDefault();
	var dataURL = $(this).attr('href');
	var modal = $(this).next('.modal');
	modal.modal({show:true});
	modal.find('.modal-body').html(pageLoadingIndicator).load(dataURL);
});

$(document).on('click', 'a.page-modal', function(e){
	e.preventDefault();
	var dataURL = $(this).attr('href');
	var modal = $('#main-page-modal');
	modal.modal({show:true});
	modal.find('.modal-body').html(pageLoadingIndicator).load(dataURL);
});

$(document).on("click", ".popover .close-btn, .popover .close" , function(){
	$(this).parents(".popover").popover('hide');
});

$(document).on('click', '.open-page-popover', function(e){
	$('.open-page-popover').not(this).popover('hide');
	e.preventDefault();
});

$(document).on('click', '.open-page-inline', function(e){
	e.preventDefault();
	var dataURL = $(this).attr('href');
	if($(this).closest('tr').length != 0){
		var tbRow = $(this).closest('tr');
		var loaded = tbRow.attr('loaded');
		var colspan = tbRow.children('td,th').length;
		if(!loaded){
			tbRow.attr('loaded', true);
			var newRow = $('<tr class="child-row"><td colspan="' + colspan + '"><div class="row justify-content-center"><div class="col-md-6"><div class="content reset-grids inline-page">' + pageLoadingIndicator + '</div></div></div></td></tr>');
			tbRow.after(newRow); 
			newRow.find('.content').load(dataURL, function(responseText, status, req){
				if(status == 'error'){
					tbRow.removeAttr('loaded');
				}
			});
		}
		else{
			tbRow.next().toggle();	
		}
	}
	else{
		var container = $(this).closest('.inline-page');
		var loaded = container.attr('loaded');
		var page = container.find('.page-content');
		if(!loaded){
			container.attr('loaded', true);
			page.html(pageLoadingIndicator).load(dataURL, function(responseText, status, req){
				if(status == 'error'){
					container.removeAttr('loaded');
				}
			});
		}
		page.toggleClass('d-none');
	}
});

$(document).on('change', '.custom-file-input', function(){
	var fileName = $(this).val().split('\\').pop();
	$(this).siblings('.custom-file-label').addClass('selected').html(fileName);
});

$(document).on('click', '.export-btn', function(e){
	var html = $(this).closest('.page').find('.page-records').html();
	var title = $(this).closest('.page').find('.record-title').html();
	$('#exportformdata').val(html);
	$('#exportformtitle').val(title);
	$('#exportform').submit();
});

$(document).on('submit', 'form.multi-form', function(e){
	var isAllRowsValid = true;
	var form = $(this)[0];
	$(form).find('tr.input-row').each(function(e){
		var validateRow = false;
		$(this).find('td').each(function(e){
			var inp = $(this).find('input.form-control,select,textarea');
			if(inp.val()){
				validateRow = true;
				return true;
			}
		});
		
		if(validateRow == true){
			$(this).find('input,select,textarea').each(function(e){
				var elem = $(this)[0];
				if(!elem.checkValidity()){
					isAllRowsValid = false;
					return true;
				}
			});
			if(isAllRowsValid == false){
				$(this).addClass('was-validated')
			}
			else{
				$(this).removeClass('was-validated')
			}
		}
	});
	
	if(isAllRowsValid == false){
		e.preventDefault();
		//form.reportValidity();
		e.preventDefault();
	}
});

$(document).on('blur', '.ctrl-check-duplicate', function(){
	var inputElem = $(this)
	var val = inputElem.val();
	var apiUrl = inputElem.data("url");
	var elemCheckStatus = inputElem.closest('.form-group').find('.check-status');
	
	var loadingMsg = inputElem.data('loading-msg');
	var availableMsg = inputElem.data('available-msg');
	var notAvailableMsg = inputElem.data('unavailable-msg');
	
	elemCheckStatus.html('<small class="text-muted">' + loadingMsg + '</small>');
	if(val){
		$.ajax({
			url : setPathLink(apiUrl + val),
			success : function(result) {
				if(result == true) {
					inputElem.addClass('is-invalid');
					elemCheckStatus.html('<small class="text-danger">' + notAvailableMsg + '</small>');
				}
				else{ 
					inputElem.removeClass('is-invalid').addClass('is-valid');
					elemCheckStatus.html('<small class="text-success">' + availableMsg + '</small>');
				}
			},
			error : function(err) {
				elemCheckStatus.html('');
				console.log(err);
			}
		});
	}
	else{
		elemCheckStatus.html('');
		inputElem.removeClass('is-valid').removeClass('is-valid');
	}
});

$(document).on('change', '[data-load-select-options]', function(e){
	var selectElem = $(this);
	var val = selectElem.val();
	var path = selectElem.data('load-path');
	path = path + '/' + encodeURIComponent(val);
	var selectID =  selectElem.data('load-select-options');
	$(selectID).html('<option value="">Loading...</option>');
	var placeholder = $(selectID).attr('placeholder') || 'Select a value...';
	$.ajax({
		type: 'GET',
		url: path,
		dataType: 'json',
		success: function(data){
			if($(selectID).hasClass('selectize')){
				$(selectID).each(function() {
					if (this.selectize) {
						this.selectize.clear();
						this.selectize.clearOptions();
						for (var i = 0; i < data.length; i++) {
							this.selectize.addOption({value:data[i].value, text: data[i].label });
						}
					}
				}); 
			}
			else{
				var options = '<option value="">' + placeholder +  '</option>';
				for (var i = 0; i < data.length; i++) {
					options += '<option value="' + data[i].value + '">' + data[i].label + '</option>';
				}
				$(selectID).html(options);
			}
		},
		error: function(data) {
			var options = '<option value="">' + placeholder +  '</option>';
			$(selectID).html(options);
		},
	});
});

$(document).on('change', '[data-load-check-options]', function(e){
	var val = $(this).val();
	var path = $(this).data('load-path');
	path = path + '/' + encodeURIComponent(val);
	var targetID =  $(this).data('load-check-options');
	var templateID =  $(this).data('template');
	var targetElem = $(targetID);
	var templateHtml = $(templateID).html();
	targetElem.html(inlineLoadingIndicator);
	$.ajax({
		type: 'GET',
		url: path,
		dataType: 'json',
		success: function (data){
			targetElem.html("");
			for (var i = 0; i < data.length; i++) {
				var option = $(templateHtml);
				option.find('input').val(data[i].value);
				option.find('.input-label-text').html(data[i].label);
				targetElem.append(option);
			}
		},
		error: function (data) {
			targetElem.html('...');
		},
	});
});

$('.form-group').on("click",'input:checkbox',function(){          
    checkboxValidate($(this).attr('name'));
});
function checkboxValidate(name){
	var checkElem = $('input[name="'+name+'"]:checked');
    var min = 1 //minumum number of boxes to be checked for this form-group
    if(checkElem.length < min){
        $('input[name="'+name+'"]').prop('required',true);
    }
    else{
        $('input[name="'+name+'"]').prop('required',false);
    }
}

$(document).on('submit', '.inline-page form', function(e){
	var formElem = $(this);
	var savingIndicator  = formElem.find('.form-ajax-status');
	savingIndicator.html(pageSavingIndicator);
	savingIndicator.show();
	
	$.ajax({
		url: $(this).attr('action'),
		type: $(this).attr('method'),
		data: $(this).serialize(),
		success: function(data) {
			var flashAlert = $('<div class="alert alert-success animated bounce fixed-alert bottom-left"><button type="button" class="close" data-dismiss="alert">&times;</button>' + data + '</div>');
			formElem.append(flashAlert);
			savingIndicator.hide();
			window.setTimeout(function(){
				flashAlert.remove();
			},3000);
		},
		error: function( xhr, err ) {
			var flashAlert = $('<div class="alert alert-danger animated bounce fixed-alert bottom-left"><button type="button" class="close" data-dismiss="alert">&times;</button>' + xhr.statusText + '</div>');
			formElem.append(flashAlert);
			savingIndicator.hide();
			window.setTimeout(function(){
				flashAlert.remove();
			},3000);
		}
	});   
	e.preventDefault();
});

$(window).bind('load', function(){
	$('img').each(function() {
		if((typeof this.naturalWidth != "undefined" && this.naturalWidth == 0 ) || this.readyState == 'uninitialized' ) {
			$(this).attr('src', './assets/images/no-image-available.png');
		}
	}); 
	}
);(function(){
	var winHeight = $(window).height();
	var navTopHeight = $('#topbar').outerHeight();
	var sideHeight = winHeight-navTopHeight;
	document.body.style.paddingTop = navTopHeight + 'px';
	$('#sidebar').css('top',navTopHeight);
	$('#sidebar').css('min-height',sideHeight);
}
)();

