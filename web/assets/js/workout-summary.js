$(document).ready(function(){
    app.workoutCharts.set_theme();
    app.workoutCharts.by_type('user');

    $('.summary').click(function() {
        app.workoutCharts.summary('user');
    });
    $('.by-type').click(function() {
        if (app.workoutCharts.typeChart) {
            app.workoutCharts.isolate_by_type($(this).attr('id'));
        } else {
            app.workoutCharts.by_type('user');
        }
    });
});