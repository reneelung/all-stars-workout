$(document).ready(function(){

    var chartData = {}, lineChart;

    by_type();

    $('.summary').click(function() {
        summary();
    });
    $('.by-type').click(function() {
        isolate_by_type($(this).attr('id'));
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
                chartData = data;
                lineChart = new Chart.Line($('.ct-chart'), { type: 'line', data: chartData, stacked: true});
            }
        );
    }

    function isolate_by_type(type) {
        $.each(chartData.datasets, function(i, obj){
            if (obj.label !== type) {
                obj.backgroundColor = changeChartOpacity(0.1, obj.backgroundColor);
            } else {
                obj.backgroundColor = changeChartOpacity(0.5, obj.backgroundColor);
            }
        });

        lineChart.update();
    }

    function changeChartOpacity(targetOpacity, rgbaVal) {
        var pattern = /[0-9]+,[0-9]+,[0-9]+,0?\.?[0-9]/;
        var match = pattern.exec(rgbaVal);
        matches = match[0].split(',');

        return "rgba(" + [matches[0], matches[1], matches[2]].concat() + "," + targetOpacity + ")";
    }

    function randomColorFactor() {
        return Math.round(Math.random() * 255);
    }
    function randomColor(opacity) {
        return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.3') + ')';
    }
});