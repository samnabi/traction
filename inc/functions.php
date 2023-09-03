<?php
// Only report errors, not warnings or notices
error_reporting(E_ERROR);

/**
 * Get active campaign
 *
 * @return array Data from active campaign file
 */
function getCampaign($slug = '') {
  // Define path to campaigns dirctory
  $campaigns_directory = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'campaigns';

  // try to get campaign from query string
  if ($slug == '' and isset($_GET['campaign'])) $slug = $_GET['campaign'];

  // if it's still empty, return null
  if ($slug == '') return null;

  $campaign_path = $campaigns_directory.DIRECTORY_SEPARATOR.$slug.'.yml';
  if (f::exists($campaign_path)) {
    // File exists
    $campaign = yaml::read($campaign_path);
  } else {
    // Otherwise, return null
    return null;
  }

  // Add some metadata to the return value
  $campaign['slug'] = $slug;
  $campaign['path'] = $campaign_path;

  return $campaign;
}

/**
 * Helper function for sending mail to multiple recipients through the Mailjet API
 *
 * @param array $options (from_name, from_email, to, subject, message, address)
 * @return Mailto link
 */
function getMailtoLink($options) {
  $campaign = getCampaign();

  // Validate and santize input
  $options['from_name'] = filter_var($options['from_name'], FILTER_SANITIZE_STRING);
  $options['from_email'] = filter_var($options['from_email'], FILTER_SANITIZE_EMAIL);
  $options['from_email'] = filter_var($options['from_email'], FILTER_VALIDATE_EMAIL);
  $recipients = [];
  foreach ($options['to'] as $key => $recipient) {
    $recipients[$key] = filter_var($recipient, FILTER_SANITIZE_EMAIL);
    $recipients[$key] = filter_var($recipients[$key], FILTER_VALIDATE_EMAIL);
  }
  $options['subject'] = filter_var($options['subject'], FILTER_SANITIZE_STRING);
  $options['message'] = str_replace("\n", "%0A", filter_var($options['message'], FILTER_SANITIZE_STRING));
  $options['address'] = filter_var($options['address'], FILTER_SANITIZE_STRING);

  // Generate link
  $response = 'mailto:'.$options['from_email'].
    '?cc='.$campaign['email'].
    '&bcc='.implode(',', $recipients).
    '&subject='.$options['subject'].
    '&body='.
      $options['message'].urlencode("\n\n").
      'Sincerely,'.urlencode("\n\n").
      $options['from_name'].urlencode("\n").
      $options['address'];

  return $response;
}