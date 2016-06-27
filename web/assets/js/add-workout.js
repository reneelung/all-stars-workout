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

    $('#datetimepicker-1').datetimepicker({
        format: 'LL'
    });

    $('#datetimepicker-2').datetimepicker({
        format: 'LT',
        stepping: 30
    });
});