<!DOCTYPE html>
<html>
  <head>
    <title>Scorekeeper</title>
    
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">    

    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
  	<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:400,700' rel='stylesheet' type='text/css'>  	
    <link href='http://fonts.googleapis.com/css?family=Kreon:300,400,700' rel='stylesheet' type='text/css'>
    
    <link rel="stylesheet" href="/wp-content/plugins/buxtahooda-scoreboard/assets/css/official.css" />
    <link rel="stylesheet" href="/wp-content/themes/theme-buxtahooda-2014/livescoreboard.css" />
    
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="/wp-content/plugins/buxtahooda-scoreboard/assets/js/LiveScoreboardRef.js"></script>
    <script src="/wp-content/plugins/buxtahooda-scoreboard/assets/js/LiveScoreboard.js"></script>    
    <?php add_ajax_library() ?>    
  </head>
  <body id="ls-official">
    <div class="main">
      <!-- h1>Scorekeeper Page</h1 -->
      <?php $currentUser = wp_get_current_user(); ?>
      <?php $venues = getVenuesByUserId($currentUser->id); ?>
      
      <?php foreach ($venues as $venue): ?>
        <?php include(plugin_dir_path( __FILE__ )."/../partials/venue.tpl.php"); ?>
      <?php endforeach; ?>      
      
      <?php lsGetScoreboard("liveScoreboard", true); ?>
      <br />
      <a href="<?php echo wp_logout_url( $redirect ); ?>" class="button">Logout</a>
    </div><!-- .main -->
    <script src="/wp-content/plugins/buxtahooda-scoreboard/assets/js/official.js"></script>
  </body>  
</html>
