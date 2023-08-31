<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\M_Mahasiswa;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    public function login()
    {
        return view('login');
    }
    
    public function dashboard()
    {
        $user = Auth::user();
        $userId = $user->userId;
        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama')
            ->first();
        $totalMahasiswa = M_Mahasiswa::count();
        // $mahasiswa = M_Mahasiswa::where('userId',$userId)->first();
        // $data = M_Mahasiswa::all();
        return view('dashboard',['data','mahasiswa'=> $mahasiswa, 'totalMahasiswa' => $totalMahasiswa]);
    }
}
