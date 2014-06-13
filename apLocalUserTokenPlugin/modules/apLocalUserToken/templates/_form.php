<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<form action="<?php echo url_for('apLocalUserToken/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('apLocalUserToken/index') ?>"><?php echo __("Back to list")?></a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to(__("Delete"), 'apLocalUserToken/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="<?php echo __("Save")?>" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['first_name']->renderLabel() ?></th>
        <td>
          <?php echo $form['first_name']->renderError() ?>
          <?php echo $form['first_name'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['last_name']->renderLabel() ?></th>
        <td>
          <?php echo $form['last_name']->renderError() ?>
          <?php echo $form['last_name'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['birth_date']->renderLabel() ?></th>
        <td>
          <?php echo $form['birth_date']->renderError() ?>
          <?php echo $form['birth_date'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['birth_place']->renderLabel() ?></th>
        <td>
          <?php echo $form['birth_place']->renderError() ?>
          <?php echo $form['birth_place'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['address']->renderLabel() ?></th>
        <td>
          <?php echo $form['address']->renderError() ?>
          <?php echo $form['address'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['city']->renderLabel() ?></th>
        <td>
          <?php echo $form['city']->renderError() ?>
          <?php echo $form['city'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['province']->renderLabel() ?></th>
        <td>
          <?php echo $form['province']->renderError() ?>
          <?php echo $form['province'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['zip']->renderLabel() ?></th>
        <td>
          <?php echo $form['zip']->renderError() ?>
          <?php echo $form['zip'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['user_status']->renderLabel() ?></th>
        <td>
          <?php echo $form['user_status']->renderError() ?>
          <?php echo $form['user_status'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['document_type']->renderLabel() ?></th>
        <td>
          <?php echo $form['document_type']->renderError() ?>
          <?php echo $form['document_type'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['document']->renderLabel() ?></th>
        <td>
          <?php echo $form['document']->renderError() ?>
          <?php echo $form['document'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['simple_network_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['simple_network_id']->renderError() ?>
          <?php echo $form['simple_network_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['local_user_profile_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['local_user_profile_id']->renderError() ?>
          <?php echo $form['local_user_profile_id'] ?>
          <input type="submit" name="submit[createticket]" value="<?php echo __("Create ticket")?>" />
        </td>
      </tr>
      <tr>
        <th><?php echo $form['payment']->renderLabel() ?></th>
        <td>
          <?php echo $form['payment']->renderError() ?>
          <?php echo $form['payment'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['ticket_notes']->renderLabel() ?></th>
        <td>
          <?php echo $form['ticket_notes']->renderError() ?>
          <?php echo $form['ticket_notes'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
