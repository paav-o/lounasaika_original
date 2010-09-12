<!DOCTYPE HTML>
<html>

<?php
  // set the Finnish locale --
  setlocale(LC_ALL, "fi_FI.UTF-8");
  // -- and the Finnish timezone
  date_default_timezone_set('Europe/Helsinki');
  // turn off error reporting in production environment
  error_reporting(0);

  // open database connection
  try {
    $db = new PDO('sqlite:db/lounasaika.db');
  }
  catch (PDOException $e) {
    echo $e->getMessage();
  }

  // fetch all restaurants from database
  $restaurants = $db->query("SELECT * FROM restaurants")->fetchAll(PDO::FETCH_ASSOC);

  // check on which campi the restaurants are located
  foreach ($restaurants as $key => $current_restaurant) {
    if (mb_strtolower($_GET["campus"]) == mb_strtolower($current_restaurant['campus'])) {
      // check that the possible campus in the url is valid
      $selected_campus_valid = TRUE;
    }
    $campi[] = $current_restaurant['campus'];
  }
  // every campus must be stored only once
  $campi = array_unique($campi);

  // use the campus posted in the url if it's valid
  if ($selected_campus_valid == TRUE) {
    $campus = $_GET["campus"];
  }
  // if no campus was posted, show all campi
  else if (empty($_GET["campus"]) || $campus == 'kaikki') {
    $campus = 'kaikki';
  }
  // if user tried to look for some non-existent redirect to front page
  else { $redirect = '<meta http-equiv="refresh" content="0; http://www.lounasaika.net/">'; }

  // in non-javascript version it is possible to post the day as part of the url
  // (i.e. lounasaika.net/otaniemi/huomenna/)
  // if that's the case, read the day for more machine-friendy format
  $day = $_GET["day"];
  if ($day == 'huomenna') {
    $time_for_header = strtotime('+1 day');
  }
  else if ($day == 'ylihuomenna') {
    $time_for_header = strtotime('+2 days');
  }
  else {
    $time_for_header = strtotime('now');
  }
?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo $redirect ?>
    <title>lounasaika.net | "Joko on lounasaika?"</title>
    <link type="text/css" rel="stylesheet" media="all" href="/css/style.css" />
    <link type="text/css" rel="stylesheet" media="all" id="themed_css" href="/css/themes/lounasaika_original.css" />
    <link type="image/x-icon" rel="icon" href="favicon.ico" />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/js/lounasaika.scripts.minified.js"></script>

    <script type="text/javascript">
      $(document).ready(function() {
        // set default campus (all) and day (today)
        window.day = '<?php echo date('N') ?>';
        window.campus = 'all'

        // check whether a color theme has been saved to a cookie previously
        if ($.cookie('theme') != null) {
          // apply the theme if available
          $("#themed_css").attr('href', '/css/themes/'+$.cookie('theme')+'.css');
        }
        // check whether a campus preference has been saved to a cookie previously
        if ($.cookie('campus') != null && $.cookie('campus') != 'all' && '<?php echo $_GET["campus"] ?>' == '') {
          window.campus = $.cookie('campus');
          loadrestaurants(window.day, window.campus);
          $('#campi').find('a').find('div').removeClass('active');
          $('#'+window.campus).find('div').addClass('active');
        }
      });
    </script>
   
   
</head>

<body>
<div id="header">
   <img src="/img/logo.png" id="logo" width="60" height="60" />
   <div id="subheader_wrapper">
     <h1><a href="http://www.lounasaika.net/<?php if ($campus != 'kaikki') { echo $campus; } ?>">lounasaika.net<span id="campus_url"><?php if (!empty($campus) && $campus != 'kaikki') { echo '/'.$campus; } ?></span></a></h1>
     <span id="slogan"><span id="day"><?php echo (date('N', $time_for_header) == 3) ? 'Keskiviikon' : ucfirst(strftime("%A", $time_for_header)) . 'n' ?></span> poikkitieteelliset ruokalistat</span>
   </div>

   <div class="header_inside">
     <a href="http://lounasaika.uservoice.com/forums/76019-general" title="Jäikö nälkä? Anna palautetta!">PALAUTE</a> |
     <a href="http://github.com/paav-o/lounasaika" title="Palvelu on avointa lähdekoodia. Osallistu kehittämiseen Githubissa!">GITHUB</a><br />
   </div>
</div>

