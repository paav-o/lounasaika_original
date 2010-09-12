<?php setlocale(LC_ALL, "fi_FI.UTF-8") ?>
<?php date_default_timezone_set('Europe/Helsinki') ?>
<?php error_reporting(0) ?>

<?php include_once('header.inc') ?>

<div class="nav" id="weekdays">
  <a href="/<?php echo ((isset($campus)) ? $campus : 'kaikki').'/tänään/' ?>" onclick="return false;" id="<?php echo strtolower(date('N')) ?>"><div <?php if ($day == 'tänään' || empty($day)) { echo 'class="active"'; } ?>>tänään</div></a>
  <?php if (date('N') <= 6) : ?>
    <a href="/<?php echo ((isset($campus)) ? $campus : 'kaikki').'/huomenna/' ?>" onclick="return false;" id="<?php echo strtolower(date('N', (time()+24*60*60))) ?>"><div <?php if ($day == 'huomenna') { echo 'class="active"'; } ?>>huomenna</div></a>
  <?php endif; ?>
  <?php if (date('N') <= 5) : ?>
    <a href="/<?php echo ((isset($campus)) ? $campus : 'kaikki').'/ylihuomenna/' ?>" onclick="return false;" id="<?php echo strtolower(date('N', (time()+48*60*60))) ?>"><div <?php if ($day == 'ylihuomenna') { echo 'class="active"'; } ?>>ylihuomenna</div></a>
  <?php endif; ?>
</div>


<div class="nav" id="campi">
  <a href="/kaikki/<?php if (!empty($day)) { echo $day.'/'; } ?>" onclick="return false;" id="all"><div <?php if ($campus == 'kaikki' || empty($campus)) { echo 'class="active"'; } ?>>kaikki ravintolat</div></a>
  <a style="display: none">tallennetut</a>
  <?php foreach ($campi as $current_campus) : ?>
     <a href="/<?php echo strtolower($current_campus).'/' ?><?php if (!empty($day)) { echo $day.'/'; } ?>" onclick="return false;" id="<?php echo $current_campus ?>">
       <div <?php if ($campus == strtolower($current_campus)) { echo 'class="active"'; } ?>><?php echo $current_campus ?></div>
     </a>
  <?php endforeach; ?>

</div>

<div class="nav" id="theme_selection">
  <a href="/js" onclick="return false;"><div id="choose_theme">valitse väriteema</div></a>
  <div id="themes">
      <a href="" onclick="return false;"><div class="theme" id="lounasaika_original" title="Lounasaika Original"></div></a>
      <a href="" onclick="return false;"><div class="theme" id="otaniemi_classic" title="Otaniemi Classic"></div></a>
      <a href="" onclick="return false;"><div class="theme" id="toolo_classic_blue" title="Töölö Classic Blue"></div></a>
      <a href="" onclick="return false;"><div class="theme" id="toolo_classic_green" title="Töölö Classic Green"></div></a>
      <a href="" onclick="return false;"><div class="theme" id="toolo_classic_grey" title="Töölö Classic Grey"></div></a>
  </div>
</div>


<div id="all_restaurants">
<?php
  if ($day == 'tänään' || empty($day)) {
    $day = date('N');
  }
  else if ($day == 'huomenna') {
    $day = date('N', (time()+24*60*60));
  }
  else if ($day == 'ylihuomenna') {
    $day = date('N', (time()+48*60*60));
  }
  else {
    $day = '';
  }
?>
<?php if (empty($campus) || $campus == 'kaikki') { $campus = 'all'; } else { $campus = ucfirst($campus); } ?>
<?php require_once('http://'.$_SERVER['SERVER_NAME'].'/ajax/loadrestaurants.php?day='.$day.'&campus='.$campus) ?>
</div>

</body>

</html>