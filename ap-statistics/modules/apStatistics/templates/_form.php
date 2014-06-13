<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form); ?>


<form action="<?php echo url_for('ap_statistics_report', array('report_name' => get_class($sf_data->getRaw('report')))) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          &nbsp;<a href="<?php echo url_for('ap_statistics_main') ?>"><?php echo __("Report index") ?></a>
          <input type="submit" value="<?php echo __("Generate"); ?>" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form ?>
    </tbody>
  </table>
</form>
