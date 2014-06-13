<h1><?php echo __('Local users list') ?></h1>
<form action="<?php echo url_for('ap_authlocaluser_login'); ?>" method="post">
<?php echo $filter; 
$event = $this->dispatcher->filter(new sfEvent($this, 'localuser.showing_list_header', array('headers' => array())), array());
$extracols = $event->getReturnValue();
$event = $this->dispatcher->filter(new sfEvent($this, 'identity.showing_list_header', array('headers' => $extracols, 'auth_type' => 'apAuthLocalUserLogin')), $extracols);
$extracols = $event->getReturnValue();  
?>
<input type="submit" value="Filter"/>
</form>
<table>
<thead>
  <tr>
    <th><?php echo __('Username') ?></th>
    <th><?php echo __('Email') ?></th>
    <th><?php echo __('Registered on') ?></th>
    <th><?php echo __('Status') ?></th>
    <?php foreach ($extracols as $extracol => $detail): ?>
      <th><?php echo __($extracol);?></th>
    <?php endforeach;?>
  </tr>
</thead>

<?php foreach ($pager->getResults() as $login) : ?>
    <tr class="online">


    <td><a href="<?php echo url_for('ap_authlocaluser_show', $login) ?>"><?php echo $login->getUsername(); ?></a></td>
    <td><?php echo $login->getEmail(); ?></td>
    <td><?php echo $login->getRegisteredOn(); ?></td>
    <td><?php echo $login->getStatusText(); ?></td>
        <?php foreach ($extracols as $extracol => $detail): ?>
      <td><?php if (isset($detail['method'])) {
        $function = $detail['method'];
        if (method_exists($login, $function))
            echo __($login->$function);
        else echo "Method $function does not exist for object login";
      } elseif (isset($detail['callable'])) {
          echo __(call_user_func_array($detail['callable'], array($login)));
      }?></td>
    <?php endforeach;?>

  </tr>
<?php endforeach ?>
</table>
 
<?php if ($pager->haveToPaginate()): ?>
  <div class="pagination">
    <a href="<?php echo url_for('ap_authlocaluser_login') ?>?page=1">
      <img src="/sfDoctrinePlugin/images/first.png" alt="First page" title="First page" />
    </a>
 
    <a href="<?php echo url_for('ap_authlocaluser_login') ?>?page=<?php echo $pager->getPreviousPage() ?>">
      <img src="/sfDoctrinePlugin/images/previous.png" alt="Previous page" title="Previous page" />
    </a>
 
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <a href="<?php echo url_for('ap_authlocaluser_login') ?>?page=<?php echo $page ?>"><?php echo $page ?></a>
      <?php endif; ?>
    <?php endforeach; ?>
 
    <a href="<?php echo url_for('ap_authlocaluser_login') ?>?page=<?php echo $pager->getNextPage() ?>">
      <img src="/sfDoctrinePlugin/images/next.png" alt="Next page" title="Next page" />
    </a>
 
    <a href="<?php echo url_for('ap_authlocaluser_login') ?>?page=<?php echo $pager->getLastPage() ?>">
      <img src="/sfDoctrinePlugin/images/last.png" alt="Last page" title="Last page" />
    </a>
  </div>
<?php endif; ?>
 
<div class="pagination_desc">
  <strong><?php echo count($pager) ?></strong> total users
 
  <?php if ($pager->haveToPaginate()): ?>
    - page <strong><?php echo $pager->getPage() ?>/<?php echo $pager->getLastPage() ?></strong>
  <?php endif; ?>


  <p><a href="<?php echo url_for('apAuthLocalUserLogin/new') ?>">New</a></p>
</div>
