<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\PersonResource;
use App\Http\Requests\PersonStoreRequest;
use App\Http\Requests\PersonUpdateRequest;

class PersonController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Persons Controller
    |--------------------------------------------------------------------------
    |
    |
    */

    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $collection = QueryBuilder::for(Person::class)
            ->with('contacts')
            ->defaultSort('-id')
            ->paginate($request->perPage());

        return PersonResource::collection($collection);
    }

    /**
     * store
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PersonStoreRequest $request)
    {
        //\DB::beginTransaction();
        $person = new Person();
        $person->fill($request->safe()->only(['name']));
        $person->save();
        
        $person->syncContacts($request->contacts);
        return new PersonResource($person->load('contacts'));
    }


    /**
     * show
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Person $person)
    {
        return new PersonResource($person);
    }

    public function update(PersonUpdateRequest $request, Person $person)
    {
        $person->update($request->validated());
        $person->syncContacts($request->contacts);
        return new PersonResource($person);
    }

    /**
     * delete
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Person $person)
    {
        $person->delete();
        return response()->json([], Response::HTTP_OK);
    }
}
