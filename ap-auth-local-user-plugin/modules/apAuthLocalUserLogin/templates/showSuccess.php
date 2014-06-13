<table>
  <tbody>
    <tr>
      <th>Id:</th>
      <td><?php echo $login->getId() ?></td>
    </tr>
    <tr>
      <th>Username:</th>
      <td><?php echo $login->getUsername() ?></td>
    </tr>
    <tr>
      <th>Email:</th>
      <td><?php echo $login->getEmail() ?></td>
    </tr>
    <tr>
      <th>Registered On:</th>
      <td><?php echo $login->getRegisteredOn() ?></td>
    </tr>
    <tr>
      <th>Status:</th>
      <td><?php echo $login->getStatusText(); ?></td>
    </tr>
  </tbody>
</table>

<hr />

<a href="<?php echo  url_for('ap_authlocaluser_edit', $login) ?>">Edit</a>
&nbsp;
<a href="<?php echo url_for('apAuthLocalUserLogin/index') ?>">List</a>
