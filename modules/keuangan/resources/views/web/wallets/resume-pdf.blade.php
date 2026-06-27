<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resume Dompet - {{ $wallet->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #386650;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #386650;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }
        .summary-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            width: 33.33%;
        }
        .summary-label {
            display: block;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
        }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .text-dark { color: #1f2937; }
        
        .transactions-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .tx-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .tx-table th {
            background-color: #f8f9fa;
            color: #333;
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .tx-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 10px;
            color: white;
            display: inline-block;
        }
        .badge-income { background-color: #16a34a; }
        .badge-expense { background-color: #dc2626; }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Resume Keuangan</h1>
        <p>Dompet: <strong>{{ $wallet->name }}</strong> | Dicetak pada: {{ date('d M Y H:i') }}</p>
    </div>

    <table class="summary-table">
        <tr>
            <td>
                <span class="summary-label">Total Pemasukan</span>
                <span class="summary-value text-green">Rp {{ number_format($allTimeIncome, 0, ',', '.') }}</span>
            </td>
            <td>
                <span class="summary-label">Total Pengeluaran</span>
                <span class="summary-value text-red">Rp {{ number_format($allTimeExpense, 0, ',', '.') }}</span>
            </td>
            <td>
                <span class="summary-label">Saldo Bersih</span>
                <span class="summary-value text-dark">Rp {{ number_format($balance, 0, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    <div class="transactions-title">Daftar Transaksi</div>
    
    @if($transactions->count() > 0)
    <table class="tx-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th>Jenis</th>
                <th style="text-align: right;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
            <tr>
                <td>{{ \Carbon\Carbon::parse($tx->transaction_date)->format('d M Y') }}</td>
                <td>{{ $tx->category->name ?? '-' }}</td>
                <td>{{ $tx->description ?: '-' }}</td>
                <td>
                    @if($tx->type == 'income')
                        <span class="badge badge-income">Pemasukan</span>
                    @else
                        <span class="badge badge-expense">Pengeluaran</span>
                    @endif
                </td>
                <td style="text-align: right;">
                    <span class="{{ $tx->type == 'income' ? 'text-green' : 'text-red' }}">
                        {{ $tx->type == 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p style="text-align: center; color: #666; margin-top: 20px;">Belum ada transaksi di dompet ini.</p>
    @endif

    <div class="footer">
        Dicetak dari {{ config('app.name', 'Radiohead Wallet') }} - Solusi Pencatatan Keuangan Anda
    </div>

</body>
</html>
