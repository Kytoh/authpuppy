<h1><?php echo __("Network users list")?></h1>
<form action="<?php echo url_for('ap_local_user_token_index'); ?>" method="post">
<?php echo $filter; 
?>
<input type="submit" value="Filter"/>
</form>
<table>
  <thead>
    <tr>
      <th><?php echo __("First name")?></th>
      <th><?php echo __("Last name")?></th>
      <th><?php echo __("Birth date")?></th>
      <th><?php echo __("Birth place")?></th>
      <th><?php echo __("Document")?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($pager->getResults() as $ap_physical_user): ?>
    <tr>
      <td><a href="<?php echo url_for('apLocalUserToken/edit?id='.$ap_physical_user->getId()) ?>"><?php echo $ap_physical_user->getFirstName() ?></a></td>
      <td><?php echo $ap_physical_user->getLastName() ?></td>
      <td><?php echo $ap_physical_user->getBirthDate() ?></td>
      <td><?php echo $ap_physical_user->getBirthPlace() ?></td>
      <td><?php echo $ap_physical_user->getDocument() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php if ($pager->haveToPaginate()): ?>
  <div class="pagination">
    <a href="<?php echo url_for('ap_local_user_token_index') ?>?page=1">
      <img src="/sfDoctrinePlugin/images/first.png" alt="First page" title="First page" />
    </a>
 
    <a href="<?php echo url_for('ap_local_user_token_index') ?>?page=<?php echo $pager->getPreviousPage() ?>">
      <img src="/sfDoctrinePlugin/images/previous.png" alt="Previous page" title="<?php echo __("Previous page")?>" />
    </a>
 
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <a href="<?php echo url_for('ap_local_user_token_index') ?>?page=<?php echo $page ?>"><?php echo $page ?></a>
      <?php endif; ?>
    <?php endforeach; ?>
 
    <a href="<?php echo url_for('ap_local_user_token_index') ?>?page=<?php echo $pager->getNextPage() ?>">
      <img src="/sfDoctrinePlugin/images/next.png" alt="Next page" title="<?php echo __("Next page")?>" />
    </a>
 
    <a href="<?php echo url_for('ap_local_user_token_index') ?>?page=<?php echo $pager->getLastPage() ?>">
      <img src="/sfDoctrinePlugin/images/last.png" alt="Last page" title="<?php echo __("Last page")?>" />
    </a>
  </div>
<?php endif; ?>
 
<div class="pagination_desc">
  <strong><?php echo count($pager) ?></strong> <?php echo __("total users")?>
 
  <?php if ($pager->haveToPaginate()): ?>
    - <?php echo __("page")?> <strong><?php echo $pager->getPage() ?>/<?php echo $pager->getLastPage() ?></strong>
  <?php endif; ?>


  <p><a href="<?php echo url_for('apLocalUserToken/new') ?>"><?php echo __("New")?></a></p>
</div>

