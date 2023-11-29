<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
        $contacts = Contact::where("user_id", Auth::id())
            ->when($request->has("keyword"), function ($q) {
                $q->where(function ($q) {
                    $q->orwhere("name", "like", "%" . request('keyword') . "%")
                        ->orwhere("phone", "like", "%" . request('keyword') . "%")
                        ->orwhere("email", "like", "%" . request('keyword') . "%");
                });
            })
            ->latest("id")->paginate(10)->withQueryString();

        return response()->json([
            "success" => true,
            "contacts" => ContactResource::collection($contacts)->response()->getData()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|min:2|max:20",
            "phone" => "required|numeric",
            "address" => "nullable|max:100",
            "email" => "nullable|email|max:100",
            "photo" => "nullable|file|max:1024|mimes:png,jpeg",
        ]);

        $contact = new Contact();
        $contact->name = $request->name;
        $contact->phone = $request->phone;
        $contact->address = $request->address;
        $contact->email = $request->email;
        $contact->user_id = Auth::id();

        if ($request->hasFile('photo')) {
            $newName = md5(uniqid()) . "-profile_photo" . "." . $request->file("photo")->extension();
            $path = $request->file('photo')->storeAs(
                'public/profiles',
                $newName
            );

            $contact->photo = $path;
        }

        $contact->save();

        return response()->json([
            "success" => true,
            "message" => "contact created",
            "contact" => new ContactResource($contact)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $contact = Contact::find($id);

        if (is_null($contact)) {
            return response()->json([
                "success" => false,
                "message" => "contact not found"
            ], 404);
        }

        Gate::authorize("view", $contact);

        return response()->json([
            "success" => true,
            "contact" => new ContactResource($contact)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            "name" => "required|min:2|max:20",
            "phone" => "required|numeric",
            "address" => "nullable|max:100",
            "email" => "nullable|email|max:100",
            "photo" => "nullable|file|max:1024|mimes:png,jpeg",
        ]);


        $contact = Contact::find($id);

        if (is_null($contact)) {
            return response()->json([
                "success" => false,
                "message" => "contact not found"
            ], 404);
        }

        Gate::authorize("update", $contact);

        if ($request->has('name')) {
            $contact->name = $request->name;
        }

        if ($request->has('phone')) {
            $contact->phone = $request->phone;
        }

        if ($request->has('address')) {
            $contact->address = $request->address;
        }

        if ($request->has('email')) {
            $contact->email = $request->email;
        }

        if ($request->hasFile('photo')) {

            if ($contact->photo) {
                Storage::delete("public/profiles/" . $contact->photo);
            }

            $newName = md5(uniqid()) . "-profile_photo" . "." . $request->file("photo")->extension();
            $path = $request->file('photo')->storeAs(
                'public/profiles',
                $newName
            );

            $contact->photo = $path;
        }

        $contact->update();

        return response()->json([
            "success" => true,
            "message" => "contact updated",
            "contact" => new ContactResource($contact)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): JsonResponse
    {

        $contact = Contact::find($id);

        if (is_null($contact)) {
            return response()->json([
                "success" => false,
                "message" => "contact not found"
            ], 404);
        }

        Gate::authorize("delete", $contact);



        $contact->delete();

        return response()->json([
            "success" => true,
            "message" => "contact have been moved to trash."
        ]);
    }
}
