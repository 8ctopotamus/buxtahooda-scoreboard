<?php

global $wpdb;
global $teamTable;
global $playerTable;
global $matchTable;
global $venueTable;
global $gameTable;
global $venueGameTable;

$teamTable = $wpdb->prefix.'livescoreboard_team';
$playerTable = $wpdb->prefix.'livescoreboard_player';
$matchTable = $wpdb->prefix.'livescoreboard_match';
$venueTable = $wpdb->prefix.'livescoreboard_venue';
$officialTable = $wpdb->prefix.'livescoreboard_official';
$gameTable = $wpdb->prefix.'livescoreboard_game';
$venueGameTable = $wpdb->prefix.'livescoreboard_venuegame';

function addGame($name, $description, $venues) {
  global $wpdb;
  global $gameTable;
  
  $wpdb->insert(
    $gameTable,
    array(
      'name' => $name,
      'description' => $description
    )
  );
  $gameId = $wpdb->insert_id;
  
  foreach ($venues as $key => $venueId) {
    addVenueGame($venueId, $gameId);
  }
}

function addMatch($teamA, $teamB, $gameId, $venueId, $result, $score, $mode = 'solo') {
  global $wpdb;
  global $matchTable;
  
  return $wpdb->insert(
    $matchTable,
    array(
      "team_a" => $teamA,
      "team_b" => $teamB,
      "game_id" => $gameId,
      "venue_id" => $venueId,
      "result" => $result,
      "score" => $score,
      "mode" => $mode
    )
  );
}

function addPlayer($name, $team_id) {
  global $wpdb;
  global $playerTable;
  
  return $wpdb->insert(
    $playerTable,
    array(
      "name" => $name,
      "team_id" => $team_id
    )
  );
}

function addTeam($name) {
  global $wpdb;
  global $teamTable;
  
  return $wpdb->insert($teamTable, array('name' => $name));
}

function addVenue($name, $address) {
  global $wpdb;
  global $venueTable;
  
  if ($wpdb->insert($venueTable, array('name' => $name, 'address' => $address)))
    return $wpdb->insert_id;
    
  return false;  
}

function addVenueGame($venueId, $gameId) {
  global $wpdb;
  global $venueGameTable;
  
  $wpdb->insert(
    $venueGameTable,
    array(
      'venue_id' => $venueId,
      'game_id' => $gameId
    )
  );
}

function cmpName($a, $b) {
  return strcasecmp($a['team']->name, $b['team']->name);
}

function cmpTotal($a, $b) {
  if ($a['total'] == $b['total']) {
    return 0;
  }
  return ($a['total'] < $b['total']) ? 1 : -1;  
}

function cmpGameTotal($a, $b) {
  if ($a['game'] == $b['game']) {
    return 0;
  }
  return ($a['game'] < $b['game']) ? 1 : -1;  
}


function deleteGame($gameId) {
  global $wpdb;
  global $gameTable;
  
  $wpdb->get_results(
    "DELETE FROM $gameTable WHERE id = $gameId"
  );
  
  deleteVenueGame($gameId);
} 

function deleteMatch($matchId) {
  global $wpdb;
  global $matchTable;
    
  $wpdb->get_results(
    "DELETE FROM $matchTable WHERE id = $matchId"
  );
  return true;  
}

function deletePlayer($playerId) {
  global $wpdb;
  global $playerTable;
  
  return $wpdb->get_results(
    "DELETE FROM $playerTable WHERE id = $playerId"
  );
}

function deleteTeam($team_id) {
  global $wpdb;
  global $playerTable;
  global $teamTable;
  global $matchTable;
  
  $wpdb->get_results(
    "DELETE FROM $teamTable WHERE id = $team_id"
  );
  
  $wpdb->get_results(
    "DELETE FROM $matchTable WHERE team_a = $team_id OR team_b = $team_id"
  );  
  
  $wpdb->update(
    $playerTable,
    array(
      "team_id" => ''
    ),
    array(
      "team_id" => $team_id
    ),
    array(
      "%s"
    )
  );
  
  return;
}

function deleteVenue($venueId) {
  global $wpdb;
  global $venueTable;
  global $matchTable;
  
  $wpdb->get_results(
    "DELETE FROM $venueTable WHERE id = $venueId"
  );
  
  $wpdb->get_results(
    "DELETE FROM $matchTable WHERE venue_id = $venueId"
  );
  
  deleteVenueGameByVenueId($venueId);
}

function deleteVenueGameByGameId($gameId) {
  global $wpdb;
  global $venueGameTable;
  
  $wpdb->get_results("DELETE FROM $venueGameTable WHERE game_id = $gameId");
}

function deleteVenueGameByVenueId($venueId) {
  global $wpdb;
  global $venueGameTable;
  
  $wpdb->get_results("DELETE FROM $venueGameTable WHERE venue_id = $venueId");
}

