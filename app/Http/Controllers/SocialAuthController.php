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
    protected $firebaseAuth;

    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }

    public function login(Request $request)
    {
        $idTokenString = $request->input('idToken');

        try {
            // 1. Verifica o token com o Firebase
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($idTokenString);
            $claims = $verifiedIdToken->claims();
            
            $uid = $claims->get('sub');
            $email = $claims->get('email');
            $name = $claims->get('name') ?? 'Usuário sem nome';
            $photo = $claims->get('picture');

            // 2. Procura o usuário no banco ou cria um novo
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make(Str::random(24)), // Senha aleatória segura
                    'email_verified_at' => now(), // Firebase já valida o email
                ]);
                
                // Opcional: Salvar o UID do firebase ou foto em outra tabela
            }

            // 3. Loga o usuário no Laravel
            Auth::login($user, true); // true = Lembrar de mim

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 401);
        }
    }
}