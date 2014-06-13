<h1>Ap simple networks List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Name</th>
      <th>Address</th>
      <th>Owner</th>
      <th>Email</th>
      <th>Created at</th>
      <th>Updated at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ap_simple_networks as $ap_simple_network): ?>
    <tr>
      <td><a href="<?php echo url_for('apSimpleNetwork/edit?id='.$ap_simple_network->getId()) ?>"><?php echo $ap_simple_network->getId() ?></a></td>
      <td><?php echo $ap_simple_network->getName() ?></td>
      <td><?php echo $ap_simple_network->getAddress() ?></td>
      <td><?php echo $ap_simple_network->getOwner() ?></td>
      <td><?php echo $ap_simple_network->getEmail() ?></td>
      <td><?php echo $ap_simple_network->getCreatedAt() ?></td>
      <td><?php echo $ap_simple_network->getUpdatedAt() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('apSimpleNetwork/new') ?>">New</a>
