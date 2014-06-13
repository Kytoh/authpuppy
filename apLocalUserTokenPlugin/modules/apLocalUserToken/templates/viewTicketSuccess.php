
<table>
	<tr>
		<td colspan="4" style="align:center;">
		<?php  if (!is_null($network->getNetworkLogo())) {
        	   echo image_tag(url_for("/uploads/assets/apSimpleNetworksPlugin/".$network->getNetworkLogo()), array('alt' => $network->getName(), 'class' => 'toplogo'));
		}
        ?>
		</td>
	</tr>


	<tr>
		<td><?php echo __("Username") ?> :</td>
		<td><?php echo $apUser->getUsername() ?></td>
		<td><?php echo __("Profile") ?> :</td>
		<td> <?php echo $profile->getName() ?></td>
	</tr>
	<tr>
		<td><?php echo __("Password") ?> :</td>
		<td><?php echo $password?></td>
		<td><?php echo __("Ticket date") ?> :</td>
		<td><?php echo $apUser->getRegisteredOn() ?></td>
	</tr>
	<tr>
		<td><?php echo __("First name") ?> :</td>
		<td><?php echo $physicalUser->getFirstName() ?></td>
		<td><?php echo __("Last name") ?> :</td>
		<td><?php echo $physicalUser->getLastName()?></td>
	</tr>
	<tr>
		<td><?php echo __("Profile description") ?> :</td>
		<td><?php echo $profile->getDescription() ?></td>
		<td><?php echo __("Profile price") ?> :</td>
		<td><?php echo $profile->getPrice()?></td>
	</tr>
	<tr>
		<td><?php echo __("Birth date") ?> :</td>
		<td><?php echo $physicalUser->getBirthDate() ?></td>
		<td><?php echo __("Birth place") ?> :</td>
		<td><?php echo $physicalUser->getBirthPlace()?></td>
	</tr>
	<tr>
		<td><?php echo __("Address") ?> :</td>
		<td><?php echo $physicalUser->getAddress() ?></td>
		<td><?php echo __("City") ?> :</td>
		<td><?php echo $physicalUser->getCity()?></td>
	</tr>
	<tr>
		<td><?php echo __("Province") ?> :</td>
		<td><?php echo $physicalUser->getProvince() ?></td>
		<td><?php echo __("Zip") ?> :</td>
		<td><?php echo $physicalUser->getZip()?></td>
	</tr>
	<tr>
		<td><?php echo __("Status") ?> :</td>
		<td><?php echo $physicalUser->getUserStatus() ?></td>
		<td><?php echo __("Document type") ?> :</td>
		<td><?php echo $physicalUser->getDocumentType()?></td>
	</tr>
	<tr>
		<td><?php echo __("Payment method") ?> :</td>
		<td><?php echo $apUser->getPayment() ?></td>
		<td><?php echo __("Ticket notes") ?> :</td>
		<td><?php echo $apUser->getTicketNotes()?></td>
	</tr>

</table>