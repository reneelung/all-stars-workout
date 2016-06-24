<?php
//namespace TordAllStars;
Class User {

    function __construct($db) {
        $this->db = $db;
        return $this;
    }

    function login($username, $password) {

        $query = "SELECT * FROM `users` WHERE `users`.`user_name` = ? OR `users`.`email` = ?";
        $user = $this->db->fetchAssoc($query, array($username, $username));

        return $user['password'] == $password;
    }

    function get_all() {
        return $this->db->fetchAll('select * from `users`');
    }

    function get_user($id) {
        return $this->db->fetchAssoc('SELECT * FROM `users` WHERE `users`.`id` = ?', array($id));
    }

    function save_user($params, $id = null) {

        if ($id) {
            $this->db->update('user', $params, array('id' => $id));
        } else {
            $this->db->insert('user', $params);
        }
    }

    function delete_workout($id) {
        $this->db->delete('workouts', array('id' => $id));
    }
}