<?php
  $mode = 'add';
  $title = 'Trivia Scoreboard';
  $actionVerb = "add";
  $action = "add";
  $currentObj = false;

  if (isset($_POST['form_action'])) {
    $formAction = $_POST['form_action'];

    switch ($formAction) {
      case 'add' :
        $result = addRound($_POST['test']);
        if ($result) {
          $message = "Round added successfully!";
        } else {
          $message = "There was an error!";
          $nameValue = $_POST['test'];
        }
        break;
      default:
        $message = "Form Submission!";
        break;
    }
  }

  $teams = getTeams();
  $rounds = array(1, 2, 3, 4, 5);
?>

<style>
  .round-row {
    width: 100%;
    display: flex;
    justify-content: space-between;
  }

  .round-row-title {
    background: black;
    color: white;
    padding: 5px;
    margin-bottom: 0;
  }

  .round-row-team {
    text-align: center;
  }

  .round-row-team-score {
    font-size: 2em;
  }

  .add-round-form input {
    width: 50%;
  }

  .current-round .round-row-title {
    background: #0085ba;
  }

  @media (max-width: 767px) {
    .round-row {
      flex-wrap: wrap;
    }
    .round-row-team {
      width: 50%;
    }
  }
</style>

<h2>Trivia Scoreboard</h2>

<div id="rounds-list">
  <!-- show the past rounds/scores -->
  <?php foreach ($rounds as $index => $round): ?>
    <h3 class="round-row-title">Round <?php echo $round; ?></h3>
    <div class="round-row">
      <?php
        foreach ($teams as $index => $team): ?>
          <div class="round-row-team">
            <h4><?php echo $team->name ?></h4>
            <span class="round-row-team-score">0</span>
          </div>
        <?php endforeach; ?>
    </div>
  <?php endforeach; ?>


  <div class="current-round">
    <h3 class="round-row-title">Current Round: <?php echo count($rounds) + 1; ?></h3>
    <form action="<?php echo admin_url('admin.php?page=rounds'); ?>"
          class="add-round-form"
          method="post">
      <input type="hidden" name="form_action" value="<?php echo $action ?>" />
      <input type="hidden" name="round_id" value="<?php echo count($rounds) + 1; ?>" />
      <label for="test"> test
        <input type="text" name="test" value="">
      </label>
      <div class="round-row">
        <?php // list each team to submit score
        foreach ($teams as $index => $team): ?>
        <div class="team">
          <h4><?php echo $team->name ?></h4>
          <label for="score">Score: </label>
          <input type="number" name="score" value="">
        </div>
        <?php endforeach; ?>
      </div>

      <button
        class="button button-primary button-large"
        type="submit"
        value="<?php echo $actionVerb; ?>"
        name="submit"
        style="margin-top: 20px;">
        Submit Round Scores
      </button>
    </form>
  </div>
</div>
