<?php

namespace App\Http\Controllers\Profile\Avatar;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateAvatarRequest;

class AvatarController extends Controller
{
    public function update(UpdateAvatarRequest $request)
    {

        $path = Storage::disk('public')->put('avatars', $request->file('avatar'));
        //$path = $request->file('avatar')->store('avatars', 'public');

        if ($oldAvatar = $request->user()->avatar) {
            Storage::disk('public')->delete($oldAvatar);
        }


        auth()->user()->update(['avatar' => $path]);
        //return back()->with('message', 'Avatar is changed');
        return redirect(route('profile.edit'))->with('message', 'Avatar is updated');
    }

    public function generate(Request $request)
    {
        $result = OpenAI::images()->create([        
            'prompt' => 'Create avatar for user with cool style animated in technologies world.',
            'n' => 1,
            'size' => "256x256",
        ]);

        //echo $result['choices'][0]['text']; // an open-source, widely-used, server-side scripting language.
        //return response(['url' => $result->data[0]->url]);

        $contents = file_get_contents($result->data[0]->url);
        $filename = Str::random(25);

        if ($oldAvatar = $request->user()->avatar) {
            Storage::disk('public')->delete($oldAvatar);
        }

        Storage::disk('public')->put("avatars/$filename.jpg", $contents);

        auth()->user()->update(['avatar' => "avatars/$filename.jpg"]);
        return redirect(route('profile.edit'))->with('message', 'Avatar is updated');
    }
}
