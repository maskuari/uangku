<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UangkuTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_register_and_sign_in(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('Masuk ke Uangku');
        $this->post('/register', [
            'name' => 'Dina', 'email' => 'dina@example.test',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->get('/dashboard')->assertOk()->assertSee('Halo, Dina');
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
        $this->post('/login', ['email' => 'dina@example.test', 'password' => 'password123'])->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_transactions_change_balance_and_are_private(): void
    {
        $user = User::factory()->create(['opening_balance' => 100000]);
        $other = User::factory()->create();
        $this->actingAs($user)->post('/transactions', [
            'type' => 'expense', 'title' => 'Makan siang', 'category' => 'Makanan & minuman',
            'amount' => 10000, 'occurred_on' => now()->toDateString(),
        ])->assertRedirect('/transactions');
        $transaction = $user->transactions()->firstOrFail();
        $this->get('/dashboard')->assertOk()->assertSee('Rp 90.000')->assertSee('Rp 10.000');
        $this->get('/transactions/create')->assertOk()->assertSee('Tambah transaksi');
        $this->get('/transactions/'.$transaction->id.'/edit')->assertOk()->assertSee('Makan siang');
        $this->get('/settings')->assertOk()->assertSee('Saldo saat ini')->assertSee('value="90000"', false);
        $this->actingAs($other)->get('/transactions')->assertOk()->assertDontSee('Makan siang');
        $this->get('/transactions/'.$transaction->id.'/edit')->assertNotFound();
        $this->delete('/transactions/'.$transaction->id)->assertNotFound();
        $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
        $this->actingAs($user)->put('/transactions/'.$transaction->id, [
            'type' => 'expense', 'title' => 'Makan malam', 'category' => 'Makanan & minuman',
            'amount' => 15000, 'occurred_on' => now()->toDateString(),
        ])->assertRedirect('/transactions');
        $this->get('/dashboard')->assertOk()->assertSee('Rp 85.000');
        $this->delete('/transactions/'.$transaction->id)->assertRedirect();
        $this->get('/dashboard')->assertOk()->assertSee('Rp 100.000');
    }

    public function test_balance_setup_and_settings_edit_the_current_balance(): void
    {
        $newUser = User::factory()->create(['opening_balance' => 0]);
        $this->post('/login', ['email' => $newUser->email, 'password' => 'password'])->assertRedirect('/dashboard');
        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Berapa saldo yang kamu punya sekarang?')
            ->assertSee('Asisten Uangku');
        $this->put('/settings/balance', ['current_balance' => 250000])->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertOk()->assertSee('Rp 250.000');

        $newUser->transactions()->create([
            'type' => 'expense', 'title' => 'Belanja', 'category' => 'Belanja',
            'amount' => 10000, 'occurred_on' => now()->toDateString(),
        ]);
        $this->get('/settings')->assertOk()
            ->assertSee('Saldo saat ini')
            ->assertSee('value="240000"', false);
        $this->put('/settings', [
            'name' => $newUser->name,
            'current_balance' => 300000,
            'monthly_budget' => null,
        ])->assertRedirect();
        $this->assertSame(310000, $newUser->fresh()->opening_balance);
        $this->get('/dashboard')->assertOk()->assertSee('Rp 300.000');
    }
    public function test_admin_can_manage_users_without_seeing_financial_data(): void
    {
        config([
            'admin.email' => 'maskuari@adminuangku.com',
            'admin.bootstrap_password' => 'test-admin-secret',
        ]);
        $managedUser = User::factory()->create(['opening_balance' => 987654321]);
        $transaction = $managedUser->transactions()->create([
            'type' => 'income', 'title' => 'Data keuangan rahasia', 'category' => 'Gaji',
            'amount' => 123456789, 'occurred_on' => now()->toDateString(),
        ]);

        $this->get('/admin')->assertRedirect('/login');
        $this->post('/login', [
            'email' => 'maskuari@adminuangku.com',
            'password' => 'test-admin-secret',
        ])->assertRedirect('/admin');
        $admin = User::where('email', 'maskuari@adminuangku.com')->firstOrFail();

        $response = $this->get('/admin')->assertOk()
            ->assertSee($managedUser->email)
            ->assertSee('Terenkripsi')
            ->assertDontSee('Data keuangan rahasia')
            ->assertDontSee('987654321')
            ->assertDontSee($managedUser->password);
        $this->actingAs($managedUser)->get('/admin')->assertForbidden();

        $this->actingAs($admin)->put('/admin/users/'.$managedUser->id.'/password', [
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ])->assertRedirect('/admin');
        $this->assertTrue(Hash::check('password-baru', $managedUser->fresh()->password));

        $this->delete('/admin/users/'.$managedUser->id)->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $managedUser->id]);
        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
        $this->delete('/admin/users/'.$admin->id)->assertForbidden();
    }
    public function test_old_data_cleanup_keeps_current_balance(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 1));
        $user = User::factory()->create(['opening_balance' => 50000]);
        $user->transactions()->create(['type' => 'income', 'title' => 'Gaji lama', 'category' => 'Gaji', 'amount' => 100000, 'occurred_on' => '2026-06-01']);
        $user->transactions()->create(['type' => 'expense', 'title' => 'Belanja lama', 'category' => 'Belanja', 'amount' => 20000, 'occurred_on' => '2026-06-02']);
        $user->transactions()->create(['type' => 'expense', 'title' => 'Belanja baru', 'category' => 'Belanja', 'amount' => 10000, 'occurred_on' => '2026-09-20']);
        $this->actingAs($user)->post('/settings/prune', ['confirmation' => 'HAPUS'])->assertRedirect();
        $this->assertSame(130000, $user->fresh()->opening_balance);
        $this->assertSame(1, $user->transactions()->count());
        $this->get('/dashboard')->assertOk()->assertSee('Rp 120.000');
    }

    public function test_monthly_report_and_pdf_export(): void
    {
        $user = User::factory()->create();
        $user->transactions()->create(['type' => 'expense', 'title' => 'Kopi', 'category' => 'Makanan & minuman', 'amount' => 12000, 'occurred_on' => '2026-09-02']);
        $this->actingAs($user)->get('/reports?month=2026-09')->assertOk()->assertSee('Kopi', false);
        $response = $this->get('/reports/pdf?month=2026-09')->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-1.4', $response->getContent());
    }
}
