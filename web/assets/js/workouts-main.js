$(document).ready(function(){
    $('.like').on('click', function(){
        app.like.like($(this), $(this).attr('workout-id'));
    });
});