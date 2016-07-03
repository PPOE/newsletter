<?php

require_once 'functions.inc.php';

class db {
    private $dbConn_ = null;
    public $dbType_ = null;

    public function __construct($dbType, $name, $host = null) {
        // todo: implement
    }

    public function query($query) {
        if (strpos(strtolower($query), 'select * from users') === 0) {
            return [
                [
                    'email' => 'fake@fake.fake',
                    'prefs' => '1',
                    'confirmed' => '0',
                ],
                [
                    'email' => 'fake2@fake.fake',
                    'prefs' => '5',
                    'confirmed' => '1',
                ],
            ];
        } else if (strpos(strtolower($query), 'select * from content') === 0) {
            return [
                [
                    'id' => '1',
                    'pref_id' => '1',
                    'first_eyes_usr_id' => '',
                    'second_eyes_usr_id' => '',
                    'content' => 'blub',
                ],
                [
                    'id' => '2',
                    'pref_id' => '4',
                    'first_eyes_usr_id' => '',
                    'second_eyes_usr_id' => '',
                    'content' => 'blub',
                ],
            ];
        } else if (strpos(strtolower($query), 'select usd_value from ppoe_mitglieder.adm_user_data') === 0) {
            return ['peter'];
        }

        // todo: implement

        return [];
    }

    public function close() {
        // todo: implement
    }

    public function escape($text) {
        // todo: implement
    }
}

class User {

    private $values = [
        'usr_id' => 1,
    ];

    public function getValue($key) {
        return $this->values[$key];
    }
}

$gCurrentUser = new User();
$roles = array(2,37,38,39,40,41,42,43,44,45);
$access = [2];

