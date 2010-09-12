<?php
include_once('php/simple_html_dom.php');
include_once('php/explodex.php');

function get_amica_menu($restaurant)
{
    // Create HTML DOM
    $html = file_get_html('http://www.amica.fi/'.$restaurant);
    
    if (!empty($html)) {
    // Get lunch menu block
    $menu = $html->find('#Ruokalista', 0)->find('.ContentArea', 1)->innertext;
    $menu = strip_tags($menu, '<br>');
    $menu = str_replace('&nbsp;', '', $menu);
    $menu = nl2br($menu);
    $menu = preg_replace('/(<br[^>]*>\s*){2,}/', '<br/>', $menu);


    // Return the menu exploded as an array
    $menu_array = explodeX(Array('Maanantai', 'Tiistai', 'Keskiviikko', 'Torstai', 'Perjantai'), $menu);
    if (count($menu_array) < 5) {
        $menu_array = explodeX(Array('maanantai', 'tiistai', 'keskiviikko', 'torstai', 'perjantai'), $menu);
    }
    if (count($menu_array) < 5) {
        $menu_array = explodeX(Array('MA', 'TI', 'KE', 'TO', 'PE'), $menu);
    }
    
    $menu_array[0] = $restaurant;
    $friday_array_temp = explode('VL =', $menu_array[5]);
    $menu_array[5] = $friday_array_temp[0];
    }
    else { $menu_array = null; }    

    // Clean up memory
    $html->clear();
    unset($html);

    return $menu_array;
}

function get_sodexo_rss_menu($restaurant)
{
    if ($restaurant == 'teekkariravintolat') {
        $url = 'http://www.sodexo.fi/_channels/?ChannelId=66737b2c-9f6d-4c3c-afd6-5546e8d9cd73&format=rss2';
    }
    if ($restaurant == 'smokki') {
        $url = 'http://www.sodexo.fi/_channels/?ChannelId=73846440-f3f7-4f57-84ee-b41ad2621221&format=rss2';
    }

    // Create HTML DOM
    $html = file_get_html($url);
    
    // Get lunch menu block
    if (strlen($html) < 150) {
        return false;
    }

    $menu = $html->find('item', 0)->find('description', 0)->innertext;
    $menu = str_replace('&lt;', '<', $menu);
    $menu = str_replace('&gt;', '>', $menu);
    $menu = strip_tags($menu, '<br/>');
    $menu = utf8_encode($menu);
    $menu_array = explodeX(Array('Maanantai', 'Tiistai', 'Keskiviikko', 'Torstai', 'Perjantai', 'Lauantai'), $menu);
    $menu_array[0] = $restaurant;
    
    // Clean up memory
    $html->clear();
    unset($html);

    return $menu_array;
}

function get_taffa_menu()
{
    // Create HTML DOM
    $html = file_get_html('http://joomla.teknolog.fi/weekmenu.php?lang=fi');
    
    // Get lunch menu block
    $menu = $html->innertext;
    $menu = strip_tags($menu, '<br>');
    $menu = str_replace('<br>', '<br/>', $menu);
    $menu = str_replace('Aukioloajat', '', $menu);
    $menu_array_temp = explodeX(Array('Maanantai', 'Tiistai', 'Keskiviikko', 'Torstai', 'Perjantai'), $menu);
    foreach ($menu_array_temp as $menu_day) {
        $menu_array[] = substr($menu_day, 6);
    }
    $menu_array[0] = 'taffa';
    $menu_array[6] = '';
    $menu_array[7] = '';

    
    // Clean up memory
    $html->clear();
    unset($html);

    return $menu_array;
}

