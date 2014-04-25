var selector = '[class^="curr-"]';

/**
 * Function taken from JQueryByExample, found at
 * http://www.jquerybyexample.net/2012/06/get-url-parameters-using-jquery.html
 */
function getURLParameter(sParam)
{
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) 
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) 
        {
            return sParameterName[1];
        }
    }
}

// Only show USD by default
function hideOtherCurrencies() {
    $(selector).not('[class$="USD"]').hide();

    // Bit of a hack. Make the net value columns and the advanced
    // settings appear if any of the advanced parameters are supplied
    var wattage = getURLParameter('w');
    var wattage_set = (typeof wattage != "undefined") && (wattage > 0);
    var perkwh = getURLParameter('perkwh');
    var perkwh_set = (typeof perkwh != "undefined") && (perkwh > 0);
    if(!wattage_set || !perkwh_set)
    {
        $('#power_costs').hide();
        $('.col_net').hide();
    }
}

$('#advanced_options').click(function() {
    $('#power_costs').slideToggle(250);
    $('.col_net').toggle();
});

// Cycle through available currencies
$('.currency_switch').click(function() {
    var spans_th = $('th ' + selector);

    var max = spans_th.length - 1;

    idx = 0;
    spans_th.each(function(index) {
        if($(this).is(':visible')) {
            idx = index + 1;
            if(idx > max) {
                idx = 0;
            }

            // First show/hide the table headers
            var next = $(spans_th).get(idx);
            $(this).hide();
            $(next).show();

            // Now show/hide the table divisions
            var spans_td = $('td ' + selector);
            $(spans_td).filter(':visible').hide();
            $(spans_td).filter('.' + $(next).attr('class')).show();

            // Return false to jump out of the loop
            return false;
        }
    });
});

$(document.body).on('click', '.dropdown-menu li', function(event) {
    var source = $(event.currentTarget);
    // New text to fill into space
    var newText = $(source).children(0).text();

    var target = $(source).parent().prev().prev();
    $(target).html(newText + ' <span class="caret"></span>');
});
