var glocalCallback=showPreviousButton();
var isHistorique=false;
var pageLoaded=0;
function showPreviousButton(){ if (pageLoaded>0) { $(".hero_section").first().prepend('<div class="center_div"><a href="#" class="primary_btn w-button" id="previous_button" onclick="goBackHistory(); return false;">&lt;&lt; Page précédente</a></div>'); } }
function loadTo(urlCalled, dataUsed, location, type, isImage, callback) // path vers le fichier, voir loadToMain, lieu pour afficher le retour (.class #div ect...), les différents types d'écriture (replace, append, prepend), function à appeller a la fin du call ajax
{
	if (typeof isImage =="undefined" || !isImage)
	{
		$params={url:urlCalled, type:"POST", data:dataUsed};
	}
	else
	{
		$params={url: urlCalled,
		type: "POST",
		data: dataUsed,
		contentType: false,
		processData: false};
	}
	$.ajax($params).done(function (data) {
		if (String(urlCalled).indexOf(".php")==-1){
			var str=String(window.location);
			if(!isHistorique && urlCalled!=str.replace('http://www.campandjoy.fr/demo/', '')){
				window.history.pushState($params.data,'',urlCalled);
				pageLoaded=parseInt(pageLoaded+1);
			} else {
				window.history.replaceState($params.data,'',urlCalled);
				isHistorique=false;
			}
		}
		if (type=="replace")
			$(location).html(data);
		else if (type=="append")
			$(location).append(data);
		else if (type=="after")
			$(location).after(data);
		else if (type=="before")
			$(location).before(data);
		else
			$(location).prepend(data);
		
		if (typeof(callback) === "function") { callback(); }
		initLink();
		if (location==".page_bg_color.page_container" || $(".menu-button.w-nav-button.w--open").length==0)
		{
			Webflow.destroy();
			setTimeout(function(){ $(Webflow.ready); }, 1);
		}
		$("div[class*='step_list_item']").on("click", function(){
			var all=$("div[class*='step_list_item']");
			goToStep(all.index($(this)), $(this));
		});
	});
}
function loadToMain(urlCalled, dataUsed, callback) // dataUsed : { nomVar : valeur, nomVar2 : valeur2 }
{
	glocalCallback=function(){ showPreviousButton(); if (typeof(callback) === "function") { callback(); } };
	loadTo(urlCalled, dataUsed, ".page_bg_color.page_container", "append", false, callback);
}
function loadToMainRedirect(urlCalled, dataUsed) // dataUsed : { nomVar : valeur, nomVar2 : valeur2 }
{
	loadTo(urlCalled, dataUsed, ".page_bg_color.page_container", "replace", false, glocalCallback);
}
function webflowCampandJoy(name)
{
	var data=Webflow.require('ix').getConfig()[name].triggers;
	for (var i in data){
		Webflow.require("ix").run(data[i]);
	}
}
function initLink()
{
	$("a").each(function(){
		if (!$(this).hasClass("ajaxed")){
			if (typeof $(this).attr('href')!="undefined" && String($(this).attr('href')).indexOf("#")==-1 && String($(this).attr('href')).indexOf("http")==-1){
				$(this).click(function(){
					loadToMain($(this).attr("href"), {}); 
					return false;
				});
				$(this).addClass('ajaxed');
			}
			if (typeof $(this).attr("data-ix") !== typeof undefined && $(this).attr("data-ix")!==false && $(this).attr("data-ix")=="show-modal-msg")
			{
				$(this).click(function(){
					$modal = $(".modal_message");
					var attr=$(this).attr("data-type");
					if (typeof attr !== typeof undefined && attr!==false)
						$modal.find(".w-container").addClass(attr+"_msg");
					
					attr=$(this).attr("data-title");
					if (typeof attr !== typeof undefined && attr!==false)
						$modal.find("#title_modal").html(attr);
					
					attr=$(this).attr("data-message");
					if (typeof attr !== typeof undefined && attr!==false)
						$modal.find("#message_modal").html(attr);
					
					$modal.find("#confirm_button").prop('onclick',null).off('click');
					attr=$(this).attr("data-on-confirm");
					if (typeof attr !== typeof undefined && attr!==false)
						$modal.find("#confirm_button").on("click", function(){ eval(attr); });
					
					$modal.find("#refuse_button").prop('onclick',null).off('click');
					var refuse=$(this).attr("data-on-refuse");
					if (typeof refuse !== typeof undefined && refuse!==false)
						$modal.find("#refuse_button").on("click", function(){ eval(refuse); });
					
					return false;
				});
				$(this).addClass('ajaxed');
			}
		}
	});
	$("form").find("a[class^='primary_btn w-button form_button']").each(function(){
		if (!$(this).hasClass("ajaxed")){
			if (typeof $(this).attr('href')=="undefined" || String($(this).attr('href')).indexOf("#")!=-1){
				var o = $(this);
				o.prevUntil("form, a").each(function(){
					if ($(this).prop("tagName")=="INPUT"){
						if ($(this).attr("type")!="checkbox" && $(this).attr("type")!="radio"){
							$(this).on("keypress", function(e){
								if ($(this).is(":focus") && (e.keyCode==13 || e.which==13)){
									o.click();
								}
							});
						}
					} else {
						$(this).find("input").each(function(){
							if ($(this).attr("type")!="checkbox" && $(this).attr("type")!="radio"){
								$(this).on("keypress", function(e){
									if ($(this).is(":focus") && (e.keyCode==13 || e.which==13)){
										o.click();
									}
								});
							}
						});
					}
				});
			}
			$(this).addClass('ajaxed');
		}
	});
}
$(window).bind('popstate', function(){
	loadToMain(location.pathname, {});
});
function goBackHistory()
{
	isHistorique=true;
	pageLoaded=pageLoaded-1;
	history.back();
}
function animationTest(selector)
{
	$o = $(selector);
	$a = $(selector+"_res");
	$o.slideToggle('fast');
	$a.slideToggle('fast');
}
function seekRestaurant(page)
{
	var argsPage="";
	if ($("#datepicker").val()!="" && $("#timepicker").val()!="")
	{
		var result=String($("#datepicker").val()).split('/');
		argsPage=$("#datepicker").val();
		result=String($("#timepicker").val()).split(':');
		argsPage=argsPage+"/"+result[0]+"/"+result[1];
		if ($("#pers").val()!="")
		{
			argsPage=argsPage+"/"+$("#pers").val();
		}
	}
	else
	{
		if ($("#pers").val()!="")
		{
			argsPage=$("#pers").val();
		}
	}
	if (argsPage!="")
		loadToMain(page+argsPage, {});
}
function runScript(e, $o){
    if (e.keyCode==13 || e.which==13){
        $o.click();
    }
}
function goNextStep(){
	var currentStep = $("div[class*='step_current_item']"); // On récupère l'objet HTML de notre "menu" d'état qui est activé
	var nextStep = currentStep.next("div[class*='step_list_item']"); // On prend le prochain 
	currentStep.children("div[class*='step_current_number']").children("div[class*='current_step_title']").removeClass('current_step_title'); // On retire l'état "current"
	currentStep.children("div[class*='step_current_number']").removeClass('step_current_number'); //  de même du nombre actuel
	currentStep.removeClass("step_current_item"); //  et encore pareil (tout ça c'est juste du css)
	$(".step_current_arrow").remove(); // on supprime la fleche
	nextStep.addClass("step_current_item").children("div[class*='step_number']").addClass('step_current_number').children("div[class*='step_title']").addClass('current_step_title');
	nextStep.append('<div class="step_current_arrow"></div>'); // Puis apres on rajoute les class au bon enfant ect.. rien de bien ouf
}
function goPrevStep(){
	var currentStep = $("div[class*='step_current_item']");
	var nextStep = currentStep.prev("div[class*='step_list_item']");
	currentStep.children("div[class*='step_current_number']").children("div[class*='current_step_title']").removeClass('current_step_title');
	currentStep.children("div[class*='step_current_number']").removeClass('step_current_number');
	currentStep.removeClass("step_current_item");
	$(".step_current_arrow").remove();
	nextStep.addClass("step_current_item").children("div[class*='step_number']").addClass('step_current_number').children("div[class*='step_title']").addClass('current_step_title');
	$("div[class*='step_current_number']").after('<div class="step_current_arrow"></div>');
}

