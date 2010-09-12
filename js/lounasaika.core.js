$(function() {
 // Wrap the tipTips generation inside a function
 // This function works in ajax loaded content
 var tipTip = function(selector, options) {
     var elements = jQuery(selector);
     $('.tooltip').tipTip(options);    // apply tipTips as usual
     $('body').ajaxComplete(function() {
         elements = jQuery(selector); // reselect elements --
         elements.tipTip(options);    // -- and apply again after ajax requests
     });
     return elements;
 }

 // This function gets used instead of the original one
 tipTip("a[rel]", {maxWidth: "auto", edgeOffset: 10, attribute: 'rel'});
});


// set up ajax loader for menu information
function loadrestaurants(day, campus) {
    var dayNames = new Array("Maanantain", "Tiistain", "Keskiviikon", "Torstain", "Perjantain", "Lauantain", "Sunnuntain");
    $.ajax({
      url: '/ajax/loadrestaurants.php',
      data: ({ day    : day,
               campus : campus }),
      beforeSend: function() {
        // show the ajax loader animation
        $('#all_restaurants').html('<img id="loader" src="/img/ajax-loader.gif" />');
      },
      success: function(data) {
        // put the loaded menus to their own area
        $('#all_restaurants').html(data);
        // show the day name in header (i.e. "Maanantain poikkitieteelliset ruokalistat")
        $('#day').html(dayNames[day-1]);
        // show the campus name in header (i.e. "lounasaika.net/otaniemi")
        if (campus == 'all') {
          $('#campus_url').html('');
          $('h2').find('a').attr('href', 'http://www.lounasaika.net/');
        }
        else {
          $('#campus_url').html('/'+campus.toLowerCase());
          $('h2').find('a').attr('href', 'http://www.lounasaika.net/'+campus.toLowerCase()+'/');
        }
      }
    });
}

// change day
$('#weekdays').find('a').live('click', function(event) {
    window.day = $(this).attr('id');
    loadrestaurants(window.day, window.campus);
    $('#weekdays').find('a').find('div').removeClass('active');
    $(this).find('div').addClass('active');
});

// change campus
$('#campi').find('a').live('click', function(event) {
    window.campus = $(this).attr('id');
    loadrestaurants(window.day, window.campus);
    $('#campi').find('a').find('div').removeClass('active');
    $(this).find('div').addClass('active');
    $.cookie('campus', window.campus, { expires: 365, path: "/" });
});

// show and hide color theme options
$('#choose_theme').live('click', function(event) {
    if ($('#themes').is(':hidden')) {
      $('#themes').show("slide", { direction: "left" }, 'slow');
      $('#choose_theme').addClass('active');
    }
    else {
      $('#themes').hide("slide", { direction: "left" }, 'slow');
      $('#choose_theme').removeClass('active');
    }
});

// change theme
$('.theme').live('click', function(event) {
    var selected_theme = $(this).attr('id');
    $("#themed_css").attr('href', '/css/themes/'+selected_theme+'.css');
    $('#themes').hide("slide", { direction: "left" }, 'slow');
    $('#choose_theme').removeClass('active');
    $.cookie('theme', selected_theme, { expires: 365, path: "/" });
});