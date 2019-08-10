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

	$(".infovisuals-main-container").delegate(".infovisualBox .actionCMD[data-cmd]", "click", function(e) {
		cmd = $(this).data("cmd");
		infovisualAction(cmd, this);
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

function infovisualAction(cmd, src) {
	cmdOriginal=cmd;
	cmd=cmd.split("@");
	cmd=cmd[0];console.log(cmd,src);
	switch(cmd) {
		case "filterbar":
			$(src).toggleClass("active");
			// rpt.getGrid().find("thead.tableFilter").toggleClass("hidden");
			// rpt.settings("filterbar",(!rpt.getGrid().find("thead.tableFilter").hasClass("hidden")));
		break;
		case "report:print":
			window.print();
		break;
		case "report:exportimg":
			html2canvas(document.body, {
				  onrendered: function(canvas) {
						window.open().document.body.appendChild(canvas);
						window.location.reload();
				  }
				});
		break;
		case "forms":case "reports":case "infoview":
			hash=$(src).closest(".tableRow").data('hash');
			title=$(src).text().trim();
			if(title==null || title.trim().length<=0) {
				title=$(src).attr("title");
			}
			if(title==null || title.trim().length<=0) {
				title="Dialog";
			}
			
			cmdX=cmdOriginal.split("@");
			if(cmdX[1]!=null) {
				cmdX[1]=cmdX[1].replace("{hashid}",hash);
				
				showLoader();
				lgksOverlayURL(_link("popup/"+cmd+"/"+cmdX[1]),title,function() {
						hideLoader();
					},{"className":"overlayBox reportPopup infovisualPopup"});
			}
		break;
		case "page":
			hash=$(src).closest(".tableRow").data('hash');
			title=$(src).text().trim();
			if(title==null || title.trim().length<=0) {
				title=$(src).attr("title");
			}
			if(title==null || title.trim().length<=0) {
				title="Dialog";
			}
			
			cmdX=cmdOriginal.split("@");
			if(cmdX[1]!=null) {
				cmdX[1]=cmdX[1].replace("{hashid}",hash);
				window.location=_link(cmdX[1]);
			}
			break;
		case "module":case "popup":
			hash=$(src).closest(".tableRow").data('hash');
			title=$(src).text().trim();
			if(title==null || title.trim().length<=0) {
				title=$(src).attr("title");
			}
			if(title==null || title.trim().length<=0) {
				title="Dialog";
			}
			
			cmdX=cmdOriginal.split("@");
			if(cmdX[1]!=null) {
				cmdX[1]=cmdX[1].replace("{hashid}",hash);
				
				if(cmd=="module" || cmd=="modules") {
					top.openLinkFrame(title,_link("modules/"+cmdX[1]),true);
				} else {
					showLoader();
					lgksOverlayURL(_link("popup/"+cmdX[1]),title,function() {
							hideLoader();
						},{"className":"overlayBox reportPopup infovisualPopup"});
				}
			}
		break;
		case "ui":
			cmdX=cmdOriginal.split("@");
			if(cmdX[1]!=null) {
				cmd=cmdX[1];
				gkey=$(src).closest(".reportTable").data('gkey');
				if(gkey==null) return;
				$.cookie("RPTVIEW-"+gkey,cmd,{ path: '/' });
				window.location.reload();
			}
		break;
		default:
			if(typeof window[cmd]=="function") {
				window[cmd](rpt, src);
			} else {
				console.warn("Report CMD not found : "+cmd);
			}
	}
}


function colorize(opaque, context) {
	var value = context.dataset.data[context.dataIndex];
	var x = value.x / 100;
	var y = value.y / 100;
	var r = x < 0 && y < 0 ? 250 : x < 0 ? 150 : y < 0 ? 50 : 0;
	var g = x < 0 && y < 0 ? 0 : x < 0 ? 50 : y < 0 ? 150 : 250;
	var b = x < 0 && y < 0 ? 0 : x > 0 && y > 0 ? 250 : 150;
	var a = opaque ? 1 : 0.5 * value.v / 1000;

	return 'rgba(' + r + ',' + g + ',' + b + ',' + a + ')';
}

function transparentize(color, opacity) {
	var alpha = opacity === undefined ? 0.5 : 1 - opacity;
	return Color(color).alpha(alpha).rgbString();
}
