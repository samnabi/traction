<?php require_once('controller.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?= $campaign['title'] ?></title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  
  <?php if (isset($success)) { ?>
    <?php
      // On success, force a redirect so reloading doesn't cause the form to be submitted twice 
      $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
      $host     = $_SERVER['HTTP_HOST'];
      $params   = $_SERVER['QUERY_STRING'];
    ?>
    <meta http-equiv="refresh" content="0; url=<?= $protocol.'://'.$host.'?'.$params ?>&success=true">
  <?php } ?>
</head>
<body>

<main>

  <h1 class="logo">
    <?php if (isset($settings['logo']) and $settings['logo'] != null and f::exists(__DIR__.DIRECTORY_SEPARATOR.$settings['logo'])) { ?>
      <img src="<?= $settings['logo'] ?>" alt="<?= $settings['title'] ?>">
    <?php } else { ?>
      <?= $settings['title'] ?>
    <?php } ?>
  </h1>

  <?php if (!isset($success) and !get('success')) { ?>

    <?php if (isset($error)) { ?>
      <p class="notice error">Sorry, something went wrong.</p>
    <?php } ?>

    <form action="" method="POST">
      <h2><?= $campaign['title'] ?></h2>

      <p><?= $campaign['info'] ?></p>

      <hr>

      <fieldset>
        <legend class="label">To: (<?= count($campaign['recipients']) ?> recipients)</legend>
        <div class="recipients-list">
          <?php foreach ($campaign['recipients'] as $recipient) { ?>
            <label class="checkbox-label">
              <input checked type="checkbox" name="recipients[]" value="<?= $recipient['email'] ?>">
              <span><?= $recipient['name'] ?></span>
            </label>
          <?php } ?>
        </div>
      </fieldset>

      <fieldset class="flex">
        <label class="label">
          <span>Your name</span><br>
          <input type="text" name="name" required>
        </label>
        
        <label class="label">
          <span>Your email</span><br>
          <input type="email" name="email" required placeholder="your@email.com">
        </label>
      </fieldset>
      
      <label>
        <span>Subject</span><br>
        <input type="text" name="subject" value="<?= $campaign['subject'] ?>" required>
      </label>

      <label>
        <span>Your message</span><br>
        <p class="message_salutation">Dear [Name],</p>
        <textarea name="message" required><?php foreach (str::lines($campaign['message']) as $line) { echo $line."\n\n"; } ?></textarea>
      </label>

      <label>
        <span>City / postal code (optional)</span>
        <input type="text" name="address" value="">
      </label>

      <input type="text" name="address2" class="sr-only" title="Do not fill this field out. It is a trap for spam bots.">

      <button name="submit" type="submit">Send emails</button>

      <p class="help">Recipients can reply directly to your email address. This website does not store or track any information you send through this form.</p>
    </form>

  <?php } else { ?>
    <div class="notice success">
      <p><strong>Success!</strong> Thank you for your sending your message.</p>
    </div>
  <?php } ?>

  <?php if (isset($campaign['social_text'])) { ?>
    <div class="social">
      <h1>Spread the word</h1>
      <blockquote><?= $campaign['social_text'] ?> <?= str::link($campaign['social_url']) ?></blockquote>
      
      <div class="flex">
        <a class="btn" target="_blank" href="https://twitter.com/intent/tweet?text=<?= urlencode($campaign['social_text']) ?>&amp;url=<?= urlencode($campaign['social_url']) ?>&amp;via=<?= $campaign['twitter'] ?>">
          <svg style="vertical-align: middle; transform: translateY(-2px); xmlns=" http:="" www.w3.org="" 2000="" svg"="" width="18" height="18" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"></path></svg>
          Tweet this
        </a>

        <a class="btn" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($campaign['social_url']) ?>&amp;quote=<?= urlencode($campaign['social_text']) ?>">
          <svg style="vertical-align: middle; transform: translateY(-2px);" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="18" height="18" viewBox="0 0 32 32" enable-background="new 0 0 32 32" xml:space="preserve"><path d="M30.7,0H1.3C0.6,0,0,0.6,0,1.3v29.3C0,31.4,0.6,32,1.3,32H17V20h-4v-5h4v-4 c0-4.1,2.6-6.2,6.3-6.2C25.1,4.8,26.6,5,27,5v4.3l-2.6,0c-2,0-2.5,1-2.5,2.4V15h5l-1,5h-4l0.1,12h8.6c0.7,0,1.3-0.6,1.3-1.3V1.3 C32,0.6,31.4,0,30.7,0z"></path></svg>
          Share on Facebook
        </a>
      </div>
    </div>
  <?php } ?>

</main>

</body>
</html>