<style>
  .live-scoreboard .loader {
    height: 100px;
    width: 100%;
    display: block;
    background: url('/wp-content/plugins/buxtahooda-scoreboard/assets/img/loader.gif') center center no-repeat;
    z-index: 2;
    display: none;
  }
</style>

<div id="<?php echo $elementId ?>" class="live-scoreboard">
  <h2>Scoreboard</h2>
  <div class="loader"></div>
  <div class="scoreboard">
    <?php include(plugin_dir_path( __FILE__ )."/../partials/scoreboard_table.tpl.php"); ?>
  </div>
  <script>
  jQuery(window).load(function() {
  	jQuery("#<?php echo $elementId ?>").liveScoreboard({
  	 'ajaxUrl' : ajaxurl
  	 <?php if ($mobile) echo (', "mobile" : true') ?>
  	});
  });
  </script>
</div>