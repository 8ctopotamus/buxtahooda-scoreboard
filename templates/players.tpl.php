<?php
  $mode = 'add';
  $title = 'Add Player';
  $actionVerb = "add";
  $action = "add";
  $currentTeam = false;
  $teams = getTeams();
  
  if (isset($_GET['edit'])) {
    $mode = 'edit';
    $title = "Edit Player";
    $actionVerb = "edit";
    $action = "edit";
    
    $currentPlayer = getPlayerById($_GET['edit']);    
  }
  
  if (isset($_GET['delete'])) {
    $message = "Player Deleted!";
    deletePlayer($_GET['delete']);
  }

  if (isset($_POST['form_action'])) {    
    $formAction = $_POST['form_action'];
    
    switch ($formAction) {
      case 'add' :
        $result = addPlayer($_POST['name'], $_POST['team_id']);        
        if ($result) {
          $message = "Player added successfully!";
        } else {
          $message = "There was an error!";
          $nameValue = $_POST['name'];
          $teamId = $_POST['team_id'];
        }
        break;
      case 'edit':
        $result = updatePlayer($_POST['player_id'], $_POST['name'], $_POST['team_id']);
        if ($result) {
          $message = "Player Updated!";
        } else {
          $message = "There was an error updating player.";
        }
        break;
      default:
        $message = "Form Submission!";
        break;
    }
  }

  $players = getPlayers();
  
  $nameValue = isset($nameValue)
    ? $nameValue
    : ($currentPlayer ? $currentPlayer->name : false);
    
  $teamId = isset($teamId)
    ? $teamId
    : ($currentPlayer ? $currentPlayer->team_id : false);
    
  $playerId = isset($playerId)
    ? $playerId
    : ($currentPlayer ? $currentPlayer->id : false);
    
?>
<h2>Players</h2>
<div id="team-form">
  <?php if (isset($message) && $message): ?>
    <div class="message">
      <?php echo $message; ?>
    </div>
  <?php endif; ?>
  <h3><?php echo $title ?></h3>
  <form method="post" action="<?php echo admin_url('admin.php?page=players') ?>">
    <input type="hidden" name="form_action" value="<?php echo $action ?>" />
    <input type="hidden" name="player_id" value="<?php echo $playerId ?>" />
    <ul>
      <li>
        <label for="name">Player Name</label>
        <input type="text" name="name" value="<?php echo $nameValue ?>" />
      </li>
     <li>
        <label for="name">Team</label>
        <select name="team_id">
          <?php foreach ($teams as $team): ?>
            <option value="<?php echo $team->id ?>" <?php echo $teamId = $team->id ? 'selected="selected"' : '' ?>><?php echo $team->name ?></option>
          <?php endforeach; ?>
        </select>
      </li>
       
    </ul>
    <input type="submit" name="submit" value="<?php  echo $actionVerb ?>" />
  </form>
</div>
<div id="team-list">
  <h2>Players</h2>
  <?php foreach ($players as $index => $player): ?>
    <div>
      <?php $team = getTeamById($player->team_id); ?>
       
      <p><strong><?php echo $player->name ?></strong> : <?php echo $team->name; ?></p>
      <a href="<?php echo admin_url('admin.php?page=players&edit='.$player->id) ?>" >edit</a> 
      <a href="<?php echo admin_url('admin.php?page=players&delete='.$player->id) ?>" >delete</a>
    </div>
  <?php endforeach; ?>
</div>