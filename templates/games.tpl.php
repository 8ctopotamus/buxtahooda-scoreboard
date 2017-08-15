<?php
  $mode = 'add';
  $title = 'Add Game';
  $actionVerb = "add";
  $action = "add";
  $currentObj = false;
  
  if (isset($_GET['edit'])) {
    $mode = 'edit';
    $title = "Edit Game";
    $actionVerb = "edit";
    $action = "edit";
    
    $currentObj = getGameById($_GET['edit']);
  }
  
  if (isset($_GET['delete'])) {
    $message = "Game Deleted!";
    deleteGame($_GET['delete']);
  }

  if (isset($_POST['form_action'])) {    
    $formAction = $_POST['form_action'];
    
    switch ($formAction) {
      case 'add' :
        $result = addGame($_POST['name'], $_POST['description'], $_POST['venues']);        
        if ($result) {
          $message = "Game added successfully!";
        } else {
          $message = "There was an error!";
          $nameValue = $_POST['name'];
          $descriptionValue = $_POST['description'];
          $venuesValue = $_POST['venues'];
        }
        break;
      case 'edit':
        $result = updateGame($_POST['game_id'], $_POST['name'], $_POST['description'], $_POST['venues']);
        if ($result || $result === 0) {
          $message = "Game Updated!";
        } else {
          $message = "There was an error updating game.";
        }
        break;
      default:
        $message = "Form Submission!";
        break;
    }
  }

  $games = getGames();
  $venues = getVenues();
  
  $nameValue = isset($nameValue)
    ? $nameValue
    : ($currentObj ? $currentObj->name : false);
    
  $venuesValue = isset($venuesValue)
    ? $venuesValue
    : ($currentObj ? $currentObj->venues : false);
    
  $gameId = isset($gameId)
    ? $gameId
    : ($currentObj ? $currentObj->id : false);
    
  $descriptionValue = isset($descriptionValue)
    ? $descriptionValue
    : ($currentObj ? $currentObj->description : false);
    
?>
<h2>Games</h2>
<div id="team-form">
  <?php if (isset($message) && $message): ?>
    <div class="message">
      <?php echo $message; ?>
    </div>
  <?php endif; ?>
  <h3><?php echo $title ?></h3>
  <form method="post" action="<?php echo admin_url('admin.php?page=games') ?>">
    <input type="hidden" name="form_action" value="<?php echo $action ?>" />
    <input type="hidden" name="game_id" value="<?php echo $gameId ?>" />
    <ul>
      <li>
        <label for="name">Name</label>
        <input type="text" name="name" value="<?php echo $nameValue ?>" />
      </li>
      <li>      
        <label for="description">Description</label>
        <textarea name="description"><?php echo $descriptionValue; ?></textarea>
      </li>      
      <li>
        <label for="name">Venues</label>
        <select name="venues[]" multiple="multiple">
          <?php $selected = ''; ?>
          <?php $venueIds = array(); ?>
          <?php if ($currentObj) foreach (getVenuesByGameId($currentObj->id) as $gameVenue) $venueIds[] = $gameVenue->id; ?>
          <?php foreach ($venues as $key => $venue) : ?>
            <?php
              if (array_search($venue->id, $venueIds) !== false) {
                $selected = 'selected="selected"';
              } else 
            ?>
            <option value="<?php echo $venue->id ?>" <?php echo $selected ?>><?php echo $venue->name ?></option>
          <?php endforeach; ?>
          
        </select>
      </li>
    </ul>
    <input type="submit" name="submit" value="<?php  echo $actionVerb ?>" />
  </form>
</div>
<div id="game-list">
  <h2>Games</h2>
  <?php foreach ($games as $index => $game): ?>
    <div>
      <h4><?php echo $game->name ?></h4>
      <p><?php echo $game->description ?></p>
      <a href="<?php echo admin_url('admin.php?page=games&edit='.$game->id) ?>" >edit</a> 
      <a href="<?php echo admin_url('admin.php?page=games&delete='.$game->id) ?>" >delete</a>
      <?php $gameVenues = getVenuesByGameId($game->id); ?>
      <p>
        <?php $venuesString = array(); ?>
        <?php foreach ($gameVenues as $venue) $venuesString[] = $venue->name ?>
        <?php $venuesString = implode(', ', $venuesString); ?>
        <strong>Venues:</strong> <?php echo $venuesString; ?> 
      </p>
    </div>
  
  <?php endforeach; ?>
</div>