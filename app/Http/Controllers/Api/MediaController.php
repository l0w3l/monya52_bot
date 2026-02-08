<?php

namespace App\Http\Controllers\Api;

use App\Console\Commands\UniqueMediaCommand;
use App\Http\Controllers\Controller;
use App\Models\TgFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 50);
        $offset = $request->input('offset', 0);

        return TgFile::with('fileable')->offset($offset)->limit($limit)->get();
    }

    public function empty(Request $request)
    {
        $limit = $request->input('limit', 50);
        $offset = $request->input('offset', 0);

        return TgFile::with('fileable')->whereHas('fileable', fn (Builder $builder) => $builder->whereNull('text'))->offset($offset)->limit($limit)->get();
    }

    public function update(TgFile $tgFile, Request $request)
    {
        $text = $request->input('text');

        $fileable = $tgFile->fileable;

        $fileable->text = $text;

        $fileable->save();

        return response()->noContent();
    }

    public function unique()
    {
        return Process::command(UniqueMediaCommand::class)->run()->output();
    }
}
