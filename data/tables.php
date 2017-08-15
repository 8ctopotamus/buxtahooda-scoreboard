<?php
function lsVenueTable() {
  global $wpdb;
  $table_name = $wpdb->prefix . "livescoreboard_venue";
  
  $sql = "CREATE TABLE $table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    address varchar(255) NOT NULL,
    UNIQUE KEY id (id)
  );";
  
  dbDelta($sql);
}

function lsPlayerTable() {
  global $wpdb;
  
  $table_name = $wpdb->prefix . "livescoreboard_player";
  $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            team_id int(11),
            UNIQUE KEY id (id)
        );";
        
  dbDelta($sql);  
}

function lsTeamTable() {
  global $wpdb;
  $table_name = $wpdb->prefix . "livescoreboard_team";
  
  $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL UNIQUE,
            UNIQUE KEY id (id)
        );";
  
  dbDelta($sql);
}

function lsGameTable() {
  global $wpdb;
  $table_name = $wpdb->prefix."livescoreboard_game";
  $sql = "CREATE TABLE $table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) UNIQUE,
    description varchar(255) UNIQUE,
    UNIQUE KEY id (id)    
  )";
  dbDelta($sql);
}

function lsVenueGameTable() {
  global $wpdb;
  $table_name = $wpdb->prefix."livescoreboard_venuegame";
  
  $sql = "CREATE TABLE $table_name (
    venue_id int(11),
    game_id int(11)
  )";
  dbDelta($sql);
}

function lsMatchTable() {
  global $wpdb;
  $table_name = $wpdb->prefix . "livescoreboard_match";
  
  $sql = "CREATE TABLE $table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    venue_id int(11),
    game_id int(11),
    team_a int(11),
    team_b int(11),
    mode varchar(10),
    score int(11),
    result varchar(255) DEFAULT '',
    UNIQUE KEY id (id)    
  )";
  
  dbDelta($sql);
}

function lsOfficialTable() {
  global $wpdb;
  $table_name = $wpdb->prefix . "livescoreboard_official";
  
  $sql = "CREATE TABLE $table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    venue_id int(11),
    user_id int(11),
    UNIQUE KEY id (id)    
  )";
  
  dbDelta($sql);
}
