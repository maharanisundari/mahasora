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
            ]
        );
    }
}
