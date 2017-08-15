<?php
  if (function_exists("teamName") != true) {
    function teamName($team, $winner) {
      if ($team->id == $winner->id) {
        return "<strong>".$team->name."</strong>";
      } else {
        return $team->name;
      }    
    }
  }
?>
<?php $teamA = getTeamById($match->team_a); ?>
<?php $teamB = getTeamById($match->team_b); ?>
<?php $winner = $teamA->id == $match->result ? $teamA : $teamB; ?>
<?php $game = getGameById($match->game_id); ?>
<div class="match" id="match-<?php echo $match->id; ?>">
      <div class="info">
        <div class="team-names">
          <strong><?php echo $teamA->name ?></strong><br /><?php echo $game->name; ?>    
        </div>
      
        <?php if ($match->result): ?>
          <?php if ($match->mode == 'solo'): ?>
            <p>Score <?php echo $match->score ?></p>          
          <?php else: ?>
            <p><?php echo $winner->name ?> won with a score of <?php echo $match->score ?>.</p>          
          <?php endif; ?>
        <?php else: ?>
          <p>No Result</p>
        <?php endif; ?>
      </div>
      <div class="loader">
        
      </div>
      <div class="update">
        <div class="team-names">
          <strong><?php echo $teamA->name ?></strong><br /><?php echo $game->name; ?>    
        </div>      
      
        <form class="match-form">
          <input type="hidden" name="match_id" value="<?php echo $match->id ?>" />
          <input type="hidden" name="mode" value="<?php echo $match->mode ?>" />
          <input type="hidden" name="result" value="<?php echo $teamA->id ?>" />
          <input type="hidden" name="venue_id" value="<?php echo $venue->id ?>" />
          
          <?php if ($match->mode == 'solo'): ?>
            <p>
              <label for="score">Score</label>
              <select name="score">
                <option value="">Not Played / No result</option>
                <?php for ($i = 1 ; $i < 60 ; $i++): ?>
                  <option value="<?php echo $i ?>" <?php if ($match->score == $i) echo 'selected="selected"' ?>><?php echo $i ?></option>
                <?php endfor; ?>
              </select>          
            </p>
          <?php else: ?>
            <p>
              <label for="result">Winner</label>
              <select name="result">
                <option value="no result">No Result</option>
                <option value="<?php echo $teamA->id; ?>" <?php if ($match->result && $winner->id == $teamA->id) echo 'selected="selected"' ?>><?php echo $teamA->name ?></option>
                <option value="<?php echo $teamB->id; ?>" <?php if ($match->result && $winner->id == $teamB->id) echo 'selected="selected"' ?>><?php echo $teamB->name ?></option>        
              </select>
            </p>          
          <?php endif; ?>
          <!-- p>Score: <input type="text" name="score" class="score" value="<?php echo $match->score ?>"></p -->
          <div class="buttons">
            <a class="button save-button" href="#" data-match-id="match-<?php echo $match->id ?>">Save</a>
            <a class="button cancel-button" href="#" data-match-id="match-<?php echo $match->id ?>">Cancel</a>          
          </div>
        </form>
      </div>
</div>