<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
  

class Cinema extends Model
{
    //mendaftarkan SoftDeletes
    use SoftDeletes;
    //mendaftarkan column yg akan diisi oleh pengguna (column migration selain id dan timetamps)
    protected $fillable = ['name', 'location'];

    public function schedules(){

        return $this->hasMany(Schedule::class);
    }
}
