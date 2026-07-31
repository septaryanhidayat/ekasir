<?php

namespace Database\Seeders;

use App\Models\CashFlow;
use App\Models\CashRegister;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Outlets / Tenants
        $tenantPusat = Tenant::create([
            'name' => 'Kantin Pusat Telkom',
            'code' => 'OUT-001',
            'address' => 'Jl. Gatot Subroto No. 52, Jakarta Selatan',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        $tenantKemang = Tenant::create([
            'name' => 'Outlet Branch Kemang',
            'code' => 'OUT-002',
            'address' => 'Jl. Kemang Raya No. 12, Jakarta Selatan',
            'phone' => '081987654321',
            'is_active' => true,
        ]);

        $tenantBSD = Tenant::create([
            'name' => 'Kantin Cabang BSD',
            'code' => 'OUT-003',
            'address' => 'Ruko BSD Green Office Park, Tangerang',
            'phone' => '085612345678',
            'is_active' => true,
        ]);

        // 2. Create Users
        $superadmin = User::create([
            'tenant_id' => null,
            'name' => 'Budi Santoso (Owner)',
            'email' => 'owner@ekasir.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'pin' => '123456',
            'phone' => '081111111111',
        ]);

        $managerKemang = User::create([
            'tenant_id' => $tenantKemang->id,
            'name' => 'Siti Rahma (Manager)',
            'email' => 'manager.kemang@ekasir.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'pin' => '123456',
            'phone' => '082222222222',
        ]);

        $cashierKemang = User::create([
            'tenant_id' => $tenantKemang->id,
            'name' => 'Andi Kasir',
            'email' => 'kasir.kemang@ekasir.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'pin' => '123456',
            'phone' => '083333333333',
        ]);

        $cashierPusat = User::create([
            'tenant_id' => $tenantPusat->id,
            'name' => 'Dewi Kasir',
            'email' => 'kasir.pusat@ekasir.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'pin' => '123456',
            'phone' => '084444444444',
        ]);

        // 3. Create Products for Kemang and Pusat
        $productsKemang = [
            ['name' => 'Indomie Goreng Spesial', 'barcode' => '8992388123456', 'hpp' => 2800, 'harga_jual' => 3500, 'stock' => 150, 'image' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Kopi Kapal Api Mix 25g', 'barcode' => '8991001100114', 'hpp' => 1200, 'harga_jual' => 2000, 'stock' => 90, 'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Air Mineral Aqua 600ml', 'barcode' => '8992701000010', 'hpp' => 2500, 'harga_jual' => 4000, 'stock' => 220, 'image' => 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Teh Botol Sosro 450ml', 'barcode' => '8991002100021', 'hpp' => 4000, 'harga_jual' => 6000, 'stock' => 75, 'image' => 'https://images.unsplash.com/photo-1556881286-fc6915169721?auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Chitato Sapi Panggang 68g', 'barcode' => '8992751100012', 'hpp' => 8000, 'harga_jual' => 11500, 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Roti Sobek Cokelat', 'barcode' => '8993005100099', 'hpp' => 11000, 'harga_jual' => 15000, 'stock' => 35, 'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Es Teh Manis Jumbo', 'barcode' => 'POS-ESTEH-01', 'hpp' => 2000, 'harga_jual' => 5000, 'stock' => 500, 'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Nasi Goreng Spesial', 'barcode' => 'POS-NASGOR-01', 'hpp' => 12000, 'harga_jual' => 20000, 'stock' => 100, 'image' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=400&q=80'],
        ];

        foreach ($productsKemang as $p) {
            Product::create(array_merge($p, ['tenant_id' => $tenantKemang->id, 'is_active' => true]));
            Product::create(array_merge($p, ['tenant_id' => $tenantPusat->id, 'is_active' => true]));
            Product::create(array_merge($p, ['tenant_id' => $tenantBSD->id, 'is_active' => true]));
        }

        // 4. Open Cash Register for Kemang
        $registerKemang = CashRegister::create([
            'tenant_id' => $tenantKemang->id,
            'user_id' => $cashierKemang->id,
            'opening_amount' => 200000,
            'status' => 'open',
            'opened_at' => now()->subHours(4),
            'notes' => 'Shift Pagi Kemang',
        ]);

        // Cash flow entries
        CashFlow::create([
            'tenant_id' => $tenantKemang->id,
            'cash_register_id' => $registerKemang->id,
            'user_id' => $cashierKemang->id,
            'type' => 'out',
            'amount' => 15000,
            'description' => 'Beli Es Batu 2 Plastik & Sedotan',
        ]);

        CashFlow::create([
            'tenant_id' => $tenantKemang->id,
            'cash_register_id' => $registerKemang->id,
            'user_id' => $cashierKemang->id,
            'type' => 'in',
            'amount' => 50000,
            'description' => 'Tambahan Pecahan Rp 5.000 untuk Kembalian',
        ]);

        // 5. Seed Transactions for Kemang & Pusat
        $prodIndomie = Product::withoutGlobalScopes()->where('tenant_id', $tenantKemang->id)->where('barcode', '8992388123456')->first();
        $prodAqua = Product::withoutGlobalScopes()->where('tenant_id', $tenantKemang->id)->where('barcode', '8992701000010')->first();
        $prodNasgor = Product::withoutGlobalScopes()->where('tenant_id', $tenantKemang->id)->where('barcode', 'POS-NASGOR-01')->first();

        // Transaction 1
        $t1 = Transaction::create([
            'invoice_number' => 'INV/' . date('Ymd') . '/0001',
            'tenant_id' => $tenantKemang->id,
            'user_id' => $cashierKemang->id,
            'cash_register_id' => $registerKemang->id,
            'total_hpp' => ($prodIndomie->hpp * 2) + ($prodAqua->hpp * 1),
            'total_amount' => ($prodIndomie->harga_jual * 2) + ($prodAqua->harga_jual * 1),
            'cash_paid' => 50000,
            'change_amount' => 50000 - (($prodIndomie->harga_jual * 2) + ($prodAqua->harga_jual * 1)),
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_at' => now()->subHours(3),
        ]);

        TransactionDetail::create([
            'transaction_id' => $t1->id,
            'product_id' => $prodIndomie->id,
            'product_name' => $prodIndomie->name,
            'cost_price' => $prodIndomie->hpp,
            'selling_price' => $prodIndomie->harga_jual,
            'qty' => 2,
            'subtotal' => $prodIndomie->harga_jual * 2,
        ]);

        TransactionDetail::create([
            'transaction_id' => $t1->id,
            'product_id' => $prodAqua->id,
            'product_name' => $prodAqua->name,
            'cost_price' => $prodAqua->hpp,
            'selling_price' => $prodAqua->harga_jual,
            'qty' => 1,
            'subtotal' => $prodAqua->harga_jual * 1,
        ]);

        // Transaction 2
        $t2 = Transaction::create([
            'invoice_number' => 'INV/' . date('Ymd') . '/0002',
            'tenant_id' => $tenantKemang->id,
            'user_id' => $cashierKemang->id,
            'cash_register_id' => $registerKemang->id,
            'total_hpp' => ($prodNasgor->hpp * 1),
            'total_amount' => ($prodNasgor->harga_jual * 1),
            'cash_paid' => 20000,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_at' => now()->subHour(),
        ]);

        TransactionDetail::create([
            'transaction_id' => $t2->id,
            'product_id' => $prodNasgor->id,
            'product_name' => $prodNasgor->name,
            'cost_price' => $prodNasgor->hpp,
            'selling_price' => $prodNasgor->harga_jual,
            'qty' => 1,
            'subtotal' => $prodNasgor->harga_jual * 1,
        ]);
    }
}
