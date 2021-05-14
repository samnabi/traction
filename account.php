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

    // Grab settings data
    $settings = [
      'title' => $_POST['title'],
      'logo' => $settings['logo'],
      'mailjet_key_public' => $_POST['mailjet_key_public'],
      'mailjet_key_secret' => $_POST['mailjet_key_secret'],
      'mailjet_email_from' => $_POST['mailjet_email_from']
    ];

    // Grab new password
    if ($_POST['new_password'] != '') {
      $settings['password'] = password::hash($_POST['new_password']);
    }

    // Grab new logo
    // Todo: size, type checks, delete logo
    if ($_FILES['logo']['tmp_name']) {
      f::remove($settings['logo']);
      f::copy($_FILES['logo']['tmp_name'], $_FILES['logo']['name']);
      $settings['logo'] = $_FILES['logo']['name'];
    } else if ($_POST['remove_logo'] == 'true') {
      f::remove($settings['logo']);
      $settings['logo'] = null;
    }

    // Write to file
    yaml::write(__DIR__.DIRECTORY_SEPARATOR.'settings.yml', $settings);
  }

  // Include settings
  $settings = yaml::read(__DIR__.DIRECTORY_SEPARATOR.'settings.yml');

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
  <link rel="stylesheet" href="style.css">
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
      <button type="submit">Save settings</button>
      <fieldset>
        <legend>Branding</legend>
        <label>
          <span>Organization name</span>
          <input name="title" type="text" value="<?= $settings['title'] ?>">
        </label>
        <?php if ($settings['logo']) { ?>
          <label>
            <span>Logo</span>
            <img class="logo-preview" src="<?= $settings['logo'] ?>" />
            <details>
              <summary>Change logo</summary>
              <input name="logo" type="file">
            </details>
          </label>
          <label>
            <input type="checkbox" name="remove_logo" value="true">
            Remove logo
          </label>
        <?php } else { ?>
          <label>
            <span>Logo</span>
            <img class="logo-preview" src="<?= $settings['logo'] ?>" />
            <input name="logo" type="file">
          </label>
        <?php } ?>
      </fieldset>
      <fieldset>
        <legend>Mailjet settings</legend>
        <label>
          <span>Mailjet public key</span>
          <input name="mailjet_key_public" type="text" value="<?= $settings['mailjet_key_public'] ?>">
          <span class="help">Find your <a href="https://app.mailjet.com/account/api_keys" target="_blank">API keys</a> in Mailjet</span>
        </label>
        <label>
          <span>Mailjet secret key</span>
          <input name="mailjet_key_secret" type="text" value="<?= $settings['mailjet_key_secret'] ?>">
          <span class="help">Find your <a href="https://app.mailjet.com/account/api_keys" target="_blank">API keys</a> in Mailjet</span>
        </label>
        <label>
          <span>From email address</span>
          <input name="mailjet_email_from" type="text" value="<?= $settings['mailjet_email_from'] ?>">
          <span class="help">Must be an <a href="https://app.mailjet.com/account/sender" target="_blank">active sender address</a> in Mailjet</span>
        </label>
      </fieldset>


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