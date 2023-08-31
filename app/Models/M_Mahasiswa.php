<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class M_Mahasiswa extends Model
{
    use HasFactory;
    protected $table = "mahasiswa";
    public function allData()
    {
        // return DB::table('mahasiswa')
        //     ->leftJoin('users','users.userId', '=', 'mahasiswa.userId')
        //     ->get();
            return $this->hasOne(Mahasiswa::class);
    }       
}
