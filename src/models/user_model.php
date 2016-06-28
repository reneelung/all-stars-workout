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
        if ($user && $this->valid_password($password, $user)) {
            $user = $this->get_user($user['id']);
            $this->app['session']->set('user', $user);
            $this->db->update('users', array('last_login' => date('Y-m-d H:i:s')), array('id' => $user['id']));
            return true;
        };
        return false;
    }

    function get_all() {

        return $this->db->fetchAll('select * from `users`');
    }

    function get_user($id) {
        $user = $this->db->fetchAssoc('SELECT * FROM `users` WHERE `users`.`id` = ?', array($id));
        $user['sharing_is_on'] = $user['private'] ? false : true;
        if (isset($user['derby_name'])) {
            $user['name'] = $user['derby_name'];
        }
        return $user;
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

    function obfuscate_password($pass, $id) {
        $secret = SECRET_SALT;
        $token = $this->generate_token();
        $token_updated = $this->db->update('users', array('token' => $token), array('id' => $id));
        if ($token_updated) {
            return sha1($pass.$secret.$token);
        }
        return false;
    }

    function valid_password($pass, $user) {
        return sha1($pass.SECRET_SALT.$user['token']);
        return $user['password'] == sha1($pass.SECRET_SALT.$user['token']);
    }

    function generate_token($length = 16) {
        $chars = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 1));
        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= $chars[rand(0, count($chars) - 1)];
        }
        return $token;
    }

    function sharing_is_on($user) {
        return !$user['private'];
    }

    function refresh_session_user_data() {
        $current_user = $this->app['session']->get('user');
        $refreshed = $this->get_user($current_user['id']);
        $this->app['session']->set('user', $refreshed);
        $this->app['session']->set('theme', $refreshed['theme']);
    }
}