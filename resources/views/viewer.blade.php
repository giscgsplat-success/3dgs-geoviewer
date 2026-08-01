<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>3DGS GeoViewer</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap');
*{margin:0;padding:0;box-sizing:border-box}
html,body{width:100%;height:100%;overflow:hidden;background:#0D1117;color:#E2E8F0;font-family:'Space Grotesk',sans-serif;font-size:13px}
:root{--teal:#2DD4BF;--amber:#F97316;--blue:#38BDF8;--green:#4ADE80;--red:#F87171;--panel:#111827;--panel2:#1C2B3A;--border:#1E3A5F;--muted:#64748B;--mono:'JetBrains Mono',monospace}
.app{display:grid;grid-template-rows:44px 1fr 32px;height:100vh}
nav{background:var(--panel);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 16px;gap:16px}
.brand{display:flex;align-items:center;gap:8px;font-weight:600}
.dot{width:8px;height:8px;border-radius:50%;background:var(--teal);box-shadow:0 0 8px var(--teal)}
.badge{background:rgba(45,212,191,.15);color:var(--teal);padding:1px 6px;border-radius:3px;font-size:10px;font-family:var(--mono)}
.nav-coords{margin-left:auto;display:flex;gap:12px;font-family:var(--mono);font-size:11px;color:var(--muted)}
.nav-coords b{color:var(--teal)}
.nav-tab-btns{display:flex;gap:2px;margin-left:16px}
.nav-tab-btn{padding:3px 10px;border-radius:4px;font-size:11px;cursor:pointer;color:var(--muted);border:1px solid transparent;transition:all .15s}
.nav-tab-btn.active{background:rgba(45,212,191,.15);color:var(--teal);border-color:var(--teal)}
.main{display:grid;grid-template-columns:200px 1fr 180px;overflow:hidden}
/* Split mode: 3D viewer + basemap side by side */
.main.split-mode{grid-template-columns:200px 1fr 1fr}
.main.map-only{grid-template-columns:200px 1fr 180px}
.sidebar{background:var(--panel);border-right:1px solid var(--border);padding:10px;display:flex;flex-direction:column;gap:12px;overflow-y:auto}
.sec-label{font-size:10px;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);padding-bottom:4px;border-bottom:1px solid var(--border);margin-bottom:4px}
.method-card{border:1px solid var(--border);border-radius:6px;padding:8px 10px;cursor:pointer;transition:all .15s}
.method-card:hover{border-color:var(--teal);background:rgba(45,212,191,.04)}
.method-card.active{border-color:var(--teal);background:rgba(45,212,191,.08)}
.mc-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:5px}
.mc-name{font-weight:600;font-size:12px}
.mc-dot{width:7px;height:7px;border-radius:50%}
.mrow{display:flex;justify-content:space-between;margin-top:2px}
.mk{font-size:11px;color:var(--muted)}.mv{font-family:var(--mono);font-size:11px}
.good{color:var(--green)}.warn{color:var(--amber)}.err{color:var(--red)}.info{color:var(--blue)}
.layer-item{display:flex;align-items:center;gap:7px;padding:4px 0;cursor:pointer}
.layer-cb{width:14px;height:14px;border:1px solid var(--border);border-radius:3px;display:flex;align-items:center;justify-content:center;font-size:10px;transition:all .15s;flex-shrink:0}
.layer-cb.on{background:var(--teal);border-color:var(--teal);color:#0D1117}
.layer-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.layer-label{font-size:11px;color:var(--muted)}
.layer-item.on .layer-label{color:#E2E8F0}
/* Canvas & Map wrap */
.canvas-wrap{position:relative;background:#0D1117}
.map-wrap{position:relative;border-left:1px solid var(--border);display:none;overflow:hidden}
.map-wrap.visible{display:block}
canvas{width:100%!important;height:100%!important;display:block}
#cesiumContainer{width:100%;height:100%;position:absolute;top:0;left:0}
/* Map toolbar — positioned inside map-wrap */
.map-toolbar{position:absolute;top:8px;left:8px;display:flex;flex-direction:column;gap:4px;z-index:100}
.map-tile-btn{background:rgba(13,17,23,.9);border:1px solid var(--border);border-radius:4px;padding:3px 8px;font-size:10px;font-family:var(--mono);color:var(--muted);cursor:pointer;transition:all .15s;white-space:nowrap}
.map-tile-btn.active{background:var(--teal);color:#0D1117;border-color:var(--teal)}
.map-label{position:absolute;top:8px;right:8px;background:rgba(13,17,23,.85);border:1px solid var(--border);border-radius:4px;padding:3px 8px;font-size:10px;font-family:var(--mono);color:var(--teal);z-index:100}
.footprint-info{position:absolute;bottom:16px;left:16px;background:rgba(13,17,23,.9);border:1px solid var(--teal);border-radius:4px;padding:5px 8px;font-size:10px;font-family:var(--mono);color:var(--text);z-index:100;pointer-events:none}
.hud{position:absolute;top:10px;left:10px;display:flex;flex-direction:column;gap:4px;pointer-events:none}
.hud-chip{background:rgba(13,17,23,.85);border:1px solid var(--border);border-radius:4px;padding:3px 8px;font-family:var(--mono);font-size:10px;color:var(--teal)}
.view-btns{position:absolute;top:10px;right:10px;display:flex;flex-direction:column;gap:3px}
.vbtn{background:rgba(13,17,23,.85);border:1px solid var(--border);border-radius:4px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--muted);font-size:14px;transition:all .15s}
.vbtn:hover{border-color:var(--teal);color:var(--teal)}
.vbtn.on{background:var(--teal);color:#0D1117;border-color:var(--teal)}
.hint{position:absolute;bottom:10px;left:50%;transform:translateX(-50%);font-size:10px;color:var(--muted);pointer-events:none;white-space:nowrap}
.loading-bar{position:absolute;bottom:0;left:0;height:2px;background:var(--teal);transition:width .3s;width:0}
.info{background:var(--panel);border-left:1px solid var(--border);padding:10px;display:flex;flex-direction:column;gap:10px;overflow-y:auto}
.stat-card{background:var(--panel2);border:1px solid var(--border);border-radius:6px;padding:8px}
.stat-label{font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)}
.stat-val{font-family:var(--mono);font-size:20px;font-weight:500;margin:2px 0}
.stat-unit{font-size:10px;color:var(--muted)}
.bar-wrap{height:4px;background:var(--border);border-radius:2px;margin-top:6px;overflow:hidden}
.bar-fill{height:100%;border-radius:2px;transition:width .8s ease}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:5px}
.cell{background:var(--panel2);border:1px solid var(--border);border-radius:4px;padding:5px 7px;text-align:center}
.cell-label{font-size:9px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted)}
.cell-val{font-family:var(--mono);font-size:12px;font-weight:500;margin-top:2px}
.ttest{background:rgba(74,222,128,.06);border:1px solid rgba(74,222,128,.2);border-radius:6px;padding:8px}
.trow{display:flex;justify-content:space-between;font-size:11px;color:var(--muted);margin-bottom:2px}
.trow b{font-family:var(--mono);color:#E2E8F0}
.tconc{font-size:10px;color:var(--green);font-weight:600;margin-top:5px;padding-top:5px;border-top:1px solid rgba(74,222,128,.15)}
.statusbar{background:var(--panel);border-top:1px solid var(--border);display:flex;align-items:center;overflow:hidden}
.sc{display:flex;align-items:center;gap:5px;padding:0 12px;height:100%;border-right:1px solid var(--border);font-family:var(--mono);font-size:10px;white-space:nowrap}
.sc:last-child{border-right:none}.sl{color:var(--muted)}.sv{color:var(--teal)}.sv.a{color:var(--amber)}
.btn-load{margin-top:6px;background:rgba(45,212,191,.15);color:var(--teal);border:1px solid var(--teal);border-radius:4px;padding:4px 8px;font-size:10px;cursor:pointer;width:100%;font-family:var(--mono);transition:all .15s}
.btn-load:hover{background:var(--teal);color:#0D1117}
.btn-load:disabled{opacity:.4;cursor:not-allowed}
/* Cesium overrides */
.cesium-widget-credits{display:none!important}
.cesium-viewer-toolbar{display:none!important}
.cesium-viewer-animationContainer{display:none!important}
.cesium-viewer-timelineContainer{display:none!important}
.cesium-viewer-bottom{display:none!important}
</style>
<link rel="stylesheet" href="https://cesium.com/downloads/cesiumjs/releases/1.120/Build/Cesium/Widgets/widgets.css">
<script src="https://cesium.com/downloads/cesiumjs/releases/1.120/Build/Cesium/Cesium.js"></script>
</head>
<body>
<div class="app">
  <nav>
    <div class="brand"><div class="dot"></div>3DGS GeoViewer<span class="badge">v2.1</span></div>
    <div class="nav-tab-btns">
      <div class="nav-tab-btn" onclick="setViewPanel('3d',this)">🧊 3D Viewer</div>
      <div class="nav-tab-btn" onclick="setViewPanel('split',this)">⊟ Split</div>
      <div class="nav-tab-btn active" onclick="setViewPanel('map',this)">🗺 Basemap</div>
    </div>
    <div class="nav-coords">
      <span>EPSG:<b id="coordEpsg">Lokal</b></span>
      <span>X:<b id="cE">—</b></span>
      <span>Y:<b id="cN">—</b></span>
      <span>Z:<b id="cZ">—</b></span>
    </div>
  </nav>
  <div class="main">
    <div class="sidebar">
      <div>
        <div class="sec-label">Metode Rekonstruksi</div>
        <div style="display:flex;flex-direction:column;gap:5px">
          <div class="method-card active" onclick="setMethod('sfmmvs',this)">
            <div class="mc-head"><span class="mc-name">SfM-MVS</span><div class="mc-dot" style="background:#4ADE80"></div></div>
            <div class="mrow"><span class="mk">RMSE XY</span><span class="mv good">2.54 cm</span></div>
            <div class="mrow"><span class="mk">RMSE Z</span><span class="mv good">1.44 cm</span></div>
            <div class="mrow"><span class="mk">Software</span><span class="mv" style="font-size:10px">Agisoft Metashape</span></div>
            <button class="btn-load" id="btn-sfmmvs" onclick="event.stopPropagation();loadSplat('sfmmvs')">⬇ Load Model Asli</button>
          </div>
          <div class="method-card" onclick="setMethod('3dgs',this)">
            <div class="mc-head"><span class="mc-name">3DGS + Similarity</span><div class="mc-dot" style="background:#F97316"></div></div>
            <div class="mrow"><span class="mk">CE90</span><span class="mv warn">2.64 cm</span></div>
            <div class="mrow"><span class="mk">LE90</span><span class="mv warn">2.47 cm</span></div>
            <div class="mrow"><span class="mk">RMSE 3D</span><span class="mv good">2.30 cm</span></div>
            <button class="btn-load" id="btn-3dgs" onclick="event.stopPropagation();loadSplat('3dgs')">⬇ Load Model Asli</button>
          </div>
          <div class="method-card" onclick="setMethod('geo3dgs',this)">
            <div class="mc-head"><span class="mc-name">GeoRefGS</span><div class="mc-dot" style="background:#818CF8"></div></div>
            <div class="mrow"><span class="mk">CE90</span><span class="mv err">25.53 cm</span></div>
            <div class="mrow"><span class="mk">LE90</span><span class="mv good">2.47 cm</span></div>
            <div class="mrow"><span class="mk">RMSE 3D</span><span class="mv warn">16.89 cm</span></div>
            <button class="btn-load" id="btn-geo3dgs" onclick="event.stopPropagation();loadSplat('geo3dgs')">⬇ Load Model Asli</button>
          </div>
        </div>
      </div>
      <div>
        <div class="sec-label">Layer</div>
        <div class="layer-item on" onclick="toggleLayer('cloud',this)">
          <div class="layer-cb on">✓</div><div class="layer-dot" style="background:#2DD4BF"></div>
          <span class="layer-label">Point Cloud</span>
        </div>
        <div class="layer-item on" onclick="toggleLayer('grid',this)">
          <div class="layer-cb on">✓</div><div class="layer-dot" style="background:#38BDF8"></div>
          <span class="layer-label">Grid Koordinat</span>
        </div>
        <div class="layer-item on" onclick="toggleLayer('gcp',this)">
          <div class="layer-cb on">✓</div><div class="layer-dot" style="background:#F97316"></div>
          <span class="layer-label">GCP (4 titik)</span>
        </div>
        <div class="layer-item on" onclick="toggleLayer('icp',this)">
          <div class="layer-cb on">✓</div><div class="layer-dot" style="background:#F87171"></div>
          <span class="layer-label">ICP (3 titik)</span>
        </div>
      </div>
      <div>
        <div class="sec-label">Info Lokasi</div>
        <div class="mrow"><span class="mk">Objek</span><span class="mv" id="infoObjek" style="font-size:10px">Geosite Stone Garden</span></div>
        <div class="mrow"><span class="mk">Kota</span><span class="mv" id="infoKota" style="font-size:10px">Citatah, Kab. Bandung Barat</span></div>
        <div class="mrow"><span class="mk">Metode</span><span class="mv" id="infoMetode" style="font-size:10px">SfM-MVS</span></div>
        <div class="mrow"><span class="mk">Software</span><span class="mv" id="infoSoftware" style="font-size:10px">Agisoft Metashape</span></div>
        <div class="mrow"><span class="mk">Kamera</span><span class="mv" id="infoKamera" style="font-size:10px">DJI Mavic 3 Enterprise</span></div>
        <div class="mrow"><span class="mk">GCP</span><span class="mv" id="infoGcp">4</span></div>
        <div class="mrow"><span class="mk">ICP</span><span class="mv" id="infoIcp">3</span></div>
        <div class="mrow"><span class="mk">CRS</span><span class="mv" id="infoCrs" style="font-size:10px">Lokal</span></div>
        <div class="mrow"><span class="mk">Format</span><span class="mv" id="infoFormat" style="font-size:10px">PLY (mesh)</span></div>
      </div>
    </div>
    <div class="canvas-wrap" id="canvasWrap">
      <canvas id="mainCanvas"></canvas>
      <div class="hud">
        <div class="hud-chip" id="hMethod">SfM-MVS · Demo</div>
        <div class="hud-chip" id="hVerts">~30,000 titik (demo)</div>
        <div class="hud-chip" id="hFps">-- fps</div>
      </div>
      <div class="view-btns">
        <div class="vbtn" onclick="resetCam()" title="Reset">⌂</div>
        <div class="vbtn on" id="rotBtn" onclick="toggleRot()" title="Auto-rotate">↻</div>
        <div class="vbtn" onclick="doZoom(0.8)">+</div>
        <div class="vbtn" onclick="doZoom(1.2)">−</div>
      </div>
      <div class="hint" id="hint">Klik "Load Model Asli" untuk tampilkan data real · Drag orbit · Scroll zoom</div>
      <div class="loading-bar" id="loadingBar"></div>
    </div>

    <!-- BASEMAP PANEL -->
    <div class="map-wrap" id="mapWrap">
      <div id="cesiumContainer"></div>
      <div class="map-toolbar">
        <div class="map-tile-btn active" id="btnOSM" onclick="setBasemap('osm',this)">🗺 OpenStreetMap</div>
        <div class="map-tile-btn" id="btnSat" onclick="setBasemap('satellite',this)">🛰 Google Satellite</div>
        <div class="map-tile-btn" id="btnTopo" onclick="setBasemap('topo',this)">⛰ Esri Topo</div>
      </div>
      <div class="map-label">EPSG:4326 · WGS84</div>
      <div class="footprint-info">
        📍 Geosite Stone Garden, Citatah<br>
        <span style="color:var(--teal)">6.8244535°S, 107.4382284°E</span><br>
        <span style="color:var(--muted)">UTM 48S: 769483 E, 9244976 N</span>
      </div>
    </div>
    <div class="info">
      <div class="sec-label">Akurasi Georeferensi</div>
      <div class="stat-card">
        <div class="stat-label">CE90 Horizontal</div>
        <div class="stat-val good" id="ce90val">2.54 <span class="stat-unit">cm</span></div>
        <div class="bar-wrap"><div class="bar-fill" id="ce90bar" style="width:13%;background:#4ADE80"></div></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">LE90 Vertikal</div>
        <div class="stat-val warn" id="le90val">— <span class="stat-unit"></span></div>
        <div class="bar-wrap"><div class="bar-fill" id="le90bar" style="width:0%;background:#F97316"></div></div>
      </div>
      <div class="sec-label">RMSE</div>
      <div class="grid2">
        <div class="cell"><div class="cell-label">RMSE H</div><div class="cell-val good" id="rmseH">1.81 cm</div></div>
        <div class="cell"><div class="cell-label">RMSE V</div><div class="cell-val warn" id="rmseV">1.44 cm</div></div>
      </div>
      <div class="sec-label">Jumlah Titik</div>
      <div class="stat-card">
        <div class="stat-label">Point Cloud</div>
        <div class="stat-val" id="pointCount" style="font-size:16px;color:var(--teal)">—</div>
        <div class="stat-unit" id="pointNote">Load model asli untuk lihat data nyata</div>
      </div>
    </div>
  </div>
  <div class="statusbar">
    <div class="sc"><span class="sl">METODE</span><span class="sv" id="sMode">SINGLE · SfM-MVS</span></div>
    <div class="sc"><span class="sl">PIPELINE</span><span class="sv a" id="sPipe">SfM → COLMAP → MVS</span></div>
    <div class="sc"><span class="sl">RENDER</span><span class="sv">WebGL2 · Three.js r169</span></div>
    <div class="sc"><span class="sl">REF</span><span class="sv">Kerbl et al. 2023 · Hou et al. 2026 (GeoRefGS)</span></div>
  </div>
</div>

<script type="importmap">{"imports":{"three":"https://cdn.jsdelivr.net/npm/three@0.169.0/build/three.module.js"}}</script>
<script type="module">
import * as THREE from 'three';

// ── URL file splat di Hugging Face ─────────────────────────────────────────
const HF_BASE = 'https://huggingface.co/datasets/giscgsplat/3dgs-geoviewer-models/resolve/main';
const SPLAT_URLS = {
  sfmmvs:  HF_BASE + '/splat_sfmmvs.ply',
  '3dgs':  HF_BASE + '/splat_3dgs.ply',
  geo3dgs: HF_BASE + '/splat_georefgs.ply',
};

const METHODS = {
  sfmmvs:{
    label:'SfM-MVS', desc:'Dense Mesh',
    ce90:'2.54', le90:'—', rmseH:'1.81', rmseV:'1.44',
    pipe:'UAV → Agisoft Metashape (SfM + MVS)',
    colors:[0x2DD4BF,0x38BDF8,0x4ADE80],
    info:{
      objek:'Geosite Stone Garden', kota:'Citatah, Kab. Bandung Barat',
      metode:'SfM-MVS (Structure from Motion — Multi-View Stereo)',
      software:'Agisoft Metashape Pro',
      kamera:'DJI Mavic 3 Enterprise',
      gcp:'5 titik GCP (Sokkia iM-52 ETS)',
      icp:'3 titik ICP (CP02, G1CP01, G2CP02)',
      crs:'WGS84 / UTM Zone 48S (EPSG:32748)',
      format:'PLY Binary (Dense Mesh)',
      titik:'6,534,772',
      rmse3d:'—', rmseAlign:'—',
    },
    ce90color:'#4ADE80', le90color:'#F97316', ce90pct:13, le90pct:0,
    epsg:'EPSG:32748', coordPrefix:['E','N','H'],
  },
  '3dgs':{
    label:'3DGS + Similarity', desc:'Kerbl + Transform',
    ce90:'2.64', le90:'2.47', rmseH:'1.74', rmseV:'1.50',
    pipe:'SfM → 3DGS (Kerbl 2023) → Similarity Transform (s·R·t)',
    colors:[0xF97316,0xFBBF24,0xF87171],
    info:{
      objek:'Geosite Stone Garden', kota:'Citatah, Kab. Bandung Barat',
      metode:'3D Gaussian Splatting + Similarity Transform',
      software:'3DGS (Kerbl et al. 2023)',
      kamera:'DJI Mavic 3 Enterprise',
      gcp:'5 titik GCP (Sokkia iM-52 ETS)',
      icp:'3 titik ICP (CP02, G1CP01, G2CP02)',
      crs:'WGS84 / UTM Zone 48S (EPSG:32748)',
      format:'PLY Binary (Gaussian Splat)',
      titik:'3,285,986',
      rmse3d:'0.0230 m', rmseAlign:'0.0230 m',
      transform:'s=0.99976, t=[982.10, 2177.39, -1919.80]',
    },
    ce90color:'#F97316', le90color:'#F97316', ce90pct:13, le90pct:4,
    epsg:'EPSG:32748', coordPrefix:['E','N','H'],
  },
  geo3dgs:{
    label:'GeoRefGS', desc:'Joint Georeferencing',
    ce90:'25.53', le90:'2.47', rmseH:'16.83', rmseV:'1.50',
    pipe:'SfM → GeoRefGS (Hou et al. 2026) — joint optimization',
    colors:[0x818CF8,0x38BDF8,0x2DD4BF],
    info:{
      objek:'Geosite Stone Garden', kota:'Citatah, Kab. Bandung Barat',
      metode:'Georeferenced 3DGS (GeoRefGS — Hou et al. 2026)',
      software:'GeoRefGS pipeline',
      kamera:'DJI Mavic 3 Enterprise',
      gcp:'5 titik GCP (CP01, CP03, CP04, G1CP02, G2CP01)',
      icp:'3 titik ICP (CP02, G1CP01, G2CP02)',
      crs:'WGS84 / UTM Zone 48S (EPSG:32748)',
      format:'PLY Binary (Georef Gaussian Splat)',
      titik:'2,063,667',
      rmse3d:'0.1689 m', rmseAlign:'0.0230 m',
      transform:'s=0.99976, t=[982.10, 2177.39, -1919.80]',
    },
    ce90color:'#F87171', le90color:'#F97316', ce90pct:100, le90pct:4,
    epsg:'EPSG:32748', coordPrefix:['E','N','H'],
  },
};

let autoRotate=true, theta=Math.PI/4, phi=Math.PI/5, radius=12;
let currentMethod='sfmmvs', isLoading=false;

const canvas=document.getElementById('mainCanvas');
const scene=new THREE.Scene();
scene.background=new THREE.Color(0x0D1117);
scene.fog=new THREE.FogExp2(0x0D1117,0.025);
const camera=new THREE.PerspectiveCamera(55,1,0.01,500);
const renderer=new THREE.WebGLRenderer({canvas,antialias:true});
renderer.setPixelRatio(Math.min(devicePixelRatio,2));
scene.add(new THREE.AmbientLight(0x223344,3));
const sun=new THREE.DirectionalLight(0xB8D4FF,2);sun.position.set(10,20,10);scene.add(sun);

const gridObj=new THREE.GridHelper(50,50,0x1E3A5F,0x0F2235);scene.add(gridObj);
scene.add(new THREE.AxesHelper(3));
const gcpGroup=new THREE.Group(),icpGroup=new THREE.Group();
scene.add(gcpGroup);scene.add(icpGroup);

[[-1.4,0,1.4],[1.4,0,1.4],[1.4,0,-1.4],[-1.4,0,-1.4]].forEach(p=>{
  const m=new THREE.Mesh(new THREE.SphereGeometry(.07,12,12),new THREE.MeshStandardMaterial({color:0xF97316,emissive:0xF97316,emissiveIntensity:.4}));
  m.position.set(...p);gcpGroup.add(m);
});
[[0,0,.9],[-.9,0,-.3],[.9,0,-.3]].forEach(p=>{
  const m=new THREE.Mesh(new THREE.OctahedronGeometry(.08),new THREE.MeshStandardMaterial({color:0xF87171,emissive:0xF87171,emissiveIntensity:.5}));
  m.position.set(...p);icpGroup.add(m);
});

// ── Demo point cloud ───────────────────────────────────────────────────────
let cloudObj=null;
let realModelLoaded={};

// Debug helper — ketik _dbg() di console setelah model load
window._dbg = () => {
  if(!cloudObj?.geometry) { console.log('cloudObj belum ada'); return; }
  cloudObj.geometry.computeBoundingBox();
  const bb  = cloudObj.geometry.boundingBox;
  const pos = cloudObj.geometry.attributes.position;
  console.log('=== DEBUG PLY ===');
  console.log('Points:', pos.count);
  console.log('BB min:', bb.min.x.toFixed(3), bb.min.y.toFixed(3), bb.min.z.toFixed(3));
  console.log('BB max:', bb.max.x.toFixed(3), bb.max.y.toFixed(3), bb.max.z.toFixed(3));
  const cx=(bb.min.x+bb.max.x)/2, cy=(bb.min.y+bb.max.y)/2, cz=(bb.min.z+bb.max.z)/2;
  console.log('Center:', cx.toFixed(3), cy.toFixed(3), cz.toFixed(3));
  console.log('Size:', (bb.max.x-bb.min.x).toFixed(3), (bb.max.y-bb.min.y).toFixed(3), (bb.max.z-bb.min.z).toFixed(3));
  console.log('Camera pos:', camera.position.x.toFixed(2), camera.position.y.toFixed(2), camera.position.z.toFixed(2));
  for(let i=0;i<3;i++) console.log(`pt[${i}]:`, pos.getX(i).toFixed(4), pos.getY(i).toFixed(4), pos.getZ(i).toFixed(4));
};
function buildDemoCloud(method){
  if(cloudObj){scene.remove(cloudObj);cloudObj.geometry?.dispose();cloudObj=null;}
  const d=METHODS[method],N=30000;
  const geo=new THREE.BufferGeometry();
  const pos=new Float32Array(N*3),col=new Float32Array(N*3);
  const rand=(a,b)=>a+(b-a)*Math.random();
  const h2r=h=>[((h>>16)&255)/255,((h>>8)&255)/255,(h&255)/255];
  for(let i=0;i<N;i++){
    const a=Math.random()*Math.PI*2,hh=rand(0,5),r=rand(.1,.6)*(1-hh*.08);
    pos[i*3]=Math.cos(a)*r;pos[i*3+1]=hh;pos[i*3+2]=Math.sin(a)*r;
    const c=h2r(d.colors[i%d.colors.length]),br=rand(.5,1);
    col[i*3]=c[0]*br;col[i*3+1]=c[1]*br;col[i*3+2]=c[2]*br;
  }
  geo.setAttribute('position',new THREE.Float32BufferAttribute(pos,3));
  geo.setAttribute('color',new THREE.Float32BufferAttribute(col,3));
  cloudObj=new THREE.Points(geo,new THREE.PointsMaterial({size:.035,vertexColors:true,sizeAttenuation:true}));
  scene.add(cloudObj);
  document.getElementById('hVerts').textContent='~30,000 titik (demo)';
}
buildDemoCloud('sfmmvs');

// ── Load PLY asli dari Hugging Face ───────────────────────────────────────
window.loadSplat = async function(method) {
  if(isLoading) return;
  isLoading = true;

  const btn = document.getElementById('btn-'+method);
  const bar = document.getElementById('loadingBar');
  const hud = document.getElementById('hVerts');
  btn.disabled = true;
  btn.textContent = '⏳ Mengunduh...';
  bar.style.width = '5%';

  try {
    const url = SPLAT_URLS[method];
    hud.textContent = 'Mengunduh dari Hugging Face...';

    const response = await fetch(url);
    if(!response.ok) throw new Error('HTTP ' + response.status);

    const total = parseInt(response.headers.get('content-length') || '0');
    const reader = response.body.getReader();
    const chunks = [];
    let received = 0;

    while(true) {
      const {done, value} = await reader.read();
      if(done) break;
      chunks.push(value);
      received += value.length;
      if(total > 0) {
        const pct = Math.round(received/total*80)+5;
        bar.style.width = pct+'%';
        hud.textContent = `Mengunduh... ${Math.round(received/1024/1024)} / ${Math.round(total/1024/1024)} MB`;
      }
    }

    bar.style.width = '90%';
    hud.textContent = 'Memproses PLY...';

    // Gabungkan chunks jadi satu ArrayBuffer
    const allChunks = new Uint8Array(received);
    let pos = 0;
    for(const chunk of chunks) { allChunks.set(chunk, pos); pos += chunk.length; }
    const buffer = allChunks.buffer;

    // Parse PLY dan buat point cloud
    const points = parsePlyToPoints(buffer, method);
    if(points) {
      if(cloudObj){ scene.remove(cloudObj); cloudObj.geometry?.dispose(); }
      cloudObj = points;
      scene.add(cloudObj);
      autoRotate = false;
      document.getElementById('rotBtn').classList.remove('on');
      fitCamera(cloudObj);

      // Tandai model sudah loaded
      realModelLoaded[method] = true;

      // Update info adaptif
      const d = METHODS[method];
      const ptCount = cloudObj.geometry.attributes.position.count;
      document.getElementById('hMethod').textContent = d.label + ' · ' + d.desc;
      document.getElementById('pointCount').textContent = ptCount.toLocaleString();
      document.getElementById('pointNote').textContent = 'titik asli dari rekonstruksi';

      // Fly basemap ke lokasi
      if (cesiumViewer) {
        cesiumViewer.camera.flyTo({
          destination: Cesium.Cartesian3.fromDegrees(107.4382284, -6.8244535, 600),
          orientation: { heading:0, pitch: Cesium.Math.toRadians(-50), roll:0 },
          duration: 1.5,
        });
      }
    }

    bar.style.width = '100%';
    btn.textContent = '✓ Loaded';
    btn.style.background = 'rgba(74,222,128,.2)';
    btn.style.color = '#4ADE80';
    btn.style.borderColor = '#4ADE80';
    setTimeout(()=>bar.style.width='0',1000);

  } catch(e) {
    console.error(e);
    hud.textContent = 'Gagal load: ' + e.message;
    btn.disabled = false;
    btn.textContent = '⬇ Coba Lagi';
    bar.style.width = '0';
  }
  isLoading = false;
};

// ── Parser PLY → THREE.Points ────────────────────────────────────────────
function parsePlyToPoints(buffer, method) {
  try {
    // Cari end_header di binary buffer langsung
    const uint8 = new Uint8Array(buffer);
    const endHeaderStr = 'end_header\n';
    const endHeaderBytes = new TextEncoder().encode(endHeaderStr);
    let headerByteLen = -1;

    for (let i = 0; i < Math.min(uint8.length - endHeaderBytes.length, 4096); i++) {
      let match = true;
      for (let j = 0; j < endHeaderBytes.length; j++) {
        if (uint8[i+j] !== endHeaderBytes[j]) { match = false; break; }
      }
      if (match) { headerByteLen = i + endHeaderBytes.length; break; }
    }

    // Coba juga dengan \r\n
    if (headerByteLen === -1) {
      const endHeaderStr2 = 'end_header\r\n';
      const endHeaderBytes2 = new TextEncoder().encode(endHeaderStr2);
      for (let i = 0; i < Math.min(uint8.length - endHeaderBytes2.length, 4096); i++) {
        let match = true;
        for (let j = 0; j < endHeaderBytes2.length; j++) {
          if (uint8[i+j] !== endHeaderBytes2[j]) { match = false; break; }
        }
        if (match) { headerByteLen = i + endHeaderBytes2.length; break; }
      }
    }

    if (headerByteLen === -1) {
      console.error('end_header tidak ditemukan');
      return null;
    }

    // Parse header text
    const headerText = new TextDecoder().decode(buffer.slice(0, headerByteLen));
    const lines = headerText.split(/\r?\n/);

    let nVertex = 0;
    const propOrder = [];
    for (const line of lines) {
      const trimmed = line.trim();
      if (trimmed.startsWith('element vertex')) nVertex = parseInt(trimmed.split(/\s+/)[2]);
      if (trimmed.startsWith('property float')) propOrder.push(trimmed.split(/\s+/)[2]);
      if (trimmed.startsWith('property double')) propOrder.push(trimmed.split(/\s+/)[2]);
    }

    console.log(`PLY: ${nVertex} vertex, ${propOrder.length} props, header ${headerByteLen} bytes`);
    console.log('Props:', propOrder.join(', '));

    if (nVertex === 0 || propOrder.length === 0) {
      console.error('Header tidak valid');
      return null;
    }

    const stride = propOrder.length * 4;
    const dataStart = headerByteLen;

    // Validasi ukuran buffer
    const expectedSize = dataStart + nVertex * stride;
    if (buffer.byteLength < expectedSize) {
      console.warn(`Buffer ${buffer.byteLength} < expected ${expectedSize}, adjusting nVertex`);
      nVertex = Math.floor((buffer.byteLength - dataStart) / stride);
    }

    const data = new DataView(buffer, dataStart);
    const xi = propOrder.indexOf('x');
    const yi = propOrder.indexOf('y');
    const zi = propOrder.indexOf('z');
    const ri = propOrder.indexOf('f_dc_0');
    const gi = propOrder.indexOf('f_dc_1');
    const bi = propOrder.indexOf('f_dc_2');
    const rRed = propOrder.indexOf('red');

    const MAX_PTS = Math.min(nVertex, 800000);
    const step    = Math.max(1, Math.floor(nVertex / MAX_PTS));
    const positions = new Float32Array(MAX_PTS * 3);
    const colors    = new Float32Array(MAX_PTS * 3);
    const SH = 0.28209479177387814;

    let pi = 0;
    for (let i = 0; i < nVertex && pi < MAX_PTS; i += step) {
      const off = i * stride;
      if (off + stride > data.byteLength) break;

      const px = data.getFloat32(off + xi*4, true);
      const py = data.getFloat32(off + yi*4, true);
      const pz = data.getFloat32(off + zi*4, true);
      // 3DGS/COLMAP: X=kanan, Y=bawah, Z=depan
      // Three.js: X=kanan, Y=atas, Z=belakang
      // Konversi: three_x=x, three_y=-z, three_z=y
      positions[pi*3]   = px;
      positions[pi*3+1] = -pz;
      positions[pi*3+2] = py;

      if (ri >= 0) {
        colors[pi*3]   = Math.max(0, Math.min(1, 0.5 + SH * data.getFloat32(off + ri*4, true)));
        colors[pi*3+1] = Math.max(0, Math.min(1, 0.5 + SH * data.getFloat32(off + gi*4, true)));
        colors[pi*3+2] = Math.max(0, Math.min(1, 0.5 + SH * data.getFloat32(off + bi*4, true)));
      } else if (rRed >= 0) {
        // RGB uint8 stored as float — Agisoft format
        const rv = data.getFloat32(off + rRed*4, true);
        colors[pi*3]   = rv > 1 ? rv/255 : rv;
        const gv = data.getFloat32(off + (rRed+1)*4, true);
        colors[pi*3+1] = gv > 1 ? gv/255 : gv;
        const bv = data.getFloat32(off + (rRed+2)*4, true);
        colors[pi*3+2] = bv > 1 ? bv/255 : bv;
      } else {
        const d = METHODS[method];
        const c = d.colors[pi % d.colors.length];
        colors[pi*3]   = ((c>>16)&255)/255;
        colors[pi*3+1] = ((c>>8)&255)/255;
        colors[pi*3+2] = (c&255)/255;
      }
      pi++;
    }

    console.log(`Rendered ${pi.toLocaleString()} points`);
    document.getElementById('hVerts').textContent = pi.toLocaleString() + ' titik (asli)';

    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.Float32BufferAttribute(positions.slice(0, pi*3), 3));
    geo.setAttribute('color',    new THREE.Float32BufferAttribute(colors.slice(0, pi*3),    3));
    return new THREE.Points(geo, new THREE.PointsMaterial({size: .02, vertexColors: true, sizeAttenuation: true}));

  } catch(err) {
    console.error('parsePlyToPoints error:', err);
    throw err;
  }
}

function fitCamera(obj) {
  // Hitung bounding box dari geometry langsung (bukan dari object transform)
  const geo = obj.geometry;
  geo.computeBoundingBox();
  const bb = geo.boundingBox;
  const cx = (bb.min.x + bb.max.x) / 2;
  const cy = (bb.min.y + bb.max.y) / 2;
  const cz = (bb.min.z + bb.max.z) / 2;
  const sx = bb.max.x - bb.min.x;
  const sy = bb.max.y - bb.min.y;
  const sz = bb.max.z - bb.min.z;
  const maxDim = Math.max(sx, sy, sz);

  console.log(`fitCamera: center=(${cx.toFixed(1)},${cy.toFixed(1)},${cz.toFixed(1)}) size=${maxDim.toFixed(1)}`);

  // Geser semua vertex ke origin (0,0,0)
  const posAttr = geo.attributes.position;
  for (let i = 0; i < posAttr.count; i++) {
    posAttr.setXYZ(
      i,
      posAttr.getX(i) - cx,
      posAttr.getY(i) - cy,
      posAttr.getZ(i) - cz,
    );
  }
  posAttr.needsUpdate = true;
  geo.computeBoundingBox();
  geo.computeBoundingSphere();

  // Scale ke ukuran scene yang wajar (target ~10 unit)
  if (maxDim > 20) {
    const scale = 10 / maxDim;
    obj.scale.setScalar(scale);
    console.log(`fitCamera: scale=${scale.toFixed(4)}`);
  }

  // Reset kamera ke posisi standar menghadap origin
  radius = 15;
  theta  = Math.PI / 4;
  phi    = Math.PI / 5;
  autoRotate = false;
  document.getElementById('rotBtn').classList.remove('on');
  updateCam();
}

// ── Resize & Camera ────────────────────────────────────────────────────────
function resize(){
  const w=canvas.parentElement.clientWidth,h=canvas.parentElement.clientHeight;
  renderer.setSize(w,h);camera.aspect=w/h;camera.updateProjectionMatrix();
}
resize();window.addEventListener('resize',resize);

function updateCam(){
  camera.position.set(radius*Math.sin(phi)*Math.sin(theta),radius*Math.cos(phi),radius*Math.sin(phi)*Math.cos(theta));
  camera.lookAt(0,1,0);
}
updateCam();

let isDrag=false,lastX=0,lastY=0;
canvas.addEventListener('mousedown',e=>{isDrag=true;lastX=e.clientX;lastY=e.clientY;});
canvas.addEventListener('mouseup',()=>isDrag=false);
canvas.addEventListener('mouseleave',()=>{
  isDrag=false;
  document.getElementById('cE').textContent='—';
  document.getElementById('cN').textContent='—';
  document.getElementById('cZ').textContent='—';
});
canvas.addEventListener('mousemove',e=>{
  if(isDrag){
    theta-=(e.clientX-lastX)*.008;phi=Math.max(.1,Math.min(Math.PI/2.1,phi+(e.clientY-lastY)*.008));
    lastX=e.clientX;lastY=e.clientY;updateCam();
  }
  // Koordinat realtime via raycasting ke ground plane
  const rect=canvas.getBoundingClientRect();
  const mx=((e.clientX-rect.left)/rect.width)*2-1;
  const my=-((e.clientY-rect.top)/rect.height)*2+1;
  const raycaster=new THREE.Raycaster();
  raycaster.setFromCamera(new THREE.Vector2(mx,my),camera);
  const ground=new THREE.Plane(new THREE.Vector3(0,1,0),0);
  const pt=new THREE.Vector3();
  raycaster.ray.intersectPlane(ground,pt);
  if(pt){
    const d=METHODS[currentMethod];
    const prefix=d.coordPrefix;
    document.getElementById('cE').textContent=pt.x.toFixed(3);
    document.getElementById('cN').textContent=pt.z.toFixed(3);
    document.getElementById('cZ').textContent=pt.y.toFixed(3);
  }
});
canvas.addEventListener('wheel',e=>{radius=Math.max(1,Math.min(200,radius+e.deltaY*.015));updateCam();e.preventDefault();},{passive:false});

let fc=0,lastT=Date.now();
(function loop(){
  requestAnimationFrame(loop);
  if(autoRotate){theta+=.003;updateCam();}
  renderer.render(scene,camera);
  fc++;const now=Date.now();
  if(now-lastT>800){document.getElementById('hFps').textContent=Math.round(fc*1000/(now-lastT))+' fps';fc=0;lastT=now;}
})();

// ── UI ─────────────────────────────────────────────────────────────────────
const layers={cloud:true,grid:true,gcp:true,icp:true};

window.setMethod=function(method,el){
  currentMethod=method;
  document.querySelectorAll('.method-card').forEach(c=>c.classList.remove('active'));
  el.classList.add('active');
  const d=METHODS[method];

  if (!realModelLoaded[method]) buildDemoCloud(method);
  document.getElementById('hMethod').textContent=d.label+' · '+d.desc+(realModelLoaded[method]?'':' (demo)');
  document.getElementById('sMode').textContent='SINGLE · '+d.label.toUpperCase();
  document.getElementById('sPipe').textContent=d.pipe;

  // Akurasi
  document.getElementById('ce90val').innerHTML=d.ce90+' <span class="stat-unit">mm</span>';
  document.getElementById('ce90val').style.color=d.ce90color;
  document.getElementById('ce90bar').style.cssText='width:'+d.ce90pct+'%;background:'+d.ce90color;
  document.getElementById('le90val').innerHTML=d.le90+' <span class="stat-unit">mm</span>';
  document.getElementById('le90val').style.color=d.le90color;
  document.getElementById('le90bar').style.cssText='width:'+d.le90pct+'%;background:'+d.le90color;
  document.getElementById('rmseH').textContent=d.rmseH+'mm';
  document.getElementById('rmseH').className='cell-val '+(method==='sfmmvs'?'good':'warn');
  document.getElementById('rmseV').textContent=d.rmseV+'mm';
  document.getElementById('rmseV').className='cell-val '+(method==='sfmmvs'?'warn':'err');

  // Jumlah titik
  document.getElementById('pointCount').textContent=realModelLoaded[method]?d.info.titik:'—';
  document.getElementById('pointNote').textContent=realModelLoaded[method]?'titik asli dari rekonstruksi':'Load model asli untuk lihat data nyata';

  // Info Lokasi adaptif
  const info=d.info;
  document.getElementById('infoObjek').textContent=info.objek;
  document.getElementById('infoKota').textContent=info.kota;
  document.getElementById('infoMetode').textContent=info.metode;
  document.getElementById('infoSoftware').textContent=info.software;
  document.getElementById('infoKamera').textContent=info.kamera;
  document.getElementById('infoGcp').textContent=info.gcp;
  document.getElementById('infoIcp').textContent=info.icp;
  document.getElementById('infoCrs').textContent=info.crs;
  document.getElementById('infoFormat').textContent=info.format;

  // Koordinat navbar label
  document.getElementById('coordEpsg').textContent=d.epsg;

  // Fly basemap ke lokasi model
  if (cesiumViewer) {
    cesiumViewer.camera.flyTo({
      destination: Cesium.Cartesian3.fromDegrees(107.4382284, -6.8244535, 600),
      orientation: { heading:0, pitch: Cesium.Math.toRadians(-50), roll:0 },
      duration: 1.5,
    });
  }
};

window.toggleLayer=function(name,el){
  layers[name]=!layers[name];
  el.classList.toggle('on');
  el.querySelector('.layer-cb').classList.toggle('on');
  el.querySelector('.layer-cb').textContent=layers[name]?'✓':'';
  if(name==='cloud'&&cloudObj)cloudObj.visible=layers[name];
  if(name==='grid')gridObj.visible=layers[name];
  if(name==='gcp')gcpGroup.visible=layers[name];
  if(name==='icp')icpGroup.visible=layers[name];
};

window.resetCam=()=>{theta=Math.PI/4;phi=Math.PI/5;radius=12;updateCam();};
window.toggleRot=()=>{autoRotate=!autoRotate;document.getElementById('rotBtn').classList.toggle('on',autoRotate);};
window.doZoom=f=>{radius=Math.max(1,Math.min(200,radius*f));updateCam();};

// ── CesiumJS Basemap ──────────────────────────────────────────────────────
let cesiumViewer = null;
let cesiumReady  = false;

function initCesium() {
  if (cesiumReady) return;
  cesiumReady = true;

  // Nonaktifkan token requirement — pakai imagery provider sendiri
  Cesium.Ion.defaultAccessToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJlYWE1OWUxNy1mMWZiLTQzYjYtYTQ0OS1kMWFjYmFkNjc4Y2EiLCJpZCI6NTc3MzMsImlhdCI6MTYyNzg0NTE4Mn0.XcKpgANiY19MC4bdFUXMVEBToBmqS8kuYpUlxJHYZxk';

  cesiumViewer = new Cesium.Viewer('cesiumContainer', {
    imageryProvider: new Cesium.OpenStreetMapImageryProvider({
      url: 'https://tile.openstreetmap.org/'
    }),
    baseLayerPicker:    false,
    geocoder:           false,
    homeButton:         false,
    sceneModePicker:    false,
    navigationHelpButton: false,
    animation:          false,
    timeline:           false,
    fullscreenButton:   false,
    infoBox:            false,
    selectionIndicator: false,
    terrainProvider:    new Cesium.EllipsoidTerrainProvider(),
  });

  // Fly to Geosite Stone Garden, Citatah, Kab. Bandung Barat
  const SITE_LAT = -6.8244535;
  const SITE_LON = 107.4382284;
  const SITE_H   = 800; // elevasi sekitar 800m dpl

  cesiumViewer.camera.flyTo({
    destination: Cesium.Cartesian3.fromDegrees(SITE_LON, SITE_LAT, 800),
    orientation: { heading: 0, pitch: Cesium.Math.toRadians(-45), roll: 0 },
    duration: 2,
  });

  // Marker utama Stone Garden
  cesiumViewer.entities.add({
    name: 'Geosite Stone Garden',
    position: Cesium.Cartesian3.fromDegrees(SITE_LON, SITE_LAT, SITE_H),
    billboard: {
      image: 'data:image/svg+xml;base64,' + btoa(`
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
          <circle cx="16" cy="16" r="10" fill="#2DD4BF" opacity="0.9"/>
          <circle cx="16" cy="16" r="5"  fill="#0D1117"/>
          <circle cx="16" cy="16" r="2"  fill="#2DD4BF"/>
        </svg>`),
      width: 32, height: 32,
      verticalOrigin: Cesium.VerticalOrigin.CENTER,
    },
    label: {
      text: 'Geosite Stone Garden\nCitatah, Kab. Bandung Barat',
      font: '11px Space Grotesk',
      fillColor: Cesium.Color.fromCssColorString('#2DD4BF'),
      outlineColor: Cesium.Color.fromCssColorString('#0D1117'),
      outlineWidth: 2,
      style: Cesium.LabelStyle.FILL_AND_OUTLINE,
      verticalOrigin: Cesium.VerticalOrigin.BOTTOM,
      pixelOffset: new Cesium.Cartesian2(0, -36),
    },
  });

  // Footprint area survey (approx)
  cesiumViewer.entities.add({
    name: 'Survey Area',
    polygon: {
      hierarchy: Cesium.Cartesian3.fromDegreesArray([
        107.4375, -6.8250,
        107.4390, -6.8250,
        107.4390, -6.8238,
        107.4375, -6.8238,
      ]),
      material: Cesium.Color.fromCssColorString('#2DD4BF').withAlpha(0.15),
      outline: true,
      outlineColor: Cesium.Color.fromCssColorString('#2DD4BF'),
      outlineWidth: 2,
      height: SITE_H,
    },
  });

  // GCP markers — CP01, CP03, CP04, G1CP02, G2CP01
  const gcps = [
    { lon: 107.4378, lat: -6.8247, label: 'CP01' },
    { lon: 107.4385, lat: -6.8247, label: 'CP03' },
    { lon: 107.4385, lat: -6.8241, label: 'CP04' },
    { lon: 107.4378, lat: -6.8241, label: 'G1CP02' },
    { lon: 107.4382, lat: -6.8244, label: 'G2CP01' },
  ];
  gcps.forEach(gcp => {
    cesiumViewer.entities.add({
      position: Cesium.Cartesian3.fromDegrees(gcp.lon, gcp.lat, SITE_H),
      point: { pixelSize: 10, color: Cesium.Color.fromCssColorString('#F97316'), outlineColor: Cesium.Color.WHITE, outlineWidth: 1 },
      label: { text: gcp.label, font: '10px monospace', fillColor: Cesium.Color.fromCssColorString('#F97316'), pixelOffset: new Cesium.Cartesian2(12, 0), scale: 0.8 },
    });
  });

  // ICP markers — CP02, G1CP01, G2CP02
  const icps = [
    { lon: 107.4380, lat: -6.8248, label: 'CP02' },
    { lon: 107.4376, lat: -6.8243, label: 'G1CP01' },
    { lon: 107.4388, lat: -6.8243, label: 'G2CP02' },
  ];
  icps.forEach(icp => {
    cesiumViewer.entities.add({
      position: Cesium.Cartesian3.fromDegrees(icp.lon, icp.lat, SITE_H),
      point: { pixelSize: 8, color: Cesium.Color.fromCssColorString('#F87171'), outlineColor: Cesium.Color.WHITE, outlineWidth: 1 },
      label: { text: icp.label, font: '10px monospace', fillColor: Cesium.Color.fromCssColorString('#F87171'), pixelOffset: new Cesium.Cartesian2(12, 0), scale: 0.8 },
    });
  });
}

// ── Basemap switcher ─────────────────────────────────────────────────────
const BASEMAPS = {
  osm: () => new Cesium.OpenStreetMapImageryProvider({ url: 'https://tile.openstreetmap.org/' }),
  satellite: () => new Cesium.UrlTemplateImageryProvider({
    url: 'https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
    credit: 'Google Satellite',
  }),
  topo: () => new Cesium.UrlTemplateImageryProvider({
    url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}',
    credit: 'Esri World Topo',
  }),
};

window.setBasemap = function(name, btn) {
  if (!cesiumViewer) return;
  document.querySelectorAll('.map-tile-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const layers = cesiumViewer.imageryLayers;
  layers.removeAll();
  layers.addImageryProvider(BASEMAPS[name]());
};

// ── Panel switcher ────────────────────────────────────────────────────────
window.setViewPanel = function(mode, btn) {
  document.querySelectorAll('.nav-tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const mainEl    = document.querySelector('.main');
  const canvasWrap = document.getElementById('canvasWrap');
  const mapWrap   = document.getElementById('mapWrap');
  const infoPanel = document.querySelector('.info');

  if (mode === '3d') {
    mainEl.className = 'main';
    canvasWrap.style.display = 'block';
    mapWrap.classList.remove('visible');
    infoPanel.style.display = '';
  } else if (mode === 'split') {
    mainEl.className = 'main split-mode';
    canvasWrap.style.display = 'block';
    mapWrap.classList.add('visible');
    infoPanel.style.display = 'none';
    initCesium();
  } else if (mode === 'map') {
    mainEl.className = 'main map-only';
    canvasWrap.style.display = 'none';
    mapWrap.classList.add('visible');
    infoPanel.style.display = '';
    initCesium();
  }

  // Trigger resize Three.js
  setTimeout(() => {
    const w = canvasWrap.clientWidth, h = canvasWrap.clientHeight;
    if (w > 0 && h > 0) {
      renderer.setSize(w, h);
      camera.aspect = w / h;
      camera.updateProjectionMatrix();
    }
  }, 50);
};

// ── Default landing page: Basemap ─────────────────────────────────────────
// Langsung tampilkan basemap saat halaman pertama dibuka
(function initDefaultView() {
  const mapBtn = document.querySelector('.nav-tab-btn:last-child');
  if (mapBtn) setViewPanel('map', mapBtn);
})();
</script>
</body>
</html>
