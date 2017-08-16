<?php
  $mode = 'add';
  $title = 'Add Venue';
  $actionVerb = "add";
  $action = "add";
  $currentTeam = false;
  $teams = getTeams();

  if (isset($_GET['edit'])) {
    $mode = 'edit';
    $title = "Edit Venue";
    $actionVerb = "edit";
    $action = "edit";

    $currentVenue = getVenueById($_GET['edit']);
  }

  if (isset($_GET['delete'])) {
    $message = "Venue Deleted!";
    deleteVenue($_GET['delete']);
  }

  if (isset($_POST['form_action'])) {
    $formAction = $_POST['form_action'];

    switch ($formAction) {
      case 'add' :
        $result = addVenue($_POST['name'], $_POST['address']);
        if ($result) {
          setOfficials($result, $_POST['officials']);
          $message = "Venue added successfully!";
        } else {
          $message = "There was an error!";
          $nameValue = $_POST['name'];
          $addressValue = $_POST['address'];
        }
        break;
      case 'edit':
        $result = updateVenue($_POST['venue_id'], $_POST['name'], $_POST['address']);
        setOfficials($_POST['venue_id'], $_POST['officials']);
        $message = "Venue Updated!";
        break;
      default:
        $message = "Form Submission!";
        break;
    }
  }

  $venues = getVenues();

  $nameValue = isset($nameValue)
    ? $nameValue
    : ($currentVenue ? $currentVenue->name : false);

  $addressValue = isset($addressValue)
    ? $addressValue
    : ($currentVenue ? $currentVenue->address : false);

  $venueId = isset($venueId)
    ? $venueId
    : ($currentVenue ? $currentVenue->id : false);

  if (!isset($officialsValue)) {
    if ($currentVenue) {
      $officialsValue = getOfficialsByVenueId($currentVenue->id);
    } else
      $officialsValue = false;
  }

?>
<h2>Venues</h2>
<div id="team-form">
  <?php if (isset($message) && $message): ?>
    <div class="message">
      <?php echo $message; ?>
    </div>
  <?php endif; ?>
  <h3><?php echo $title ?></h3>
  <form method="post" action="<?php echo admin_url('admin.php?page=venues') ?>">
    <input type="hidden" name="form_action" value="<?php echo $action ?>" />
    <input type="hidden" name="venue_id" value="<?php echo $venueId ?>" />
    <ul>
      <li>
        <label for="name">Venue</label>
        <input type="text" name="name" value="<?php echo $nameValue ?>" />
      </li>
     <li>
        <label for="name">Address</label>
        <textarea name="address"><?php echo $addressValue; ?></textarea>
      </li>
    </ul>
    <ul>
      <li>
        Officials:
        <?php $users = get_users(); ?>
        <?php if ($officialsValue) {
          foreach ($officialsValue as $value)
            $ids[] = $value->user_id;
        } else { $ids = array(); } ?>
        <select name="officials[]" multiple="multiple">
          <?php foreach ($users as $user): ?>
            <option value="<?php echo $user->id ?>" <?php if (in_array($user->id, $ids)) echo 'selected="selected"' ?>><?php echo $user->first_name ?></option>
          <?php endforeach; ?>
        </select>
      </li>
    </ul>
    <input type="submit" name="submit" value="<?php  echo $actionVerb ?>" />
  </form>
</div>
<div id="team-list">
  <h2>Venues</h2>
  <?php foreach ($venues as $index => $venue): ?>
    <div>
      <p><strong><?php echo $venue->name ?></strong><br /><?php echo $venue->address; ?></p>
      <a href="<?php echo admin_url('admin.php?page=venues&edit='.$venue->id) ?>" >edit</a>
      <a href="<?php echo admin_url('admin.php?page=venues&delete='.$venue->id) ?>" >delete</a>
    </div>
  <?php endforeach; ?>
</div>
