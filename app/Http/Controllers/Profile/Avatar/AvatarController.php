<?php

namespace App\Http\Controllers\Profile\Avatar;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    public function update(UpdateAvatarRequest $request)
    {

        $path = Storage::disk('public')->put('avatars', $request->file('avatar'));
        //$path = $request->file('avatar')->store('avatars', 'public');

        if($oldAvatar = $request->user()->avatar) {
            Storage::disk('public')->delete($oldAvatar);
        }


        auth()->user()->update(['avatar' => $path ]);
        //return back()->with('message', 'Avatar is changed');
        return redirect(route('profile.edit'))->with('message', 'Avatar is updated');
    }
}
