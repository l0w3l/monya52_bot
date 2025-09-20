<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TgFile;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 50);
        $offset = $request->input('offset', 0);

        return TgFile::with('fileable')->offset($offset)->limit($limit)->get();
    }

    public function update(TgFile $tgFile, Request $request)
    {
        $text = $request->input('text');

        $fileable = $tgFile->fileable;

        $fileable->text = $text;

        $fileable->save();

        return response()->noContent();
    }
}
