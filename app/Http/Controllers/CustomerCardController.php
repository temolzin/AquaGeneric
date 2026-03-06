<?php

namespace App\Http\Controllers;

use App\Models\CustomerCard;
use App\Models\Customer;
use App\Models\Locality;
use App\Services\OpenPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerCardController extends Controller
{
    const MAX_CARDS = 10;

    protected function getOpenPayServiceForUser(): OpenPayService
    {
        $user = Auth::user();
        $locality = $user->locality;

        if ($locality && $locality->hasOpenPayEnabled()) {
            return OpenPayService::forLocality($locality);
        }

        return OpenPayService::global();
    }

    public function index()
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();
        $locality = $user->locality;

        if (!$customer) {
            return redirect()->route('dashboard')
                ->with('error', 'No se encontró un cliente asociado a tu cuenta.');
        }

        $cards = CustomerCard::where('customer_id', $customer->id)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customerCards.index', compact('cards', 'customer', 'locality'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token_id' => 'required|string',
            'device_session_id' => 'required|string',
            'alias' => 'nullable|string|max:50',
            'holder_name' => 'required|string|max:70',
            'card_number' => 'required|string|min:13|max:19',
            'expiration_month' => 'required|string|size:2',
            'expiration_year' => 'required|string|size:2',
            'brand' => 'required|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();
        $locality = $user->locality;

        if (!$customer) {
            return response()->json([
                'success' => false,
                'error' => 'No se encontró un cliente asociado a tu cuenta.',
            ], 404);
        }

        if (!$locality || !$locality->hasOpenPayEnabled()) {
            return response()->json([
                'success' => false,
                'error' => 'El pago con tarjeta no está habilitado para tu localidad.',
            ], 400);
        }

        $currentCardsCount = CustomerCard::where('customer_id', $customer->id)->count();

        if ($currentCardsCount >= self::MAX_CARDS) {
            return response()->json([
                'success' => false,
                'error' => 'Has alcanzado el límite máximo de ' . self::MAX_CARDS . ' tarjetas guardadas.',
            ], 422);
        }

        $lastFour = substr(preg_replace('/[^0-9]/', '', $request->card_number), -4);
        $duplicate = CustomerCard::where('customer_id', $customer->id)
            ->where('last_four', $lastFour)
            ->where('expiration_month', $request->expiration_month)
            ->where('expiration_year', $request->expiration_year)
            ->first();

        if ($duplicate) {
            return response()->json([
                'success' => false,
                'error' => 'Esta tarjeta ya se encuentra en tu lista (' . $duplicate->display_name . ').',
                'duplicate' => true,
            ], 422);
        }

        $openPayService = $this->getOpenPayServiceForUser();

        $result = $openPayService->createCard(
            $request->token_id,
            $request->device_session_id
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Error al registrar la tarjeta en el procesador de pagos.',
            ], 400);
        }

        $isDefault = $currentCardsCount === 0;

        $card = CustomerCard::create([
            'customer_id' => $customer->id,
            'alias' => $request->alias,
            'openpay_card_id' => $result['card_id'],
            'brand' => $result['brand'] ?: $request->brand,
            'last_four' => $result['last_four'] ?: substr(preg_replace('/[^0-9]/', '', $request->card_number), -4),
            'holder_name' => $result['holder_name'] ?: $request->holder_name,
            'expiration_month' => $result['expiration_month'] ?: $request->expiration_month,
            'expiration_year' => $result['expiration_year'] ?: $request->expiration_year,
            'is_default' => $isDefault,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tarjeta registrada exitosamente.',
            'card' => [
                'id' => $card->id,
                'display_name' => $card->display_name,
                'brand_icon' => $card->brand_icon,
                'is_default' => $card->is_default,
            ],
        ]);
    }

    public function setDefault(Request $request, $id)
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'error' => 'No se encontró un cliente asociado a tu cuenta.',
            ], 404);
        }

        $card = CustomerCard::where('id', $id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$card) {
            return response()->json([
                'success' => false,
                'error' => 'Tarjeta no encontrada.',
            ], 404);
        }

        $card->setAsDefault();

        return response()->json([
            'success' => true,
            'message' => 'Tarjeta establecida como predeterminada.',
        ]);
    }

    public function updateAlias(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'alias' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'error' => 'No se encontró un cliente asociado a tu cuenta.',
            ], 404);
        }

        $card = CustomerCard::where('id', $id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$card) {
            return response()->json([
                'success' => false,
                'error' => 'Tarjeta no encontrada.',
            ], 404);
        }

        $card->update(['alias' => $request->alias]);

        return response()->json([
            'success' => true,
            'message' => 'Alias actualizado.',
            'display_name' => $card->fresh()->display_name,
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();
        $locality = $user->locality;

        if (!$customer) {
            return response()->json([
                'success' => false,
                'error' => 'No se encontró un cliente asociado a tu cuenta.',
            ], 404);
        }

        $card = CustomerCard::where('id', $id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$card) {
            return response()->json([
                'success' => false,
                'error' => 'Tarjeta no encontrada.',
            ], 404);
        }

        if ($locality && $locality->hasOpenPayEnabled()) {
            $openPayService = $this->getOpenPayServiceForUser();
            $result = $openPayService->deleteCard($card->openpay_card_id);
        }

        $wasDefault = $card->is_default;
        $card->delete();

        if ($wasDefault) {
            $newDefault = CustomerCard::where('customer_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tarjeta eliminada exitosamente.',
        ]);
    }

    public function getCardsForPayment()
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'cards' => [],
            ]);
        }

        $cards = CustomerCard::where('customer_id', $customer->id)
            ->valid()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($card) {
                return [
                    'id' => $card->id,
                    'openpay_card_id' => $card->openpay_card_id,
                    'display_name' => $card->display_name,
                    'brand' => $card->brand,
                    'brand_icon' => $card->brand_icon,
                    'last_four' => $card->last_four,
                    'is_default' => $card->is_default,
                    'expiration' => $card->expiration_month . '/' . $card->expiration_year,
                ];
            });

        return response()->json([
            'success' => true,
            'cards' => $cards,
        ]);
    }
}
