<?php
use App\Http\Controllers\WebController; // Asegúrate de importar el controlador

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::post('/login', [WebController::class, 'inicioSesion']);
Route::get('/menusySubmenus/{id}', [WebController::class, 'menusySubmenus']);
Route::get('/categoriasYsub/{id}', [WebController::class, 'categoriasYsub']);
Route::get('/getSubcategorias', [WebController::class, 'getSubcategorias']);
Route::post('/agregarProducto', [WebController::class, 'insertProdUpdateInventario']);
Route::get('csrf-token', function () {
    return response()->json(['csrfToken' => csrf_token()]);
});

Route::get('/getProductos',[WebController::class, 'getProductos']);
Route::get('/getTiposEntrega',[WebController::class, 'getTipoEntrega']); 
Route::post('/ingresarNuevaCompra',[WebController::class, 'ingresarNuevaCompra']); 
Route::post('/agregarProductoVenta',[WebController::class, 'ingresarProductoFactura']);
Route::post('/eliminarProductoVenta',[WebController::class, 'eliminarProductoVenta']);
Route::post('/finalizarVenta',[WebController::class, 'finalizarVenta']);
Route::get('/getLogVentas',[WebController::class, 'getLogVentas']);
Route::get('/getEmpleados',[WebController::class, 'getEmpleados']);
Route::post('/agregarEmpleado',[WebController::class, 'agregarEmpleado']);
Route::post('/editarEmpleado',[WebController::class, 'editarEmpleado']);
Route::get('/getRoles',[WebController::class, 'getRoles']);
Route::get('/generarReporteVenta',[WebController::class, 'generarReporteVenta']);
Route::post('/eliminarEmpleado',[WebController::class, 'eliminarEmpleado']);
Route::post('/getProductosEnVenta',[WebController::class, 'getProductosEnVenta']);
Route::get('/getVentasTotales',[WebController::class, 'getVentasTotales']);


