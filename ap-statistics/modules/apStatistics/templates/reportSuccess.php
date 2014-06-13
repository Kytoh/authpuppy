<h1><?php echo __("Statistics") ?></h1>
<?php include_partial('form', array('form' => $form, 'report' => $sf_data->getRaw('report')));

if (!is_null($data)) {
  include_partial('report', array('data' => $sf_data->getRaw('data'), 'report' => $report));
}
