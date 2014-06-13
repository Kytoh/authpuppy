<?php use_stylesheet('/apConnectionPoliciesPlugin/css/policies.css', sfWebResponse::LAST)?>

<div style="padding-bottom:20px">
<?php
$max_data = $status->getTotalData();
$id = $status->getId();
$policy = $status->getPolicy();
$string = '';
if ($max_data > 0) {
  $remaining = $status->getThisTotalData();
  $remaining = ($remaining < 0? 0: $remaining);
  $used = $max_data - $remaining;
  $percent = $remaining * 100 / $max_data;
  $color = "red";
  if ($percent > 50) {
    $color = "green";
  } elseif ($percent > 25) {
    $color = "YellowGreen";
  } elseif ($percent > 10) {
    $color = "Yellow";
  } ?>
  <script type='text/javascript'>
	$(function() {
		$('#progressbar_<?php echo $id?>').progressbar({
			value: <?php echo 100-$percent; ?>
		});
	});
	</script>
 

  <?php echo sprintf(__("You have used <strong>%s out of %s</strong> %s for the last %s") , apUtils::size_readable($used), apUtils::size_readable($max_data),
        (($policy->getScope() == apConnectionPoliciesTable::SCOPE_GLOBAL) ? __("on the network") : __("on this node")), $policy->getTimeWindowOutput()); ?>
  <div id='progressbar_<?php echo $id?>' style='background-color:<?php echo $color?>;background-repeat:no-repeat;background-position:left top'></div>
  <?php 

}
else {
  $expiry = strtotime($status->getDisconnectAt());
  $difftime = $expiry - time();
  echo sprintf(__("Remaining %s to this connection %s") , apUtils::displayDuration($difftime),
     (($policy->getScope() == apConnectionPoliciesTable::SCOPE_GLOBAL) ? __("on the network") : __("on this node")));
}

if ($status->expired()) {
  $policy = $status->getPolicy();
  ?> <div class="error"><?php echo  $policy->getStatusMessage(); ?></div> <?php 
}
?></div>