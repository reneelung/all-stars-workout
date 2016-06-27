$(document).ready(function(){

    summary();
    $('.summary').click(function() {
        summary();
    });
    $('.by-type').click(function() {
        by_type($(this).attr('id'));
    });

    function summary() {
        $.get(
            '/async/team/workouts',
            {},
            function(response) {
                var data = {
                    labels: [],
                    datasets: [{
                        label: "Workout Times",
                        data:[],
                        backgroundColor: randomColor(),
                        borderColor: randomColor()
                    }]
                };
                $.each(response.by_date, function(index, workout) {
                    data.labels.push(workout.date);
                    data.datasets[0].data.push(workout.duration);
                });

                lineChart = new Chart.Line($('.ct-chart'), { type: 'line', data: data});
            }
        );
    }

    function by_type(type) {
        $.get(
            '/async/team/workouts/type/' + type,
            {},
            function(response) {
                var data = {
                    labels : response.dates,
                    datasets : []
                };

                $.each(response.types, function(name, vals) {
                    data.datasets.push({
                        label: name,
                        data: vals,
                        backgroundColor: randomColor(),
                        borderColor: randomColor(),
                        lineTension: 0
                    });
                });

                lineChart = new Chart.Bar($('.ct-chart'), { type: 'line', data: data});
            }
        );
    }

    function randomColorFactor() {
        return Math.round(Math.random() * 255);
    }
    function randomColor(opacity) {
        return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.3') + ')';
    }
});