function getGameById($id) {
  global $wpdb;
  global $gameTable;
  
  $results = $wpdb->get_results("SELECT * FROM $gameTable WHERE id = $id");

  if (count($results) > 0) {
    $game = $results[0];
    $game->venues = getVenuesByGameId($game->id);
    
    return $game;
  }
  return false;
  
  return count($results) > 0 ? $results[0] : false;
}

function getGamesByVenueId($venueId) {
  global $wpdb;
  global $venueGameTable;
  global $gameTable;
  
  $sql = "
    SELECT
      *
    FROM
      $venueGameTable
    JOIN
      $gameTable
    ON
      {$venueGameTable}.game_id = {$gameTable}.id
    WHERE
      {$venueGameTable}.venue_id = $venueId  
  ";
  return $wpdb->get_results($sql);
}

function getGames() {
  global $wpdb;
  global $gameTable;
  
  return $wpdb->get_results("SELECT * FROM $gameTable");
}

function getOfficialsByVenueId($venueId) {
  global $wpdb;
  global $officialTable;
  
  return $wpdb->get_results(
    "SELECT * FROM $officialTable WHERE venue_id = $venueId"
  );
}

function getPlayersByTeam($team) {
  global $wpdb;
  global $playerTable;
  
  return $wpdb->get_results(
    "SELECT * FROM $playerTable WHERE team_id = {$team->id}"
  );
} 

function getTeamMatchByGameId($teamId, $gameId) {
  global $wpdb;
  global $matchTable;
  
  $sql = "SELECT * FROM $matchTable WHERE (team_a = $teamId OR team_b = $teamId) AND game_id = $gameId";
  $results = $wpdb->get_results(
    $sql
  );
  
  return $results[0]; 
}

function setOfficials($venueId, $officials) {
  global $wpdb;
  global $officialTable;
  
  $wpdb->get_results("DELETE FROM $officialTable WHERE venue_id = $venueId");
  
  if (is_array($officials)) {
    foreach ($officials as $index => $value) {
      $wpdb->insert(
        $officialTable,
        array(
          'venue_id' => $venueId,
          'user_id' => $value
        )
      );  
    }
  }
}


function getMatchById($matchId) {
  global $wpdb;
  global $matchTable;
    
  $result = $wpdb->get_results("SELECT * FROM $matchTable WHERE id = $matchId");  
  return $result[0];
}

function getMatchesByVenueId($venueId) {
  global $wpdb;
  global $matchTable;
  global $teamTable;
  
  return $wpdb->get_results(
    "SELECT 
      $matchTable.id as id,
      $matchTable.venue_id,
      $matchTable.game_id,
      $matchTable.team_a,
      $matchTable.team_b,
      $matchTable.mode,
      $matchTable.score,
      $matchTable.result,
      $teamTable.name
    FROM 
      $matchTable
    LEFT JOIN
      $teamTable
    ON
      $matchTable.team_a = $teamTable.id
    OR
      $matchTable.team_b = $teamTable.id
    WHERE 
      venue_id = $venueId
    ORDER BY $teamTable.name"
  );
}

function getMatchCount($team) {
  global $wpdb;
  global $matchTable;
  
  $sql = "SELECT count(id) as num_matches FROM $matchTable WHERE team_a = {$team->id} OR team_b = {$team->id}";
  $results = $wpdb->get_results($sql);
  
  return $results[0]->num_matches;
}

function getMatches() {
  global $wpdb;
  global $matchTable;
  
  return $wpdb->get_results('SELECT * FROM '.$matchTable);  
}

function getTeamById($id) {
  global $wpdb;
  global $teamTable;
  
  $team = $wpdb->get_results(
    "SELECT * FROM $teamTable WHERE id = $id"
  );

  return $team[0];
}

function getPlayerById($player_id) {
  global $wpdb;
  global $playerTable;
  
  $results = $wpdb->get_results(
    "SELECT * FROM $playerTable WHERE id = $player_id"
  );
  return $results[0];
}

function getPlayers() {
  global $wpdb;
  global $playerTable;
  
  return $wpdb->get_results(
    "SELECT * FROM $playerTable"
  );
}


function getTeamByName($team_name) {
  global $wpdb;
  global $teamTable;
  
  $results = $wpdb->get_results( 
    'SELECT * FROM '.$teamTable.' WHERE name = "'.$team_name.'"', OBJECT 
  );
  
  return $results;
}

function getMatchesByTeamId($teamId) {
  global $wpdb;
  global $matchTable;
  
  return $wpdb->get_results(
    "SELECT
      *
    FROM
      {$matchTable}
    WHERE
      team_a = $teamId
    OR
      team_b = $teamId"
  );
}

