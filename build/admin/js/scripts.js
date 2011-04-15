$(document).ready(function(){
	dhtmlHistory.initialize();
	dhtmlHistory.addListener(function(newLocation, historyData){
		if(newLocation == ""){
			return;
		}
		if(historyData == null){
			load(newLocation, null);
		}else{
			load(newLocation, historyData);
		}
	});
});

function load(url, args){
	if(args == false){
		dhtmlHistory.add(url);
		PLX.Request({"url": url + ".php", "target": "content", "preloader": "preloadDiv", mode:"rw"});
	}else{
		dhtmlHistory.add(url, args);
		PLX.Request({"url": url + ".php", "target": "content", "preloader": "preloadDiv", params: args, mode:"rw"});
	}
}

function getResponse(r){
	var r = eval('(' + r + ')');
	if(r.error.length != 0){
		// there is an error
		for(var i = 0; i < r.error.length; i++){
			switch(r.error[i].errorType){
			case "generic":
				alert(r.error[i].message);
				break;
			default:
				alert("errortype: " + r.error[i].errorType + " with error: " +r.error[i].message);
			}
		}
	}
	for(var i = 0; i < r.action.length; i++){
		// execute actions
		eval(r.action[i]);
	}
	return r.value; // return value
}

/**
 * dumps js object/array
 * 
 * @param arr object/array to be dumped
 * @param level
 * @return dumped object/array
 */
function dump(arr,level){
	var dumped_text = "";
	if(!level) level = 0;
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	if(typeof(arr) == 'object') { //Array/Hashes/Objects
		for(var item in arr) {
			var value = arr[item];	 
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			}else{
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	}else{ //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}