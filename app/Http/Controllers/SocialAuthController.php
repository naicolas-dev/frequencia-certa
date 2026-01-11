<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SocialAuthController extends Controller
{
    protected FirebaseAuth $firebaseAuth;

    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }

    public function login(Request $request)
    {
        $idTokenString = $request->input('idToken');

        if (!$idTokenString) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token não enviado.',
            ], 400);
        }

        try {
            // 1. Verifica token
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($idTokenString);
            $claims = $verifiedIdToken->claims();

            $email = $claims->get('email');
            $name  = $claims->get('name') ?? 'Usuário';
            $photo = $claims->get('picture');

            // 2. GitHub / outros podem não retornar email
            if (!$email) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Não foi possível obter o email da conta.',
                ], 422);
            }

            // 3. Cria ou atualiza usuário
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make(Str::random(32)),
                    'email_verified_at' => now(), // 👈 decisão do backend
                ]
            );

            // (Opcional) salvar foto futuramente
            // $user->update(['avatar' => $photo]);

            // 4. Login no Laravel
            Auth::login($user, true);

            return response()->json(['status' => 'success']);

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Falha ao autenticar com o provedor social.',
            ], 401);
        }
    }
}
