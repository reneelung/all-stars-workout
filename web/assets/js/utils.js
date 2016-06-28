function utilsInit() {
    return {
        randomColor: function(opacity) {
            return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.3') + ')';

            function randomColorFactor() {
                return Math.round(Math.random() * 255);
            }
        }
    }
}