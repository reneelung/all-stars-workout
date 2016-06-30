function likeInit() {
    return {
        like: function(el, workout_id) {
            $.get(
                app.vars.app_path + '/async/workouts/like',
                {
                    workout_id: workout_id,
                    user_id: app.vars.user_id
                },
                function(response) {
                    el.children('.like-count').text(response);
                    el.attr('disabled', 'disabled').addClass('disabled');
                }
            );
        }
    }
}