<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TrashController extends Controller
{
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
            ->onlyTrashed()->latest("id")->paginate(10)->withQueryString();

        return response()->json([
            "success" => true,
            "contacts" => ContactResource::collection($contacts)->response()->getData()
        ]);
    }

    public function show($id): JsonResponse
    {
        $contact = Contact::onlyTrashed()->find($id);

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

    public function destroy($id)
    {
        $contact = Contact::onlyTrashed()->find($id);

        if (is_null($contact)) {
            return response()->json([
                "success" => false,
                "message" => "contact not found"
            ], 404);
        }


        Gate::authorize("delete", $contact);

        if (request('delete') === 'force') {

            if ($contact->photo) {
                Storage::delete($contact->photo);
            }

            $contact->forceDelete();
            $message = "contact have been deleted.";
        }
        if (request('delete') === 'restore') {

            $contact->restore();
            $message = "contact have been restored.";
        }

        return response()->json([
            "success" => true,
            "message" => $message
        ]);
    }
}
