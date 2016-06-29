<?php
//namespace TordAllStars;
Class Workout {

    function __construct() {
        global $app;

        $this->db = $app['db'];
        $this->app = $app;
        return $this;
    }

    // Get workouts for a single user
    function get_workouts_by_user($user_id, $order='DESC') {
        return $this->db->fetchAll('SELECT * FROM `workouts`'
          .'LEFT JOIN ( SELECT `workout_id`, COUNT(`workout_id`) AS `likes` FROM `workout_likes` GROUP BY `workout_id`) AS `like_totals` ON `like_totals`.`workout_id` = `workouts`.`id`'
          .'LEFT JOIN (SELECT `workout_id`, COUNT(`workout_id`) AS `user_comments` FROM `workout_comments` GROUP BY `workout_id`) AS `user_comments` ON `user_comments`.`workout_id` = `workouts`.`id`'
          .'WHERE `workouts`.`user_id` = ?'
          .'ORDER BY `date` '.$order, array($user_id));
    }

    // Get all workouts regardless of user
    function get_all_workouts($order_by='DESC') {
        return $this->db->fetchAll('SELECT * FROM `workouts` ORDER BY `date` '.$order_by);
    }

    // Wrapper for getting workouts regardless of whether user or group
    function get_workout_data($id=null, $order_by='DESC') {
        $workouts = $id ? $this->get_workouts_by_user($id, $order_by) : $this->get_all_workouts($order_by);
        $time_data = $this->get_workout_times($workouts);
        return array(
            'by_date' => $workouts,
            'by_time' => $time_data
        );
    }

    // Get workouts for home page stats
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


    function get_workouts_by_type($user_id = null, $type = 'undefined', $order_by='DESC') {
        $workouts = $user_id ? $this->get_workouts_by_user($user_id, $order_by) : $this->get_all_workouts($order_by);
        $types = $this->get_workout_types();
        foreach ($types as $t) {
            $times_by_type[$t] = $this->date_range($workouts);
        }
        foreach ($types as $t) {
            foreach ($workouts as $workout) {
                $times_by_type[$t][date('Y-m-d',strtotime($workout['date']))] += ($workout['type'] == $t) ? $workout['duration'] : 0;
            }
        }

        return array('dates' => array_keys($this->date_range($workouts)), 'types' => $times_by_type);
    }

    function get_workout_times($workouts) {
        $total_time = 0;
        $time_by_day = $this->date_range($workouts);
        $types = array();
        foreach ($workouts as $workout) {
            $total_time += $workout['duration'];
            if (!in_array($workout['type'], array_keys($types))) {
                $types[$workout['type']] = $workout['duration'];
            };

            $day = date('Y-m-d', strtotime($workout['date']));
            $time_by_day[$day] = $time_by_day[$day] + $workout['duration'];
        }
        $data = array(
            'total_time' => $total_time,
            'time_by_types' => $types,
            'time_by_day' => $time_by_day
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

        if($result) {
            return $id ? $this->get_workout($id) : $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    function delete_workout($id) {
        $this->db->delete('workouts', array('id' => $id));
    }

    // Calculations

    function date_range($workouts) {
        $end_date = new DateTime(date('Y-m-d', strtotime(array_pop($workouts)['date'])));
        $date_range = new DatePeriod(
            new DateTime(date('Y-m-d', strtotime($workouts[0]['date']))),
            new DateInterval('P1D'),
            $end_date->add(new DateInterval('P1D'))
        );

        $dates = array();
        foreach ($date_range as $date) {
            $dates[$date->format("Y-m-d")] = 0;
        };

        return $dates;
    }

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