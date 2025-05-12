<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    public function test_registration_view_can_be_rendered()
    {
        $response = $this->get('/register');
        $response->assertStatus(200)
            ->assertVIewIs('user.register');
    }

    public function test_show_message_user_register_without_name()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'dummy@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください'
        ]);
        $this->assertGuest();
    }

    public function test_show_message_user_register_without_email()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'Dummy User',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください'
        ]);
        $this->assertGuest();
    }

    public function test_show_message_user_register_without_password()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'Dummy User',
            'email' => 'dummy@example.com',
            'password' => '',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);
        $this->assertGuest();
    }

    public function test_show_message_user_register_with_8characters_less_password()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'Dummy User',
            'email' => 'dummy@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください'
        ]);
        $this->assertGuest();
    }

    public function test_show_message_user_register_with_mismatch_password()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'Dummy User',
            'email' => 'dummy@example.com',
            'password' => 'password',
            'password_confirmation' => 'pass1234',
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません'
        ]);
        $this->assertGuest();
    }

    public function test_user_can_register_success()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'dummy@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/mypage/profile');
    }

    public function test_show_message_user_login_without_email()
    {
        $user = User::factory()->create();
        $this->assertGuest();

        $response = $this->get('/login');
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください'
        ]);
        $this->assertGuest();
    }

    public function test_show_message_user_login_without_password()
    {
        $user = User::factory()->create();
        $this->assertGuest();

        $response = $this->get('/login');
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => '',
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);
        $this->assertGuest();
    }

    public function test_show_message_user_login_with_wrong_data()
    {
        $user = User::factory()->create();
        $this->assertGuest();

        $response = $this->get('/login');
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);
        $response->assertSessionHasErrors([
            'email' => trans('auth.failed')
        ]);
        $this->assertGuest();
    }

    public function test_user_can_login_success()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);
        $response = $this->get('/login');
        $response->assertStatus(200)
            ->assertViewIs('user.login');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_can_logout_user()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user);
        $response = $this->get('/');
        $this->assertAuthenticated();

        $this->post('logout');
        $this->assertGuest();
    }

    public function test_show_users_purchased_items()
    {
        $sellUser = User::factory()->create();
        $buyUser = User::factory()->create();
        $this->actingAs($buyUser);

        $items = Item::factory()->count(5)->create();

        $purchasedItems = $items->take(2);
        //ユーザーが購入
        foreach ($purchasedItems as $item) {
            $item->update([
                'status' => 'sold'
            ]);
            Purchase::create([
                'user_id' => $buyUser->id,
                'item_id' => $item->id,
                'payment' => 'card',
                'zip_code' => '0000000',
                'address' => '北海道札幌市',
            ]);
        }

        $response = $this->get('/mypage');
        $expected = ['id="purchasedItems"'];
        foreach ($purchasedItems as $item) {
            $expected[] = e($item->name);
        }
        $response->assertSeeText('購入しました');
        $response->assertSeeInOrder($expected, false);
    }
}
