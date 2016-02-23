/**
 * Created by Fabian on 2016-02-22.
 */

var Form = function() {
    if (this instanceof Form) {}
    else {
        return new Form();
    }
}

Form.prototype.setBindings = function() {
    var ratingForm = $('form[name=rating-form]');
    ratingForm.on("submit", function(event){
        event.preventDefault();
        $submittedForm = $(this);
        $.post($submittedForm.attr('action'), ratingForm.serialize());
    });
    var radioButtons = ratingForm.find('input[type=radio]');
    radioButtons.each(function() {
        $(this).change(function(){
           if(this.checked) {
               ratingForm.submit();
           }
        });
    });
}
