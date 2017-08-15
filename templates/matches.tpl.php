<?php
  $mode = 'add';
  $title = 'Add Match';
  $actionVerb = "add";
  $action = "add";
  $currentTeam = false;
  $teams = getTeams();
  
  if (isset($_GET['edit'])) {
    $mode = 'edit';
    $title = "Edit Match";
    $actionVerb = "edit";
    $action = "edit";
    
    $currentMatch = getMatchById($_GET['edit']);
  }
  
  if (isset($_GET['delete'])) {
    $message = "Match Deleted!";
    deleteMatch($_GET['delete']);
  }

  if (isset($_POST['form_action'])) {    
    $formAction = $_POST['form_action'];
    
    $matchResult = isset($_POST['result']) ? $_POST['result'] : '';
    $matchScore = isset($_POST['score']) ? $_POST['score'] : '';
    
    $gameVenue = explode('-', $_POST['game_venue']);
    $gameId = $gameVenue[0];
    $venueId = $gameVenue[1];    
    
    switch ($formAction) {
      case 'add' :        
        $result = addMatch($_POST['team_a'], $_POST['team_b'], $gameId, $venueId, $matchResult, $matchScore, $_POST['mode']);        
        if ($result) {
          $message = "Match added successfully!";
        } else {
          $message = "There was an error!";
          $modeValue = $_POST['mode'];
          $teamAValue = $_POST['team_a'];
          $teamBValue = $_POST['team_b'];
          $gameVenueValue = $_POST['game_venue'];
          $resultValue = $_POST['result'];
          $scoreValue = $_POST['score'];          
        }
        break;
      case 'edit':
        $result = updateMatch($_POST['match_id'], $_POST['team_a'], $_POST['team_b'], $gameId, $venueId, $matchResult, $matchScore, $_POST['mode']);
        if ($result) {
          $message = "Match Updated!";
        } else {
          $message = "There was an error updating match.";
        }
        break;
      default:
        $message = "Form Submission!";
        break;
    }
  }

  $matches = getMatches();
  $teams = getTeams();
  $venues = getVenues();
  $games = getGames();
  
  
  $matchId = isset($matchId)
    ? $matchId
    : ($currentMatch ? $currentMatch->id : false);
    
  $modeValue = isset($modeValue) 
    ? $modeValue
    : ($currentMatch ? $currentMatch->mode : 'solo');
    
  $teamAValue = isset($teamAValue)
    ? $teamAValue
    : ($currentMatch ? $currentMatch->team_a : false);    

  $teamBValue = isset($teamBValue)
    ? $teamBValue
    : ($currentMatch ? $currentMatch->team_b : false);    

  $gameVenueValue = isset($gameVenueValue)
    ? $gameVenueValue
    : ($currentMatch ? $currentMatch->game_id."-".$currentMatch->venue_id : false);

  $resultValue = isset($resultValue)
    ? $resultValue
    : ($currentMatch ? $currentMatch->result : false);    

  $scoreValue = isset($scoreValue)
    ? $scoreValue
    : ($currentMatch ? $currentMatch->score : false);
    
