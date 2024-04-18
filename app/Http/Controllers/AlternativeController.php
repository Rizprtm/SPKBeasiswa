<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\CriteriaWeight;
use App\Models\CriteriaRating;
use App\Models\User;
use App\Models\M_Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\QueryException;

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
        ->get();

        $alternatives = Alternative::get();

        $user = Auth::user();
        $co_admin = User::join('admin_jurusan', 'users.userId', '=', 'admin_jurusan.userId')
        ->where('users.userId', $user->userId)
        ->select('admin_jurusan.nama')
        ->first();

        $criteriaweights = CriteriaWeight::get();
        return view('alternative.index', compact('scores', 'alternatives', 'criteriaweights','co_admin'))->with('i', 0);
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
                ->select('mahasiswa.nama', 'mahasiswa.jurusan', 'mahasiswa.prodi','mahasiswa.userId')
                ->first();
            $nama = $mahasiswa->nama;
                
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
        try {
            // ddd($request);
            
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
                if ($request->hasFile('dokumen')) {
                    $file = $request->file('dokumen');
                    $fileName = $file->getClientOriginalName();
                    $filePath = $file->storeAs('dokumen', $fileName); // Misalnya, menyimpan file dalam folder 'dokumen' di penyimpanan lokal
                    $score->dokumen = $filePath; // Simpan path file ke dalam kolom 'dokumen'
                }
                $score->save();
            }




            // $nm = $request->berkas;
            // $namaFile = $nm->getClientOriginalName();
            // $dokUpload = new AlternativeScore;
            // $dokUpload->berkas = $namaFile;
            // $nm->move(public_path().'/dokumen', $namaFile);
            // $dokUpload->save();

            return redirect()->route('alternatives.create')
                ->with('success', 'Data disimpan permanen.');
            // If the operation was successful, send a success response
            return response()->json(['status' => 'success']);
        

            // If no duplicate, proceed with creating the user
            // Your code for creating the user goes here


        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tidak bisa diubah (silakan hubungi admin)');
        }
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
