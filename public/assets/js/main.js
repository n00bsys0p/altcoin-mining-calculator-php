selector = '[class^="curr-"]';

// Only show USD by default
function hideOtherCurrencies() {
    $(selector).not('[class$="USD"]').hide();
}

// Cycle through available currencies
$('#switch').click(function() {
    spans_th = $('th ' + selector);

    max = spans_th.length - 1;

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
            spans_td = $('td ' + selector);
            $(spans_td).filter(':visible').hide();
            $(spans_td).filter('.' + $(next).attr('class')).show();

            // Return false to jump out of the loop
            return false;
        }
    });
});

