<?php
  $mode = 'add';
  $title = 'Add Team';
  $actionVerb = "add";
  $action = "add_team";
  $currentTeam = false;
  
  if (isset($_GET['edit'])) {
    $mode = 'edit';
    $title = "Edit Team";
    $actionVerb = "edit";
    $action = "edit_team";
    
    $currentTeam = getTeamById($_GET['edit']);
  }
  
  if (isset($_GET['delete'])) {
    $message = "Team Deleted!";
    deleteTeam($_GET['delete']);
  }

  if (isset($_POST['form_action'])) {    
    $formAction = $_POST['form_action'];
    
    switch ($formAction) {
      case 'add_team' :
        $result = addTeam($_POST['name']);        
        if ($result) {
          $message = "Team added successfully!";
        } else {
          $message = "There was an error!";
          $nameValue = $_POST['name'];
        }
        break;
      case 'edit_team':
        $result = updateTeam($_POST['team_id'], $_POST['name']);
        if ($result) {
          $message = "Team Updated!";
        } else {
          $message = "There was an error updating team.";
        }
        break;
      default:
        $message = "Form Submission!";
        break;
    }
  }

  $teams = getTeams();
  
  $nameValue = isset($nameValue)
    ? $nameValue
    : ($currentTeam ? $currentTeam->name : false);
    
  $teamId = isset($teamId)
    ? $teamId
    : ($currentTeam ? $currentTeam->id : false);
    
?>
<h2>Teams</h2>
<div id="team-form">
  <?php if (isset($message) && $message): ?>
    <div class="message">
      <?php echo $message; ?>
    </div>
  <?php endif; ?>
  <h3><?php echo $title ?></h3>
  <form method="post" action="<?php echo admin_url('admin.php?page=teams') ?>">
    <input type="hidden" name="form_action" value="<?php echo $action ?>" />
    <input type="hidden" name="team_id" value="<?php echo $teamId ?>" />
    <ul>
      <li>
        <label for="name">Team Name</label>
        <input type="text" name="name" value="<?php echo $nameValue ?>" />
      </li>
    </ul>
    <input type="submit" name="submit" value="<?php  echo $actionVerb ?>" />
  </form>
</div>
<div id="team-list">
  <h2>Teams</h2>
  <?php foreach ($teams as $index => $team): ?>
    <div>
      <h4><?php echo $team->name ?></h4>
      <a href="<?php echo admin_url('admin.php?page=teams&edit='.$team->id) ?>" >edit</a> 
      <a href="<?php echo admin_url('admin.php?page=teams&delete='.$team->id) ?>" >delete</a>
      <?php $players = getPlayersByTeam($team); ?>
      <p>
        <?php $playerString = array(); ?>
        <?php foreach ($players as $player) $playerString[] = $player->name ?>
        <?php $playerString = implode(', ', $playerString); ?>
        <strong>Players</strong>: <?php echo $playerString; ?> 
      </p>
    </div>
  
  <?php endforeach; ?>
</div>