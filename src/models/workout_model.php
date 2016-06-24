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
        return $this->db->fetchAll('SELECT * FROM `workouts` WHERE `user_id` = ?', array($user_id));
    }

    function get_workout($id) {
        return $this->db->fetchAssoc('SELECT * FROM `workouts` WHERE `workouts`.`id` = ?', array($id));
    }

    function get_workout_types() {
        return $this->db->fetchAll('SELECT DISTINCT `type` from `workouts`');
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
}