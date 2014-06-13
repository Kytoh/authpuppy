<h1><?php echo __("Edit network user")?></h1>

<?php if ($has_ticket == 1) : ?>
  <div class="notice"><a href="<?php echo url_for('apLocalUserToken/viewTicket') ?>" target="_blank"><?php echo __("View ticket")?></a></div>
<?php endif; ?>

<?php include_partial('form', array('form' => $form)) ?>
