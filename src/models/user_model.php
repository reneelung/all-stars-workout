<?php
//namespace TordAllStars;
Class User {

    function __construct() {
        global $app;

        $this->db = $app['db'];
        $this->app = $app;
        return $this;
    }

    function login($username, $password) {
        $query = "SELECT * FROM `users` WHERE `users`.`user_name` = ? OR `users`.`email` = ?";
        $user = $this->db->fetchAssoc($query, array($username, $username));

        if ($user && $user['password'] == $password) {
            $this->app['session']->set('user', $user);
        };

        return $user ? true : false;
    }

    function get_all() {
        return $this->db->fetchAll('select * from `users`');
    }

    function get_user($id) {
        return $this->db->fetchAssoc('SELECT * FROM `users` WHERE `users`.`id` = ?', array($id));
    }

    function save_user($params, $id = null) {

        if ($id) {
            $result = $this->db->update('users', $params, array('id' => $id));
            $this->app['session']->set('user', $this->get_user($id));
        } else {
            $result = $this->db->insert('users', $params);
        }

        return $result;
    }

    function delete_workout($id) {
        $this->db->delete('workouts', array('id' => $id));
    }
}