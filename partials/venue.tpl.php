<?php $matches = getMatchesByVenueId($venue->id) ?>
<?php if (count($matches) == 0) continue; ?>
<div class="venue-container" id="venue-<?php echo $venue->id ?>">
  <h2 class="venue-name venue-link"><?php echo $venue->name ?></h2>
  <div class="nav-buttons">
    <a href="#a">A - I</a>
    <a href="#h">H - Q</a>
    <a href="#r">R - Z</a>
  </div>

  <div class="matches">
    <h3>Open Games</h3>
    <a name="a" id="a"></a>
    <?php $h = false; $r = false; ?>
    <?php foreach($matches as $match) : ?>
      <?php if (!$match->result): ?>
        <?php if (strtolower($match->name[0]) >= 'h' && $h == false): $h = true ?>
          <a name="h" id="h"></a>
        <?php endif; ?>
        <?php if (strtolower($match->name[0]) >= 'r' && $r == false): $r = true ?>
          <a name="r" id="r"></a>
        <?php endif; ?>
        <?php include(plugin_dir_path( __FILE__ )."match.tpl.php"); ?>
      <?php endif; ?>
    <?php endforeach; ?>
    <h3>Completed Games</h3>
    <?php foreach($matches as $match) : ?>
      <?php if ($match->result): ?>
        <?php include(plugin_dir_path( __FILE__ )."match.tpl.php"); ?>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>          
</div>    
