<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'description',
        'address',
        'phone',
        'email',
        'whatsapp',
        'instagram',
        'facebook',
        'logo',
        'opening_hours',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'payment_instructions',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'store_name' => 'MahaSora',
                'description' => 'MahaSora adalah usaha jasa kreatif SMKN 1 Katapang — melayani pembuatan website, desain branding, service laptop, kursus coding, foto produk, dan print dokumen dengan kualitas TeFa.',
                'address' => 'Jl. Katapang No. 1, Katapang, Kab. Bandung, Jawa Barat 40921',
                'phone' => '081234567890',
                'email' => 'halo@mahasora.test',
                'whatsapp' => '081234567890',
                'instagram' => '@mahasora.id',
                'facebook' => 'MahaSora Official',
                'opening_hours' => 'Senin - Sabtu, 08:00 - 17:00 WIB',
                'bank_name' => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_name' => 'MahaSora Official',
                'payment_instructions' => "DP 50% wajib sebelum diproses.\nCash: bayar di toko\nTransfer Bank: BCA 1234567890 a.n MahaSora\nDANA/OVO/GoPay/ShopeePay: 081234567890 a.n MahaSora\nSetelah transfer, konfirmasi via WA admin dengan kirim bukti.",
            ]
        );
    }
}
