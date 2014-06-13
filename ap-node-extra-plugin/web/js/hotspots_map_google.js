var map;

var infowindows = {};
var markers = {};

function GoogleHotspotMap (config) {

	var defaults = {
		sourceUrl: "",
		hotspotInfoList: "",
		hotspotMap: null,
		mapInfo: { latitude: 0, longitude: 0, zoom: 1 },
		mapType: google.maps.MapTypeId.ROADMAP,
		imagePath: "",
		translations: { mapopen: "Open on map" }
	};
	
	
    for (var i in config) {
    	defaults[i] = config[i];
    }
	
	this.config = defaults;
	this.markers = {};
	this.infowindows = {};
	
	var latlng = new google.maps.LatLng(this.config.mapInfo.latitude, this.config.mapInfo.longitude);
    var myOptions = {
      zoom: this.config.mapInfo.zoom,
      center: latlng,
      mapTypeId: this.config.mapType
    };
    this.map = new google.maps.Map(document.getElementById(this.config.hotspotMap), myOptions);

	
    this.displayHotspots = function() {	
    	
        icons = { "online": {"image": this.config.imagePath + 'up.png', 
    						"icon": new google.maps.MarkerImage(this.config.imagePath + 'up.png', new google.maps.Size(20, 34)) } ,
    			  "offline": {"image": this.config.imagePath + 'down.png',
    		                "icon": new google.maps.MarkerImage(this.config.imagePath + 'down.png', new google.maps.Size(20, 34))} ,
                  "unknown": {"image": this.config.imagePath + 'unknown.png', 
    						"icon": new google.maps.MarkerImage(this.config.imagePath + 'unknown.png', new google.maps.Size(20, 34)) } ,
    	};
    
	
		var shadow = new google.maps.MarkerImage(this.config.imagePath + 'shadow.png',
			      new google.maps.Size(37, 34), new google.maps.Point(0,0), new google.maps.Point(10,34));
	
		this.fetchHotspotData();
		
		nodelistdata = "";
		
		
		for ( var i in this.nodes )
		{
			
			var node = new Node(this.nodes[i]);
			
			if (node.latitude != '') {
			
		        var nodeLatlng = new  google.maps.LatLng(node.latitude, node.longitude);	 
		        var marker = new google.maps.Marker({
			        position: nodeLatlng, 
			        map: this.map, 
			        clickable: true,
			        title:i
			    });   
		        if (node.deployment_status == "NON_WIFIDOG_NODE")
		          icon = icons.unknown;
		        else {
		          if (node.isOnline)
		            icon = icons.online;
		          else
		            icon = icons.offline;
		        }

		        marker.setIcon(icon.icon);
		        marker.setShadow(shadow);
		        node.buildHtml(icon.image);
		    
		        var infowindow = new google.maps.InfoWindow(
				      { content: node.html});
			    google.maps.event.addListener(marker, 'click', function() {
				    infowindows[this.title].open(this.map,this);
			    });
				
			    this.markers[i] = marker;
			    this.infowindows[i] = infowindow;
			    markers[i] = marker;
			    infowindows[i] = infowindow;
		        nodelistdata += node.html;
		    
		        nodelistdata += "<a href=\"javascript:" + this.config.external_object_name +".openwindow('"+i+"');\">" + this.config.translations.mapopen + "</a><hr width='95%'/>";
			}
		}
		
		if (this.config.hotspotInfoList.length > 0) {
			el = document.getElementById(this.config.hotspotInfoList);
			if (el != null)
				el.innerHTML =  nodelistdata;
		}

    };
    
    this.openwindow = function(i) {
    	if (infowindows[i] != null) {
    		infowindows[i].open(this.map, markers[i]);
    	}
    }

    this.fetchHotspotData = function() {
        url = this.config.sourceUrl + '/json';
        var client = null;

        if(window.XMLHttpRequest) { //is browser later than IE6
            client = new XMLHttpRequest();
        }
        else
		{ //check client is IE6 or below
		     client = new ActiveXObject("Microsoft.XMLHTTP");
		     //alert("Your browser is outdated, please consider upgrading"); commented out because of supporting ie6countdown.com
        }
		
        if(client == null)
		{
		     alert("ERROR: Your browser does not support AJAX, this page may not be shown correctly.");
		}
        else
		{
		    client.open("GET", url, false);
		    client.send(null);
		    if (client.status == 200)
		    {
		        this.nodes = eval('(' + client.responseText + ')');
		    }
		    else
		    {
		        this.nodes = {};
		    }
		}
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





