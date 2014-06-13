<?php

foreach ($sf_data->getRaw('data') as $rptname => $onereport) {
  if (empty($onereport)) : ?> No result
  <?php endif;
  $headerset = false; 
  ?><h2><?php echo __($rptname);?></h2><table>
  <?php foreach ($onereport as $arow):
    if (!$headerset): ?>
    <thead>
        <tr>
          <?php foreach ($arow as $key => $value): ?>
            <th><?php echo __($key); ?></th>
          <?php endforeach; ?>
        </tr>
    </thead>
    <tfoot>
        <tr>
          <?php foreach ($arow as $key => $value): ?>
            <td><?php echo $report->getSum($key, $rptname); ?></td>
          <?php endforeach; ?>
        </tr>
    </tfoot>
  <?php $headerset = true; endif; ?>
    <tr>
    <?php foreach ($arow as $key => $value): ?>
        <td><?php echo __($value); ?></td>
      <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
    
  </table><?php 
}