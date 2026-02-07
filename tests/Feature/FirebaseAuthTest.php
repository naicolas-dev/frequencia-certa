<?php

namespace Tests\Feature;

use Tests\TestCase;

class FirebaseAuthTest extends TestCase
{
    public function test_firebase_service_loads()
    {
        try {
            $auth = app(\Kreait\Firebase\Contract\Auth::class);
            $this->assertInstanceOf(\Kreait\Firebase\Contract\Auth::class, $auth);
            echo "\n✅ Firebase authentication service loaded successfully!\n";
        } catch (\Exception $e) {
            $this->fail("Firebase failed to load: " . $e->getMessage());
        }
    }
}
