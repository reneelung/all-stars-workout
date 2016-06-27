$(document).ready(function() {
    // With JQuery
    $(".slider-duration").on("change", function(slideEvt) {
        $("#slider-duration-cur-val").text($(this).val());
    });

    $('#type_select').on('change', function() {
        if ($(this).val() == "add_new") {
            $('#type_text').removeClass('hidden');
        }
        else {
            $('#type_text').addClass('hidden');
        }
    });
});