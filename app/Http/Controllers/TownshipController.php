<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Township;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class TownshipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $data = Township::all();
        // return Inertia::render('township', ['data' => $data]);
      ;
        $townships = Township::when($request->township, function($query, $tsp){
            $query->where('name','LIKE','%'.$tsp.'%')
            ->orWhere('short_code','LIKE','%'.$tsp.'%');
        })
        ->paginate(10);

        return Inertia::render('Setup/Township', ['townships' => $townships]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
   
        Validator::make($request->all(), [
            'name' => ['required'],
            'city' => ['required'],
            'short_code' => ['required'],
        ])->validate();
  
        Township::create($request->all());
  
        return redirect()->route('township.index')->with('message', 'Township Created Successfully.');
        // return redirect()->route('posts.index') 
        // ->with('message', 'Post Created Successfully.');
    }

   

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'city' => ['required'],
            'short_code' => ['required'],
        ])->validate();
  
        if ($request->has('id')) {
            Township::find($request->input('id'))->update($request->all());
            return redirect()->back()
                    ->with('message', 'Township Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            Township::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
