<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VersionController extends Controller
{
    public function index()
    {
        return Cache::rememberForever('versions', function () {
            return collect(DB::connection('mysqluserDB')->table('versions')->pluck('version'))->toArray();
        });
    }
}