function get_unicafe_rss_menu($restaurant)
{
    switch ($restaurant) {
        case 'metsatalo':
          $url = 'http://www.unicafe.fi/rss/fin/1/';
          break;
        case 'olivia':
          $url = 'http://www.unicafe.fi/rss/fin/2/';
          break;
        case 'porthania':
          $url = 'http://www.unicafe.fi/rss/fin/3/';
          break;
        case 'paarakennus':
          $url = 'http://www.unicafe.fi/rss/fin/4/';
          break;
        case 'rotunda':
          $url = 'http://www.unicafe.fi/rss/fin/5/';
          break;
        case 'sockom':
          $url = 'http://www.unicafe.fi/rss/fin/15/';
          break;
        case 'topelias':
          $url = 'http://www.unicafe.fi/rss/fin/6/';
          break;
        case 'valtiotiede':
          $url = 'http://www.unicafe.fi/rss/fin/7/';
          break;
        case 'ylioppilasaukio':
          $url = 'http://www.unicafe.fi/rss/fin/8/';
          break;
    }

    // Create HTML DOM
    $html = file_get_html($url);
    
    // Get lunch menu block
    if (strlen($html) < 150) {
        return false;
    }

    $menu_items = $html->find('item');
    
    foreach ($menu_items as $menu_item) {
      $menu_item = $menu_item->find('description', 0)->innertext;
      $menu_item = str_replace('</p>', '<br/>', $menu_item);
      $menu_item = strip_tags($menu_item, '<br/>');
      $menu_item = str_replace(Array(', Edullisesti', ', Maukkaasti', ', Makeasti', ', Kevyesti', ']]>', ' 1,20€', ' 1,2€'), '', $menu_item);
      $menu_array[] = $menu_item;
    }
    
    if (isset($menu_array)) {
      array_unshift($menu_array, $restaurant);
    }
    
    // Clean up memory
    $html->clear();
    unset($html);

    return $menu_array;
}

function get_kipsari_menu()
{
    // Create HTML DOM
    $html = file_get_html('http://www.kipsari.com/menu.php');
    
    // Get lunch menu block
    $menu = $html->innertext;
    $menu = strip_tags($menu);
    $menu_array = explodeX(Array('Ma,Mon', 'Ti,Tue', 'Ke,Wed', 'To,Tho', 'Pe,Fri'), $menu);
    
    $friday_array_temp = explode('Lounasvaihtoehdot', $menu_array[5]);
    $menu_array[5] = $friday_array_temp[0];
    $menu_array[0] = 'kipsari';
    $menu_array[6] = '';
    $menu_array[7] = '';
    
    // Clean up memory
    $html->clear();
    unset($html);
    
    return $menu_array;
}

function get_manala_menu()
{
    // Create HTML DOM
    $html = file_get_html('http://www.botta.fi/LOUNAS.108.0.html');
    
    // Get lunch menu block
    $menu = $html->plaintext;
    $menu = nl2br($menu);
    $menu = preg_replace('/(<br[^>]*>\s*){2,}/', '<br/>', $menu);
    $menu_array_temp = explodeX(Array('Maanantaina', 'Tiistaina', 'Keskiviikkona', 'Torstaina', 'Perjantaina'), $menu);

    foreach ($menu_array_temp as $day_menu) {
        $day_menu = substr($day_menu, 6);
        $day_menu = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $day_menu);
        $day_menu = explode('Suosittelemme:', $day_menu);
        $menu_array[] = $day_menu[0];
    }
    
    $menu_array[0] = 'manala';
    
    // Clean up memory
    $html->clear();
    unset($html);
    
    return $menu_array;
}


$teekkariravintolat = get_sodexo_rss_menu('teekkariravintolat');
$smokki = get_sodexo_rss_menu('smokki');
$taffa = get_taffa_menu();
$alvari = get_amica_menu('alvari');
$tuas = get_amica_menu('TUAS');
$puu = get_amica_menu('puu2');
$kvarkki = get_amica_menu('kvarkki');
$rafla = get_amica_menu('rafla');
$chydenia = get_amica_menu('chydenia');
$hanken = get_amica_menu('hanken');
$kipsari = get_kipsari_menu();
$metsatalo = get_unicafe_rss_menu('metsatalo');
$olivia = get_unicafe_rss_menu('olivia');
$porthania = get_unicafe_rss_menu('porthania');
$paarakennus = get_unicafe_rss_menu('paarakennus');
$rotunda = get_unicafe_rss_menu('rotunda');
$sockom = get_unicafe_rss_menu('sockom');
$topelias = get_unicafe_rss_menu('topelias');
$valtiotiede = get_unicafe_rss_menu('valtiotiede');
$ylioppilasaukio = get_unicafe_rss_menu('ylioppilasaukio');
$hamis = get_unicafe_rss_menu('ylioppilasaukio');
$manala = get_manala_menu();


try {
  $db = new PDO('sqlite:db/lounasaika.db');
}
catch (PDOException $e) {
  echo $e->getMessage();
}

$db->exec("PRAGMA foreign_keys = ON;");

$db->exec("DROP TABLE menus;");
$db->exec("DROP TABLE restaurants;");

$db->exec("CREATE TABLE restaurants (restaurant varchar(20) primary key,
                                     name varchar(50),
                                     link varchar(200),
                                     address varchar(50),
                                     campus varchar(15),
                                     school varchar(10),
                                     votes integer(4),
                                     openingtime_mon2thu integer(4),
                                     closingtime_mon2thu integer(4),
                                     openingtime_fri integer(4),
                                     closingtime_fri integer(4),
                                     openingtime_sat integer(4),
                                     closingtime_sat integer(4)
                                    );");

