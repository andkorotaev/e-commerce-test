<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\CityLookupService;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    public function __construct(protected CityLookupService $cities) {}

    public function index(): JsonResponse
    {
        return response()->json($this->cities->all()->values());
    }
}
