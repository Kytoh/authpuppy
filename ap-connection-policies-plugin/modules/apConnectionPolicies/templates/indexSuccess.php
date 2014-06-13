<h1><?php echo __('Network policies') ?></h1>

<table>
<thead>
  <tr>
    <th><?php echo __('Policy name') ?></th>
    <th><?php echo __('Policy type') ?></th>
    <th><?php echo __('Scope') ?></th>
    <th><?php echo __('Authenticator') ?></th>
    <th><?php echo __('Authenticator sub-type') ?></th>
  </tr>
</thead>
<?php foreach ($policies as $policy) : ?>
    <tr>

	<td><a href="<?php echo url_for('apConnectionPolicies/edit?id='.$policy->getId()) ?>"><?php echo (is_null($policy->getPolicyName())? __("No name"): $policy->getPolicyName()); ?></a></td>
    <td><?php echo $policy->getType(); ?></td>
    <td><?php echo $policy->getScope(); ?></td>
    <td><?php echo $policy->getAuthType(); ?></td>
    <td><?php echo $policy->getAuthSubType(); ?></td>

  </tr>
<?php endforeach ?>
</table>

  <a href="<?php echo url_for('apConnectionPolicies/new') ?>"><?php echo __("New");?></a>
