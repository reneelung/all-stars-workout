function workoutChartsInit() {
    return {
        typeChartData: {},
        summaryChartData: {},
        defaultOpacity: 0.7,
        summary: function(groupType) {
            var base = this;
            $.get(
                app.vars.app_path + '/async/' + groupType + '/workouts',
                {},
                function(response) {
                    var data = {
                        labels: [],
                        datasets: [{
                            label: "Workout Times",
                            data:[],
                            backgroundColor: app.utils.randomColor(base.defaultOpacity),
                            borderColor: app.utils.randomColor(base.defaultOpacity),
                            lineTension: 0
                        }]
                    };
                    $.each(response.by_time.time_by_day, function(date, minutes) {
                        data.labels.push(date);
                        data.datasets[0].data.push(minutes);
                    });
                    base.summaryChartData = data;
                    base.summaryChart = new Chart.Line($('.ct-chart'), { type: 'line', data: data });
                }
            );
        },
        by_type: function(groupType, type) {
            var base = this;
            $.get(
                app.vars.app_path + '/async/' + groupType + '/workouts/type/' + type,
                {},
                function(response) {
                    var data = {
                        labels : response.dates,
                        datasets : []
                    };

                    $.each(response.types, function(name, vals) {
                        var totals = [];
                        $.each(vals, function(date, mins) {
                            totals.push(mins);
                        });
                        data.datasets.push({
                            label: name,
                            data: totals,
                            backgroundColor: app.utils.randomColor(base.defaultOpacity),
                            borderColor: app.utils.randomColor(base.defaultOpacity),
                            lineTension: 0
                        });
                    });
                    base.typeChartData = data;
                    base.typeChart = new Chart($('.ct-chart'), { type: 'bar', data: base.typeChartData, options: {
                        scales: {
                            xAxes: [{
                                stacked: true
                            }]
                        }
                    }});
                }
            );
        },
        isolate_by_type: function(type) {
            var base = this;
            $.each(base.typeChartData.datasets, function(i, obj){
                if (obj.label !== type && typeof type != 'undefined' && type != null) {
                    obj.backgroundColor = changeChartOpacity(0.3*base.defaultOpacity, obj.backgroundColor);
                    obj.borderDash = [5,5];
                } else {
                    obj.backgroundColor = changeChartOpacity(base.defaultOpacity, obj.backgroundColor);
                    obj.borderDash = [0,0];
                }
            });

            base.typeChart.update();

            function changeChartOpacity(targetOpacity, rgbaVal) {
                var pattern = /[0-9]+,[0-9]+,[0-9]+,0?\.?[0-9]/;
                var match = pattern.exec(rgbaVal);
                matches = match[0].split(',');

                return "rgba(" + [matches[0], matches[1], matches[2]].concat() + "," + targetOpacity + ")";
            }
        },
        set_theme: function() {
            var base = this;
            if (app.vars.theme == 'dark') {
                Chart.defaults.scale.gridLines.color = "rgba(255,255,255,0.8)";
                Chart.defaults.scale.scaleLabel.fontColor = "#FFFFFF";
                Chart.defaults.scale.ticks.fontColor = "#FFFFFF";
                base.defaultOpacity = 0.8;
            }
        }
    }
}