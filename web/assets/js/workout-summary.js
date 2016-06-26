$(document).ready(function(){
    $.get(
        'http://localhost:8888/all-stars-workout/web/async/workouts',
        {},
        function(response) {
            var data = {
                labels: [],
                series: [[]]
            };
            $.each(response.by_date, function(index, workout) {
                data.labels.push(workout.date);
                data.series[0].push(workout.duration);
            });
            new Chartist.Line('.ct-chart', data, { height: '600px'});
        }
    );
});