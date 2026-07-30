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
.main{display:grid;grid-template-columns:200px 1fr 180px;overflow:hidden}
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
.canvas-wrap{position:relative;background:#0D1117}
canvas{width:100%!important;height:100%!important;display:block}
.hud{position:absolute;top:10px;left:10px;display:flex;flex-direction:column;gap:4px;pointer-events:none}
.hud-chip{background:rgba(13,17,23,.85);border:1px solid var(--border);border-radius:4px;padding:3px 8px;font-family:var(--mono);font-size:10px;color:var(--teal)}
.view-btns{position:absolute;top:10px;right:10px;display:flex;flex-direction:column;gap:3px}
.vbtn{background:rgba(13,17,23,.85);border:1px solid var(--border);border-radius:4px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--muted);font-size:14px;transition:all .15s}
.vbtn:hover{border-color:var(--teal);color:var(--teal)}
.vbtn.on{background:var(--teal);color:#0D1117;border-color:var(--teal)}
.hint{position:absolute;bottom:10px;left:50%;transform:translateX(-50%);font-size:10px;color:var(--muted);pointer-events:none;white-space:nowrap}
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
</style>
</head>
<body>
<div class="app">
  <nav>
    <div class="brand"><div class="dot"></div>3DGS GeoViewer<span class="badge">v2.1</span></div>
    <div class="nav-coords">
      <span>EPSG:<b>32749</b></span>
      <span>E:<b id="cE">856223.300</b></span>
      <span>N:<b id="cN">9045773.007</b></span>
      <span>Z:<b id="cZ">37.995</b></span>
    </div>
  </nav>
  <div class="main">
    <div class="sidebar">
      <div>
        <div class="sec-label">Metode Rekonstruksi</div>
        <div style="display:flex;flex-direction:column;gap:5px">
          <div class="method-card active" onclick="setMethod('sfmmvs',this)">
            <div class="mc-head"><span class="mc-name">SfM-MVS</span><div class="mc-dot" style="background:#4ADE80"></div></div>
            <div class="mrow"><span class="mk">CE90</span><span class="mv good">2.74 mm</span></div>
            <div class="mrow"><span class="mk">LE90</span><span class="mv warn">11.46 mm</span></div>
            <div class="mrow"><span class="mk">RMSE H</span><span class="mv good">1.81 mm</span></div>
          </div>
          <div class="method-card" onclick="setMethod('3dgs',this)">
            <div class="mc-head"><span class="mc-name">3DGS Lokal</span><div class="mc-dot" style="background:#F97316"></div></div>
            <div class="mrow"><span class="mk">CE90</span><span class="mv err">17.70 mm</span></div>
            <div class="mrow"><span class="mk">LE90</span><span class="mv err">68.90 mm</span></div>
            <div class="mrow"><span class="mk">RMSE H</span><span class="mv warn">11.66 mm</span></div>
          </div>
          <div class="method-card" onclick="setMethod('geo3dgs',this)">
            <div class="mc-head"><span class="mc-name">Geo-3DGS</span><div class="mc-dot" style="background:#818CF8"></div></div>
            <div class="mrow"><span class="mk">CE90</span><span class="mv warn">17.70 mm</span></div>
            <div class="mrow"><span class="mk">T-test</span><span class="mv good">Lulus 95%</span></div>
            <div class="mrow"><span class="mk">CRS</span><span class="mv info">UTM 49S</span></div>
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
        <div class="mrow"><span class="mk">Objek</span><span class="mv" style="font-size:10px">Tugu Temple</span></div>
        <div class="mrow"><span class="mk">Kota</span><span class="mv" style="font-size:10px">Semarang</span></div>
        <div class="mrow"><span class="mk">Kamera</span><span class="mv" style="font-size:10px">DJI Ph4</span></div>
        <div class="mrow"><span class="mk">GCP</span><span class="mv">4</span></div>
        <div class="mrow"><span class="mk">ICP</span><span class="mv">3</span></div>
      </div>
    </div>
    <div class="canvas-wrap">
      <canvas id="mainCanvas"></canvas>
      <div class="hud">
        <div class="hud-chip" id="hMethod">SfM-MVS · Dense Mesh</div>
        <div class="hud-chip" id="hVerts">~30,000 titik</div>
        <div class="hud-chip" id="hFps">-- fps</div>
      </div>
      <div class="view-btns">
        <div class="vbtn" onclick="resetCam()" title="Reset">⌂</div>
        <div class="vbtn on" id="rotBtn" onclick="toggleRot()" title="Auto-rotate">↻</div>
        <div class="vbtn" onclick="doZoom(0.8)">+</div>
        <div class="vbtn" onclick="doZoom(1.2)">−</div>
      </div>
      <div class="hint">Drag untuk orbit · Scroll untuk zoom</div>
    </div>
    <div class="info">
      <div class="sec-label">Akurasi</div>
      <div class="stat-card">
        <div class="stat-label">CE90 Horizontal</div>
        <div class="stat-val good" id="ce90val">2.74 <span class="stat-unit">mm</span></div>
        <div class="bar-wrap"><div class="bar-fill" id="ce90bar" style="width:14%;background:#4ADE80"></div></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">LE90 Vertikal</div>
        <div class="stat-val warn" id="le90val">11.46 <span class="stat-unit">mm</span></div>
        <div class="bar-wrap"><div class="bar-fill" id="le90bar" style="width:16%;background:#F97316"></div></div>
      </div>
      <div class="sec-label">RMSE</div>
      <div class="grid2">
        <div class="cell"><div class="cell-label">RMSE H</div><div class="cell-val good" id="rmseH">1.81mm</div></div>
        <div class="cell"><div class="cell-label">RMSE V</div><div class="cell-val warn" id="rmseV">6.95mm</div></div>
      </div>
      <div class="sec-label">T-Test (α=0.05)</div>
      <div class="ttest">
        <div class="trow"><span>t-value</span><b id="tval">—</b></div>
        <div class="trow"><span>t-kritis</span><b>±2.101</b></div>
        <div class="trow"><span>df</span><b>18</b></div>
        <div class="tconc" id="tconc">Metode referensi</div>
      </div>
    </div>
  </div>
  <div class="statusbar">
    <div class="sc"><span class="sl">METODE</span><span class="sv" id="sMode">SINGLE · SfM-MVS</span></div>
    <div class="sc"><span class="sl">PIPELINE</span><span class="sv a" id="sPipe">SfM → COLMAP → MVS</span></div>
    <div class="sc"><span class="sl">RENDER</span><span class="sv">WebGL2 · Three.js r169</span></div>
    <div class="sc"><span class="sl">REF</span><span class="sv">Dzulvikar et al. 2025 · GeoRefGS 2026</span></div>
  </div>
</div>

<script type="importmap">{"imports":{"three":"https://cdn.jsdelivr.net/npm/three@0.169.0/build/three.module.js"}}</script>
<script type="module">
import * as THREE from 'three';

const METHODS = {
  sfmmvs:{label:'SfM-MVS',desc:'Dense Mesh',ce90:'2.74',le90:'11.46',rmseH:'1.81',rmseV:'6.95',tval:'—',conc:'Metode referensi',pipe:'SfM → COLMAP → MVS Dense',colors:[0x2DD4BF,0x38BDF8,0x4ADE80]},
  '3dgs':{label:'3DGS Lokal',desc:'KSplat/PLY',ce90:'17.70',le90:'68.90',rmseH:'11.66',rmseV:'41.76',tval:'—',conc:'Koordinat lokal — belum georeferensi',pipe:'SfM → Postshot 3DGS',colors:[0xF97316,0xFBBF24,0xF87171]},
  geo3dgs:{label:'Geo-3DGS',desc:'UTM 49S',ce90:'17.70',le90:'68.90',rmseH:'11.66',rmseV:'41.76',tval:'-0.80',conc:'H₀ tidak ditolak — setara SfM-MVS α=0.05',pipe:'SfM-GS + XML Transform → UTM 49S',colors:[0x818CF8,0x38BDF8,0x2DD4BF]},
};

let autoRotate=true, theta=Math.PI/4, phi=Math.PI/5, radius=12;
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

let cloudObj=null;
function buildCloud(method){
  if(cloudObj){scene.remove(cloudObj);cloudObj.geometry.dispose();}
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
}
buildCloud('sfmmvs');

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
canvas.addEventListener('mouseleave',()=>isDrag=false);
canvas.addEventListener('mousemove',e=>{
  if(!isDrag)return;
  theta-=(e.clientX-lastX)*.008;phi=Math.max(.1,Math.min(Math.PI/2.1,phi+(e.clientY-lastY)*.008));
  lastX=e.clientX;lastY=e.clientY;updateCam();
});
canvas.addEventListener('wheel',e=>{radius=Math.max(2,Math.min(50,radius+e.deltaY*.015));updateCam();e.preventDefault();},{passive:false});

let fc=0,lastT=Date.now();
(function loop(){
  requestAnimationFrame(loop);
  if(autoRotate){theta+=.003;updateCam();}
  renderer.render(scene,camera);
  fc++;const now=Date.now();
  if(now-lastT>800){document.getElementById('hFps').textContent=Math.round(fc*1000/(now-lastT))+' fps';fc=0;lastT=now;}
  const t=Date.now()*.00008;
  document.getElementById('cE').textContent=(856223.300+Math.sin(t)*.002).toFixed(3);
  document.getElementById('cN').textContent=(9045773.007+Math.cos(t)*.002).toFixed(3);
})();

const layers={cloud:true,grid:true,gcp:true,icp:true};

window.setMethod=function(method,el){
  document.querySelectorAll('.method-card').forEach(c=>c.classList.remove('active'));
  el.classList.add('active');
  const d=METHODS[method];
  buildCloud(method);
  document.getElementById('hMethod').textContent=d.label+' · '+d.desc;
  document.getElementById('sMode').textContent='SINGLE · '+d.label.toUpperCase();
  document.getElementById('sPipe').textContent=d.pipe;
  const ce90pct=method==='sfmmvs'?14:89,le90pct=method==='sfmmvs'?16:99;
  const cc=method==='sfmmvs'?'#4ADE80':method==='geo3dgs'?'#F97316':'#F87171';
  const lc=method==='sfmmvs'?'#F97316':'#F87171';
  document.getElementById('ce90val').innerHTML=d.ce90+' <span class="stat-unit">mm</span>';
  document.getElementById('ce90val').style.color=cc;
  document.getElementById('ce90bar').style.cssText='width:'+ce90pct+'%;background:'+cc;
  document.getElementById('le90val').innerHTML=d.le90+' <span class="stat-unit">mm</span>';
  document.getElementById('le90val').style.color=lc;
  document.getElementById('le90bar').style.cssText='width:'+le90pct+'%;background:'+lc;
  document.getElementById('rmseH').textContent=d.rmseH+'mm';
  document.getElementById('rmseH').className='cell-val '+(method==='sfmmvs'?'good':'warn');
  document.getElementById('rmseV').textContent=d.rmseV+'mm';
  document.getElementById('rmseV').className='cell-val '+(method==='sfmmvs'?'warn':'err');
  document.getElementById('tval').textContent=d.tval;
  document.getElementById('tconc').textContent=d.conc;
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
window.doZoom=f=>{radius=Math.max(2,Math.min(50,radius*f));updateCam();};
</script>
</body>
</html>
