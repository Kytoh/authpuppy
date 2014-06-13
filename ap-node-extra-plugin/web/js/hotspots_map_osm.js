// include the google map API
function include(filename)
{
	var head = document.getElementsByTagName('head')[0];
	
	script = document.createElement('script');
	script.src = filename;
	script.type = 'text/javascript';
	
	head.appendChild(script)
}

include ('/apNodeExtraPlugin/js/lib/build/OpenLayers.js');
include ('/apNodeExtraPlugin/js/lib/build/cloudmade.js');
var map;

function OsmHotspotMap (config) {
	
	var defaults = {
		sourceUrl: "",
		hotspotInfoList: "",
		hotspotMap: null,
		mapInfo: { latitude: 0, longitude: 0, zoom: 1 },
		mapType: "",
		imagePath: "",
		translations: { mapopen: "Open on map" }
	};
	
	
    for (var i in config) {
    	defaults[i] = config[i];
    }
	
	this.config = defaults;
	this.markerObjects = {};
	this.infowindows = {};

    this.map = new OpenLayers.Map( this.config.hotspotMap, {controls: [
                                                         	         new OpenLayers.Control.Navigation(),
                                                        	         new OpenLayers.Control.PanZoomBar(),
                                                        	         new OpenLayers.Control.ScaleLine(),
                                                        			 new OpenLayers.Control.Attribution(),
                                                        			 new OpenLayers.Control.LayerSwitcher()
                                                        	      ]});


    this.layer = new OpenLayers.Layer.WMS( "Metacarta World Map",
            "http://labs.metacarta.com/wms/vmap0",
            {layers: 'basic'} );
    //this.map.addLayer(this.layer);
    // var mapnik = new OpenLayers.Layer.OSM.Mapnik("Mapnik"); 
    var ol_wms = new OpenLayers.Layer.WMS( "Canada Map",
            "http://atlas.gc.ca/cgi-bin/atlaswms_en?",
            {layers: 'na_road,na_pop_place,na_pop_place_nam,na_freshwat,na_drain', format:"image/png", transparency: true} );
    if (this.config.key) {
    this.layer = new OpenLayers.Layer.CloudMade("CloudMade", {
		key: this.config.key, styleId: 1
	});
    }
    this.map.addLayers([this.layer, ol_wms]);
    
    var epsg4326 = new OpenLayers.Projection("EPSG:4326");

    //this.map.addControl(new OpenLayers.Control.LayerSwitcher());
    this.map.setCenter(new OpenLayers.LonLat(this.config.mapInfo.longitude, this.config.mapInfo.latitude).transform(epsg4326, this.map.getProjectionObject()), this.config.mapInfo.zoom);
    
    this.displayHotspots = function() {	

    	 this.markers = new OpenLayers.Layer.Markers( "Markers" );
    	 this.markers.features = new Array();
         this.map.addLayer(this.markers);

    	icons = { "online": {"image": this.config.imagePath + 'up.png', 
    						"icon": new OpenLayers.Icon(this.config.imagePath + 'up.png', new OpenLayers.Size(20, 34)) } ,
    			  "offline": {"image": this.config.imagePath + 'down.png',
    		                "icon": new OpenLayers.Icon(this.config.imagePath + 'down.png',
    		      			      new OpenLayers.Size(20, 34))},
    		      "unknown": {"image": this.config.imagePath + 'unknown.png',
    		    	        "icon": new OpenLayers.Icon(this.config.imagePath + 'unknown.png',
    		    			      new OpenLayers.Size(20, 34))}
    	};
    
	
		this.fetchHotspotData();
		
		nodelistdata = "";
		
		
		for ( var i in this.nodes )
		{
			data = {};
			var node = new Node(this.nodes[i]);
			
			if (node.latitude != '') {
			    if (node.isOnline)
		    	    icon = icons.online;
		        else if (node.deployment_status == "NON_WIFIDOG_NODE")
		            icon = icons.unknown;
		        else
		    	    icon = icons.offline;
			
			    // Create the marker
			    // Code greatly inspired by the Markers/Text.js class of the OpenLayer API
			    // Why isn't there such a class for json?
		        var nodeLatlng = new  OpenLayers.LonLat(node.longitude, node.latitude);	 
		    
		        node.buildHtml(icon.image);
	            data['popupContentHTML'] = node.html;
	            
	            data['overflow'] =  "auto"; 
	            data.icon = icon.icon.clone();
	            
	            var markerFeature = new OpenLayers.Feature(this.markers, nodeLatlng.transform(epsg4326, this.map.getProjectionObject()), data);
	            this.markers.features.push(markerFeature);
	            var marker = markerFeature.createMarker();
                marker.events.register('click', markerFeature, this.markerClicked);
                this.markers.addMarker(marker);
            
                this.markerObjects[i] = markerFeature;
            
		        nodelistdata += node.html;
		    
		        nodelistdata += "<br /><a href=\"javascript:" + this.config.external_object_name +".openwindow('"+i+"');\">" + this.config.translations.mapopen + "</a><hr width='95%'/>";
			}
		}
		
		if (this.config.hotspotInfoList.length > 0) {
			el = document.getElementById(this.config.hotspotInfoList);
			if (el != null)
				el.innerHTML =  nodelistdata;
		}

    };
    
    this.markerClicked = function(evt) {
    	var sameMarkerClicked = (this == this.layer.selectedFeature);
        this.layer.selectedFeature = (!sameMarkerClicked) ? this : null;
        for(var i=0, len=this.layer.map.popups.length; i<len; i++) {
            this.layer.map.removePopup(this.layer.map.popups[i]);
        }
        if (!sameMarkerClicked) {
            this.layer.map.addPopup(this.createPopup()); 
        }
        OpenLayers.Event.stop(evt);
    }
    
    this.openwindow = function(i) {
    	this._openwindow(this.markerObjects[i]);
    }
    
    this._openwindow = function(marker) {
    	var sameMarkerClicked = (marker == marker.layer.selectedFeature);
    	marker.layer.selectedFeature = (!sameMarkerClicked) ? marker : null;
        for(var i=0, len=marker.layer.map.popups.length; i<len; i++) {
        	marker.layer.map.removePopup(marker.layer.map.popups[i]);
        }
        if (!sameMarkerClicked) {
        	marker.layer.map.addPopup(marker.createPopup()); 
        }
    }

	
	this.fetchHotspotData = function() {
		url = this.config.sourceUrl + '/json';
		var client = new XMLHttpRequest();

		client.open("GET", url, false);
		client.send(null);
		if (client.status == 200) {
			this.nodes =  eval('(' + client.responseText + ')');
		} else
			this.nodes = {};
		
	};
	
};

function Node(info) {
	for (var i in info) {
		this[i] = info[i];
		
	}
	this.html = "";

	this.buildHtml = function(icon) {
		html = "<table><tr><td><img src='" + icon + "' /></td><td>";
		if (this.name != null) 
			html+= "<b>"+this.name+"</b><br/>";
		html += "<i>";
		if (this.civic_number != null)
			html += this.civic_number;
		if (this.street_name != null) 
			html += " "+this.street_name;
		if (this.city != null && this.province != null)
			html += "<br/>" + this.city+", "+this.province;
		else if (this.city != null) {
			html += "<br/>" + this.city;
		}
		else if (this.province != null)
			html += "<br/>" + this.province;
		if (this.postal_code != null)
			html += "<br/>" +this.postal_code;
		if (this.public_phone_number != null)
			html += "<br/>" + this.public_phone_number;
		html += "</i>";
		if (this.mass_transit_info != null)
			html += "<br/>" + this.mass_transit_info;
		
		html += "</td></tr></table>";
		this.html = html;
		
	};
	
}





