<?php
namespace TordAllStars;
Class User {

    function __construct($db) {
        $this->db = $db;
        return $this;
    }

    public function login($username, $password) {

        $query = "SELECT * FROM `users` WHERE `users`.`username` = ? OR `users`.`email` = ?";
        $user = $this->db->fetchAssoc($query, array($username, $username));

        return $user['password'] == $password;
    }

    public function get_user($id) {
        return $this->db->fetchAssoc('SELECT * FROM `users` WHERE `users`.`id` = ?', array($id));
    }

    public function save_user($params, $id = null) {

        if ($id) {
            $this->db->update('user', $params, array('id' => $id));
        } else {
            $this->db->insert('user', $params);
        }
    }

    public function delete_workout($id) {
        $this->db->delete('workouts', array('id' => $id));
    }
}