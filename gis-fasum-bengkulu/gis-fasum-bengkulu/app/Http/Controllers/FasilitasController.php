<?php
namespace App\Http\Controllers;

use App\Models\Fasilitas;
use Illuminate\Http\Request;

class FasilitasController extends Controller
{
    public function index()
    {
        return view('peta');
    }

    // GeoJSON; opsional ?kategori=, ?q=, ?lat=&lng= (urut terdekat)
    public function geojson(Request $r)
    {
        $q = Fasilitas::query();
        if ($r->filled('kategori')) $q->where('kategori', $r->kategori);
        if ($r->filled('q')) $q->where('nama', 'like', '%' . $r->q . '%');
        if ($r->filled('lat') && $r->filled('lng')) {
            $q->nearest((float) $r->lat, (float) $r->lng);
        }

        $features = $q->get()->map(fn ($f) => [
            'type' => 'Feature',
            'geometry' => ['type' => 'Point', 'coordinates' => [$f->longitude, $f->latitude]],
            'properties' => [
                'id' => $f->id, 'nama' => $f->nama, 'kategori' => $f->kategori,
                'alamat' => $f->alamat, 'telepon' => $f->telepon,
                'jarak_km' => isset($f->jarak_km) ? round($f->jarak_km, 2) : null,
            ],
        ]);

        return response()->json(['type' => 'FeatureCollection', 'features' => $features]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:sekolah,rumah_sakit,pemerintahan',
            'alamat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:30',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);
        return response()->json(Fasilitas::create($data), 201);
    }
}
