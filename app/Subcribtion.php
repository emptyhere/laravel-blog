<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcribtion extends Model
{
    public static function add($email) {
        $sub = new static;
        $sub->email = $email;
        $sub->token = str_random(100);
        $sub->save();

        return $sub;
    }

    public function remove() {
        $this->delete();
    }
}
