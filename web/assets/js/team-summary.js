$(document).ready(function(){
    app.workoutCharts.by_type('team');

    $('.summary').click(function() {
        app.workoutCharts.summary('team');
    });
    $('.by-type').click(function() {
        if (app.workoutCharts.typeChart) {
            app.workoutCharts.isolate_by_type($(this).attr('id'));
        } else {
            app.workoutCharts.by_type('team');
        }
    });
});