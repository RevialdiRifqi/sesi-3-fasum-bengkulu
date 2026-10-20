<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    protected $table = 'fasilitas';
    protected $fillable = ['nama', 'kategori', 'alamat', 'telepon', 'latitude', 'longitude'];
    protected $casts = ['latitude' => 'float', 'longitude' => 'float'];

    // Jarak (km) dengan rumus Haversine, urut dari yang terdekat.
    public function scopeNearest($q, float $lat, float $lng)
    {
        $d = '(6371 * acos(least(1, cos(radians(?)) * cos(radians(latitude))
              * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))))';
        return $q->selectRaw("fasilitas.*, $d as jarak_km", [$lat, $lng, $lat])->orderBy('jarak_km');
    }
}
