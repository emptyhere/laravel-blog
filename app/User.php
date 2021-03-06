<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts() {
        return $this->hasMany(Post::class);
    }

    public static function add($fields) {
        $user = new static;
        $user->fill($fields);
        $user->password = bcrypt(['password']);
        $user->save();

        return $user;
    }

    public function edit($fields) {
        $this->fill($fields);
        $this->password = bcrypt($fields['password']);
        $this->save();
    }

    public function remove() {
        Storage::delete('upload/' . $this->image);
        $this->delete();
    }

    public function uploadAvatar($image) {
        if($image == null) { return; }

        Storage::delete('upload/' . $this->image);
        $filename = str_random(10) . '.' . $image->extensions();
        $image->saveAs('uploads', $filename);
        $this->image = $filename;
        $this->save();
    }

    public function getImage() {
        if($this->image == null) { return 'img/no-user-image.png'; }
        return '/uploads/' . $this->image;
    }

    public function makeAdmin() {
        $this->is_admin = 1;
        $this->save();
    }

    public function makeNormie() {
        $this->is_admin = 0;
        $this->save();
    }

    public function toggleAdmin($value) {
        if($value == null) {
            return $this->makeNormie();
        }
        return $this->makeAdmin();
    }

    public function ban() {
        $this->status = 1;
        $this->save();
    }

    public function unban() {
        $this->status = 0;
        $this->save();
    }

    public function toggleStatus($value) {
        if($value == null) { return $this->unban(); }
        return $this->ban();
    }
}
