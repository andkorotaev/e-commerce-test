<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeliveryPointController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'city' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(['branch', 'postomat'])],
        ]);

        $points = config("delivery_points.{$request->string('type')}.{$request->string('city')}")
            ?? config("delivery_points.{$request->string('type')}.default");

        return response()->json($points);
    }
}
