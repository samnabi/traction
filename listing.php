<!DOCTYPE html>
<html lang="en">
<head>
  <title>petition town!</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css?v=3">

  <?php
    $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
    $host     = $_SERVER['HTTP_HOST'];
    $path = $_SERVER['SCRIPT_NAME'];
    $params   = $_SERVER['QUERY_STRING'];
  ?>
</head>
<body>

<main>

  <h1 class="logo">Petition Town!</h1>

  <div class="notice">
    <?php
      require_once('inc/toolkit/bootstrap.php');
      require_once('inc/functions.php');
    ?>
    <?php $campaigns = array_diff(scandir('campaigns'), ['.', '..', '.gitkeep']); ?>
    <ul class="listing">
      <?php foreach($campaigns as $campaign_file) { ?>
        <?php $slug = str_replace('.yml', '', $campaign_file); ?>
        <?php $campaign = getCampaign($slug) ?>
        <?php if (!$campaign['title']) continue; ?>
        <li>
          <a href="/index.php?campaign=<?= $slug ?>">
            <strong>
              <?php if (isset($campaign['logo']) and $campaign['logo'] != null and f::exists(__DIR__.DIRECTORY_SEPARATOR.$campaign['logo'])) { ?>
                <img src="<?= $campaign['logo'] ?>" alt="<?= $campaign['title'] ?>">
                <span><?= $campaign['title'] ?></span>
              <?php } else { ?>
                <?= $campaign['title'] ?>
              <?php } ?>
            </strong>
            <span class="btn">Sign this petition</span>
          </a>
        </li>
      <?php } ?>
    </ul>
  </div>

</main>

<aside>
  <p>Petition Town is hosted by <a href="https://samnabi.com">Sam Nabi</a> on the Haldimand Tract, territory of the <a href="https://native-land.ca/maps/territories/attiwonderonk-neutral/">Chonnonton</a>, <a href="https://native-land.ca/maps/territories/haudenosauneega-confederacy/">Haudenosaunee</a>, and <a href="https://native-land.ca/maps/territories/anishinabek-%e1%90%8a%e1%93%82%e1%94%91%e1%93%88%e1%90%af%e1%92%83/">Anishinaabe</a> peoples.</p>
</aside>

</body>
</html>