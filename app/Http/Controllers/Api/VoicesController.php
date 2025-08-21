<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voice;
use Illuminate\Http\Request;

class VoicesController extends Controller
{
    public function index()
    {
        return Voice::with('file')->get();
    }

    public function update(Voice $voice, Request $request)
    {
        $text = $request->input('text');

        $voice->text = $text;

        $voice->save();

        return response()->noContent();
    }
}
