<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\GHNService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class LocationController extends Controller
{
    public function __construct(
        private readonly GHNService $ghn
    ) {
    }

    public function provinces(): JsonResponse
    {
        return $this->execute(
            fn () => $this->ghn->provinces()
        );
    }

    public function districts(int $provinceId): JsonResponse
    {
        return $this->execute(
            fn () => $this->ghn->districts($provinceId)
        );
    }

    public function wards(int $districtId): JsonResponse
    {
        return $this->execute(
            fn () => $this->ghn->wards($districtId)
        );
    }

    public function calculateFee(Request $request): JsonResponse
    {
        $data = $request->validate([
            'district_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'ward_code' => [
                'required',
                'string',
                'max:50',
            ],

            'insurance_value' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        return $this->execute(
            fn () => [
                'shipping_fee' => $this->ghn->calculateFee($data),
            ]
        );
    }

    private function execute(callable $callback): JsonResponse
    {
        try {
            return response()->json([
                'ok' => true,
                'data' => $callback(),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'ok' => false,
                'message' => $exception->getMessage(),
                'data' => null,
            ], 422);
        }
    }
}