<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\CriteriaWeight;
use App\Models\CriteriaRating;
use App\Models\getMahasiswa;
use App\Models\M_Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Mahasiswa extends Controller
{

    
    public function profile()
    {
        $user = Auth::user();
        $userId = $user->userId;
        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama')
            ->first();
        // $mahasiswa = M_Mahasiswa::where('userId',$userId)->first();
        $data = M_Mahasiswa::all();
        return view('mahasiswa.profile',['data','mahasiswa'=> $mahasiswa]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function formulir()
    {
        $user = Auth::user();
        $userId = $user->userId;
        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama')
            ->first();
        // $mahasiswa = M_Mahasiswa::where('userId',$userId)->first();
        $data = M_Mahasiswa::all();
        return view('mahasiswa.formulir',['data','mahasiswa'=> $mahasiswa]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        // Save the alternative
        $alt = new Alternative;
        $alt->name = $request->name;
        $alt->save();

        // Save the score
        $criteriaweight = CriteriaWeight::get();
        foreach ($criteriaweight as $cw) {
            $score = new AlternativeScore();
            $score->alternative_id = $alt->id;
            $score->criteria_id = $cw->id;
            $score->rating_id = $request->input('criteria')[$cw->id];
            $score->save();
        }

        return redirect()->route('alternatives.index')
            ->with('success', 'Alternative created successfully.');
    }
    public function create()
    {
        $criteriaweights = CriteriaWeight::get();
        $criteriaratings = CriteriaRating::get();
        return view('alternative.create', compact('criteriaweights', 'criteriaratings'));
    }
}
