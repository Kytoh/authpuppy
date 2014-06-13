<script type="text/javascript">
  
  function initialize() {
    
    map = new <?php echo $mapclass ?>({
        imagePath: '<?php echo url_for('@homepage') . 'apNodeExtraPlugin/images/HotspotStatusMap/' ?>',
        sourceUrl: '<?php echo url_for('ap_nodeextra_nodelist') ?>',
		hotspotInfoList: "map_hotspots_list",
		hotspotMap: 'map_canvas',
		mapInfo: { latitude: <?php echo $latitude; ?>, longitude: <?php echo $longitude; ?>, zoom: <?php echo $zoom; ?> },
		external_object_name: "map",
		translations: {
			mapopen: "<?php echo __('Show me on the map')?>"
		},
		key: "<?php echo $key;?>"

    });
    map.displayHotspots();
  }

  window.onload = function(){initialize()};
</script>
<div id="mapdiv">
  <div id="map_canvas"></div>
  <div id="map_hotspots_list"></div>
</div>
  

