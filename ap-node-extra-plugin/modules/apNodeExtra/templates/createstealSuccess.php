<?php echo __("You can either add a new node or steal an existing one."); ?>

<form method="post" action="<?php echo url_for('apNodeExtra/createsteal'); ?>">
<?php echo $form->renderHiddenFields();?>
<table>
  <tr>
    <td>
      &nbsp;
    </td>
    <td><input type="submit"name="submit[create]" value="Create"/></td>
  </tr>
  <tr><td><?php echo $form['nodes_list']; ?></td>
    <td><input type="submit"name="submit[steal]" value="Steal"/></td>
  </tr>
</table>
</form>