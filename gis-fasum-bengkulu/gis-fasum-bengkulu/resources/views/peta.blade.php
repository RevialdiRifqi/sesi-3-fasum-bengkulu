<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Peta Fasilitas Umum Kota Bengkulu</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
 :root{--ink:#1d2a2a;--bg:#f6f7f4;--line:#d9ddd5}
 *{box-sizing:border-box}
 body{margin:0;font:15px/1.5 system-ui,sans-serif;color:var(--ink);background:var(--bg);display:grid;grid-template-columns:340px 1fr;height:100vh}
 aside{padding:16px;overflow:auto;border-right:1px solid var(--line)}
 h1{font-size:1.25rem;margin:0 0 12px}
 input,select,button{font:inherit;padding:8px;border:1px solid var(--line);border-radius:6px;width:100%;margin-bottom:8px;background:#fff}
 button{background:var(--ink);color:#fff;cursor:pointer}
 button.alt{background:#fff;color:var(--ink)}
 :focus-visible{outline:3px solid #e0a100;outline-offset:2px}
 ul{list-style:none;padding:0;margin:12px 0}
 li{padding:10px 0;border-bottom:1px solid var(--line);cursor:pointer}
 li b{display:block}
 .tag{font-size:.8rem;color:#fff;padding:1px 8px;border-radius:99px}
 #map{height:100%}
 #hint{font-size:.85rem;color:#5a6767}
 @media(max-width:720px){body{grid-template-columns:1fr;grid-template-rows:45vh 1fr}#map{order:-1}}
</style>
</head>
<body>
<aside>
 <h1>Fasilitas umum Kota Bengkulu</h1>
 <input id="q" type="search" placeholder="Cari nama fasilitas" aria-label="Cari nama fasilitas">
 <select id="kategori" aria-label="Kategori">
  <option value="">Semua kategori</option>
  <option value="sekolah">Sekolah</option>
  <option value="rumah_sakit">Rumah sakit</option>
  <option value="pemerintahan">Kantor pemerintahan</option>
 </select>
 <button id="near">Cari terdekat dari saya</button>
 <button id="add" class="alt">Tambah fasilitas dari peta</button>
 <p id="hint"></p>
 <ul id="list"></ul>
</aside>
<div id="map"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const LABEL = {sekolah:'Sekolah', rumah_sakit:'Rumah sakit', pemerintahan:'Kantor pemerintahan'};
const COLOR = {sekolah:'#1f6f8b', rumah_sakit:'#b3261e', pemerintahan:'#6b5b1f'};
const map = L.map('map').setView([-3.7928, 102.2608], 13);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',
  {maxZoom: 19, attribution: '&copy; OpenStreetMap contributors'}).addTo(map);

let layer = L.layerGroup().addTo(map), me = null, adding = false;
const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

async function load() {
  const p = new URLSearchParams({q: $('q').value, kategori: $('kategori').value});
  if (me) { p.set('lat', me.lat); p.set('lng', me.lng); }
  [...p.keys()].forEach(k => { if (!p.get(k)) p.delete(k); });
  const res = await fetch('/api/fasilitas?' + p);
  const fc = await res.json();
  layer.clearLayers(); $('list').innerHTML = '';
  if (!fc.features.length) { $('hint').textContent = 'Tidak ada fasilitas yang cocok. Ubah kata kunci atau kategori.'; return; }
  $('hint').textContent = fc.features.length + ' fasilitas ditemukan.';
  fc.features.forEach(f => {
    const [lng, lat] = f.geometry.coordinates, pr = f.properties;
    const jarak = pr.jarak_km != null ? ` (${pr.jarak_km} km)` : '';
    const m = L.circleMarker([lat, lng], {radius: 9, color: '#fff', weight: 2, fillColor: COLOR[pr.kategori], fillOpacity: 1})
      .bindPopup(`<b>${esc(pr.nama)}</b><br>${LABEL[pr.kategori]}<br>${esc(pr.alamat)}${jarak}`).addTo(layer);
    const li = document.createElement('li');
    li.tabIndex = 0;
    li.innerHTML = `<b>${esc(pr.nama)}${jarak}</b><span class="tag" style="background:${COLOR[pr.kategori]}">${LABEL[pr.kategori]}</span>`;
    const go = () => { map.setView([lat, lng], 16); m.openPopup(); };
    li.onclick = go; li.onkeydown = e => { if (e.key === 'Enter') go(); };
    $('list').appendChild(li);
  });
}

$('q').oninput = load; $('kategori').onchange = load;
$('near').onclick = () => {
  if (!navigator.geolocation) { $('hint').textContent = 'Browser tidak mendukung geolokasi.'; return; }
  navigator.geolocation.getCurrentPosition(pos => {
    me = {lat: pos.coords.latitude, lng: pos.coords.longitude};
    L.marker([me.lat, me.lng]).addTo(map).bindPopup('Lokasi Anda');
    map.setView([me.lat, me.lng], 14); load();
  }, () => { $('hint').textContent = 'Izin lokasi ditolak. Aktifkan izin lokasi di browser.'; });
};
$('add').onclick = () => { adding = true; $('hint').textContent = 'Klik lokasi fasilitas pada peta.'; };
map.on('click', async e => {
  if (!adding) return; adding = false;
  const nama = prompt('Nama fasilitas:'); if (!nama) return;
  const kategori = prompt('Kategori (sekolah / rumah_sakit / pemerintahan):');
  const res = await fetch('/api/fasilitas', {method: 'POST',
    headers: {'Content-Type': 'application/json', 'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
    body: JSON.stringify({nama, kategori, latitude: e.latlng.lat, longitude: e.latlng.lng})});
  $('hint').textContent = res.ok ? 'Fasilitas tersimpan.' : 'Gagal menyimpan. Periksa nama dan kategori.';
  load();
});
load();
</script>
</body>
</html>
