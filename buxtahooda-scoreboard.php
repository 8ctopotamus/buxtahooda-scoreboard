<?php
/**
 * Plugin Name: Buxtahooda Live Scoreboard
 * Plugin URI: www.buxtahooda.com
 * Description: A wordpress plugin for managing a live scoreboard for a pub crawl.
 * Version: 0.0.1
 * Author: Paul Canfield
 * Author URI: http://paulcanfield.com
 * License: ###
 */
require_once( ABSPATH. 'wp-admin/includes/upgrade.php' );


include (plugin_dir_path( __FILE__ ).'functions.php');
include (plugin_dir_path( __FILE__ )."/data/tables.php");
include (plugin_dir_path( __FILE__ )."/data/fixtures.php");

global $livescoreboard_version;
$livescoreboard_version = "0.1";

add_action( 'wp_ajax_update_match', 'lsRefUpdateFormAjax' );
add_action( 'wp_ajax_nopriv_reload_scoreboard', 'lsAjaxScoreboard' );
add_action( 'wp_ajax_reload_scoreboard', 'lsAjaxScoreboard' );
add_action( 'wp_head', 'add_ajax_library' );

function lsInstall() {
  global $livescoreboard_version;

  lsPlayerTable();
  lsTeamTable();
  lsVenueTable();
  lsMatchTable();
  lsOfficialTable();
  lsGameTable();
  lsVenueGameTable();
  // INSER TRIVIA ROUNDS
  lsRoundsTable();

  add_role(
    'ls-offical',
    'Livescoreboard Official',
    array (
      "update_matches" => true,
      "official" => true,
      'read' => true
    )
  );
  $roles = array('administrator', 'editor', 'author');
  $roles_obj = new WP_Roles();
  foreach ($roles as $role_name) {
    $roles_obj->add_cap($role_name, 'update_matches');
  }

  add_option( "livescoreboard_version", "0.1" );
}
register_activation_hook( __FILE__, 'lsInstall' );

function lsUninstall() {
  remove_role( 'update_matches' );
  $roles = array('administrator', 'editor', 'author');
  $roles_obj = new WP_Roles();
  foreach ($roles as $role_name) {
    $roles_obj->remove_cap($role_name, 'update_matches');
  }
}
register_uninstall_hook( __FILE__, 'lsUninstall' );

function lsFlushRules(){
    $rules = get_option( 'rewrite_rules' );

    if ( ! isset( $rules['matches'] ) ) {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }
}
add_action('wp_loaded', 'lsFlushRules');

function lsRewriteRules( $wp_rewrite ) {
  $new_rules = array(
    'matches' => 'index.php?matches=true'
  );

  $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}
add_action('generate_rewrite_rules', 'lsRewriteRules');

function lsQueryVars( $vars ) {
  $vars[] = 'matches';
  $vars[] = 'action';
  return $vars;
}
add_filter('query_vars', 'lsQueryVars');

function lsInit( ) {
}
add_action('init', 'lsInit');

function lsTemplateInclude($template) {
  $matches = get_query_var('matches');
  if ( current_user_can( 'official' ) ) {
    if ($matches) {
      $template = plugin_dir_path( __FILE__ )."/templates/official.tpl.php";
    } else {
      wp_redirect(get_site_url()."/matches");
    }
  }

  return $template;
}
add_filter("template_include", 'lsTemplateInclude');

function add_ajax_library() {
    $html = '<script type="text/javascript">';
    $html .= 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '"';
    $html .= '</script>';

    echo $html;
}

function lsLogin($user) {
  $currentUser = get_user_by('slug', $user);

  if ( $currentUser->has_cap( 'official' ) ) {
    wp_redirect(get_site_url(false, 'matches'));
    exit;
  }
  return;
}
add_action("wp_login", "lsLogin");

function livescoreboard_menu() {
  add_object_page(
    "Scoreboard",
    "Scoreboard",
    "publish_pages",
    "scoreboard",
    "page_scoreboard"
  );

  add_submenu_page(
    "scoreboard",
    "Venues",
    "Venues",
    "publish_posts",
    "venues",
    "page_venues"
  );

  add_submenu_page(
    "scoreboard",
    "Players",
    "Players",
    "publish_posts",
    "players",
    "page_players"
  );

  add_submenu_page(
    "scoreboard",
    "Matches",
    "Matches",
    "publish_posts",
    "matches",
    "page_matches"
  );

  add_submenu_page(
    "scoreboard",
    "Teams",
    "Teams",
    "publish_posts",
    "teams",
    "page_teams"
  );

  add_submenu_page(
    "scoreboard",
    "Games",
    "Games",
    "publish_posts",
    "games",
    "page_games"
  );

  // ROUNDS
  add_submenu_page(
    "scoreboard",
    "Trivia Rounds",
    "Trivia Rounds",
    "publish_posts",
    "rounds",
    "page_rounds"
  );

}
add_action("admin_menu", "livescoreboard_menu");

function page_scoreboard() {
  $scoreboard = getScoreboardData();

  include(plugin_dir_path( __FILE__ )."/templates/scoreboard_admin.tpl.php");
}

function page_venues() {
  include(plugin_dir_path( __FILE__ )."/templates/venues.tpl.php");
}

function page_players() {
  include(plugin_dir_path( __FILE__ )."/templates/players.tpl.php");
}

function page_matches() {
  include(plugin_dir_path( __FILE__ )."/templates/matches.tpl.php");
}

function page_teams() {
  include(plugin_dir_path( __FILE__ )."/templates/teams.tpl.php");
}

function page_games() {
  include(plugin_dir_path( __FILE__ )."/templates/games.tpl.php");
}

function page_rounds() {
  include(plugin_dir_path( __FILE__ )."/templates/rounds.tpl.php");
}

function lsRefUpdateFormAjax() {
  if ( current_user_can( 'update_matches' ) ) {
    if ($_POST['score'] == '') {
      $gameResult = '';
      $gameScore = 0;
    } else {
      $gameResult = $_POST['result'];
      $gameScore = $_POST['score'];
    }
    $result = quickUpdateMatch($_POST['match_id'], $gameResult, $gameScore);
    $venue = getVenueById($_POST['venue_id']);

    include(plugin_dir_path( __FILE__ )."/partials/venue.tpl.php");
  }
  die();
}

function lsGetScoreboard($elementId = "liveScoreboard", $mobile = false) {
  wp_enqueue_script( "LiveScoreboard", '/wp-content/plugins/buxtahooda-scoreboard/assets/js/LiveScoreboard.js', array(), '1.0.0', true );

  $scoreboard = getScoreboardData();

  include(plugin_dir_path( __FILE__ )."/templates/scoreboard.tpl.php");
}

function lsAjaxScoreboard() {
  $gameId = isset($_POST['game'])
    ? $_POST['game']
    : false;

  $scoreboard = getScoreboardData($_POST['sort'], $gameId);

  if (isset($_POST['mobile'])) {
    $mobile = true;
  } else $mobile = false;

  include(plugin_dir_path( __FILE__ )."/partials/scoreboard_table.tpl.php");
  die();
}
