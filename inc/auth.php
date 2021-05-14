<?php
  // Load settings
  $settings = yaml::read('settings.yml');

  // Start session
  s::start();

  // Setting a password
  if ($_POST['create_password']) {
    $settings['password'] = password::hash($_POST['create_password']);
    yaml::write('settings.yml', $settings);
  }

  // Log in
  if ($_POST['log_in'] and password::match($_POST['log_in'], $settings['password'])) { 
    s::set('logged_in', true);
  }

  // Todo: log out
  //

  if (s::get('logged_in') == true) {
    // We're in
  } else {
    // Need to log in
    if ($settings['password'] == null) {
      // No password set, need to set it for the first time.
      ?>
      <p>A password hasn't been set yet. Create one now.</p>
      <form action="" method="POST">
        <label>
          <span>Password</span>
          <input name="create_password" type="password">
        </label>
        <button type="submit">Create password</button>
      </form>
      <?php
      die;
    } else {
      // Password is set.
      ?>
      <p>This area is protected.</p>
      <form action="" method="POST">
        <label>
          <span>Password</span>
          <input name="log_in" type="password">
        </label>
        <button type="submit">Log in</button>
      </form>
      <?php
      die;
    }
  }

?>