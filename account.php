<?php 
  // Only report errors, not warnings or notices
  error_reporting(E_ERROR);

  // Include helper functions
  require_once('inc/toolkit/bootstrap.php');
  require_once('inc/functions.php');

  // Authenticate
  require_once('inc/auth.php');

  // Handle form submission
  if ($_POST['update_account']) {
    $settings = yaml::read('settings.yml');

    // Grab new password
    if ($_POST['new_password'] != '') {
      $settings['password'] = password::hash($_POST['new_password']);
    }

    // Write to file
    yaml::write(__DIR__.DIRECTORY_SEPARATOR.'settings.yml', $settings);
  }

  // Log out if the password changed
  if ($_POST['new_password'] != '') {
    s::set('logged_in', false);
    header::redirect('account.php');
  }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css?v=3">
</head>
<body class="tpl--edit">

  <aside>
    <section>
      <h2>Campaigns</h2>
      <ul>
        <?php $campaigns = array_diff(scandir('campaigns'), ['.', '..']); ?>
        <?php foreach($campaigns as $campaign_file) { ?>
          <?php $slug = str_replace('.yml', '', $campaign_file); ?>
          <?php $campaign = getCampaign($slug) ?>
          <li>
            <a href="/edit.php?campaign=<?= $slug ?>"><?= $campaign['title'] ?></a>
          </li>
        <?php } ?>
      </ul>
      <form action="edit.php" method="POST">
        <details>
          <summary>+ New Campaign</summary>
          <label>
            <span>Campaign title</span>
            <input name="title" type="text">
          </label>
          <input type="hidden" name="action" value="new_campaign">
          <button type="submit">Create</button>
        </details>
      </form>
    </section>
    <section>
      <ul>
        <li class="active"><a href="/account.php">Account settings</a></li>
      </ul>
    </section>
  </aside>

  <main>
    <form action="" method="POST" enctype="multipart/form-data">
      <fieldset>
        <legend>Password</legend>
        <label>
          <span>New password</span>
          <input name="new_password" type="password" value="">
          <span class="help">Leave this blank to keep the same password</span>
        </label>
      </fieldset>

      <input type="hidden" name="update_account" value="true">
      <button type="submit">Save settings</button>
    </form>
  </main>

  <script>
    document.querySelector('input[type="file"]').addEventListener('change', function () {
        var reader = new FileReader();
        reader.onload = function (e) {
            // get loaded data and render thumbnail.
            document.querySelector('.logo-preview').src = e.target.result;
        };

        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0]);
    });

    document.querySelector('input[name="remove_logo"]').addEventListener('change', function (event) {
      console.log(event.target.value);
      if (event.target.checked) {
        document.querySelector('.logo-preview').classList.add('hide');
      } else {
        document.querySelector('.logo-preview').classList.remove('hide');
      }
    });
  </script>

</body>
</html>