<?php

// Only report errors, not warnings or notices
error_reporting(E_ERROR);

// Include settings and helper functions
require_once('inc/toolkit/bootstrap.php');
require_once('inc/functions.php');
$settings = yaml::read('settings.yml');

// Honeypot trap for spambots
// address2 is a fake field that isn't actually used for form submission
if (isset($_POST['address2']) and $_POST['address2'] != null) die;

// Handle form submission
if(isset($_POST['submit'])) {
  $email = sendMail([
    'from_name' => $_POST['name'],
    'from_email' => $_POST['email'],
    'to' => $_POST['recipients'],
    'subject' => $_POST['subject'],
    'message' => $_POST['message'],
    'address' => $_POST['address']
  ]);

  if (isset($email) and $email->success()) {
    $success = true;
  } else {
    $error = true;
  }
}

// Load campaign data
$campaign = getCampaign();

// Check that there is actually a campaign
if ($campaign == null) {
  header::redirect('edit.php');
}

?>