function getScoreboardData($sort = false, $gameId = false) {
  global $wpdb;
  $scoreboardData = array();
  
  $teams = getTeams();  
  foreach ($teams as $index => $team) {
    $matches = getMatchesByTeamId($team->id);
    $total = 0;
    $gameTotal = 0;
    
    foreach ($matches as $match) {
      if ($match->result == $team->id) {
        $total += $match->score;
        if ($gameId == $match->game_id) 
          $gameTotal += $match->score;
      }
    }
    
    $data = array(
      'team' => $team,
      'matches' => $matches,
      'total' => $total,
      'game' => $gameTotal      
    );
        
    $scoreboardData[] = $data;        
  }

  if ($sort) {
    switch ($sort) {
      case 'name' :
        usort($scoreboardData, 'cmpName');    
        break;
      case 'total' :
        usort($scoreboardData, 'cmpTotal');    
        break;
      case 'game' :
        usort($scoreboardData, 'cmpGameTotal');      
        break;
      default:
        break;      
    } 
  }
  
  return $scoreboardData;
}

function getTeamMatches($team) {
  return getMatchesByTeamId($team->id);
}

function getTeams() {
  global $wpdb;
  global $teamTable;
  
  $teams = $wpdb->get_results( 
    'SELECT * FROM '.$teamTable, OBJECT 
  );

  return $teams;
}

function getVenuesByGameId($gameId) {
  global $wpdb;
  global $venueGameTable;
  global $venueTable;
  
  $results = $wpdb->get_results(
    "SELECT 
      * 
    FROM
      $venueGameTable    
    JOIN
      $venueTable
    ON
      {$venueTable}.id = {$venueGameTable}.venue_id
    WHERE 
      {$venueGameTable}.game_id = $gameId"
  );
  return $results;  
}

function getVenuesByUserId($userId) {
  global $wpdb;
  global $officialTable;
  
  $results = $wpdb->get_results(
    "SELECT * FROM $officialTable WHERE user_id = $userId"
  );
  
  $venues = array();
  foreach ($results as $official)
    $venues[] = getVenueById($official->venue_id);
    
  return $venues;
}


function el($object) {
  ob_start();
  print_r($object);
  error_log(ob_get_clean());
}


function getVenueById($venueId) {
  global $wpdb;
  global $venueTable;
  
  $venue = $wpdb->get_results("SELECT * FROM $venueTable WHERE id = $venueId");
  return $venue[0];
}

function getVenues() {
  global $wpdb;
  global $venueTable;
  
  return $wpdb->get_results("SELECT * FROM $venueTable");
}

function updateVenue($venueId, $name, $address) {
  global $wpdb;
  global $venueTable;
  
  return $wpdb->update(
    $venueTable,
    array (
      "name" => $name,
      "address" => $address
    ),
    array( "ID" => $venueId ),
    array( "%s" )  
  );
}

function updateGame($gameId, $name, $description, $venues) {
  global $wpdb;
  global $gameTable;
  
  $wpdb->update(
    $gameTable,
    array(
      'name' => $name,
      'description' => $description
    ),
    array(
      "ID" => $gameId
    ),
    array(
      '%s',
      '%s'
    )  
  );
  
  deleteVenueGameByGameId($gameId);
  
  foreach ($venues as $key => $venueId) {
    addVenueGame($venueId, $gameId);
  }
}

function updateTeam($id, $name) {
  global $wpdb;
  global $teamTable;
  
  return $wpdb->update(
    $teamTable,
    array(
      "name" => $name
    ),
    array(
      "ID" => $id
    ),
    array(
      "%s"
    )
  );
}



function updateMatch($matchId, $teamA, $teamB, $gameId, $venueId, $result, $score, $mode = 'team') {
  global $wpdb;
  global $matchTable;
  
  return $wpdb->update(
    $matchTable,
    array(
      'team_a' => $teamA,
      'team_b' => $teamB,
      'venue_id' => $venueId,
      'game_id' => $gameId,
      'result' => $result,
      'score' => $score,
      'mode' => $mode
    ),
    array(
      'ID' => $matchId
    ),
    array('%s', '%d', '%d', '%d', '%d', '%d', '%s', '%s')
  );
}


function updatePlayer($playerId, $name, $team_id) {
  global $wpdb;
  global $playerTable;
  
  return $wpdb->update(
    $playerTable,
    array(
      "name" => $name,
      "team_id" => $team_id
    ),
    array(
      "ID" => $playerId
    ),
    array(
      "%s",
      "%d"
    )
  );
}

function quickUpdateMatch($matchId, $result, $score) {
  global $wpdb;
  global $matchTable;
  
  return $wpdb->update(
    $matchTable,
    array(
      'result' => $result,
      'score' => $score
    ),
    array (
      "ID" => $matchId
    ),
    array (
      "%d",
      "%d"
    )
  );
}

?>