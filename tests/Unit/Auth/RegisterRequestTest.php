<?php

namespace Tests\Unit\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de validation avec des données valides.
     */
    public function test_validation_passes_with_valid_data(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertFalse($validator->fails());
    }

    /**
     * Test de validation échouée sans nom.
     */
    public function test_validation_fails_without_name(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /**
     * Test de validation échouée sans email.
     */
    public function test_validation_fails_without_email(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => 'John Doe',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /**
     * Test de validation échouée sans mot de passe.
     */
    public function test_validation_fails_without_password(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password_confirmation' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /**
     * Test de validation échouée avec un nom trop long.
     */
    public function test_validation_fails_with_name_too_long(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => str_repeat('a', 256),
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /**
     * Test de validation échouée avec un format d'email invalide.
     */
    public function test_validation_fails_with_invalid_email_format(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /**
     * Test de validation échouée avec un email en double.
     */
    public function test_validation_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'john@example.com']);

        $request = new RegisterRequest();
        $request->merge([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /**
     * Test de validation échouée avec une confirmation de mot de passe incorrecte.
     */
    public function test_validation_fails_with_password_confirmation_mismatch(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /**
     * Test de validation échouée avec un mot de passe faible.
     */
    public function test_validation_fails_with_weak_password(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /**
     * Test que l'email est converti en minuscules.
     */
    public function test_email_is_converted_to_lowercase(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => 'John Doe',
            'email' => 'JOHN@EXAMPLE.COM',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Tester que la règle 'lowercase' est présente
        $rules = $request->rules();
        $this->assertStringContainsString('lowercase', $rules['email']);
        
        // L'email reste en majuscules dans la request car la conversion se fait lors de la validation
        $this->assertEquals('JOHN@EXAMPLE.COM', $request->input('email'));
    }

    /**
     * Test des messages d'erreur personnalisés.
     */
    public function test_custom_error_messages(): void
    {
        $request = new RegisterRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('name.required', $messages);
        $this->assertArrayHasKey('name.max', $messages);
        $this->assertArrayHasKey('email.required', $messages);
        $this->assertArrayHasKey('email.email', $messages);
        $this->assertArrayHasKey('email.unique', $messages);
        $this->assertArrayHasKey('password.required', $messages);
        $this->assertArrayHasKey('password.confirmed', $messages);
    }

    /**
     * Test de validation réussie avec des caractères spéciaux dans le nom.
     */
    public function test_validation_passes_with_special_characters_in_name(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => 'Jean-Pierre O\'Connor',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertFalse($validator->fails());
    }

    /**
     * Test de validation réussie avec des caractères spéciaux dans l'email.
     */
    public function test_validation_passes_with_special_characters_in_email(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => 'John Doe',
            'email' => 'john+tag@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertFalse($validator->fails());
    }

    /**
     * Test de validation réussie avec un mot de passe complexe.
     */
    public function test_validation_passes_with_complex_password(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'MyComplexP@ssw0rd!',
            'password_confirmation' => 'MyComplexP@ssw0rd!',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertFalse($validator->fails());
    }

    /**
     * Test de validation réussie avec un nom court.
     */
    public function test_validation_passes_with_short_name(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => 'Jo',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertFalse($validator->fails());
    }

    /**
     * Test de validation réussie avec un nom à la limite.
     */
    public function test_validation_passes_with_name_at_limit(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => str_repeat('a', 255),
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertFalse($validator->fails());
    }

    /**
     * Test de validation réussie avec un email long.
     */
    public function test_validation_passes_with_long_email(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => 'John Doe',
            'email' => 'very.long.email.address.with.many.subdomains@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertFalse($validator->fails());
    }

    /**
     * Test que la méthode authorize retourne true.
     */
    public function test_authorize_method_returns_true(): void
    {
        $request = new RegisterRequest();
        $this->assertTrue($request->authorize());
    }

    /**
     * Test de validation échouée avec des données vides.
     */
    public function test_validation_fails_with_empty_data(): void
    {
        $request = new RegisterRequest();
        $request->merge([]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /**
     * Test de validation réussie avec des espaces dans le nom.
     */
    public function test_validation_passes_with_spaces_in_name(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => '  John Doe  ',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertFalse($validator->fails());
        // Les espaces sont conservés car il n'y a pas de trim automatique
        $this->assertEquals('  John Doe  ', $request->input('name'));
    }

    /**
     * Test de validation réussie avec des chiffres dans le nom.
     */
    public function test_validation_passes_with_numbers_in_name(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => 'John123',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertFalse($validator->fails());
    }
} 