function goToStep(num, object){
	var currentStep = $("div[class*='step_current_item']"); // On récupère l'objet HTML de notre "menu" d'état qui est activé
	currentStep.children("div[class*='step_current_number']").children("div[class*='current_step_title']").removeClass('current_step_title'); // On retire l'état "current"
	currentStep.children("div[class*='step_current_number']").removeClass('step_current_number'); //  de même du nombre actuel
	currentStep.removeClass("step_current_item"); //  et encore pareil (tout ça c'est juste du css)
	$(".step_current_arrow").remove(); // on supprime la fleche
	var newStep=object;
	newStep.addClass("step_current_item").children("div[class*='step_number']").addClass('step_current_number').children("div[class*='step_title']").addClass('current_step_title');
	newStep.append('<div class="step_current_arrow"></div>'); // Puis apres on rajoute les class au bon enfant ect.. rien de bien ouf
	$("div[id*='part_']:visible").hide();
	$("div[id='part_"+parseInt(num+1)+"']").show();
}
function removeStep(){
	$(".step_head").remove();
	$("form").find("a").each(function(){
		if ($(this).html()=="Suivant" || $(this).html()=="Retour")
		{
			if ($(this).html()=="Suivant")
			{
				$(this).after("<div class='customcaj_separator'></div>");
			}
			$(this).remove();
		}
	});
}
var nombresImages=4;
function imageUpload(object){
	var files = object[0].files;
	if (files.length > 0) {
		var file = files[0], objectP=object.parent("div[class^='photo_item']");
		if ($("input[type='file']").length<=nombresImages)
		{ 
			var c=$("input[type='file']").length;
			while ($("input[id='image"+c+"']").length>0)
			{ 
				c=parseInt(c+1);
			}
			var htmlAdd=String(objectP.prop('outerHTML'));
			htmlAdd=htmlAdd.replace(new RegExp("image[0-9]+","gi"), "image"+c);
			$("div[class^='flex_list_photo']").append(htmlAdd);
			initAddPhotos();
			Webflow.destroy();
			setTimeout(function(){ $(Webflow.ready); }, 1);
		}
		objectP.find("img").attr('src', window.URL.createObjectURL(file));
		objectP.find("div[class^='add_photo_content']").remove();
		objectP.append('<div class="photo_delete"></div>');
		objectP.find("div[class^='add_photo_overlay']").remove();
		objectP.find("div[class^='photo_delete']").on("click", function(){ deleteUpload($(this)); });
	}
}
function deleteUpload(o)
{
	$("div[class^='add_photo_content']").parent("div[class^='photo_item']").remove();
	var tempP=o.parent("div[class^='photo_item']");
	tempP.find("img").attr('src', 'images/image-placeholder.svg');
	tempP.find("img").after('<div class="add_photo_content" data-ix="add-photo-trigger"><div class="add_block"></div><div class="text-block-2">Ajouter une photo</div></div><div class="add_photo_overlay"></div>');
	o.remove();
	$("div[class^='flex_list_photo']").append(tempP.prop('outerHTML'));
	tempP.remove();
	initAddPhotos();
	Webflow.destroy();
	setTimeout(function(){ $(Webflow.ready); }, 1);
}
function initAddPhotos(maxPictures){
	if (typeof maxPictures=="undefined")
		nombresImages=4;
	else
		nombresImages=maxPictures-1;
	
	$("div[class^='add_photo_content']").on("click", function(){
		$(this).parent("div[class^='photo_item']").find("input[type='file']").click();
	});
}
function endForm(){
	$("form").find("div[id^='part']").show();
	$("form").find("a").each(function(){
		if ($(this).html()=="Suivant" || $(this).html()=="Retour")
		{
			if ($(this).html()=="Suivant")
			{
				$(this).after("<div class='customcaj_separator'></div>");
			}
			$(this).remove();
		}
	});
}
var titleAnimtimeout;
var basicTitle=$("title").html();
var nextTitle;
var headerCentreNotif;
function titleAnim(newCall){
	if (typeof newCall!="undefined")
	{
		stopTitleAnim();
	}
	if ($("title").html()==basicTitle)
		$("title").html(nextTitle);
	else
		$("title").html(basicTitle);
	
	titleAnimtimeout=setTimeout(function(){ titleAnim(); }, 950);
}
function stopTitleAnim()
{
	if (typeof titleAnimtimeout!="undefined")
	{
		clearTimeout(titleAnimtimeout);
	}
}
function editTitle(title){
	$(".chat_owner_name").html(title);
}
var refreshChattimeout;
function startRefreshChat(f, force){
	if (typeof refreshChattimeout=="undefined" || typeof force!="undefined")
	{
		refreshChattimeout=setTimeout(function(){ f(); startRefreshChat(f, true); }, 10000);
	}
}
function stopRefreshChat(){
	if (typeof refreshChattimeout!="undefined")
	{
		clearTimeout(refreshChattimeout);
	}
}
var refreshMessagestimeout;
function startRefreshMessages(f){
	refreshMessagestimeout=setTimeout(function(){ f(); startRefreshMessages(f); }, 5000);
}
function stopRefreshMessages(){
	if (typeof refreshMessagestimeout!="undefined")
	{
		clearTimeout(refreshMessagestimeout);
	}
}
function contact(idVar){
	if ($(".chat_block").length>0)
	{
		startRefreshChat(function(){ loadTo('/demo/includes/view/chat/chatViewUpdate.php', {}, ".chat_block", "prepend"); });
		loadTo('/demo/includes/view/chat/chatMessages.php', {id:idVar}, "#chat-messages", "replace", false, function(){
			webflowCampandJoy("showchat");
			setTimeout(function(){ $(".tabs-menu-2").find("a[data-w-tab='Tab 2']").click(); webflowCampandJoy("showchatmessage"); }, 500);
		});
		$("#left_arrow").show();
	}
	else
	{
		loadTo('/demo/includes/view/chat/chatView.php', {}, "body", "append", false, function(){
			startRefreshChat(function(){ loadTo('/demo/includes/view/chat/chatViewUpdate.php', {}, ".chat_block", "prepend"); });
			loadTo('/demo/includes/view/chat/chatMessages.php', {id:idVar}, "#chat-messages", "replace", false, function(){
				webflowCampandJoy("showchat");
				setTimeout(function(){ $(".tabs-menu-2").find("a[data-w-tab='Tab 2']").click(); webflowCampandJoy("showchatmessage"); }, 500);
			});
			$("#left_arrow").show();
		});
	}
}
$(document).ready(function(){
	initLink();
});