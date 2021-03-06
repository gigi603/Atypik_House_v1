<?php

namespace App\Http\Controllers;

use App\House;
use App\Ville;
use App\Category;
use App\Comment;
use App\Reservation;
use App\Propriete;
use App\Valuecatpropriete;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Session;
use Image;
use View;

class HousesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(House $house)
    {
        $houses = house::with('valuecatproprietes', 'proprietes', 'category')->where('statut', 'Validé')->get();
        return view('houses.index')->with('houses', $houses);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Category  $categories
     * @param  \App\Ville  $villes
     * @return \Illuminate\Http\Response
     */
    public function create(Category $categories, Ville $villes)
    {
        $categories = category::all();
        $villes = ville::all();

        return view('houses.create', [
            'villes'=> $villes,
            'categories' => $categories,
        ]);
    }

    public function create_step1(Request $request) {

        return view('houses.create_step1');
    }

    public function postcreate_step1(Request $request) {
        $houseVille = session('houseVille', $request->ville);
        $request->session()->push('houseVille', $request->ville);

        $houseUser = session('houseUser', $request->user_id);
        $request->session()->push('houseUser', $request->user_id);

        return redirect('/house/create_step2');
    }

    public function create_step2(Category $categories, Request $request) {
        $categories = category::all();
        $request->session()->forget('houseProprietes');
        $request->session()->forget('houseProprietesId');

        $houseVille = $request->session()->get('houseVille');
        
        return view('houses.create_step2', [
            'categories' => $categories,
            'houseVille' => $houseVille
        ]);
    }

    public function postcreate_step2(Request $request) {
        $categories = category::all();
        
        $proprietes = $request->input('propriete');
        $proprietes_id = $request->input('propriete_id');

        $housePropriete = session('houseProprietes', $proprietes);
        $houseProprieteId = session('houseProprietesId', $proprietes_id);

        var_dump($proprietes);
        if ($proprietes == null) {

        } else {
            foreach ($proprietes as $valuePropriete){
                $request->session()->push('houseProprietes', $valuePropriete);
            }
            foreach ($proprietes_id as $keyId => $id){
                $request->session()->push('houseProprietesId', $id);
            }
        }
        
        $houseVille = $request->session()->get('houseVille');

        $houseCategory = session('houseCategory', $request->category_id);
        $request->session()->push('houseCategory', $request->category_id);

        $houseTitle = session('houseTitle', $request->title);
        $request->session()->push('houseTitle', $request->title);

        $houseDescription = session('houseDescription', $request->description);
        $request->session()->push('houseDescription', $request->description);

        return redirect('/house/create_step3');
    }

    public function create_step3(Request $request) {
        return view('houses.create_step3');
    }

    public function postcreate_step3(Request $request) {
        $housePrix = session('housePrix', $request->price);
        $request->session()->push('housePrix', $request->price);
    
        $houseVille = $request->session()->get('houseVille');
        $houseTitle = $request->session()->get('houseTitle');
        $houseDescription = $request->session()->get('houseDescription');
        $housePrix = $request->session()->get('housePrix');
        return redirect('/house/create_step4');
    }
    

    public function create_step4(Request $request) {
        return view('houses.create_step4');
    }

    public function postcreate_step4(Request $request) {
        $houseTitle = $request->session()->get('houseTitle');
        $houseUser = $request->session()->get('houseUser');
        $houseCategory = $request->session()->get('houseCategory');
        $houseVille = $request->session()->get('houseVille');
        $houseDescription = $request->session()->get('houseDescription');
        $housePrix = $request->session()->get('housePrix');
        
        $housePhoto = session('housePhoto', $request->photo);
        $request->session()->push('housePhoto', $request->photo);
        
        $house = new house;
        $house->title = last($houseTitle);
        $house->user_id = last($houseUser);
        $house->category_id = last($houseCategory);
        $house->ville = last($houseVille);
        $house->description = last($houseDescription);
        $house->price = last($housePrix);
        $house->statut = "En attente de validation";

        $housePropriete = $request->session()->get('houseProprietes');
        $houseProprieteId = $request->session()->get('houseProprietesId');        

        $this->validate($request, [
            'photo' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:20000',
        ]);

        $picture = $request->file('photo');
        $filename  = time() . '.' . $picture->getClientOriginalExtension();
        $path = public_path('img/houses/' . $filename);
        Image::make($picture->getRealPath())->resize(350, 200)->save($path);
        $house->photo = $filename;

        //$photo_name = date('mdYHis') . uniqid() . $request->file('photo')->getClientOriginalName();
        //$path = base_path() . '/public/img/houses';
        //$request->file('photo')->move($path,$photo_name);
        //Image::make($image_name->getRealPath())->resize(350, 200)->save($path);
        //Image::make($request->file('photo'))->resize(350, 200)->save($path);

        $house->save();
        if($housePropriete == null){

        } else {
            foreach($housePropriete as $proprietes){ 
                  
                $valuecatProprietesHouse = new valuecatPropriete;
                $valuecatProprietesHouse->value = $proprietes;
    
                $valuecatProprietesHouse->category_id = $house->category_id;
                foreach($houseProprieteId as $propriete_id){
                    $valuecatProprietesHouse->propriete_id = $propriete_id;
                }
                $valuecatProprietesHouse->house_id = $house->id;
                $valuecatProprietesHouse->save();
            }
        
        }
        
        return redirect('/house/confirmation_create_house');
        
    }

    public function confirmation_create_house(){
        return view('houses.confirmation_create_house');
    }

    public function json_propriete($id){
        $category = category::find($id);
        $proprietes = propriete::where('category_id', $category->id)->get();
        return response()->json(["proprietes" => $proprietes,
                                 "category_id" => $category->id], 200);     
    }
    
    /*public function proprietescategory(Category $categories, $id) {
        $category = category::find($id);
        
        return view('houses.create_step2', [
            'categories' => $categories,
            'houseVille' => $houseVille
        ]);
    }*/


    /**
     * Display the specified resource.
     *
     * @param  \App\House  $house
     * @return \Illuminate\Http\Response
     */
    public function show(House $house, Reservation $reservation)
    {
        $house = house::find($house->id);
        return view('houses.show', compact('house', 'id'))->with('house', $house);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\House  $house
     * @return \Illuminate\Http\Response
     */
    public function edit(House $house, Category $categories, Ville $villes)
    {
        $house = house::find($house->id);
        $categories = category::all();
        $villes = ville::all();
        return view('houses.edit', compact('house', 'id'))->with('categories', $categories)->with('villes', $villes);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\House  $house
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, House $house)
    {
        $house = house::find($house->id);
        $house->title = $request->get('title');
        $house->category_id = $request->get('category_id');
        $house->ville_id = $request->get('ville_id');
        $house->price = $request->get('price');
        $house->photo = $request->get('photo');
        $house->description = $request->get('description');
        /*$this->validate($request, [
        // check validtion for image or file
            'photo' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:20000',
        ]);*/

        //$picture = $request->file('photo');
        $picture = $request->file('photo');
        $filename  = time() . '.' . $picture->getClientOriginalExtension();
        $path = public_path('img/houses/' . $filename);
        Image::make($picture->getRealPath())->resize(350, 200)->save($path);
        $house->photo = $filename;

        $house->save();
        return redirect('/houses/index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\House  $house
     * @return \Illuminate\Http\Response
     */
    public function destroy(House $house)
    {
        $house = house::find($house->id);
        $house->delete();
        return redirect('houses/index');
        
    }

    public function mylocations($id) {

     $houseProfil = DB::table('users')
     ->select('users.*', 'houses.*')
     ->leftJoin('houses', 'houses.user_id','users.id')
     ->where('users.id', '=', $id)
     ->where('houses.id', '!=', NULL)
     ->get();
        return view('houses.mylocations', compact('houseProfil'))->with('data', Auth::user()->user);
    }

    public function note(House $house, Note $note) {
        $note = note::find($house->id);
        $house->title = $request->get('title');
        $house->idCategory = $request->get('idCategory');
        $house->price = $request->get('price');
    }
}