$db->exec("INSERT INTO restaurants (restaurant, name, votes, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri, openingtime_sat, closingtime_sat) 
           VALUES ('teekkariravintolat', 'Teekkariravintolat', 7, 'Otakaari 24, Espoo', 'Otaniemi', 'TKK', 'http://www.sodexo.fi/fi-FI/dipoli/lounas/', 
                    1030, 1600,
                    1030, 1500,
                    1130, 1500);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, votes, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri) 
           VALUES ('TUAS', 'TUAS-talo', 5, 'Otaniementie 17, Espoo', 'Otaniemi', 'TKK', 'http://www.amica.fi/TUAS#Ruokalista',
           1030, 1530,
           1030, 1430);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, votes, address, campus, school, link) VALUES ('smokki', 'Smökki', 0, 'Jamerantaival 4, Espoo' 'Otaniemi', 'TKK', 'http://www.sodexo.fi/fi-FI/servinmokki/lounas/');");
$db->exec("INSERT INTO restaurants (restaurant, name, votes, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri) 
           VALUES ('alvari', 'Alvari', 9, 'Otakaari 2, Espoo', 'Otaniemi', 'TKK', 'http://www.amica.fi/alvari#Ruokalista',
           1030, 1700,
           1030, 1500);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, votes, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri) 
           VALUES ('puu2', 'Puu', 2, 'Tekniikantie 3, Espoo', 'Otaniemi', 'TKK', 'http://www.amica.fi/Puu2#Ruokalista',
           1030, 1300,
           1030, 1300);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri, openingtime_sat, closingtime_sat) 
           VALUES ('smokki', 'Smökki', 'Jämeräntaival 6, Espoo', 'Otaniemi', 'TKK', 'http://www.sodexo.fi/fi-FI/servinmokki/lounas/',
           1030, 1500,
           1030, 1430,
           1130, 1530);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, votes, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri) 
           VALUES ('taffa', 'Täffä', 11, 'Otakaari 22, Espoo', 'Otaniemi', 'TKK', 'http://www.teknologforeningen.fi/index.php/fi/taman-viikon-ruokalista',
           1030, 1600, 
           1030, 1500);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri) 
           VALUES ('kvarkki', 'Kvarkki', 'Otakaari 3, Espoo', 'Otaniemi', 'TKK', 'http://www.amica.fi/kvarkki#Ruokalista',
           1030, 1400, 
           1030, 1300);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, votes, address, campus, school, link) VALUES ('cantina', 'Cantina', 0, 'Otakaari 24, Espoo', 'Otaniemi', 'TKK', 'http://www.ravintolacantina.com/lounas/');");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri)
           VALUES ('rafla', 'Rafla', 'Runeberginkatu 14, Helsinki', 'Töölö', 'HSE', 'http://www.amica.fi/rafla#Ruokalista',
           1030, 1500,
           1030, 1500);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri)
           VALUES ('chydenia', 'Chydenia', 'Runeberginkatu 22, Helsinki', 'Töölö', 'HSE', 'http://www.amica.fi/chydenia#Ruokalista',
           1030, 1430,
           1030, 1400);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri)
           VALUES ('hanken', 'Hanken', 'Arkadiankatu 22, Helsinki', 'Töölö', 'Hanken', 'http://www.amica.fi/hanken#Ruokalista',
           1100, 1500,
           1100, 1500);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri) 
           VALUES ('kipsari', 'Kipsari', 'Hämeentie 135, Helsinki', 'Arabia', 'TaiK', 'http://www.kipsari.com/menu.php',
           0800, 1900, 
           0800, 1900);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri)
           VALUES ('metsatalo', 'UniCafe Metsätalo', 'Fabianinkatu 39, Helsinki', 'Keskusta', 'HY', 'http://www.unicafe.fi/index.php?ravintola=8#/Keskusta/Metsätalo/1/1',
           1030, 1600, 
           1030, 1500);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri) 
           VALUES ('olivia', 'UniCafe Olivia', 'Siltavuorenpenger 5, Helsinki', 'Keskusta', 'HY', 'http://www.unicafe.fi/index.php?ravintola=8#/Keskusta/Olivia/1/2',
           1030, 1600, 
           1030, 1600);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri) 
           VALUES ('porthania', 'UniCafe Porthania', 'Yliopistonkatu 3, Helsinki', 'Keskusta', 'HY', 'http://www.unicafe.fi/index.php?ravintola=8#/Keskusta/Porthania/1/3',
           1030, 1630, 
           1030, 1630);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri) 
           VALUES ('paarakennus', 'UniCafe Päärakennus', 'Fabianinkatu 33, Helsinki', 'Keskusta', 'HY', 'http://www.unicafe.fi/index.php?ravintola=8#/Keskusta/Päärakennus/1/4',
           1030, 1630, 
           1030, 1630);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri) 
           VALUES ('rotunda', 'UniCafe Rotunda', 'Unioninkatu 36, Helsinki', 'Keskusta', 'HY', 'http://www.unicafe.fi/index.php?ravintola=8#/Keskusta/Rotunda/1/5',
           1100, 1400, 
           1100, 1400);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri) 
           VALUES ('sockom', 'UniCafe Soc&amp;Kom', 'Yrjö-Koskisen katu 3, Helsinki', 'Keskusta', 'HY', 'http://www.unicafe.fi/index.php?ravintola=8#/Keskusta/Soc&amp;Kom/1/15',
           1100, 1430, 
           1100, 1430);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri) 
           VALUES ('topelias', 'UniCafe Topelias', 'Unioninkatu 38, Helsinki', 'Keskusta', 'HY', 'http://www.unicafe.fi/index.php?ravintola=8#/Keskusta/Topelias/1/6',
           1100, 1430, 
           1100, 1400);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri) 
           VALUES ('valtiotiede', 'UniCafe Valtiotiede', 'Unioninkatu 37, Helsinki', 'Keskusta', 'HY', 'http://www.unicafe.fi/index.php?ravintola=8#/Keskusta/Valtiotiede/1/7',
           1100, 1400, 
           1100, 1400);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri, openingtime_sat, closingtime_sat) 
           VALUES ('ylioppilasaukio', 'UniCafe Ylioppilasaukio', 'Mannerheimintie 3, Helsinki', 'Keskusta', 'HY', 'http://www.unicafe.fi/index.php?ravintola=8#/Keskusta/Ylioppilasaukio/1/8',
           1100, 1900, 
           1100, 1900,
           1100, 1800);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri) 
           VALUES ('hamis', 'HYS:n Osakuntabaari', 'Urho Kekkosen katu 4-6 D, Helsinki', 'Töölö', '', 'http://www.hys.net/opiskelijapalvelut/osakuntabaari/ruokalista',
           1100, 1530, 
           1100, 1500);
         ");
