<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\CriteriaWeight;
use App\Models\CriteriaRating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AlternativeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $scores = AlternativeScore::select(
            'alternativescores.id as id',
            'alternatives.id as ida',
            'criteriaweights.id as idw',
            'criteriaratings.id as idr',
            'alternatives.userId as userId',
            'criteriaweights.name as criteria',
            'criteriaratings.rating as rating',
            'criteriaratings.description as description')
        ->leftJoin('alternatives', 'alternatives.id', '=', 'alternativescores.alternative_id')
        ->leftJoin('criteriaweights', 'criteriaweights.id', '=', 'alternativescores.criteria_id')
        ->leftJoin('criteriaratings', 'criteriaratings.id', '=', 'alternativescores.rating_id')
        ->leftJoin('mahasiswa', 'alternatives.id', '=', 'mahasiswa.userId')
        ->get();

        $alternatives = Alternative::get();

        $criteriaweights = CriteriaWeight::get();
        return view('alternative.index', compact('scores', 'alternatives', 'criteriaweights'))->with('i', 0);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $user = Auth::user();
        $userId = $user->userId;
        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama')
            ->first();
        // $mahasiswa = M_Mahasiswa::where('userId',$userId)->first();
        // $data = M_Mahasiswa::all();
        
        $criteriaweights = CriteriaWeight::get();
        $criteriaratings = CriteriaRating::get();
        return view('alternative.create', compact('criteriaweights', 'criteriaratings','mahasiswa','userId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $user = Auth::user();
        $userId = $user->userId;
        // Save the alternative
        $alt = new Alternative;
        $alt->userId = $userId;
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
        
        return redirect()->route('alternatives.create')
            ->with('success', 'Alternative created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Alternative  $alternative
     * @return \Illuminate\Http\Response
     */
    public function show(Alternative $alternative)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Alternative  $alternative
     * @return \Illuminate\Http\Response
     */
    public function edit(Alternative $alternative)
    {
        $criteriaweights = CriteriaWeight::get();
        $criteriaratings = CriteriaRating::get();
        $alternativescores = AlternativeScore::where('alternative_id', $alternative->id)->get();
        return view('alternative.edit', compact('alternative', 'alternativescores', 'criteriaweights', 'criteriaratings'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Alternative  $alternative
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Alternative $alternative)
    {
        // Save the score
        $scores = AlternativeScore::where('alternative_id', $alternative->id)->get();
        $criteriaweight = CriteriaWeight::get();
        foreach ($criteriaweight as $key => $cw) {
            $scores[$key]->rating_id = $request->input('criteria')[$cw->id];
            $scores[$key]->save();
        }

        return redirect()->route('alternatives.index')
            ->with('success', 'Alternative updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Alternative  $alternative
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alternative $alternative)
    {
        $scores = AlternativeScore::where('alternative_id', $alternative->id)->delete();
        $alternative->delete();

        return redirect()->route('alternatives.index')
            ->with('success', 'Alternative deleted successfully');
    }
}