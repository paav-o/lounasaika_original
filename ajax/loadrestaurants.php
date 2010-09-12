<?php
  // set the Finnish locale --
  setlocale(LC_ALL, "fi_FI.UTF-8");
  // -- and the Finnish timezone
  date_default_timezone_set('Europe/Helsinki');
  // turn off error reporting in production environment
  error_reporting(0);

  // open database connection
  try {
    $db = new PDO('sqlite:../db/lounasaika.db');
  }
  catch (PDOException $e) {
    echo $e->getMessage();
  }

  // fetch all menus to a temporary array
  $all_menus_temp = $db->query("SELECT * FROM menus")->fetchAll(PDO::FETCH_ASSOC);
    // form a new array where restaurant name is key
    foreach ($all_menus_temp as $key => $current_menu)
    {
      $restaurant_name = $current_menu['restaurant'];
      $all_menus[$restaurant_name] = $current_menu;
    }
  
  // fetch all restaurants
  $restaurants = $db->query("SELECT * FROM restaurants")->fetchAll(PDO::FETCH_ASSOC);

  // read the possible day and campus selections from url
  $today = $_GET['day'];
  $campus_selection = $_GET['campus'];

  // form a new array where campus name is key and restaurants (with their properties) are stored as sub-arrays
  foreach ($restaurants as $key => $current_restaurant) {
    $campi[$current_restaurant['campus']] = array();
  }
  foreach ($restaurants as $key => $current_restaurant) {
    $campi['all'][$current_restaurant['restaurant']] = $current_restaurant;
    $campi[$current_restaurant['campus']][$current_restaurant['restaurant']] = $current_restaurant;
  }

?>

<div class="restaurants">
<?php $counter = 1 ?>
<?php foreach ($campi[$campus_selection] as $current_restaurant) : ?>

    <?php
      // check whether the restaurant is currently open
      if (date('N') <= 4) { $dayToCheck = 'mon2thu'; }
      elseif (date('N') == 5) { $dayToCheck = 'fri'; }
      elseif (date('N') == 6) { $dayToCheck = 'sat'; }

      $currenttime = date('H').date('i');
      $openingtime = $current_restaurant['openingtime_'.$dayToCheck];
      $closingtime = $current_restaurant['closingtime_'.$dayToCheck];

      if ($currenttime >= $openingtime && $currenttime <= $closingtime) {
        $status = 'Avoinna klo '.substr_replace($closingtime,':',-2,-2).' saakka';
      }
      else {
        $status = 'Kiinni';
      }

      // show restaurant info with open hours and a map in a tooltip window
      $status_info = '&lt;dl&gt;'
                    .'&lt;dt&gt;'.$current_restaurant['name'].' ('.$current_restaurant['campus'].')&lt;/dt&gt;'
                    .'&lt;dd&gt;'.$current_restaurant['address'].'&lt;/dd&gt;'
                    .'&lt;dt&gt;Maanantaista torstaihin:&lt;/dt&gt;'
                    .'&lt;dd&gt;'.substr_replace($current_restaurant['openingtime_mon2thu'],':',-2,-2).' - '.substr_replace($current_restaurant['closingtime_mon2thu'],':',-2,-2).'&lt;/dd&gt;'
                    .'&lt;dt&gt;Perjantaisin:&lt;/dt&gt;'
                    .'&lt;dd&gt;'.substr_replace($current_restaurant['openingtime_fri'],':',-2,-2).' - '.substr_replace($current_restaurant['closingtime_fri'],':',-2,-2).'&lt;/dd&gt;'
                    .'&lt;dt&gt;Lauantaisin:&lt;/dt&gt;'
                    .'&lt;dd&gt;'.substr_replace($current_restaurant['openingtime_sat'],':',-2,-2).' - '.substr_replace($current_restaurant['closingtime_sat'],':',-2,-2).'&lt;/dd&gt;&lt;/dl&gt;';      $status_info = str_replace(': - :', 'suljettu', $status_info);
      $address = str_replace(' ', '+', $current_restaurant['address']);
      $map_img = '&lt;img src=\'http://maps.google.com/maps/api/staticmap?center='.$address.'&amp;zoom=14&amp;size=250x200&amp;maptype=roadmap&amp;markers=color:green|'.$address.'&amp;sensor=false\'&gt;';
    ?>

      <div class="restaurant" id="<?php echo $current_restaurant['restaurant'] ?>">
         <div class="restaurant_name">
           <h2>
             <a href="<?php echo $current_restaurant['link'] ?>">
               <?php echo $current_restaurant['name'] ?>
             </a>
           </h2>
           <div class="rss"><a href="/rss/<?php echo $current_restaurant['restaurant'] ?>/"><img src="/img/rss.png" title="Tilaa ruokalista RSS-syötteenä" alt="RSS" width="12" height="12" /></a></div>
         </div>
         <div class="open_hours"><?php if ($today == date('N')) { echo $status; } ?> <a href="/js" onclick="return false;" class="tooltip" rel="<?php echo $status_info ?><?php echo $map_img ?>">[?]</a></div>
           <div class="meals">
             <?php echo ($status == 'Kiinni' && $today == date('N')) ? '<div class="closed">' : '<div>' ?>
               <?php echo $all_menus[$current_restaurant['restaurant']][$today] ?>
               
                 <?php if ($today == 6 && empty($current_restaurant['openingtime_sat'])) { echo 'Suljettu lauantaisin'; } ?>
                 <?php if ($today == 7) { echo 'Suljettu sunnuntaisin'; } ?>
             </div>
		     </div>
      </div>

<?php if ($counter % 4 == 0) { echo '</div><div class="restaurants">'; } ?>
<?php $counter++ ?>
<?php endforeach; ?>



</div>