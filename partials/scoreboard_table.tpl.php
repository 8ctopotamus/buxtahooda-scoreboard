<?php $games = getGames(); ?>
  <table class="scoreboard-table">
    <tr>
      <th class="team-name-header header-link" data-sort-by="name"><a href="#">Team Name</a></th>
      <?php if (!$mobile) : ?>
        <?php foreach($games as $game): ?>
          <th><a href="#" class="header-link game-link" data-sort-by="game" data-game-id="<?php echo $game->id ?>"><?php echo $game->name ?></a></th>
        <?php endforeach; ?>
      <?php endif; ?>
      <th class="total-header header-link" data-sort-by="total"><a href="#">Total</a></th>
    </tr>
    <?php foreach ($scoreboard as $index => $row): ?>
      <tr>
        <td><?php echo $row['team']->name ?></td>
        <?php $total = 0; ?>
        <?php foreach ($games as $game): ?>
          <?php $match = getTeamMatchByGameId($row['team']->id, $game->id); ?>
          <?php if ($match) {
              if ($match->result == $row['team']->id) {
                $total += $match->score;
                $score = $match->score;
              } else {
                $score = '-';
              }
            } else {
              $score = '-';
            }
          ?>
          <?php if (!$mobile): ?>
            <td>
              <?php echo $score ?>            
            </td>        
          <?php endif; ?>
        <?php endforeach; ?>
        <td class="total"><?php echo $total ?></td>      
      </tr>
    <?php endforeach; ?>
  </table>
