<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Caballos para disfrutar</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
*{box-sizing:border-box}
body{margin:0;font-family:Arial,sans-serif;background:#f3ede4;color:#2f241c}
header{background:linear-gradient(135deg,#4b2a12,#9a6738);color:white;padding:35px 20px;text-align:center}
header h1{margin:0;font-size:34px}
main{max-width:1250px;margin:25px auto;padding:0 18px}
.card{background:white;border-radius:14px;padding:24px;margin-bottom:20px;box-shadow:0 4px 14px rgba(0,0,0,.08)}
h2{color:#6b3f1d;margin-top:0}
input,select,textarea{width:100%;padding:11px;margin:6px 0 13px;border:1px solid #c7b39b;border-radius:8px;font-size:15px}
button,.btn{background:#6b3f1d;color:white;border:none;border-radius:8px;padding:11px 16px;text-decoration:none;display:inline-block;cursor:pointer;font-weight:bold;margin:4px}
button:hover,.btn:hover{background:#8a552a}
.btn-red{background:#9b2f2f}
.btn-green{background:#2f6b45}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px}
.item{background:#f8f3ed;border:1px solid #e0d2c2;border-radius:12px;padding:16px}
.hidden{display:none}
.msg{padding:12px;border-radius:8px;margin:10px 0}
.ok{background:#e7f6eb;color:#216b35}
.err{background:#fde7e7;color:#9b1c1c}
.info{background:#eef3fb;color:#23466c}
nav{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px}
.section{display:none}
.section.active{display:block}
.topbar{display:flex;justify-content:space-between;align-items:center;gap:15px;flex-wrap:wrap}
.badge{background:#eadcc8;padding:5px 9px;border-radius:20px;font-size:13px}
img{max-width:100%;border-radius:10px}
footer{text-align:center;padding:25px;color:#6b3f1d}
</style>
</head>
<body>

<header>
<h1>Caballos para disfrutar</h1>
<p>Aplicación web, API Laravel y app Android desplegadas en VPS con HTTPS</p>
</header>

<main>

<section class="card">
<div class="topbar">
<div>
<h2>Aplicación Android</h2>
<p>Descarga la APK para probar la aplicación móvil.</p>
</div>
<a class="btn btn-green" href="/downloads/caballos-app.apk">📱 Descargar APK</a>
</div>
</section>

<section id="loginCard" class="card">
<h2>Iniciar sesión</h2>
<div id="loginMsg"></div>

<label>Email</label>
<input id="email" type="email" >

<label>Contraseña</label>
<input id="password" type="password" >

<button type="button" onclick="login()">Entrar</button>

<hr>

<h2>Crear cuenta</h2>
<p>Regístrate para poder hacer reservas desde la web.</p>
<div id="registroMsg"></div>

<label>Nombre</label>
<input id="reg_nombre" type="text">

<label>Email</label>
<input id="reg_email" type="email">

<label>Teléfono</label>
<input id="reg_telefono" type="text">

<label>Contraseña</label>
<input id="reg_password" type="password">

<button type="button" class="btn-green" onclick="registrar()">Registrarme</button>
</section>

<section id="app" class="hidden">

<section class="card">
<div class="topbar">
<div>
<h2>Panel de gestión</h2>
<p id="userInfo"></p>
</div>
<button type="button" class="btn-red" onclick="logout()">Cerrar sesión</button>
</div>
</section>

<nav>
<button type="button" onclick="showSection('inicio')">🏠 Inicio</button>
<button type="button" onclick="showSection('caballos')">🐎 Caballos</button>
<button type="button" onclick="showSection('reservas')">📅 Reservas</button>
<button type="button" onclick="showSection('pagos')">💳 Pagos</button>
<button type="button" onclick="showSection('admin')">⚙️ Admin</button>
<a class="btn btn-green" href="/downloads/caballos-app.apk">📱 APK</a>
</nav>

<section id="inicio" class="section active card">
<h2>Resumen</h2>
<div class="grid">
<div class="item"><h3>Laravel</h3><p>Backend API REST funcionando desde Internet.</p></div>
<div class="item"><h3>MariaDB</h3><p>Base de datos conectada en VPS.</p></div>
<div class="item"><h3>Android Kotlin</h3><p>Aplicación móvil descargable desde esta web.</p></div>
</div>
</section>

<section id="caballos" class="section card">
<h2>Gestión de caballos</h2>

<h3>Alta / modificación de caballo</h3>
<div id="caballoMsg"></div>

<input id="edit_caballo_id" type="hidden">

<label>Nombre</label>
<input id="caballo_nombre" type="text">

<label>Raza</label>
<input id="caballo_raza" type="text">

<label>Fecha de nacimiento</label>
<input id="caballo_fecha_nacimiento" type="date">

<label>Foto</label>
<input id="caballo_foto" type="file" accept="image/*">
<p><small>Selecciona una imagen real: jpg, jpeg, png o webp.</small></p>

<label>Estado</label>
<select id="caballo_enfermo">
<option value="0">Disponible</option>
<option value="1">Enfermo</option>
</select>

<label>Observaciones</label>
<textarea id="caballo_observaciones"></textarea>

<button type="button" class="btn-green" onclick="guardarCaballo()">Guardar caballo</button>
<button type="button" onclick="limpiarCaballo()">Nuevo caballo</button>

<hr>

<h3>Listado de caballos</h3>
<button type="button" onclick="cargarCaballos()">Actualizar caballos</button>
<div id="caballosMsg"></div>
<div id="listaCaballos" class="grid"></div>
</section>

<section id="reservas" class="section card">
<h2>Reservas</h2>

<h3>Crear / editar reserva</h3>
<div id="reservaMsg"></div>

<input id="reserva_id" type="hidden">

<label>Caballo</label>
<select id="caballo_id"></select>

<label>Fecha</label>
<input id="fecha" type="date">

<label>Hora</label>
<select id="hora">
<option value="10:00">10:00</option>
<option value="11:00">11:00</option>
<option value="12:00">12:00</option>
<option value="13:00">13:00</option>
</select>

<label>Comentarios</label>
<textarea id="comentarios"></textarea>

<button type="button" class="btn-green" onclick="guardarReserva()">Guardar reserva</button>
<button type="button" onclick="limpiarReserva()">Nueva reserva</button>

<hr>

<h3>Mis reservas</h3>
<button type="button" onclick="cargarReservas()">Actualizar reservas</button>
<div id="reservasMsg"></div>
<div id="listaReservas" class="grid"></div>
</section>

<section id="pagos" class="section card">
<h2>Pagos</h2>

<h3>Registrar pago</h3>
<div id="pagoMsg"></div>

<label>ID reserva</label>
<input id="pago_reserva_id" type="number">

<label>Cantidad</label>
<input id="cantidad" type="number" step="0.01" value="20.00">

<label>Comisión</label>
<input id="comision" type="number" step="0.01" value="0.59">

<label>Referencia</label>
<input id="referencia_pago" value="PAGO-WEB-DEMO">

<button type="button" class="btn-green" onclick="registrarPago()">Registrar pago</button>

<hr>

<h3>Listado de pagos</h3>
<button type="button" onclick="cargarPagos()">Actualizar pagos</button>
<div id="pagosMsg"></div>
<div id="listaPagos" class="grid"></div>
</section>

<section id="admin" class="section card">
<h2>Administración</h2>
<p>Disponible para usuario administrador.</p>

<button type="button" onclick="cargarUsuarios()">Usuarios</button>
<button type="button" onclick="cargarAdminReservas()">Todas las reservas</button>
<button type="button" onclick="cargarAdminPagos()">Todos los pagos</button>

<div id="adminMsg"></div>
<div id="adminContenido" class="grid"></div>
</section>

</section>

</main>

<footer>
Caballos para disfrutar · Laravel + Android Kotlin + MariaDB + VPS
</footer>

<script>
let token = localStorage.getItem("token") || "";
let usuario = JSON.parse(localStorage.getItem("usuario") || "null");
let caballosCache = [];
let reservasCache = [];

function showSection(id){
    document.querySelectorAll(".section").forEach(s => s.classList.remove("active"));
    document.getElementById(id).classList.add("active");
}

function mensaje(id,text,type="info"){
    const el = document.getElementById(id);
    if(el){ el.innerHTML = `<div class="msg ${type}">${text}</div>`; }
}

function limpiar(id){
    const el = document.getElementById(id);
    if(el){ el.innerHTML = ""; }
}

function iniciarApp(){
    document.getElementById("loginCard").classList.add("hidden");
    document.getElementById("app").classList.remove("hidden");
    document.getElementById("userInfo").innerHTML =
        `<strong>${usuario.nombre}</strong> · ${usuario.email} · <span class="badge">${usuario.rol}</span>`;
    cargarCaballos();
    cargarReservas();
    cargarPagos();
}

if(token && usuario){ iniciarApp(); }

async function login(){
    limpiar("loginMsg");
    try{
        const res = await fetch("/api/login",{
            method:"POST",
            headers:{"Accept":"application/json","Content-Type":"application/json"},
            body:JSON.stringify({
                email:document.getElementById("email").value.trim(),
                password:document.getElementById("password").value
            })
        });

        const data = await res.json();

        if(!res.ok || !data.token){
            mensaje("loginMsg", data.mensaje || "Login incorrecto", "err");
            return;
        }

        token = data.token;
        usuario = data.usuario;
        localStorage.setItem("token", token);
        localStorage.setItem("usuario", JSON.stringify(usuario));
        iniciarApp();

    }catch(e){
        mensaje("loginMsg","Error conectando con la API: " + e.message,"err");
    }
}

async function registrar(){
    limpiar("registroMsg");

    const nombre = document.getElementById("reg_nombre").value.trim();
    const email = document.getElementById("reg_email").value.trim();
    const telefono = document.getElementById("reg_telefono").value.trim();
    const password = document.getElementById("reg_password").value;

    if(!nombre || !email || !telefono || !password){
        mensaje("registroMsg","Completa todos los campos","err");
        return;
    }

    if(password.length < 8){
        mensaje("registroMsg","La contraseña debe tener al menos 8 caracteres","err");
        return;
    }

    try{
        const res = await fetch("/api/registro",{
            method:"POST",
            headers:{
                "Accept":"application/json",
                "Content-Type":"application/json"
            },
            body:JSON.stringify({
                nombre:nombre,
                email:email,
                telefono:telefono,
                password:password
            })
        });

        const text = await res.text();
        let data;
        try{ data = text ? JSON.parse(text) : {}; }
        catch(e){ data = text; }

        if(!res.ok){
            if(data.errors){
                const errores = Object.values(data.errors).flat().join("<br>");
                mensaje("registroMsg", errores, "err");
            }else{
                mensaje("registroMsg", data.mensaje || data.message || "No se pudo registrar el usuario", "err");
            }
            return;
        }

        token = data.token;
        usuario = data.usuario;
        localStorage.setItem("token", token);
        localStorage.setItem("usuario", JSON.stringify(usuario));

        mensaje("registroMsg","Usuario registrado correctamente. Entrando...","ok");
        iniciarApp();

    }catch(e){
        mensaje("registroMsg","Error conectando con la API: " + e.message,"err");
    }
}

function logout(){
    localStorage.clear();
    location.reload();
}

async function api(url,opt={}){
    const headers = {
        "Accept":"application/json",
        "Content-Type":"application/json",
        "Authorization":"Bearer " + token,
        ...(opt.headers || {})
    };

    const res = await fetch(url,{...opt,headers});
    const text = await res.text();

    let data;
    try{ data = text ? JSON.parse(text) : {}; }
    catch(e){ data = text; }

    if(!res.ok){
        console.error("API ERROR", url, data);
        throw data;
    }

    return data;
}

function fechaES(fecha){
if(!fecha) return "";
const partes = fecha.split("-");
if(partes.length !== 3) return fecha;
return partes[2]+"/"+partes[1]+"/"+partes[0];
}

/* CABALLOS */

function limpiarCaballo(){
    document.getElementById("edit_caballo_id").value = "";
    document.getElementById("caballo_nombre").value = "";
    document.getElementById("caballo_raza").value = "";
    document.getElementById("caballo_fecha_nacimiento").value = "";
    document.getElementById("caballo_foto").value = "";
    document.getElementById("caballo_enfermo").value = "0";
    document.getElementById("caballo_observaciones").value = "";
    mensaje("caballoMsg","Formulario preparado para nuevo caballo","info");
}

async function guardarCaballo(){
    limpiar("caballoMsg");

    const id = document.getElementById("edit_caballo_id").value;

    const formData = new FormData();
    formData.append("nombre", document.getElementById("caballo_nombre").value);
    formData.append("raza", document.getElementById("caballo_raza").value);
    formData.append("fecha_nacimiento", document.getElementById("caballo_fecha_nacimiento").value);
    formData.append("enfermo", document.getElementById("caballo_enfermo").value);
    formData.append("observaciones", document.getElementById("caballo_observaciones").value);

    const foto = document.getElementById("caballo_foto").files[0];
    if(foto){ formData.append("foto", foto); }

    if(id){ formData.append("_method", "PUT"); }

    try{
        const url = id ? "/api/caballos/" + id : "/api/caballos";

        const res = await fetch(url,{
            method:"POST",
            headers:{
                "Accept":"application/json",
                "Authorization":"Bearer " + token
            },
            body:formData
        });

        const text = await res.text();
        let data;
        try{ data = text ? JSON.parse(text) : {}; }
        catch(e){ data = text; }

        if(!res.ok){ throw data; }

        mensaje("caballoMsg", data.mensaje || "Caballo guardado correctamente","ok");
        limpiarCaballo();
        cargarCaballos();

    }catch(e){
        mensaje("caballoMsg", e.mensaje || e.message || "No se pudo guardar el caballo","err");
    }
}

async function cargarCaballos(){
    limpiar("caballosMsg");

    try{
        const data = await api("/api/caballos");
        caballosCache = data;

        const cont = document.getElementById("listaCaballos");
        const sel = document.getElementById("caballo_id");

        cont.innerHTML = "";
        sel.innerHTML = "";

        data.forEach(c => {
            sel.innerHTML += `<option value="${c.id}">${c.nombre} - ${c.raza}</option>`;

            cont.innerHTML += `
            <div class="item">
                <h3>${c.nombre}</h3>
                ${c.foto_url ? `<img src="${c.foto_url}">` : "<p><em>Sin foto</em></p>"}
                <p><strong>Raza:</strong> ${c.raza}</p>
                <p><strong>Nacimiento:</strong> ${fechaES(c.fecha_nacimiento) || "-"}</p>
                <p><strong>Estado:</strong> ${c.enfermo ? "Enfermo" : "Disponible"}</p>
                <p>${c.observaciones || ""}</p>
                <button type="button" onclick="editarCaballoPorId(${c.id})">Editar</button>
                <button type="button" class="btn-red" onclick="eliminarCaballo(${c.id})">Eliminar</button>
            </div>`;
        });

        mensaje("caballosMsg","Caballos cargados correctamente","ok");

    }catch(e){
        mensaje("caballosMsg","Error cargando caballos","err");
    }
}

function editarCaballoPorId(id){
    const c = caballosCache.find(x => Number(x.id) === Number(id));
    if(!c){
        mensaje("caballosMsg","No se encontró el caballo","err");
        return;
    }

    showSection("caballos");

    document.getElementById("edit_caballo_id").value = c.id;
    document.getElementById("caballo_nombre").value = c.nombre || "";
    document.getElementById("caballo_raza").value = c.raza || "";
    document.getElementById("caballo_fecha_nacimiento").value = c.fecha_nacimiento || "";
    document.getElementById("caballo_foto").value = "";
    document.getElementById("caballo_enfermo").value = c.enfermo ? "1" : "0";
    document.getElementById("caballo_observaciones").value = c.observaciones || "";

    mensaje("caballoMsg","Editando caballo #" + c.id + ". Si no seleccionas nueva foto, se mantiene la anterior.","info");
    window.scrollTo({top:0,behavior:"smooth"});
}

async function eliminarCaballo(id){
    if(!confirm("¿Eliminar caballo #" + id + "?")) return;

    try{
        await api("/api/caballos/" + id,{method:"DELETE"});
        mensaje("caballosMsg","Caballo eliminado correctamente","ok");
        cargarCaballos();
    }catch(e){
        mensaje("caballosMsg", e.mensaje || e.message || "No se pudo eliminar el caballo","err");
    }
}

/* RESERVAS */

function limpiarReserva(){
    document.getElementById("reserva_id").value = "";
    document.getElementById("fecha").value = "";
    document.getElementById("comentarios").value = "";
    mensaje("reservaMsg","Formulario listo para nueva reserva","info");
}

async function guardarReserva(){
    limpiar("reservaMsg");

    const id = document.getElementById("reserva_id").value;

    const body = {
        caballo_id: document.getElementById("caballo_id").value,
        fecha: document.getElementById("fecha").value,
        hora: document.getElementById("hora").value,
        comentarios: document.getElementById("comentarios").value
    };

    try{
        const url = id ? "/api/reservas/" + id : "/api/reservas";
        const method = id ? "PUT" : "POST";

        const data = await api(url,{
            method: method,
            body: JSON.stringify(body)
        });

        mensaje("reservaMsg", data.mensaje || "Reserva guardada correctamente","ok");
        limpiarReserva();
        cargarReservas();

    }catch(e){
        mensaje("reservaMsg", e.mensaje || e.message || "No se pudo guardar la reserva","err");
    }
}

async function cargarReservas(){
    limpiar("reservasMsg");

    try{
        const data = await api("/api/reservas");
        reservasCache = data;
        const cont = document.getElementById("listaReservas");
        cont.innerHTML = "";

        if(!Array.isArray(data) || data.length === 0){
            cont.innerHTML = "<p>No hay reservas.</p>";
            return;
        }

        data.forEach(r => {
            cont.innerHTML += `
            <div class="item">
                <h3>Reserva #${r.id}</h3>
                <p><strong>Fecha:</strong> ${fechaES(r.fecha)}</p>
                <p><strong>Hora:</strong> ${r.hora}</p>
                <p><strong>Caballo:</strong> ${r.caballo ? r.caballo.nombre : r.caballo_id}</p>
                <p><strong>Estado:</strong> ${r.estado || "pendiente"}</p>
                <p><strong>Pago:</strong> ${r.estado_pago || "pendiente"}</p>
                <p>${r.comentarios || ""}</p>
                <button type="button" onclick="editarReservaPorId(${r.id})">Editar</button>
                <button type="button" class="btn-green" onclick="prepararPago(${r.id})">Pagar</button>
                <button type="button" class="btn-red" onclick="eliminarReserva(${r.id})">Eliminar</button>
            </div>`;
        });

        mensaje("reservasMsg","Reservas cargadas correctamente","ok");

    }catch(e){
        mensaje("reservasMsg","Error cargando reservas","err");
    }
}

function editarReservaPorId(id){
    const r = reservasCache.find(x => Number(x.id) === Number(id));
    if(!r){
        mensaje("reservasMsg","No se encontró la reserva","err");
        return;
    }

    showSection("reservas");
    document.getElementById("reserva_id").value = r.id;
    document.getElementById("caballo_id").value = r.caballo_id;
    document.getElementById("fecha").value = r.fecha;
    document.getElementById("hora").value = r.hora;
    document.getElementById("comentarios").value = r.comentarios || "";
    mensaje("reservaMsg","Editando reserva #" + r.id,"info");
    window.scrollTo({top:0,behavior:"smooth"});
}

async function eliminarReserva(id){
    if(!confirm("¿Eliminar reserva #" + id + "?")) return;

    try{
        await api("/api/reservas/" + id,{method:"DELETE"});
        mensaje("reservasMsg","Reserva eliminada correctamente","ok");
        cargarReservas();
    }catch(e){
        mensaje("reservasMsg","No se pudo eliminar la reserva","err");
    }
}

/* PAGOS */

function prepararPago(id){
    showSection("pagos");
    document.getElementById("pago_reserva_id").value = id;
    mensaje("pagoMsg","Preparado pago para reserva #" + id,"info");
}

async function registrarPago(){
    limpiar("pagoMsg");

    try{
        const data = await api("/api/pagos",{
            method:"POST",
            body:JSON.stringify({
                reserva_id: document.getElementById("pago_reserva_id").value,
                plataforma: "Stripe",
                cantidad: document.getElementById("cantidad").value,
                comision: document.getElementById("comision").value,
                referencia_pago: document.getElementById("referencia_pago").value
            })
        });

        mensaje("pagoMsg", data.mensaje || "Pago registrado correctamente","ok");
        cargarPagos();
        cargarReservas();

    }catch(e){
        mensaje("pagoMsg", e.mensaje || e.message || "No se pudo registrar el pago","err");
    }
}

async function cargarPagos(){
    limpiar("pagosMsg");

    try{
        const data = await api("/api/pagos");
        const cont = document.getElementById("listaPagos");
        cont.innerHTML = "";

        if(!Array.isArray(data) || data.length === 0){
            cont.innerHTML = "<p>No hay pagos.</p>";
            return;
        }

        data.forEach(p => {
            cont.innerHTML += `
            <div class="item">
                <h3>Pago #${p.id}</h3>
                <p><strong>Reserva:</strong> ${p.reserva_id}</p>
                <p><strong>Plataforma:</strong> ${p.plataforma}</p>
                <p><strong>Cantidad:</strong> ${p.cantidad} €</p>
                <p><strong>Comisión:</strong> ${p.comision} €</p>
                <p><strong>Referencia:</strong> ${p.referencia_pago || "-"}</p>
                <p><strong>Estado:</strong> ${p.estado || "-"}</p>
            </div>`;
        });

        mensaje("pagosMsg","Pagos cargados correctamente","ok");

    }catch(e){
        mensaje("pagosMsg","Error cargando pagos","err");
    }
}

/* ADMIN */

async function cargarUsuarios(){
    limpiar("adminMsg");

    try{
        const data = await api("/api/admin/usuarios");
        const cont = document.getElementById("adminContenido");
        cont.innerHTML = "";

        data.forEach(u => {
            cont.innerHTML += `
            <div class="item">
                <h3>${u.nombre}</h3>
                <p>${u.email}</p>
                <p><span class="badge">${u.rol}</span></p>
            </div>`;
        });

        mensaje("adminMsg","Usuarios cargados","ok");

    }catch(e){
        mensaje("adminMsg","No se pudieron cargar usuarios. Comprueba que estás con Ana admin.","err");
    }
}

async function cargarAdminReservas(){
    limpiar("adminMsg");

    try{
        const data = await api("/api/admin/reservas");
        const cont = document.getElementById("adminContenido");
        cont.innerHTML = "";

        data.forEach(r => {
            cont.innerHTML += `
            <div class="item">
                <h3>Reserva #${r.id}</h3>
                <p><strong>Usuario:</strong> ${r.usuario ? r.usuario.nombre : r.usuario_id}</p>
                <p><strong>Caballo:</strong> ${r.caballo ? r.caballo.nombre : r.caballo_id}</p>
                <p><strong>Fecha:</strong> ${fechaES(r.fecha)} ${r.hora}</p>
                <p><strong>Estado:</strong> ${r.estado}</p>
                <button type="button" class="btn-green" onclick="cambiarEstado(${r.id},'confirmada')">Confirmar</button>
                <button type="button" class="btn-red" onclick="cambiarEstado(${r.id},'cancelada')">Cancelar</button>
            </div>`;
        });

        mensaje("adminMsg","Reservas admin cargadas","ok");

    }catch(e){
        mensaje("adminMsg","No se pudieron cargar reservas admin","err");
    }
}

async function cambiarEstado(id,estado){
    try{
        await api("/api/admin/reservas/" + id + "/estado",{
            method:"PUT",
            body:JSON.stringify({estado: estado})
        });
        cargarAdminReservas();
    }catch(e){
        mensaje("adminMsg","No se pudo cambiar el estado","err");
    }
}

async function cargarAdminPagos(){
    limpiar("adminMsg");

    try{
        const data = await api("/api/admin/pagos");
        const cont = document.getElementById("adminContenido");
        cont.innerHTML = "";

        data.forEach(p => {
            cont.innerHTML += `
            <div class="item">
                <h3>Pago #${p.id}</h3>
                <p><strong>Reserva:</strong> ${p.reserva_id}</p>
                <p><strong>Cantidad:</strong> ${p.cantidad} €</p>
                <p><strong>Plataforma:</strong> ${p.plataforma}</p>
                <p><strong>Estado:</strong> ${p.estado || "-"}</p>
            </div>`;
        });

        mensaje("adminMsg","Pagos admin cargados","ok");

    }catch(e){
        mensaje("adminMsg","No se pudieron cargar pagos admin","err");
    }
}
</script>

</body>
</html>
