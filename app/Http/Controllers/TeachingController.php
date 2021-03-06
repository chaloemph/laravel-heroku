<?php

namespace App\Http\Controllers;

use App\Teaching;
use App\Classroom;
use Illuminate\Http\Request;
use File;
use Storage;

class TeachingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $classroom = Classroom::find($id);
        $teachings = Teaching::where([
            'classrooms_id' => $id
        ])->get();

        return view('portal/teaching-create', compact('teachings', 'id', 'classroom'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        
        if ($request->hasFile('teachings_image')) {

            $request->validate([
                'image' => 'mimes:jpeg,bmp,png' // Only allow .jpg, .bmp and .png file types.
            ]);

            // Save the file locally in the storage/public/ folder under a new folder named /product
            // $contents = $request->file->store('teaching', 'public');
            $contents = $request->file('teachings_image')->store('teaching', 'public');
        } else {
            $contents = "";
        }

        $datetime = explode("/", $request->teachings_datetime);
        $year_time = explode(" ", $datetime[2] );
        $year = $year_time[0];
        $time = $year_time[1];
        
        $teaching = new Teaching();
        $teaching->teachings_datetime = date('Y-m-d H:i:s' , strtotime($year."-".$datetime[1]."-".$datetime[0]." ".$time));
        $teaching->teachings_class = $request->teachings_class;
        $teaching->teachings_subject = $request->teachings_subject;
        $teaching->teachings_signature = $request->teachings_signature;
        $teaching->teachings_note = $request->teachings_note;
        $teaching->classrooms_id = $request->classrooms_id;
        $teaching->teachings_image =  $contents;
        // $teaching->teachings_image = "";
        $teaching->save();
        return redirect()->route('teaching', ['id'=> $request->classrooms_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Teaching  $teaching
     * @return \Illuminate\Http\Response
     */
    public function show(Teaching $teaching, $id = null)
    {
        $teaching = Teaching::find($id);
        $classroom = Classroom::where([
            'id' => $teaching->classrooms_id
        ])->get()->first();
        return view('portal/teaching-show', compact('teaching', 'classroom', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Teaching  $teaching
     * @return \Illuminate\Http\Response
     */
    public function edit(Teaching $teaching)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Teaching  $teaching
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Teaching $teaching, $id = null)
    {
        //
        if ($request->hasFile('teachings_image')) {

            $request->validate([
                'image' => 'mimes:jpeg,bmp,png' // Only allow .jpg, .bmp and .png file types.
            ]);

            // Save the file locally in the storage/public/ folder under a new folder named /product
            // $contents = $request->file->store('teaching', 'public');
            $contents = $request->file('teachings_image')->store('teaching', 'public');
        } else {
            $contents = $request->teachings_image_old;
        }

        $teaching = Teaching::find($id);
        $teaching->teachings_class = $request->teachings_class;
        $teaching->teachings_subject = $request->teachings_subject;
        $teaching->teachings_signature = $request->teachings_signature;
        $teaching->teachings_note = $request->teachings_note;
        $teaching->teachings_image =  $contents;
        $teaching->save();
        
        return redirect()->route('teaching', ['id'=> $teaching->classrooms_id]);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Teaching  $teaching
     * @return \Illuminate\Http\Response
     */
    public function destroy(Teaching $teaching, $id = null)
    {
        $teaching = Teaching::find($id);
        Storage::delete("public/".$teaching->teachings_image);
        Teaching::destroy($id);
        return redirect()->route('teaching', ['id' => $teaching->classrooms_id]);
    }
}
