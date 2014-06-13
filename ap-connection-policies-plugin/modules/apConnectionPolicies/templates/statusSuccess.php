<?php

$no = 0;
if (count($statuses) == 0) {
  echo __("No policy apply to this connection.");
} else {
  foreach ($statuses as $status) {
    echo $status;
  }
}


if (isset($url)) { ?>

<script language='JavaScript'>

$(function(){
	  var count = 5;
	  countdown = setInterval(function(){
	    $("p.countdown").html("<?php echo __("Redirecting to portal page in") ?> " + count + " <?php echo __("seconds");?>");
	    if (count == 0) {
	      window.location = '<?php echo $sf_data->getRaw('url'); ?>';
	    }
	    count--;
	  }, 1000);
	});


</SCRIPT>
<p class="countdown"></p>
<?php }?>