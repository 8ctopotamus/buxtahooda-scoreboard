<?php

function lsDeleteData() {
  global $wpdb;
  global $matchTable;
  global $playerTable;
  global $officialTable;
  global $teamTable;
  global $venueTable;
  global $venueGameTable;
  global $gameTable;
  
  $wpdb->get_results( "DELETE FROM $matchTable;" );
  $wpdb->get_results( "DELETE FROM $playerTable" );
  $wpdb->get_results( "DELETE FROM $officialTable" );
  $wpdb->get_results( "DELETE FROM $teamTable" );
  $wpdb->get_results( "DELETE FROM $venueTable" );
  $wpdb->get_results( "DELETE FROM $venueGameTable" );
  $wpdb->get_results( "DELETE FROM $gameTable" );  
}

function lsInstallTestData() {
  lsDeleteData();
  
  lsInstallTeams();
  lsInstallPlayers();
  lsInstallVenues();
  lsInstallGames();
#  lsInstallMatchesSolo(); 
}

function lsGenerateMatches($mode = 'team', $results = false) {
  global $wpdb;
  global $matchTable;
  
  $wpdb->get_results(
    "DELETE FROM $matchTable;"
  );
  
  if ($mode == 'solo') {
    lsInstallMatchesSolo($results);  
  } else {
    lsInstallMatches($results);
  }
}

function lsInstallGames() {

  lsInstallGame(
    "Beer Pong",
    "No bar game contest is complete without ping-pong balls bouncing all over the place. Each player tosses two balls. Get it in the near cup for 1 point, the middle cup for 3, or the Miller Lite cup for 5. No bounces, no warmups."  
  );
  lsInstallGame(
    "Bags",
    "Each team member gets four bean bags to toss underhand. Get it on the board for 1 point. Put it in the hole for 3."
  );
  lsInstallGame(
    "Pop-a-Shot",
    "Each player takes 5 shots. Each basket is worth 2 points."
  );
  lsInstallGame(
    "Golf Putt",
    "Each player putts 5 balls. Get in the hole for 2 points."
  );
  lsInstallGame(
    "Ladder Golf",
    "Players toss two bolos each. Wrap it around the top rung for 1 point, the middle for 3, or the bottom rung for 5."
  );
  
  lsInstallVenueGames();
}

function lsInstallGame($name, $description) {
  global $wpdb;
  global $gameTable;
  
  $wpdb->insert(
    $gameTable,
    array(
      "name" => $name,
      "description" => $description
    )
  );
  
  return $wpdb->insert_id;
}


function lsInstallVenueGames() {
  $games = getGames();
  $venues = getVenues();
  
  lsInstallVenueGame($venues[0]->id, $games[0]->id);
  lsInstallVenueGame($venues[0]->id, $games[1]->id);  
  
  lsInstallVenueGame($venues[1]->id, $games[1]->id);
  lsInstallVenueGame($venues[1]->id, $games[2]->id);  

  lsInstallVenueGame($venues[2]->id, $games[0]->id);
  lsInstallVenueGame($venues[2]->id, $games[2]->id);  
  
  lsInstallVenueGame($venues[3]->id, $games[0]->id);
  lsInstallVenueGame($venues[3]->id, $games[1]->id);  

  lsInstallVenueGame($venues[4]->id, $games[1]->id);
  lsInstallVenueGame($venues[4]->id, $games[2]->id);  
  
  lsInstallVenueGame($venues[5]->id, $games[0]->id);
  lsInstallVenueGame($venues[5]->id, $games[2]->id);  
  
  lsInstallVenueGame($venues[6]->id, $games[3]->id);
  
  lsInstallVenueGame($venues[7]->id, $games[4]->id);
}

