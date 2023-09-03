<?php 
  // Only report errors, not warnings or notices
  error_reporting(E_ERROR);

  // Include settings and helper functions
  require_once('inc/toolkit/bootstrap.php');
  require_once('inc/functions.php');

  // Authenticate
  require_once('inc/auth.php');

  // Set campaign
  $c = getCampaign($_GET['campaign']);

  // Handle form submission
  if ($_POST['update_campaign']) {
    
    // Grab campaign data
    $campaign = [
      'organization' => $_POST['organization'],
      'email' => $_POST['email'],
      'logo' => $c['logo'],
      'title' => $_POST['title'],
      'info' => $_POST['info'],
      'subject' => $_POST['subject'],
      'message' => $_POST['message'],
      'social_text' => $_POST['social_text'],
      'twitter' => $_POST['twitter']
    ];

    // Grab new logo
    // Todo: size, type checks
    if ($_FILES['logo']['tmp_name']) {
      f::remove($campaign['logo']);
      f::copy($_FILES['logo']['tmp_name'], $_FILES['logo']['name']);
      $campaign['logo'] = $_FILES['logo']['name'];
    } else if ($_POST['remove_logo'] == 'true') {
      f::remove($campaign['logo']);
      $campaign['logo'] = null;
    }

    // Reformat recipients info
    $recipients = [];
    foreach ($_POST as $key => $value) {
      if (str::contains($key, 'recipient_')) {
        $key_parts = str::split($key, '_');
        if (!isset($recipients[$key_parts[2]])) $recipients[$key_parts[2]] = [];
        $recipients[$key_parts[2]][$key_parts[1]] = $value;
      }
    }
    $campaign['recipients'] = $recipients;

    // Write to file
    yaml::write($c['path'], $campaign);

    // Set campaign again
    $c = getCampaign($_GET['campaign']);
  }

  // Handle new campaign
  if ($_POST['action'] == 'new_campaign') {
    $slug = str::slug($_POST['title']);
    $file_path = __DIR__.DIRECTORY_SEPARATOR.'campaigns'.DIRECTORY_SEPARATOR.$slug.'.yml';
    if (!file_exists($file_path)) {
      yaml::write($file_path, [
        'title' => $_POST['title']
      ]);
    }
    $c = getCampaign($slug);
  }

  // Handle delete campaign
  if ($_POST['action'] == 'delete_campaign') {
    $file_path = __DIR__.DIRECTORY_SEPARATOR.'campaigns'.DIRECTORY_SEPARATOR.$_POST['campaign'].'.yml';
    f::remove($file_path);
    $c = getCampaign();
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
        <?php $campaigns = array_diff(scandir('campaigns'), ['.', '..', '.gitkeep']); ?>
        <?php foreach($campaigns as $campaign_file) { ?>
          <?php $slug = str_replace('.yml', '', $campaign_file); ?>
          <?php $campaign = getCampaign($slug) ?>
          <li <?= $c['slug'] == $slug ? 'class="active"' : '' ?>>
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
      <h2>Settings</h2>
      <ul>
        <li><a href="/account.php">Account settings</a></li>
      </ul>
    </section>
  </aside>

  <main>
    <?php if ($c['slug']) { ?>
      <form action="" method="POST" enctype="multipart/form-data">
        
        <fieldset>
          <label>
            <span class="help">Link to petition</span>
            <input type="text" readonly value="<?= url() ?>/?campaign=<?= $c['slug'] ?>">
          </label>
        </fieldset>

        <button type="submit">Save changes</button>

        <fieldset>
          <legend>Organization</legend>
          <label>
            <span>Organization name</span>
            <input name="organization" type="text" value="<?= $campaign['organization'] ?>">
          </label>
          <label>
            <span>Organization email</span>
            <span class="help">This email address will be <abbr title="Carbon copied">CC</abbr>ed on all messages by default</span>
            <input name="email" type="email" value="<?= $campaign['email'] ?>">
          </label>
          <?php if ($campaign['logo']) { ?>
            <label>
              <span>Logo</span>
              <img class="logo-preview" src="<?= $campaign['logo'] ?>" />
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
              <img class="logo-preview" src="<?= $campaign['logo'] ?>" />
              <input name="logo" type="file">
            </label>
          <?php } ?>
        </fieldset>

        <fieldset>
          <legend>Campaign info</legend>
          <label>
            <span>Title</span>
            <input name="title" type="text" value="<?= $c['title'] ?>">
          </label>
          <label>
            <span>Campaign description</span>
            <span class="help">This is the message people see above the form. Use it to introduce the topic and convince them to send a message.</span>
            <textarea name="info"><?= $c['info'] ?></textarea>
          </label>
        </fieldset>
        <fieldset class="recipients">
          <legend>Recipients</legend>

          <!-- Hidden fieldset, this is the template we copy when adding a new recipient -->
          <fieldset class="recipient--template">
            <label>
              <span>Display Name</span>
              <input type="text" placeholder="Kim Campbell, Prime Minister">
            </label>
            <label>
              <span>Email</span>
              <input type="email" placeholder="recipient@mail.com">
            </label>
          </fieldset>

          <?php foreach($c['recipients'] as $key => $recipient) { ?>
            <fieldset class="recipient">
              <label>
                <span>Display Name</span>
                <input type="text" name="recipient_name_<?= $key ?>" value="<?= $recipient['name'] ?>">
              </label>
              <label>
                <span>Email</span>
                <input type="email" name="recipient_email_<?= $key ?>" value="<?= $recipient['email'] ?>">
              </label>
            </fieldset>
          <?php } ?>

          <button data-action="add_recipient" type="button">+ Add recipient</button>
        </fieldset>
        <fieldset>
          <legend>Message content</legend>
          <p class="help">This is the email that will be sent to the recipients. People can modify this text to personalize the message.</p>
          <label>
            <span>Subject</span>
            <input name="subject" type="text" value="<?= $c['subject'] ?>">
          </label>
          <label>
            <span>Message</span>
            <p>Good morning/afternoon/evening,</p>
            <textarea name="message"><?= $c['message'] ?></textarea>
            <p>Sincerely,</p>
            <p>Participant name</p>
          </label>
        </fieldset>
        <fieldset>
          <legend>Social media template</legend>
          <label>
            <span>Social text</span>
            <span class="help">This is a message that will be pre-populated in social media posts, should the user choose to share the campaign.</span>
            <textarea name="social_text"><?= $c['social_text'] ?></textarea>
          </label>
          <label>
            <span>Twitter name</span>
            <div class="flex align-center gap--small">@ <input name="twitter" type="text" value="<?= $c['twitter'] ?>" placeholder="YourTwitterName"></div>
          </label>
        </fieldset>

        <input type="hidden" name="update_campaign" value="<?= $c['slug'] ?>">
        <button type="submit">Save changes</button>
      </form>

      <form action="" method="POST">
        <details>
          <summary class="warning">Delete campaign</summary>
          <p>Are you sure? This action cannot be undone.</p>
          <input type="hidden" name="action" value="delete_campaign">
          <input type="hidden" name="campaign" value="<?= $c['slug'] ?>">
          <button class="warning" type="submit">Yes, delete this campaign</button>
        </details>
      </form>
    <?php } else { ?>
      <p class="notice">Please choose a campaign to edit.</p>
    <?php } ?>
  </main>

  <script>
    var add_recipient_button = document.querySelector('[data-action="add_recipient"]');
    if (add_recipient_button) {
      add_recipient_button.addEventListener('click', function(event){
        // Add new recipient inputs
        var recipient = document.querySelector('.recipient--template');
        var new_recipient = recipient.cloneNode(true);
        new_recipient.classList.remove('recipient--template');
        new_recipient.classList.add('recipient');
        var fieldset = document.querySelector('.recipients');
        var new_recipient_node = fieldset.insertBefore(new_recipient, add_recipient_button);
        var new_recipient_key = document.querySelectorAll('.recipient').length - 1;
        
        // Add new input names
        new_recipient_node.querySelector('label:first-child input[type="text"]').name = 'recipient_name_' + new_recipient_key;
        new_recipient_node.querySelector('input[type="email"]').name = 'recipient_email_' + new_recipient_key;
      });
    }
  </script>

</body>
</html>