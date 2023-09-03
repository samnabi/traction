<?php require_once('controller.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?= $campaign['title'] ?></title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css?v=4">

  <?php
    $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
    $host     = $_SERVER['HTTP_HOST'];
    $path = $_SERVER['SCRIPT_NAME'];
    $params   = $_SERVER['QUERY_STRING'];
  ?>
</head>
<body>

<main>

  <h1 class="logo">
    <?php if (isset($campaign['logo']) and $campaign['logo'] != null and f::exists(__DIR__.DIRECTORY_SEPARATOR.$campaign['logo'])) { ?>
      <img src="<?= $campaign['logo'] ?>" alt="<?= $campaign['title'] ?>">
    <?php } else { ?>
      <?= $campaign['title'] ?>
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
      
      <label>
        <span>Subject</span><br>
        <input type="text" name="subject" value="<?= $campaign['subject'] ?>" required>
      </label>

      <label>
        <span>Your message</span><br>
        <span class="help">We encourage you to edit this message with a story from your personal life. Why does this issue matter to you?</span>
        <textarea name="message" required><?php foreach (str::lines($campaign['message']) as $line) { echo $line."\n\n"; } ?></textarea>

        <script>
          // Initialize greeting
          var d = new Date();
          var hour = d.getHours();
          if (hour < 12) {
            var greeting = 'Good morning,' + "\n\n";
          } else if (hour < 17) {
            var greeting = 'Good afternoon,' + "\n\n";
          } else {
            var greeting = 'Good evening,' + "\n\n";
          }
          var message = document.querySelector('textarea[name="message"]');
          message.value = greeting + message.value;
        </script>
      </label>

      <p>Sincerely,</p>

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
        <span>City / postal code (optional)</span>
        <span class="help">Recipients are more likely to take your message seriously if they represent the area where you live.</span>
        <input type="text" name="address" value="">
      </label>

      <input type="text" name="last_name" class="sr-only" title="Do not fill this field out">

      <p>
        <br />
        <button name="submit" type="submit">Send email</button>
      </p>

      <p class="help">Recipients can reply directly to your email address. This website does not store or track any information you send through this form.</p>
    </form>

  <?php } else { ?>

    <p class="notice">
      <a class="btn btn--send" href="<?= $link ?>">Continue in your email app &rarr;</a>
      <script type="text/javascript">
        document.querySelector('.btn--send').click();
      </script>
    </p>

    <div class="notice success">

      <p><strong>Almost done! Redirecting to your email app...</strong></p>

      <p>If you are not automatically redirected, click the button above to continue.</p>

      <h3>Why do I need to send this message manually?</h3>

      <ul>
        <li>Sending a personal email from your own account reduces the chances of messages being flagged as spam. Recipients will know that it's coming from a real person.</li>

        <li>Recipients will also be able to reply to your message to continue the conversation.</li>

        <li>This approach makes it harder for recipients to create filters to ignore messages from petition websites.</li>
      </ul>
    </div>

    <?php if (isset($campaign['social_text'])) { ?>
      <div class="social">
        <h1>Spread the word</h1>
        <blockquote><?= $campaign['social_text'] ?> <?= str::link($protocol.'://'.$host.$path.'?'.$params) ?></blockquote>
        
        <div class="flex">
          <a class="btn" target="_blank" href="https://twitter.com/intent/tweet?text=<?= urlencode($campaign['social_text']) ?>&amp;url=<?= urlencode($protocol.'://'.$host.$path.'?'.$params) ?>&amp;via=<?= $campaign['twitter'] ?>">
            <svg style="vertical-align: middle; transform: translateY(-2px); xmlns=" http:="" www.w3.org="" 2000="" svg"="" width="18" height="18" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"></path></svg>
            Tweet this
          </a>

          <a class="btn" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($protocol.'://'.$host.$path.'?'.$params) ?>&amp;quote=<?= urlencode($campaign['social_text']) ?>">
            <svg style="vertical-align: middle; transform: translateY(-2px);" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="18" height="18" viewBox="0 0 32 32" enable-background="new 0 0 32 32" xml:space="preserve"><path d="M30.7,0H1.3C0.6,0,0,0.6,0,1.3v29.3C0,31.4,0.6,32,1.3,32H17V20h-4v-5h4v-4 c0-4.1,2.6-6.2,6.3-6.2C25.1,4.8,26.6,5,27,5v4.3l-2.6,0c-2,0-2.5,1-2.5,2.4V15h5l-1,5h-4l0.1,12h8.6c0.7,0,1.3-0.6,1.3-1.3V1.3 C32,0.6,31.4,0,30.7,0z"></path></svg>
            Post on Facebook
          </a>

          <a class="btn btn--share" target="#" title="<?= $campaign['title'] ?>" href="<?= $protocol.'://'.$host.$path.'?'.$params ?>">
            <svg style="vertical-align: middle; transform: translateY(-2px);" width="20" height="20" viewBox="0 0 20 20" fill="inherit" xmlns="http://www.w3.org/2000/svg">
            <path d="M12.3776 5.70757V3.57782C12.3776 3.09805 12.9078 2.83964 13.268 3.10864L13.3297 3.16165L17.8268 7.58283C18.0367 7.78916 18.0558 8.12058 17.8841 8.34968L17.8269 8.41512L13.3298 12.8377C12.992 13.1699 12.4429 12.9566 12.3829 12.5039L12.3776 12.4216V10.3261L12.1199 10.3493C10.3196 10.5417 8.59416 11.3728 6.93261 12.8564C6.54318 13.2042 5.94067 12.8754 6.00472 12.3501C6.50344 8.25989 8.59039 6.00547 12.153 5.72268L12.3776 5.70757ZM5.5 4C4.11929 4 3 5.11929 3 6.5V14.5C3 15.8807 4.11929 17 5.5 17H13.5C14.8807 17 16 15.8807 16 14.5V13.5C16 13.2239 15.7761 13 15.5 13C15.2239 13 15 13.2239 15 13.5V14.5C15 15.3284 14.3284 16 13.5 16H5.5C4.67157 16 4 15.3284 4 14.5V6.5C4 5.67157 4.67157 5 5.5 5H8.5C8.77614 5 9 4.77614 9 4.5C9 4.22386 8.77614 4 8.5 4H5.5Z" />
            </svg>
            Share
          </a>

          <script type="text/javascript">
            // Todo: load this script once in the footer if we ever have more than one share link on a page
            document.querySelector('.btn--share').addEventListener('click', function(e) {
              const title = document.querySelector('.btn--share').getAttribute('title');
              const url = document.querySelector('.btn--share').getAttribute('href');
              e.preventDefault(); // Stop link navigation
              if(navigator.share !== undefined) {
                navigator.share({
                  title: title,
                  url: url
                }).then(value => {
                  alert(value);
                }).catch(err => {
                  alert(err);
                });
              } else if (window.clipboardData && window.clipboardData.setData) {
                // IE: prevent textarea being shown while dialog is visible
                window.clipboardData.setData("Text", url);
                alert('The link was copied to your clipboard.');
              } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
                var textarea = document.createElement("textarea");
                textarea.textContent = url;
                textarea.style.opacity = 0;
                textarea.style.position = "fixed"; // Prevent scrolling to bottom of page in MS Edge
                document.body.appendChild(textarea);
                textarea.select();
                try {
                  // Security exception may be thrown by some browsers
                  document.execCommand("copy");
                  alert('The link was copied to your clipboard.');
                } catch (ex) {
                  alert("There was a problem copying the link. Please copy it here:\n\n" + e.target.getAttribute('href'));
                } finally {
                  document.body.removeChild(textarea);
                }
              }
            });

          </script>
        </div>
      </div>
    <?php } ?>
  <?php } ?>

</main>

<aside>
  <details>
    <summary>Embed this form</summary>
    <h3>Copy this code to embed this form on another website</h3>
    <pre>&lt;iframe src="<?= $protocol.'://'.$host.$path.'?'.$params ?>" style="width:100%;border:2px solid hsl(125,100%,93%);border-radius:10px;height:90vh;">&lt;/iframe></pre>
  </details>
</aside>

</body>
</html>