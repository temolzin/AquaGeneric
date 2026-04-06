<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Locality;
use App\Models\WaterConnection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        $authUser = Auth::user()->load([
            'locality.membership',
            'locality.users',
            'locality.waterConnections' => function ($query) {
                $query->where('is_canceled', false);
            }
        ]);

        $membershipStats = [
            'users_count' => $authUser->locality && $authUser->locality->membership ?
                DB::table('memberships as m')
                    ->join('localities as l', 'l.membership_id', '=', 'm.id')
                    ->join('users as u', 'u.locality_id', '=', 'l.id')
                    ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
                    ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                    ->where('m.id', $authUser->locality->membership->id)
                    ->whereIn('r.name', ['Secretaria', 'Supervisor'])
                    ->distinct('u.id')
                    ->count('u.id') : 0,
            'users_limit' => $authUser->locality && $authUser->locality->membership ? $authUser->locality->membership->users_number : 0,
            'water_connections_count' => $authUser->locality ? $authUser->locality->waterConnections->count() : 0,
            'water_connections_limit' => $authUser->locality && $authUser->locality->membership ? $authUser->locality->membership->water_connections_number : 0,
            'subscription_status' => $authUser->locality ? $authUser->locality->getSubscriptionStatus() : 'Sin localidad',
            'membership_name' => $authUser->locality && $authUser->locality->membership ? $authUser->locality->membership->name : 'Sin membresía'
        ];

        return view('profile.index', compact('authUser', 'membershipStats'));
    }

    public function profileUpdate(Request $request)
    {
        $authUser = User::find(Auth::id());
        $authUser->name = $request->input('nameUpdate');
        $authUser->last_name = $request->input('lastNameUpdate');
        $authUser->email = $request->input('emailUpdate');
        $authUser->phone = $request->input('phoneUpdate');

        $authUser->save();

        return redirect()->route('profile.index')->with('success', 'Perfil actualizado correctamente.');
    }

    public function updateImage(Request $request)
    {
        $request->validate([
            'profileImage' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ], [
            'profileImage.image' => 'El archivo debe ser una imagen.',
            'profileImage.mimes' => 'Solo se permiten imágenes jpg, jpeg, png.',
            'profileImage.max' => 'La imagen no puede superar los 5MB.',
        ]);

        $user = User::find(Auth::id());

        if ($user->getFirstMedia('userGallery')) {
            $user->clearMediaCollection('userGallery');
        }

        $user->addMedia($request->file('profileImage'))->toMediaCollection('userGallery');

        return redirect()->back()->with('success', 'Foto de perfil actualizada con éxito.');
    }

    public function updatePassword(Request $request)
    {
        $user = User::find(Auth::id());

        if (!Hash::check($request->input('oldPassword'), $user->password)) {
            return redirect()->back()->with('error', 'La contraseña actual es incorrecta.');
        }

        $newPassword = $request->input('updatePassword');
        $confirmPassword = $request->input('passwordConfirmation');

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'La nueva contraseña y la confirmación no coinciden.');
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Contraseña actualizada correctamente.');
    }

    public function updateWebhookConfig(Request $request)
    {
        $user = Auth::user();
        $locality = $user->locality;

        if (!$user->hasRole(['Supervisor', 'Secretaria'])) {
            return redirect()->back()->with('error', 'No tienes permiso para gestionar la configuración del webhook.');
        }

        if (!$locality) {
            return redirect()->back()->with('error', 'No tienes una localidad asignada.');
        }

        $request->validate([
            'openpay_webhook_user' => 'nullable|string|email',
            'openpay_webhook_password' => 'nullable|string',
        ]);

        $webhookUser = $request->input('openpay_webhook_user');
        $webhookPassword = $request->input('openpay_webhook_password');

        if (!empty($webhookUser)) {
            $locality->openpay_webhook_user = $webhookUser;
        }
        if (!empty($webhookPassword)) {
            $locality->openpay_webhook_password = $webhookPassword;
        }

        $locality->save();

        return redirect()->route('profile.index')->with('success', 'Configuración del webhook actualizada correctamente.');
    }

    public function testWebhookConnection()
    {
        $user = Auth::user();
        $locality = $user->locality;

        if (!$user->hasRole(['Supervisor', 'Secretaria'])) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para realizar esta acción.'], 403);
        }

        if (!$locality) {
            return response()->json(['success' => false, 'message' => 'No tienes una localidad asignada.'], 400);
        }

        if (!$locality->openpay_webhook_user || !$locality->openpay_webhook_password) {
            return response()->json([
                'success' => false,
                'message' => 'Debes configurar el correo y contraseña de OpenPay primero.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Credenciales de webhook validadas correctamente.'
        ]);
    }
}
