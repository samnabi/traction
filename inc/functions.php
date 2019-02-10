<?php
// Only report errors, not warnings or notices
error_reporting(E_ERROR);

/**
 * Get active campaign
 *
 * @return array Data from active campaign file
 */
function getCampaign() {
  // Define path to campaigns dirctory
  $campaigns_directory = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'campaigns';

  // try to get campaign from query string
  $campaign_path = $campaigns_directory.DIRECTORY_SEPARATOR.$_GET['campaign'].'.yml';
  if (f::exists($campaign_path)) {
    // File exists
    $campaign = yaml::read($campaign_path);
  } else {
    // Otherwise, use the first available campaign
    $campaigns = dir::read($campaigns_directory, ['.', '..']);
    foreach ($campaigns as $file) {
      $campaign = yaml::read($campaigns_directory.DIRECTORY_SEPARATOR.$file);
      break;
    }
  }

  return $campaign;
}

/**
 * Helper function for sending mail to multiple recipients through the Mailjet API
 *
 * @param array $options (from_name, from_email, to, subject, message, address)
 * @return Mailjet response object
 */
require('mailjet/vendor/autoload.php');
use \Mailjet\Resources;
function sendMail($options) {

  // Grab the settings from config.php
  global $settings;

  // Validate and santize input
  $options['from_name'] = filter_var($options['from_name'], FILTER_SANITIZE_STRING);
  $options['from_email'] = filter_var($options['from_email'], FILTER_SANITIZE_EMAIL);
  $options['from_email'] = filter_var($options['from_email'], FILTER_VALIDATE_EMAIL);
  foreach ($options['to'] as $key => $recipient) {
    $options['to'][$key] = filter_var($recipient, FILTER_SANITIZE_EMAIL);
    $options['to'][$key] = filter_var($recipient, FILTER_VALIDATE_EMAIL);
  }
  $options['subject'] = htmlspecialchars($options['subject']);
  $options['message'] = htmlspecialchars($options['message']);
  $options['address'] = htmlspecialchars($options['address']);

  // Connect to Mailjet
  $mj = new \Mailjet\Client($settings['mailjet.key.public'], $settings['mailjet.key.secret']);

  // Define common fields for all messages
  $body = [
    'FromEmail' => $settings['mailjet.email.from'],
    'FromName' => $options['from_name'],
    'Subject' => htmlspecialchars_decode($options['subject']),
    'Headers' => ["Reply-To" => $options['from_email']]
  ];

  // Build each message
  $messages = [];
  $campaign = getCampaign();
  foreach ($options['to'] as $recipient) {
    foreach ($campaign['recipients'] as $r) {
      if ($r['email'] == $recipient) {
        $body['Text-part'] = $r['salutation']."\n\n".$options['message']."\n\n".$options['address'];
        break;
      }
    }
    $body['Recipients'] = [['Email' => $recipient]];
    $messages[] = $body;
  }

  // Send all messages
  $response = $mj->post(Resources::$Email, ['body' => ['Messages' => $messages]]);

  return $response;
}