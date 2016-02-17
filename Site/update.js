/**
 * Created by Fabian on 2016-02-17.
 */
$(document).ready(setEvents);

function setEvents() {
    update();
    setInterval(update, 1000);
}

function update() {
    var accountID = $('input[name=account_id]').val();

    $.ajax({
        type: 'post',
        url: 'testRatings.php',
        dataType: "html",
        data: { accountID: accountID }})
        .done(function(result) {
            var newMatchID = $(result).find('input[name=match_id]');

            if(!newMatchID.exists() || $('input[name=match_id]').val() != newMatchID.val()) {
                $('#ratings').html(result);
            }
        });

};

// http://stackoverflow.com/questions/920236/how-can-i-detect-if-a-selector-returns-null
$.fn.exists = function () {
    return this.length !== 0;
}