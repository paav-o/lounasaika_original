<?php

setlocale(LC_TIME, "fi_FI.UTF-8");
date_default_timezone_set('Europe/Helsinki');
error_reporting(0);

  try {
    $db = new PDO('sqlite:db/lounasaika.db');
  }
  catch (PDOException $e) {
    echo $e->getMessage();
  }

  $selected_restaurant = $_GET['restaurant'];
  $selected_restaurant_menu = $db->query("SELECT * FROM menus WHERE restaurant='$selected_restaurant'")->fetchAll(PDO::FETCH_ASSOC);
  $selected_restaurant_info = $db->query("SELECT * FROM restaurants WHERE restaurant='$selected_restaurant'")->fetchAll(PDO::FETCH_ASSOC);

// Create a new DOM Document object, this will create a new XML declaration for us.
// We can enter the version number and encoding (in that order), by passing
// two arguments to the object's constructor.  By default, the XML
// declaration's version attribute will be set to "1.0"
$pDom = new DOMDocument('1.0', 'UTF-8');
        
// Here we create a new root elelement named rss.  
$pRSS = $pDom->createElement('rss');

// We now add a new attribute to the rss element.  We name this new 
// attribute version and give it a value of 0.91.
$pRSS->setAttribute('version', '2.0');
$pRSS->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');

// Finally we append the attribute to the XML tree using appendChild
$pDom->appendChild($pRSS);

// We repeat the same process again here, but this time we're creating
// the channel element.
$pChannel = $pDom->createElement('channel');

$pRSS->appendChild($pChannel);

// Create the main child nodes of channel, these contain the information
// related to this RSS file.  I'm not going to comment each one of these
// as they should be easy enough to understand.  Basically we're creating
// a new element for each node, the first argument specifies the name of
// the element we're creating, and the second specifies the text value
// of the node, for example, title would render as:  <title>TalkPHP</title>
$pTitle = $pDom->createElement('title', $selected_restaurant_info[0]['name'].' ('.$selected_restaurant_info[0]['campus'].')');
$pLink  = $pDom->createElement('link', $selected_restaurant_info[0]['link']);
$pDesc  = $pDom->createElement('description', 'Viikon '.date('W').' ruokalista');
$pLang  = $pDom->createElement('language', 'fi');

// Here we simply append all the nodes we just created to the channel node
$pChannel->appendChild($pTitle);
$pChannel->appendChild($pLink);
$pChannel->appendChild($pDesc);
$pChannel->appendChild($pLang);

// Loop trough each result from our imaginary database, these are the
// RSS items that the viewer of the RSS will see

foreach ($selected_restaurant_menu[0] as $day => $menu_of_the_day)
{
    if ($day == 'restaurant') { continue; }
    // Nothing new here, we're creating an item element as our parent
    // and then creating and adding three child nodes to it.
    $pItem  = $pDom->createElement('item');
    $pTitle = $pDom->createElement('title', strftime('%A %d.%m.%Y', strtotime(date('Y').'W'.date('W').$day)));
    $pDesc  = $pDom->createElement('description', htmlspecialchars($menu_of_the_day, ENT_NOQUOTES, 'UTF-8'));
    $pGuid  = $pDom->createElement('guid', $selected_restaurant_info[0]['name'].strftime('%Y-%m-%d', strtotime(date('Y').'W'.date('W').$day)));
    
    $pGuid->setAttribute('isPermaLink', 'false');
    
    // Append the nodes to the item, then append the item to the channel
    $pItem->appendChild($pTitle);
    $pItem->appendChild($pDesc);
    $pItem->appendChild($pGuid);

    $pChannel->appendChild($pItem);
}

// Set content type to XML, thus forcing the browser to render is as XML
header('Content-type: application/xml');

// Here we simply dump the XML tree to a string and output it to the browser
// We could use one of the other save methods to save the tree as a HTML string
// XML file or HTML file.
echo $pDom->saveXML();
?> 