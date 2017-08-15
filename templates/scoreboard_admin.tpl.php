<?php
$confirmDialog = false;

$action = isset($_GET['action']) ? $_GET['action'] : false;

switch ($action) {
  case 'solo_games' :
    $confirm = isset($_GET['confirm']) ? true : false;
    if ($confirm) {
      $withResults = isset($_GET['results']) ? true : false;
      lsGenerateMatches('solo', $withResults);
    } else {
      $message = "Are you sure you generate matches?<br />
      Existing matches will be removed.
      <br/><strong>This can not be undone!</strong>";
      $confirmDialog = true;
    }
    break;
  case 'head_to_head_games' :
    $confirm = isset($_GET['confirm']) ? true : false;
    if ($confirm) {
      $withResults = isset($_GET['results']) ? true : false;
      lsGenerateMatches('team', $withResults);
    } else {
      $message = "Are you sure you generate matches?<br />
      Existing matches will be removed.
      <br/><strong>This can not be undone!</strong>";
      $confirmDialog = true;
    }
    break;    
  case 'delete_data' :
    $confirm = isset($_GET['confirm']) ? true : false;
    if ($confirm)
      lsDeleteData();
    else {
      $message = "Are you sure you want to delete all the data?<br /><strong>This can not be undone!</strong>";
      $confirmDialog = true;
    }
    break;
  case 'install_test_data';
    $confirm = isset($_GET['confirm']) ? true : false;
    if ($confirm)
      lsInstallTestData();
    else {
      $message = "Are you sure you want to install the test data?<br /> This will remove all existing data.<br /><strong>This can not be undone!</strong>";
      $confirmDialog = true;
    }
    break;
  default:
    break;
}

?>
<?php if ($confirmDialog) : ?>
<div>
  <p><?php echo $message ?></p>
  <a href="<?php echo admin_url() ?>admin.php?page=scoreboard&action=<?php echo $action ?>&confirm=true<?php echo isset($_GET['results']) ? "&results=true" : '' ?>">Continue</a>
  <a href="<?php echo admin_url() ?>admin.php?page=scoreboard">Cancel</a>
</div>
<?php endif; ?>
<ul>
  <li>
    <a href="<?php echo admin_url() ?>admin.php?page=scoreboard&action=head_to_head_games">Generate Head to Head Games</a>
  </li>
  <li>
    <a href="<?php echo admin_url() ?>admin.php?page=scoreboard&action=head_to_head_game&results=true">Generate Head to Head Games With Results</a>
  </li>
  <li>
    <a href="<?php echo admin_url() ?>admin.php?page=scoreboard&action=solo_games">Generate Solo Games</a>
  </li>
  <li>
    <a href="<?php echo admin_url() ?>admin.php?page=scoreboard&action=solo_games&results=true">Generate Solo Games With Results</a>
  </li>
  <li>
    <a href="<?php echo admin_url() ?>admin.php?page=scoreboard&action=delete_data">Delete Data</a>
  </li>
  <li>
    <a href="<?php echo admin_url() ?>admin.php?page=scoreboard&action=install_test_data">Install Test Data</a>  
  </li>
</ul>

