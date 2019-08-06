$(function() {
	$(".infovisuals-main-container").delegate(".action[data-cmd]","click", function(e) {
		cmd = $(this).data("cmd");
		// e.preventDefault();
		if(typeof window[cmd]=="function") {
			window[cmd](this, e);
		} else {
			console.log("COMMAND NOT HANDLED",cmd);
		}
	});
});

function loadAllInfoCards() {
	$(".infovisualsContainer .infovisualBox[data-ivcardkey]",".infovisuals-main-container").each(function() {
		$(this).find(".panel-body").html("<div class='ajaxloading ajaxloading8'></div>");
		$(this).find(".panel-body").load(_service("infovisuals","fetchCard","html")+"&infovisualid="+$(this).data("ivkey")+"&ivcardkey="+$(this).data("ivcardkey"));
	});
}

function printPage() {
	window.print();
}