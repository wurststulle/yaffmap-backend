var map;
var selectControl;
var nodesLayer, linksLayer;
var f;
var url = "/soap.php";

function yaffmap(response, target, isDebug) {
  var r = eval('(' + response + ')');
  var zoom = 16;

  map = new OpenLayers.Map('yaffmap', 
                           { maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
                             numZoomLevels: 19,
                             maxResolution: 156543.0399,
                             units: 'm',
                             projection: new OpenLayers.Projection("EPSG:900913"),
                             displayProjection: new OpenLayers.Projection("EPSG:4326")
                           });

                           var osm = new OpenLayers.Layer.OSM();
                           var nodesLayer = new OpenLayers.Layer.Vector("Nodes", {
                             styleMap: new OpenLayers.StyleMap({
                               strokeColor : '#33EE66',
                               strokeWidth : 1.0,
                               pointRadius: "5px",
                               fillColor: "#11CC44",
                               fillOpacity : 0.70
                             })
                           });
                           var linksLayer = new OpenLayers.Layer.Vector('rpLinks',{
                             styleMap: new OpenLayers.StyleMap({
                               strokeColor : '#ee0016',
                               strokeWidth : 1.0,
                               strokeOpacity : 1,
                               fillColor : '#ee0011',
                               fillOpacity : 0.5
                             })
                           });



                           map.addLayers([osm, linksLayer, nodesLayer]);

                           selectControl = new OpenLayers.Control.SelectFeature([nodesLayer, linksLayer], { title: "Nodes + Links", select: onSelect });
                           map.addControl(selectControl);
                           selectControl.activate();
                           var lonLat = new OpenLayers.LonLat(13.483937263488770,52.562709808349609).transform(map.displayProjection,map.projection);

                           map.setCenter(lonLat, zoom);
                           if (navigator.geolocation) {
                             navigator.geolocation.getCurrentPosition(function (position) {
                               lat = position.coords.latitude;
                               lon =  position.coords.longitude;
                               zoom = 13;
                               lonLat = new OpenLayers.LonLat(lon, lat).transform(map.displayProjection,  map.projection);
                               map.setCenter (lonLat, zoom); 

                             });
                           }

                           for(var i = 0; i < r.rpLink.length; i++) {
                             aLineStringGeometry = new OpenLayers.Geometry.LineString([
                                                                                      new OpenLayers.Geometry.Point(r.rpLink[i].sourceLon,r.rpLink[i].sourceLat).transform(new OpenLayers.Projection("EPSG:4326"),new OpenLayers.Projection("EPSG:900913")), 
                                                                                      new OpenLayers.Geometry.Point(r.rpLink[i].destLon,r.rpLink[i].destLat).transform(new OpenLayers.Projection("EPSG:4326"),new OpenLayers.Projection("EPSG:900913")),
                                                                                      this.cost = r.rpLink[i].cost,
                             ]);
                             var feature = new OpenLayers.Feature.Vector(aLineStringGeometry, null);
                             feature.attributes = r.rpLink[i];
                             feature.name = 'link';
                             linksLayer.addFeatures(feature);
                           }

                           for(var i = 0; i < r.node.length; i++){
                             var feature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Point(r.node[i].longitude,r.node[i].latitude).transform(new OpenLayers.Projection("EPSG:4326"),new OpenLayers.Projection("EPSG:900913")));
                             feature.attributes = r.node[i]
                             feature.name = 'node';
                             nodesLayer.addFeatures(feature);
                           }
}


function onSelect(feature) {
  f = feature;
  if (feature.name == 'node') {
    var pl = new SOAPClientParameters();
    pl.add("id", feature.attributes.id);
    SOAPClient.invoke(url, "getFfNode", pl, true, cbNode); 
  } else {
    setPaneContent("Link Information", array2json(feature.attributes));
  }
}


var res, soap, ret, n;
function cbNode(r, soapResponse)
{
  res = r;
  soap = soapResponse;
  var data;
  if(soapResponse.xml)
      data = soapResponse.xml;
    else
      data = new XMLSerializer().serializeToString(soapResponse);
    var rv = soap.getElementsByTagName("returnValue")[0].childNodes;
    var content = "";
    ret = rv;
    for (var i = 0; i < rv.length; ++i)
    {
      var node = rv[i];
      content += node.nodeName.split(":")[1] + ": " + node.textContent + "<br />";
    }


    
   setPaneContent(soapResponse.getElementsByTagName("hostname")[0].textContent, content);
}

// debug function to show a node attribues
function array2json(arr) {
  var parts = [];
  var is_list = (Object.prototype.toString.apply(arr) === '[object Array]');

  for(var key in arr) {
    var value = arr[key];
    if(typeof value == "object") { //Custom handling for arrays
      if(is_list) parts.push(array2json(value)); /* :RECURSION: */
      else parts[key] = array2json(value); /* :RECURSION: */
    } else {
      var str = "";
      if(!is_list) str = '"' + key + '":';

      //Custom handling for multiple data types
      if(typeof value == "number") str += value; //Numbers
      else if(value === false) str += 'false'; //The booleans
      else if(value === true) str += 'true';
      else str += '"' + value + '"'; //All other things
      // :TODO: Is there any more datatype we should be in the lookout for? (Functions?)

      parts.push(str);
    }
  }
  var json = parts.join(",");

  if(is_list) return '[' + json + ']';//Return numerical JSON
  return '{' + json + '}';//Return associative JSON
}

function onNodePopupClose() {
  nodesControl.unselect(selectedNode);
}

function onLinkPopupClose() {
  linksControl.unselect(selectedLink);
}

function getYaffMap(target){
  document.getElementById(target).innerHTML = "";
  PLX.Request({"url": "/index.php?do=getFrontendData", "onFinish": function(response){yaffmap(response, target)}});
}

function getYaffMapDebug(target){
  document.getElementById(target).innerHTML = "";
  PLX.Request({"url": "/index.php?do=getFrontendData&request={\"ul\":\"52.762709,12.1234\",\"lr\":\"48.1234,16.1234\",\"node\":[\"latitude\",\"longitude\",\"id\",\"hostname\"],\"rpLinkFilter\": {\"cost\":[[\"<\",\"10\"],[\">=\", \"1\"]],\"rp\":[{\"type\":\"olsr\",\"metrics\":[\"etx_ff\"]}]},\"rpLink\":[\"cost\",\"rp\",\"metric\"]}", "onFinish": function(response){yaffmap(response, target, true)}});
}

function getYaffMapExt(target){
  document.getElementById(target).innerHTML = "";
  PLX.Request({"url": "/index.php?do=getFrontendData&request={\"ul\":\"52.762709,12.1234\",\"lr\":\"48.1234,16.1234\",\"node\":[\"latitude\",\"longitude\",\"id\",\"hostname\", \"agentrelease\"],\"rpLinkFilter\": {\"cost\":[[\"<\",\"10\"],[\">=\", \"1\"]],\"rp\":[{\"type\":\"olsr\",\"metrics\":[\"etx_ff\"]}]},\"rpLink\":[\"cost\",\"rp\",\"metric\"]}", "onFinish": function(response){yaffmap(response, target)}});
}
