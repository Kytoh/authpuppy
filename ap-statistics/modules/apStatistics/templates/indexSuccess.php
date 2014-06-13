<h1><?php echo __("Statistics"); ?></h1>

<ul>
  <?php foreach ($sf_data->getRaw('report_list') as $report) :?>
  <li><a href="<?php echo url_for('ap_statistics_report', $report['params']) ?>"><?php echo __($report['title']); ?></a></li>
  <?php endforeach; ?>
</ul>