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

    public function test_mobile_smart_input_and_desktop_support_supplier(): void
    {
        $tenant = Tenant::create([
            'name' => 'Outlet Supplier Test',
            'code' => 'OUT-SUPP',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin Supplier',
            'email' => 'admin_supp@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $supplier = \App\Models\Supplier::create([
            'tenant_id' => $tenant->id,
            'name' => 'PT Indofood Sukses',
            'code' => 'SUP-INDO',
            'phone' => '08123456789',
        ]);

        $this->actingAs($user);

        // Test Mobile smart input create with supplier_id
        $respMobile = $this->post('/m/smart-input', [
            'mode' => 'new',
            'name' => 'Indomie Goreng',
            'supplier_id' => $supplier->id,
            'hpp' => 2500,
            'harga_jual' => 3500,
            'stock' => 50,
        ]);
        $respMobile->assertRedirect(route('mobile.dashboard'));

        $product = Product::where('name', 'Indomie Goreng')->first();
        $this->assertNotNull($product);
        $this->assertEquals($supplier->id, $product->supplier_id);

        // Test Desktop store restock mode with supplier_id update
        $respDesktop = $this->post(route('desktop.products.store'), [
            'mode' => 'update',
            'product_id' => $product->id,
            'stock_action' => 'add',
            'stock' => 20,
            'supplier_id' => $supplier->id,
            'hpp' => 2600,
            'harga_jual' => 3800,
        ]);
        $respDesktop->assertSessionHasNoErrors();

        $updatedProduct = $product->fresh();
        $this->assertEquals(70, $updatedProduct->stock);
        $this->assertEquals(2600, $updatedProduct->hpp);
        $this->assertEquals(3800, $updatedProduct->harga_jual);
        $this->assertEquals($supplier->id, $updatedProduct->supplier_id);
    }

    public function test_authenticated_user_can_download_database_backup(): void
    {
        $tenant = Tenant::create([
            'name' => 'Outlet Backup Test',
            'code' => 'OUT-BACKUP',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin Backup',
            'email' => 'admin_backup@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('backup.download'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/sql');
        $this->assertStringContainsString('E-KASIR POS DATABASE BACKUP', $response->getContent());
        $this->assertTrue(str_contains($response->getContent(), 'FOREIGN_KEY_CHECKS=0') || str_contains($response->getContent(), 'foreign_keys = OFF'));
        $this->assertStringContainsString('INSERT INTO `users`', $response->getContent());
    }

    public function test_authenticated_user_can_import_database_backup(): void
    {
        $tenant = Tenant::create([
            'name' => 'Outlet Import Test',
            'code' => 'OUT-IMPORT',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin Import',
            'email' => 'admin_import@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $dummySql = "-- Dummy MySQL Backup\nSET FOREIGN_KEY_CHECKS=0;\nDELETE FROM `tenants`;\nINSERT INTO `tenants` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES (999, 'Restored Tenant', 'OUT-RESTORED', '2026-08-10 10:45:00', '2026-08-10 10:45:00');\nSET FOREIGN_KEY_CHECKS=1;\n";
        $tempPath = sys_get_temp_dir() . '/backup_test.sql';
        file_put_contents($tempPath, $dummySql);
        $file = new \Illuminate\Http\UploadedFile($tempPath, 'backup.sql', 'text/plain', null, true);

        $response = $this->post(route('backup.import'), [
            'backup_file' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tenants', [
            'id' => 999,
            'name' => 'Restored Tenant',
            'code' => 'OUT-RESTORED',
        ]);
    }

    public function test_image_optimizer_compresses_uploaded_file_under_50kb(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()->image('large_product.jpg', 1500, 1500);
        $path = \App\Services\ImageOptimizer::compressAndStore($file);

        $this->assertNotNull($path);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($path);

        $sizeInBytes = strlen(\Illuminate\Support\Facades\Storage::disk('public')->get($path));
        $this->assertLessThan(50 * 1024, $sizeInBytes, "Compressed image size should be less than 50KB");
    }

    public function test_updating_product_image_compresses_and_stores_file(): void
    {
        $tenant = Tenant::create([
            'name' => 'Outlet Image Test',
            'code' => 'OUT-IMG',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin Image',
            'email' => 'admin_image@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $product = Product::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test Product Image',
            'barcode' => 'BRD-TEST-IMG',
            'hpp' => 1000,
            'harga_jual' => 2000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $file = \Illuminate\Http\UploadedFile::fake()->image('new_product.jpg', 800, 800);

        $response = $this->put(route('desktop.products.update', $product->id), [
            'name' => 'Test Product Image Updated',
            'hpp' => 1000,
            'harga_jual' => 2000,
            'stock' => 10,
            'image' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $updatedProduct = $product->fresh();
        $this->assertNotNull($updatedProduct->image);
        $this->assertStringContainsString('products/', $updatedProduct->image);
    }

    public function test_compress_product_images_command_compresses_all_existing_large_photos(): void
    {
        $tenant = Tenant::create(['name' => 'Outlet Bulk Compress', 'code' => 'OUT-BULK']);

        $largeFile = \Illuminate\Http\UploadedFile::fake()->image('old_large.jpg', 2000, 2000);
        $storedPath = $largeFile->store('products', 'public');

        $product = Product::create([
            'tenant_id' => $tenant->id,
            'name' => 'Old Large Product',
            'barcode' => 'BRD-OLD-LARGE',
            'hpp' => 1000,
            'harga_jual' => 2000,
            'stock' => 10,
            'image' => $storedPath,
            'is_active' => true,
        ]);

        $this->artisan('products:compress-images')
            ->assertExitCode(0);

        $product->refresh();
        $this->assertNotNull($product->image);
        $this->assertStringEndsWith('.webp', $product->image);
    }
}