?>
<h2>Matches</h2>
<div id="team-form">
  <?php if (isset($message) && $message): ?>
    <div class="message">
      <?php echo $message; ?>
    </div>
  <?php endif; ?>
  <h3><?php echo $title ?></h3>
  <form method="post" action="<?php echo admin_url('admin.php?page=matches') ?>">
    <input type="hidden" name="form_action" value="<?php echo $action ?>" />
    <input type="hidden" name="match_id" value="<?php echo $matchId ?>" />
    <ul>
     <li>
        <label for="type">Type</label>
        <select name="mode">
            <option value="solo" <?php echo $modeValue == 'solo' ? 'selected="selected"' : false ?>>Solo</option>
            <option value="team" <?php echo $modeValue == 'team' ? 'selected="selected"' : false ?>>Head to Head</option>            
        </select>
      </li>    
     <li>
        <label for="team_a">Team A</label>
        <select name="team_a">
          <?php foreach($teams as $team): ?>
            <option value="<?php echo $team->id ?>" <?php echo $teamAValue == $team->id ? 'selected="selected"' : '' ?>><?php echo $team->name ?></option>
          <?php endforeach; ?>
        </select>
      </li>
     <li>
        <label for="team_b">Team B</label>
        <select name="team_b">
          <option value="">No Team</option>
          <?php foreach($teams as $team): ?>
            <option value="<?php echo $team->id ?>" <?php echo $teamBValue == $team->id ? 'selected="selected"' : '' ?>><?php echo $team->name ?></option>
          <?php endforeach; ?>
        </select>
      </li>      
     <li>
        <label for="game_venue">Venue and Game</label>
        <select name="game_venue">
          <?php foreach($venues as $venue): ?>
            <?php foreach (getGamesByVenueId($venue->id) as $game): ?>
              <?php $optionValue = $game->id."-".$venue->id; ?>
              <option value="<?php echo $optionValue ?>" <?php if ($optionValue == $gameVenueValue) echo 'selected="selected"' ?> ><?php echo $venue->name ?> - <?php echo $game->name ?></option>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </select>
      </li>
      <?php if ($mode == 'edit'): ?>
      <li>
        <label for="result">Result</label>
        <?php $teamA = getTeamById($currentMatch->team_a); ?>
        <?php $teamB = getTeamById($currentMatch->team_b); ?>
        <?php $matchResult = $currentMatch->result ?>
        <select name="result">
          <?php if ($currentMatch->mode == 'solo'): ?>
            <option value="">Unplayed</option>
            <option value="<?php echo $teamA->id ?>" <?php echo $matchResult == $teamA->id ? 'selected="selected"': '' ?>>Played</option>
          <?php else: ?>
            <option value="">No Result / Tie</option>
            <option value="<?php echo $teamA->id ?>" <?php echo $matchResult == $teamA->id ? 'selected="selected"': '' ?>><?php echo $teamA->name ?></option>
            <option value="<?php echo $teamB->id ?>" <?php echo $matchResult == $teamB->id ? 'selected="selected"': '' ?>><?php echo $teamB->name ?></option>                
          <?php endif; ?>
        </select>
        
      </li>
      <li>
        <label for="score">Score</label>
        <input type="text" name="score" value="<?php echo $scoreValue ?>" />
      </li>
      <?php endif; ?>
    </ul>
    <input type="submit" name="submit" value="<?php  echo $actionVerb ?>" />
  </form>
</div>
<div id="team-list">
  <h2>Matches</h2>
  <?php foreach ($matches as $index => $match): ?>
    <?php $teamA = getTeamById($match->team_a); ?>
    <?php $teamB = getTeamById($match->team_b); ?>   
    <?php $venue = getVenueById($match->venue_id); ?>        
    <?php $game = getGameById($match->game_id); ?>
    
    <div>       
      <p>
        <?php if ($match->mode == "solo"): ?>
          <?php echo $teamA->name ?><br />
          <?php echo $venue->name." - ".$game->name; ?><br />
          <?php if ($match->result): ?>
            Score: <?php echo $match->score ?>
          <?php else: ?>  
            No Result
          <?php endif; ?>
        <?php else: ?>
          <?php $winnerName = $match->result 
            ? ( $match->result == $teamA
                 ? $teamA->name
                 : $teamB->name )
            : ''; ?>        
          <?php echo $teamA->name ?> vs. <?php echo $teamB->name ?> at <?php echo $venue->name ?><br />
          <?php if ($match->result): ?>
            Result: <?php echo $winnerName ?> -- Score: <?php echo $match->score ?>
          <?php else: ?>  
            No Result
          <?php endif; ?>          
        <?php endif; ?>
      </p>
      <a href="<?php echo admin_url('admin.php?page=matches&edit='.$match->id) ?>" >edit</a> 
      <a href="<?php echo admin_url('admin.php?page=matches&delete='.$match->id) ?>" >delete</a>
    </div>
  <?php endforeach; ?>
</div>