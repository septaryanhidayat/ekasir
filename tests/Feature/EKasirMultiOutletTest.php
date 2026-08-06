<?php

namespace Tests\Feature;

use App\Models\CashRegister;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EKasirMultiOutletTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_login_and_access_desktop_dashboard(): void
    {
        $superadmin = User::factory()->create([
            'email' => 'super@ekasir.com',
            'role' => 'superadmin',
        ]);

        $response = $this->actingAs($superadmin)->get('/desktop/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Management Dashboard');
    }

    public function test_cashier_can_quick_login_with_pin(): void
    {
        $tenant = Tenant::create([
            'name' => 'Kantin Test',
            'code' => 'TEST-001',
        ]);

        $cashier = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Kasir Test',
            'email' => 'kasir@test.com',
            'password' => bcrypt('password'),
            'role' => 'cashier',
            'pin' => '654321',
        ]);

        $response = $this->post('/quick-pin-login', [
            'tenant_id' => $tenant->id,
            'pin' => '654321',
        ]);

        $response->assertRedirect(route('mobile.dashboard'));
        $this->assertAuthenticatedAs($cashier);
    }

    public function test_pos_checkout_deducts_stock_and_creates_transaction(): void
    {
        $tenant = Tenant::create([
            'name' => 'Kantin Outlet 1',
            'code' => 'OUT-1',
        ]);

        $cashier = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Kasir 1',
            'email' => 'kasir1@test.com',
            'password' => bcrypt('password'),
            'role' => 'cashier',
        ]);

        $product = Product::create([
            'tenant_id' => $tenant->id,
            'name' => 'Kopi Kapal Api',
            'barcode' => '123456789',
            'hpp' => 1500,
            'harga_jual' => 3000,
            'stock' => 50,
            'is_active' => true,
        ]);

        $register = CashRegister::create([
            'tenant_id' => $tenant->id,
            'user_id' => $cashier->id,
            'opening_amount' => 100000,
            'status' => 'open',
        ]);

        $this->actingAs($cashier);

        $response = $this->postJson('/m/checkout', [
            'items' => [
                ['id' => $product->id, 'qty' => 2]
            ],
            'cash_paid' => 10000,
            'payment_method' => 'cash'
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');

        $this->assertEquals(48, $product->fresh()->stock);
        $this->assertDatabaseHas('transactions', [
            'tenant_id' => $tenant->id,
            'total_amount' => 6000,
            'change_amount' => 4000,
        ]);
    }

    public function test_customer_can_order_from_mobile_shop(): void
    {
        $tenant = Tenant::create([
            'name' => 'Kantin Sekolah BSD',
            'code' => 'OUT-BSD',
        ]);

        $product = Product::create([
            'tenant_id' => $tenant->id,
            'name' => 'Nasi Goreng Spesial',
            'hpp' => 10000,
            'harga_jual' => 15000,
            'stock' => 20,
            'is_active' => true,
        ]);

        $response = $this->postJson('/shop/order', [
            'tenant_id' => $tenant->id,
            'customer_name' => 'Rian Siswa',
            'customer_phone' => '081234567890',
            'order_type' => 'dine_in',
            'table_number' => 'Meja 05',
            'payment_method' => 'qris',
            'items' => [
                ['id' => $product->id, 'qty' => 1]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');

        $this->assertEquals(19, $product->fresh()->stock);
        $this->assertDatabaseHas('transactions', [
            'customer_name' => 'Rian Siswa',
            'order_source' => 'customer_app',
            'payment_method' => 'qris',
            'total_amount' => 15000,
        ]);
    }

    public function test_mobile_receipt_handles_null_user_for_qris_customer_order(): void
    {
        $tenant = Tenant::create([
            'name' => 'Kantin Sekolah BSD',
            'code' => 'OUT-BSD',
        ]);

        $cashier = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Kasir 1',
            'email' => 'kasir1@test.com',
            'password' => bcrypt('password'),
            'role' => 'cashier',
        ]);

        $transaction = Transaction::create([
            'invoice_number' => 'INV/20260805/0001',
            'tenant_id' => $tenant->id,
            'user_id' => null,
            'customer_name' => 'Ahmad',
            'customer_phone' => '0812345678',
            'order_type' => 'dine_in',
            'order_source' => 'customer_app',
            'order_status' => 'paid',
            'total_hpp' => 8000,
            'total_amount' => 11500,
            'cash_paid' => 11500,
            'change_amount' => 0,
            'payment_method' => 'qris',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($cashier)->get(route('mobile.receipt', $transaction->id));

        $response->assertStatus(200);
        $response->assertSee('INV/20260805/0001');
        $response->assertSee('Ahmad (Online)');
    }

    public function test_customer_can_track_order_without_404(): void
    {
        $tenant = Tenant::create([
            'name' => 'Kantin SD Robbani',
            'code' => 'OUT-SD',
        ]);

        $transaction = Transaction::create([
            'invoice_number' => 'INV/20260806/0004',
            'tenant_id' => $tenant->id,
            'user_id' => null,
            'customer_name' => 'Budi Siswa',
            'customer_phone' => '081234567890',
            'order_type' => 'dine_in',
            'order_source' => 'customer_app',
            'order_status' => 'paid',
            'total_hpp' => 5000,
            'total_amount' => 10000,
            'cash_paid' => 10000,
            'change_amount' => 0,
            'payment_method' => 'qris',
            'status' => 'completed',
        ]);

        $response = $this->get(route('shop.track', $transaction->id));

        $response->assertStatus(200);
        $response->assertSee('INV/20260806/0004');
        $response->assertSee('Budi Siswa');
    }

    public function test_mobile_smart_input_can_restock_existing_product(): void
    {
        $tenant = Tenant::create([
            'name' => 'Outlet Restok',
            'code' => 'OUT-RESTOK',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Kasir Mobile',
            'email' => 'kasir_restok@test.com',
            'password' => bcrypt('password'),
            'role' => 'cashier',
        ]);

        $product = Product::create([
            'tenant_id' => $tenant->id,
            'name' => 'Teh Botol Sosro',
            'barcode' => '888123456',
            'hpp' => 2000,
            'harga_jual' => 4000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->post('/m/smart-input', [
            'mode' => 'update',
            'product_id' => $product->id,
            'stock_action' => 'add',
            'stock' => 15,
        ]);

        $response->assertRedirect(route('mobile.dashboard'));
        $this->assertEquals(25, $product->fresh()->stock);

        // Test set stock action
        $responseSet = $this->post('/m/smart-input', [
            'mode' => 'update',
            'product_id' => $product->id,
            'stock_action' => 'set',
            'stock' => 100,
        ]);

        $responseSet->assertRedirect(route('mobile.dashboard'));
        $this->assertEquals(100, $product->fresh()->stock);
    }
}

