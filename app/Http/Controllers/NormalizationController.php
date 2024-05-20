<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\CriteriaWeight;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NormalizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $periode_id = $request->periode_id;
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
        ->where('alternativescores.periode_id', $periode_id) // Filter berdasarkan periode_id
        ->get();
        

        // duplicate scores object to get rating value later,
        // because any call to $scores object is pass by reference
        // clone, replica, tobase didnt work
        $cscores = AlternativeScore::select(
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
        ->get();



        $alternatives = Alternative::select('alternatives.*', 'alternativescores.periode_id')
        ->join('alternativescores', 'alternatives.id', '=', 'alternativescores.alternative_id')
        ->where('alternativescores.periode_id', $periode_id)
        ->distinct()
        ->get();

        $criteriaweights = CriteriaWeight::get();

        // Normalization
        foreach($alternatives as $a){
            // Get all scores for each alternative id
            $afilter = $scores->where('ida', $a->id)->values()->all();
            // Loop each criteria
            foreach($criteriaweights as $icw => $cw){
                // Get all rating value for each criteria
                $rates = $cscores->map(function($val) use ($cw){
                    if($cw->id == $val->idw ){
                        return $val->rating;
                    }
                })->toArray();

                // array_filter for removing null value caused by map,
                // array_values for reiindex the array
                $rates = array_values(array_filter($rates));

                if ($cw->type == 'benefit') {
                    $result = $afilter[$icw]->rating / max($rates);
                    $msg = 'rate ' . $afilter[$icw]->rating . ' max ' . max($rates) . ' res ' . $result;
                } elseif ($cw->type == 'cost') {
                    $result = min($rates) / $afilter[$icw]->rating;
                }
                $afilter[$icw]->rating = round($result, 2);
            }
        }

        return view('normalization', compact('scores', 'alternatives', 'criteriaweights'))->with('i', 0);
    }
    public function view()
    {
        $user = Auth::user();
        $userId = $user->userId;
        $mahasiswa = User::join('mahasiswa', 'users.userId', '=', 'mahasiswa.userId')
            ->where('users.userId', $user->userId)
            ->select('mahasiswa.nama', 'mahasiswa.jurusan', 'mahasiswa.prodi','mahasiswa.userId')
            ->first();
        // $nama = $mahasiswa->nama;
        $periode = Periode::all();
        return view('normalizationview', compact('periode','mahasiswa','userId'));
    }
}
