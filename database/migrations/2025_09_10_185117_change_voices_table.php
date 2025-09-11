<?php

use App\Models\Stat;
use App\Models\TgFile;
use App\Models\Video;
use App\Models\Voice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $files = \App\Models\File::with('voice')->get();
        $voices = Voice::with('file')->get();

        Schema::create('tg_files', function (Blueprint $table) {
            $table->id();

            $table->morphs('fileable');

            $table->string('file_id');
            $table->string('file_unique_id');
            $table->integer('file_size')->nullable();
            $table->string('file_path')->nullable();

            $table->timestamps();
        });

        Schema::dropIfExists('voices');
        Schema::dropIfExists('files');

        Schema::create('voices', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('duration');
            $table->string('mime_type')->nullable();
            $table->text('text')->nullable();

            $table->timestamps();

            $table->fullText('text');
        });

        $files->each(function (\App\Models\File $file) {
            $voice = $file->voice;

            if ($voice === null) {
                return;
            }

            if ($voice->is_video) {
                $video = Video::create([
                    'text' => $voice->text,
                    'duration' => $voice->duration,
                    'created_at' => $voice->created_at,
                ]);

                TgFile::create([
                    'file_id' => $file->file_id,
                    'file_unique_id' => $file->file_unique_id,
                    'file_size' => $file->file_size,
                    'file_path' => $file->file_path,
                    'fileable_id' => $video->id,
                    'fileable_type' => Video::class,
                    'created_at' => $file->created_at,
                ]);

                Stat::create([
                    'statable_id' => $video->id,
                    'statable_type' => Video::class,
                    'usages' => $voice->usage_count,
                ]);
            } else {
                $newVoice = Voice::create([
                    'text' => $voice->text,
                    'duration' => $voice->duration,
                    'mime_type' => $voice->mime_type,
                    'created_at' => $voice->created_at,
                ]);

                TgFile::create([
                    'file_id' => $file->file_id,
                    'file_unique_id' => $file->file_unique_id,
                    'file_size' => $file->file_size,
                    'file_path' => $file->file_path,
                    'fileable_id' => $newVoice->id,
                    'fileable_type' => Voice::class,
                    'created_at' => $voice->created_at,
                ]);

                Stat::create([
                    'statable_id' => $newVoice->id,
                    'statable_type' => Voice::class,
                    'usages' => $voice->usage_count,
                ]);
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
