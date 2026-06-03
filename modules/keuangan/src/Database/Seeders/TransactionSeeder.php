<?php

namespace Modules\Keuangan\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Keuangan\Models\Category;
use Modules\Keuangan\Models\Transaction;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Membuat data dummy transaksi selama 30 hari terakhir
     * untuk user demo.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name'     => 'Demo User',
                'password' => Hash::make('password'),
            ]
        );

        // Pastikan kategori sudah ada
        $incomeCategories  = Category::where('user_id', $user->id)->where('type', 'income')->get();
        $expenseCategories = Category::where('user_id', $user->id)->where('type', 'expense')->get();

        if ($incomeCategories->isEmpty() || $expenseCategories->isEmpty()) {
            $this->command->warn('Jalankan CategorySeeder terlebih dahulu!');
            return;
        }

        // Template deskripsi
        $incomeDescriptions = [
            'Gaji bulanan', 'Proyek freelance', 'Dividen saham', 'Bonus kinerja',
            'Pembayaran klien', 'Penghasilan sampingan', 'Cashback belanja',
            'Transfer masuk', 'Komisi penjualan', 'Refund pembelian',
        ];

        $expenseDescriptions = [
            'Makan siang', 'Beli kopi', 'Bensin motor', 'Belanja mingguan',
            'Bayar listrik', 'Langganan streaming', 'Grab/Gojek',
            'Belanja online', 'Obat-obatan', 'Makan malam', 'Parkir',
            'Pulsa internet', 'Laundry', 'Snack kantor', 'Bayar air',
        ];

        $today = Carbon::today();

        // Generate transaksi untuk 30 hari terakhir
        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);

            // --- Pemasukan ---
            // Hari ke-1 setiap minggu: gaji/pemasukan besar
            if ($i % 7 === 0) {
                Transaction::create([
                    'user_id'          => $user->id,
                    'category_id'      => $incomeCategories->where('name', 'Gaji')->first()?->id ?? $incomeCategories->random()->id,
                    'type'             => 'income',
                    'amount'           => rand(3000000, 6000000),
                    'description'      => 'Gaji bulanan',
                    'transaction_date' => $date,
                ]);
            }

            // Pemasukan acak (30% kemungkinan per hari)
            if (rand(1, 100) <= 30) {
                Transaction::create([
                    'user_id'          => $user->id,
                    'category_id'      => $incomeCategories->random()->id,
                    'type'             => 'income',
                    'amount'           => rand(50000, 1500000),
                    'description'      => $incomeDescriptions[array_rand($incomeDescriptions)],
                    'transaction_date' => $date,
                ]);
            }

            // --- Pengeluaran ---
            // Pengeluaran harian kecil (makan, transport) — 1-3 transaksi per hari
            $dailyExpenseCount = rand(1, 3);
            for ($j = 0; $j < $dailyExpenseCount; $j++) {
                Transaction::create([
                    'user_id'          => $user->id,
                    'category_id'      => $expenseCategories->random()->id,
                    'type'             => 'expense',
                    'amount'           => rand(15000, 150000),
                    'description'      => $expenseDescriptions[array_rand($expenseDescriptions)],
                    'transaction_date' => $date,
                ]);
            }

            // Pengeluaran besar sesekali (20% kemungkinan — belanja, tagihan)
            if (rand(1, 100) <= 20) {
                Transaction::create([
                    'user_id'          => $user->id,
                    'category_id'      => $expenseCategories->whereIn('name', ['Belanja', 'Tagihan'])->random()->id,
                    'type'             => 'expense',
                    'amount'           => rand(200000, 800000),
                    'description'      => $expenseDescriptions[array_rand($expenseDescriptions)],
                    'transaction_date' => $date,
                ]);
            }
        }

        $totalCreated = Transaction::where('user_id', $user->id)->count();
        $this->command->info("Berhasil membuat {$totalCreated} transaksi dummy untuk 30 hari terakhir.");
    }
}
