<?php
//namespace TordAllStars;
Class Workout {

    function __construct() {
        global $app;

        $this->db = $app['db'];
        $this->app = $app;
        return $this;
    }

    function get_workouts_by_user($user_id) {
        return $this->db->fetchAll('SELECT * FROM `workouts` WHERE `user_id` = ? ORDER BY `date` DESC', array($user_id));
    }

    function get_all_workouts() {
        return $this->db->fetchAll('SELECT * FROM `workouts` ORDER BY `date` DESC');
    }

    function get_workout_data($id=null) {
        $workouts = $id ? $this->get_workouts_by_user($id) : $this->get_all_workouts();
        $time_data = $this->get_workout_times($workouts);
        return array(
            'by_date' => $workouts,
            'by_time' => $time_data
        );
    }

    function home_data($user_id, $is_team) {
        $workouts = $is_team ? $this->get_all_workouts() : $this->get_workouts_by_user($user_id);
        $data['workouts_logged'] = count($workouts);
        $data['longest_workout'] = $this->longest_workout($workouts);
        $data['longest_streak'] = $this->longest_streak($workouts);
        $data['new_workouts'] = $this->new_workouts($user_id);
        $data['popular_type'] = $this->popular_type();
        $data['peak_day'] = $this->peak_day();
        return $data;
    }

    function get_workout_types() {
        $result = $this->db->fetchAll('SELECT DISTINCT `type` from `workouts`');
        foreach ($result as $row) {
           $types[] = $row['type'];
        }
        return $types;
    }

    function get_workouts_by_type($id, $type = 'undefined') {
        $workouts = $this->get_workouts_by_user($id);
        $types = $this->get_workout_types();
        foreach ($types as $t) {
            $times_by_type[$t] = array();
        }
        foreach($workouts as $workout) {
            $dates[] = $workout['date'];
            foreach ($types as $t) {
                $times_by_type[$t][] = $workout['type'] == $t ? $workout['duration'] : 0;
            }
        }

        if ($type != 'undefined') {
            $times_by_type = array(
                $type => $times_by_type[$type]
            );
        }
        return array('dates' => $dates, 'types' => $times_by_type);
    }

    function get_workout_times($workouts) {
        $total_time = 0;
        $types = array();
        foreach ($workouts as $workout) {
            $total_time += $workout['duration'];
            if (!in_array($workout['type'], array_keys($types))) {
                $types[$workout['type']] = $workout['duration'];
            };
        }
        $data = array(
            'total_time' => $total_time,
            'time_by_types' => $types
        );
        return $data;
    }

    function get_workout($id) {
        return $this->db->fetchAssoc('SELECT * FROM `workouts` WHERE `workouts`.`id` = ?', array($id));
    }

    function save_workout($params, $id = null) {

        if ($id) {
            $result = $this->db->update('workouts', $params, array('id' => $id));
        } else {
            $result = $this->db->insert('workouts', $params);
        }

        return $result;
    }

    function delete_workout($id) {
        $this->db->delete('workouts', array('id' => $id));
    }

    // Calculations

    function longest_workout($workouts) {
        $longest['duration'] = 0;

        foreach ($workouts as $workout) {
            if ($workout['duration'] > $longest['duration']) {
                $longest = $workout;
            }
        }

        return $longest;
    }

    function longest_streak($workouts) {
        $streaks = array();
        $dates = array();
        $last_date = null;
        $current_range = array();
        $test = array();
        // process dates into datetime objects
        foreach ($workouts as $workout) {
            $dates[] = new DateTime(date('Y-m-d', strtotime($workout['date'])));
        }

        foreach ($dates as $date) {
            // if the difference between last date and current date is =< 1, belongs in range
            if (!$last_date) {
                $current_range[] = $date;
            } else {
                $interval = $date->diff($last_date);

                if ($interval->days == 1) {
                    $current_range[] = $date;
                } else {
                    $streaks[] = count($current_range);
                    $current_range = array($date);
                }
            }
            $last_date = $date;
        }

        return max($streaks);
    }

    function new_workouts($user_id) {
        $user_model = new User();
        $user = $user_model->get_user($user_id);
        $result = $this->db->fetchAll('SELECT * FROM `workouts` WHERE `workouts`.`date` > ?', array($user['last_login']));
        return count($result);
    }

    function popular_type() {
        $query = "SELECT `type` FROM `workouts` GROUP BY `type` ORDER BY COUNT(`type`) DESC";
        $result = $this->db->fetchAssoc($query);
        return $result['type'];
    }

    function peak_day() {
        $query = "SELECT DATE_FORMAT(`date`, '%b %e, %Y') as `date`, COUNT(DATE_FORMAT(`date`, '%Y-%m-%d')) as `frequency` FROM `workouts` GROUP BY DATE_FORMAT(`date`, '%Y-%m-%d') ORDER BY COUNT(DATE_FORMAT(`date`, '%Y-%m-%d')) DESC";
        $result = $this->db->fetchAssoc($query);
        return $result;
    }
}