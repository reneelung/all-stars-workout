var app = init();

$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});

function init() {
    return {
        utils: utilsInit(),
        workoutCharts: workoutChartsInit(),
        like: likeInit()
    };
}