function lsInstallVenueGame($venueId, $gameId) {
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

function lsInstallMatchesSolo($results = false) {
  global $wpdb;
  global $matchTable;
  
  $teams = getTeams();
  $venues = getVenues();
  $games = getGames();
  $venueTracker = array();
  foreach ($venues as $venue) $venueTracker[$venue->id] = 0;
  
  foreach ($teams as $currentTeam) {
    foreach ($games as $game) {
      $gameVenues = getVenuesByGameId($game->id);
      
      if (count($gameVenues) > 1) {
        $venueCapacity = array();
        foreach ($gameVenues as $gameVenue)
          $venueCapacity[$gameVenue->id] = $venueTracker[$gameVenue->id];
          
        arsort($venueCapacity);
        $keys = array_keys($venueCapacity);        
        $venueId = $keys[0];
        unset($venueCapacity[$venueId]);
        
        $index = rand() % count($venueCapacity);
        $keys = array_keys($venueCapacity);
        $venueId = $keys[$index];
      } else {
        $venueId = $gameVenues[0]->id;
      }
      $venueTracker[$venueId]++;

      if ($results) {
        $score = (rand() % 10) + 1;
        $result = $currentTeam->id;
      } else {
        $score = 0;
        $result = '';
      }
        
      $data = array(
        'team_a' => $currentTeam->id,
        'team_b' => 0,
        'game_id' => $game->id,
        'venue_id' => $venueId,
        'result' => $result,
        'score' => $score,
        'mode' => 'solo'
      );
      
      $wpdb->insert(
        $matchTable,
        $data
      );      
    }    
  }
}

function lsInstallMatches($results = false) {
  global $wpdb;
  global $matchTable;
  
  $teams = getTeams();
  $venues = getVenues();
  
  $venueCounter = 0;
  foreach ($teams as $index => $currentTeam) {    
    foreach($teams as $index2 => $secondTeam) {
      if ($secondTeam == $currentTeam) continue;
      
      if (count($matches) >= 5) break;
      
      if (getMatchCount($secondTeam) >= 5) continue;
      
      if ($results) {
        if (rand() % 2)
          $result = $currentTeam->id;
        else
          $result = $secondTeam->id;      
  
        $score = (rand() % 10) + 1;
      } else {
        $result = false;
        $score = 0;
      }
      
      $venueId = $venues[$venueCounter]->id;
      $venueCounter++;
      if (!isset($venues[$venueCounter])) $venueCounter = 0;
      
      $wpdb->insert(
        $matchTable,
        array(
          'team_a' => $currentTeam->id,
          'team_b' => $secondTeam->id,
          'venue_id' => $venueId,
          'result' => $result,
          'score' => $score,
          'mode' => $team
        )
      );
    }
  }
}

function lsFxInstallVenues($name, $address) {
  global $wpdb;  
  $wpdb->insert(
    $wpdb->prefix . "livescoreboard_venue",
    array ( 
      'name' => $name,
      'address' => $address
    )
  ); 
}

function lsInstallVenues() {
  lsFxInstallVenues(
    "Quarks Bar",
    "Deep Space 9, Promenade"
  );
  lsFxInstallVenues(
    "Enterprise Holodeck",
    "USS Enterprise NCC-1701-D, Level 23"
  );
  lsFxInstallVenues(
    "Starfleet Academy",
    "San Francisco"
  );
  lsFxInstallVenues(
    "Risa Game Square",
    "Risa, Alpha Quadrant"
  );
  lsFxInstallVenues(
    "Klingon Home World",
    "Khronos, Alpha Quadrant"
  );  
  lsFxInstallVenues(
    "Cardassian Home World",
    "Cardassia, Alpha Quadrant"
  );  
  lsFxInstallVenues(
    "Voyager Bridge",
    "Gamma Quardrant"
  );  
  lsFxInstallVenues(
    "Dahkur Province",
    "Bajor, Alpha Quadrant"
  );  
}

function lsFxInstallTeam($name) {
  global $wpdb;
  $wpdb->insert(
    $wpdb->prefix . "livescoreboard_team",
    array ( 'name' => $name )
  );  
}

function lsInstallTeams() {
  lsFxInstallTeam("Red Squad");
  lsFxInstallTeam("Deep Space 9");
  lsFxInstallTeam("USS Enterprise (NCC-1701-D)");  
  lsFxInstallTeam("Voyager");    
  lsFxInstallTeam("The Klingon Empire");      
  lsFxInstallTeam("USS Enterprise (NCC-1701)");  
  
}

function lsFxInsertPlayer($name, $team_id) {
  global $wpdb;
  $wpdb->insert(
    $wpdb->prefix . "livescoreboard_player",
    array ( 
      'name' => $name,
      'team_id' => $team_id
    )
  );  
}

function lsInstallPlayers() {
  global $wpdb;
  
  $table_name = $wpdb->prefix . "livescoreboard_player";
  $team = getTeamByName("Red Squad");
  $team_id = $team[0]->id;
    
  lsFxInsertPlayer("Nog", $team_id);
  lsFxInsertPlayer("Tim Watters", $team_id);
  lsFxInsertPlayer("Karen Farris", $team_id);
  lsFxInsertPlayer("Riley Aldrin Shepard", $team_id);
  lsFxInsertPlayer("Dorian Collins", $team_id);
    
  $team = getTeamByName("Deep Space 9");
  $team_id = $team[0]->id;
  
  lsFxInsertPlayer("Benjiman Sisko", $team_id);
  lsFxInsertPlayer("Kira Narise", $team_id);
  lsFxInsertPlayer("Oto", $team_id);
  lsFxInsertPlayer("Quark", $team_id);
  lsFxInsertPlayer("Jadzia Dax", $team_id);
  
  $team = getTeamByName("USS Enterprise (NCC-1701-D)");
  $team_id = $team[0]->id;
  
  lsFxInsertPlayer("Jean Luc Picard", $team_id);
  lsFxInsertPlayer("William T. Riker", $team_id);
  lsFxInsertPlayer("Jordi LaForge", $team_id);
  lsFxInsertPlayer("Data", $team_id);
  lsFxInsertPlayer("Worf", $team_id);

  $team = getTeamByName("Voyager");
  $team_id = $team[0]->id;
  
  lsFxInsertPlayer("Captain Janeway", $team_id);
  lsFxInsertPlayer("Harry Kim", $team_id);
  lsFxInsertPlayer("Nelix", $team_id);
  lsFxInsertPlayer("Seven of Nine", $team_id);
  lsFxInsertPlayer("Tom Paris", $team_id);

  $team = getTeamByName("Klingon Home World");
  $team_id = $team[0]->id;
  
  lsFxInsertPlayer("Worf son of Mog", $team_id);
  lsFxInsertPlayer("General Martok", $team_id);
  lsFxInsertPlayer("Chancelor Gowron", $team_id);
  lsFxInsertPlayer("Emperor Kahless", $team_id);
  lsFxInsertPlayer("Dahar master Kor", $team_id);
    
  $team = getTeamByName("USS Enterprise (NCC-1701)");
  $team_id = $team[0]->id;
  
  lsFxInsertPlayer("James T. Kirk", $team_id);
  lsFxInsertPlayer("S'chn T'gai Spock", $team_id);
  lsFxInsertPlayer("Leonard McCoy", $team_id);
  lsFxInsertPlayer("Nyota Uhura", $team_id);
  lsFxInsertPlayer("Montgomery Scott", $team_id);
  
}