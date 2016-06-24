<?php
namespace TordAllStars;
Class Workout {

    protected $name;
    protected $date;
    protected $type;

    function __construct($db) {
        $this->db = $db;
        $this->date = date(time());
        $this->type = 'default';

        return $this;
    }

    public function get_workout($id) {
        return $this->db->fetchAssoc('SELECT * FROM `workouts` WHERE `workouts`.`id` = ?', array($id));
    }

    public function save_workout($params, $id = null) {
        $defaults = array(
            'name' => 'Workout '. $this->new_id(),
            'date' => $this->date,
            'type' => $this->type,
            'duration' => 0,
            'reps' => 0,
        );

        $params = array_merge($defaults, $params);

        if ($id) {
            $this->db->update('workouts', $params, array('id' => $id));
        } else {
            $this->db->insert('workouts', $params);
        }
    }

    public function delete_workout($id) {
        $this->db->delete('workouts', array('id' => $id));
    }
}