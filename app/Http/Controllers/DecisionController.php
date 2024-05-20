<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\CriteriaWeight;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DecisionController extends Controller
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
        ->leftJoin('mahasiswa', 'alternatives.id', '=', 'mahasiswa.userId')
        ->where('alternativescores.periode_id', $periode_id) // Filter berdasarkan periode_id
        ->get();

        $alternatives = Alternative::select('alternatives.*', 'alternativescores.periode_id')
        ->join('alternativescores', 'alternatives.id', '=', 'alternativescores.alternative_id')
        ->where('alternativescores.periode_id', $periode_id)
        ->distinct()
        ->get();

        $criteriaweights = CriteriaWeight::get();

        return view('decision', compact('periode_id','scores', 'alternatives', 'criteriaweights'))->with('i', 0);
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
        return view('decisionview', compact('periode','mahasiswa','userId'));
    }
}
