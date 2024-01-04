<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\PersonResource;
use App\Http\Resources\ContactResource;
use App\Http\Requests\PersonStoreRequest;
use App\Http\Requests\PersonUpdateRequest;

class ContactController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Contacts Controller
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
        $collection = QueryBuilder::for(Contact::class)
            ->defaultSort('-id')
            ->paginate($request->perPage());

        return ContactResource::collection($collection);
    }

    /**
     * store
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PersonStoreRequest $request)
    {
        $contact = new Contact();
        $contact->fill($request->safe()->only(['name']));
        $contact->save();
        return new ContactResource($contact);
    }


    /**
     * show
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Contact $contact)
    {
        return new ContactResource($contact);
    }

    public function update(PersonUpdateRequest $request, Contact $contact)
    {
        $contact->update($request->validated());
        return new ContactResource($contact);
    }

    /**
     * delete
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return response()->json([], Response::HTTP_OK);
    }
}