$db->exec("INSERT INTO restaurants (restaurant, name, address, campus, school, link, openingtime_mon2thu, closingtime_mon2thu, openingtime_fri, closingtime_fri, openingtime_sat, closingtime_sat) 
           VALUES ('manala', 'Ostrobotnian Manala', 'Dagmarinkatu 2, Helsinki', 'Töölö', '', 'http://www.botta.fi/LOUNAS.108.0.html',
           1100, 1400, 
           1100, 1400,
           1400, 1630);
         ");

$result = $db->query("SELECT * FROM restaurants")->fetchAll(PDO::FETCH_ASSOC);

echo '<pre>';
print_r($result);
echo '</pre>';


$db->exec("CREATE TABLE menus (restaurant varchar(20) primary key,
                               '1' text,
                               '2' text,
                               '3' text,
                               '4' text,
                               '5' text,
                               '6' text,
                               '7' text,
                               FOREIGN KEY(restaurant) REFERENCES restaurants(restaurant)
                              );");

$all_menus = array($teekkariravintolat,
                   $smokki,
                   $taffa,
                   $alvari,
                   $tuas,
                   $puu,
                   $smokki,
                   $kvarkki,
                   $rafla,
                   $chydenia,
                   $hanken,
                   $kipsari,
                   $metsatalo,
                   $olivia,
                   $porthania,
                   $paarakennus,
                   $rotunda,
                   $sockom,
                   $topelias,
                   $valtiotiede,
                   $ylioppilasaukio,
                   $hamis,
                   $manala
                  );

foreach($all_menus as $key => $current_menu) {
    $db->exec("INSERT INTO menus VALUES ('$current_menu[0]', '$current_menu[1]',
                                                             '$current_menu[2]',
                                                             '$current_menu[3]',
                                                             '$current_menu[4]',
                                                             '$current_menu[5]',
                                                             '$current_menu[6]',
                                                             '$current_menu[7]'
                                        );");
}

$result = $db->query("SELECT * FROM menus")->fetchAll(PDO::FETCH_ASSOC);

echo '<pre>';
print_r($result);
echo '</pre>';


?>