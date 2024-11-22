<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use ErrorException;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;


class WebController extends Controller
{

    public function getRoles(){
        $roles=DB::TABLE("GYM_ROL")->GET();

        return response()->json(['status' => 'success', 'response'=>$roles]);


    }
    
    public function inicioSesion(Request $request){


            $correo = $request->correo;
            $password = $request->password;
        
            // Busca el usuario por correo
            $usuario = DB::table('GYM_USUARIO')
                ->where('CORREO_USUARIO', $correo)
                ->first();
        
            // Verifica si el usuario existe
            if (!$usuario) {


                return response()->json([
                    'success' => false,
                    'message' => 'Correo no encontrado.',
                ]);
            }
        
            // Valida la contraseña
            if ($usuario->password_usuario === $password) { // Si las contraseñas están cifradas, usa Hash::check()
                return response()->json([
                    'success' => true,
                    'message' => 'Inicio de sesión exitoso.',
                    'id_usuario'=>$usuario->id_usuario,
                    'id_rol'=>$usuario->id_rol
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Contraseña incorrecta.',
                ]);
            }
    
        
    }

    public function menusySubmenus($id){

        if($id==1 ){
            $resultados = DB::table("GYM_SUBMENU AS sub")
            ->join("GYM_MENU AS menu", "sub.ID_MENU", "=", "menu.ID_MENU")
            // ->where("sub.ID_ROL", $id)
            ->select("menu.NOMBRE_MENU AS menu", "menu.ID_MENU AS id_menu", "sub.NOMBRE_SUBMENU AS nombre_submenu","sub.ID_SUBMENU as id_submenu","sub.ABRIR_MODAL as abrir_modal") // cambia a minúsculas aquí
            ->get();
    
        }else{
            $resultados = DB::table("GYM_SUBMENU AS sub")
            ->join("GYM_MENU AS menu", "sub.ID_MENU", "=", "menu.ID_MENU")
            ->where("sub.ID_ROL", $id)
            ->select("menu.NOMBRE_MENU AS menu", "menu.ID_MENU AS id_menu",
             "sub.NOMBRE_SUBMENU AS nombre_submenu","sub.ID_SUBMENU as id_submenu","sub.ABRIR_MODAL as abrir_modal") // cambia a minúsculas aquí
            ->get();
        }

        
    
    // Organizar los datos en un array con menús y sus submenús
    $menus = [];
    foreach ($resultados as $resultado) {
        $menuId = $resultado->id_menu;
    
        // Si el menú aún no está en el array, lo añadimos
        if (!isset($menus[$menuId])) {
            $menus[$menuId] = [
                'id' => $menuId,
                'nombre_menu' => $resultado->menu,
                'submenus' => []
            ];
        }
        // Añadimos el submenú solo si existe
        if ($resultado->nombre_submenu) {  // Cambia a minúsculas aquí también
            $menus[$menuId]['submenus'][] = [
                'nombre_submenu' => $resultado->nombre_submenu,
                'id_submenu'=> $resultado->id_submenu ,
                'abrir_modal'=>$resultado->abrir_modal// Cambia a minúsculas aquí también
            ];
        }
    }
    

    // Convertir a un array simple para respuesta JSON
    $menus = array_values($menus);

return response()->json(array_values($menus)); // Devolver solo el array


}
public function categoriasYsub($id)
{
    $id_rol = $id;

    // Obtenemos categorías y subcategorías con join
    $getCategorias = DB::table("GYM_SUB_CATEGORIA")
        ->leftJoin("GYM_CATEGORIA as gymcat", "gymcat.ID_CATEGORIA", "=", "GYM_SUB_CATEGORIA.REFERENCIA_CATEGORIA")
        ->select("GYM_SUB_CATEGORIA.NOMBRE_SUBCATEGORIA", "GYM_SUB_CATEGORIA.ID_SUB_CATEGORIA", "gymcat.NOMBRE_CATEGORIA", "gymcat.ID_CATEGORIA")
        ->get();

    $categorias = [];

    // Recorremos los resultados y organizamos en un array de categorías con sus subcategorías
    foreach ($getCategorias as $categoria) {
        $categoriaId = $categoria->id_categoria;

        // Si la categoría aún no está en el array, la añadimos
        if (!isset($categorias[$categoriaId])) {
            $categorias[$categoriaId] = [
                'id' => $categoriaId,
                'nombre_categoria' => $categoria->nombre_categoria,
                'subcategorias' => []
            ];
        }

        // Añadimos la subcategoría solo si existe
        if ($categoria->nombre_subcategoria) {
            $categorias[$categoriaId]['subcategorias'][] = [
                'nombre_subcategoria' => $categoria->nombre_subcategoria
            ];
        }
    }

    // Convertir a un array simple para respuesta JSON
    $getCategorias = array_values($categorias);

    return response()->json($getCategorias);
}

public function getSubcategorias()
{
    $subcategorias = DB::table("GYM_SUB_CATEGORIA")->get();

    return response()->json($subcategorias);
}
public function insertProdUpdateInventario(Request $request){
    $nombre_producto = $request->nombre_producto;
    $descripcion_producto = $request->descripcion_producto;
    $dias_garantia = $request->dias_garantia;
    $precio = $request->precio;
    $id_sub_categoria = $request->id_sub_categoria;
    $img_url = $request->img_url;
    $vid_url = $request->vid_url;
    $cantidad = $request->cantidad;
    $usuario_creo = $request->usuario_creo;

        // Preparar la sentencia Oracle con PL/SQL
        $sql = "
        BEGIN
            insertProdUpdateStock(
                :nombre_producto, 
                :descripcion_producto, 
                :dias_garantia, 
                :precio, 
                :id_sub_categoria, 
                :img_url, 
                :vid_url, 
                :cantidad, 
                :usuario_creo
            );
        END;
    ";

    // Ejecutar la sentencia usando DB::statement
    try {
        DB::statement($sql, [
            ':nombre_producto' => $nombre_producto,
            ':descripcion_producto' => $descripcion_producto,
            ':dias_garantia' => $dias_garantia,
            ':precio' => $precio,
            ':id_sub_categoria' => $id_sub_categoria,
            ':img_url' => $img_url,
            ':vid_url' => $vid_url,
            ':cantidad' => $cantidad,
            ':usuario_creo' => $usuario_creo
        ]);
        
        return response()->json(['status' => 'success', 'message' => 'Producto e inventario actualizados correctamente']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
}


    public function getProductos(){
        $productos= DB::table("GYM_PRODUCTO")->
        leftjoin("GYM_INVENTARIO as inv","inv.ID_PRODUCTO","=","GYM_PRODUCTO.ID_PRODUCTO")
        ->leftjoin("GYM_SUB_CATEGORIA as subcat","subcat.ID_SUB_CATEGORIA","=","GYM_PRODUCTO.ID_SUB_CATEGORIA")


        ->SELECT("GYM_PRODUCTO.NOMBRE_PRODUCTO","GYM_PRODUCTO.DESCRIPCION_PRODUCTO","GYM_PRODUCTO.PRECIO",
        "GYM_PRODUCTO.IMG_URL","GYM_PRODUCTO.IMG_URL","subcat.NOMBRE_SUBCATEGORIA", "inv.CANTIDAD_DISPONIBLE","GYM_PRODUCTO.ID_PRODUCTO")->get();

        return response()->json(['status' => 'success', 'response'=>$productos]);
    }

    public function getTipoEntrega(){
        $tipo = DB::TABLE("GYM_TIPO_ENTREGA")->get();

        return response()->json(['status' => 'success', 'response'=>$tipo]);
    }

    public function ingresarNuevaCompra(Request $request)
    {
        $nit_cliente = $request->nit_cliente;
        $tipo_entrega = $request->tipo_entrega;
        $usuario_creo = $request->id_usuario;  // Asegúrate de tener el ID correcto de usuario
        $idVenta = 0;  // Inicializamos idVenta a un valor numérico predeterminado
    
        // Buscar al cliente en la base de datos
        $usuarioEncontrado = DB::table("GYM_CLIENTES")
            ->select("NOMBRE_CLIENTE", "APELLIDO_CLIENTE", "NIT_CLIENTE")
            ->where("NIT_CLIENTE", $nit_cliente)
            ->first();
        
        $mensaje = '';  
        if($usuarioEncontrado){
            $mensaje = "Cliente: " . $usuarioEncontrado->nombre_cliente . " " . $usuarioEncontrado->apellido_cliente;
        }else{
            $mensaje = 'Consumidor Final';
        }
    
        // Llamar al procedimiento almacenado usando el enlace correcto
        DB::statement('BEGIN insertVentaNueva(:tipoEntrega, :nitCliente, :usuarioCreo); END;', [
            ':tipoEntrega' => $tipo_entrega,  // Parámetro de entrada
            ':nitCliente' => $nit_cliente,    // Parámetro de entrada
            ':usuarioCreo' => $usuario_creo, // Parámetro de entrada
        ]);

        $ultimoIdVenta = DB::table("GYM_VENTA")
            ->select("ID_VENTA")
            ->orderBy("ID_VENTA", "DESC")
            ->first();

        $idVenta = $ultimoIdVenta->id_venta;

        return response()->json(['status' => 'success', 'message' => 'Venta registrada con éxito', 'id_venta' => $idVenta, 'cliente' => $mensaje]);
    
        // Retornar o usar el ID generado
       
    }
    
    
    
    
    

    public function insertarNuevoCliente(Request $request){
        $nombre_cliente     =$request->nombre_cliente;
        $apellido_cliente =$request->apellido_cliente;
        $correo_cliente   =$request->correo_cliente;
        $telefono_cliente =$request->telefono_cliente;
        $direccion_cliente = $request-> direccion_cliente;
        $nit_cliente=$request->nit_cliente;
        $usuario_creo=$request->id_usuario;

        $sql = "
        BEGIN
            insertClienteNuevo(
                :nombre_cliente, 
                :apellido_cliente, 
                :correo_cliente, 
                :telefono_cliente, 
                :direccion_cliente, 
                :nit_cliente, 
                :usuario_creo,
            );
        END;
    ";

    // Ejecutar la sentencia usando DB::statement
    try {
        DB::statement($sql, [
            ':nombre_cliente' => $nombre_cliente,
            ':apellido_cliente' => $apellido_cliente,
            ':correo_cliente' => $correo_cliente,
            ':telefono_cliente' => $telefono_cliente,
            ':direccion_cliente' => $direccion_cliente,
            ':nit_cliente' => $nit_cliente,
            ':usuario_creo' => $usuario_creo

        ]); 
    
    
        return response()->json(['status' => 'success', 'message' => 'Cliente agregado con exito']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }



    }


    public function ingresarProductoFactura(Request $request){
        $id_venta = $request->id_factura;
        $id_producto = $request->id_producto;
        $cantidad = $request->cantidad;
        $usuario_creo = $request->id_usuario;  // Asegúrate de tener el ID correcto de usuario
    
        // Buscar al cliente en la base de datos
        $productoEncontrado = DB::table("GYM_PRODUCTO")
            ->select("NOMBRE_PRODUCTO", "PRECIO")
            ->where("ID_PRODUCTO", $id_producto)
            ->first();
        
        $mensaje = '';  
        if($productoEncontrado){
            $mensaje = "Producto: " . $productoEncontrado->nombre_producto . " " . $productoEncontrado->precio;
        }else{
            $mensaje = 'Producto no encontrado';
        }
    
        // Llamar al procedimiento almacenado usando el enlace correcto
        DB::statement('BEGIN insertProdDetalleVenta(:id_venta, :id_producto, :cantidad); END;', [
            ':id_venta' => $id_venta,  // Parámetro de entrada
            ':id_producto' => $id_producto,    // Parámetro de entrada
            ':cantidad' => $cantidad // Parámetro de entrada
        ]);

        $productosIngresados = DB::table ("GYM_DETALLE_VENTA as gdv")
        ->leftjoin("GYM_PRODUCTO as gp" , "gp.ID_PRODUCTO", "=", "gdv.ID_PRODUCTO")

        ->where("gdv.ID_VENTA", $id_venta)
        ->select("gp.NOMBRE_PRODUCTO", "gp.PRECIO", "gdv.CANTIDAD", "gdv.ID_PRODUCTO")
        ->get();

        $totalPrecio = 0;

        foreach ($productosIngresados as $producto) {
            $totalPrecio += $producto->precio * $producto->cantidad;
        }

        return response()->json(['status' => 'success', 'message' => 'Producto agregado a la factura', 'productos' => $productosIngresados, 'total' => $totalPrecio]);


    }

    public function eliminarProductoVenta(Request $request){
        $id_venta = $request->id_venta;
        $id_producto = $request->id_producto;
        $cantidad = $request->cantidad;
        $usuario_creo = $request->id_usuario;  // Asegúrate de tener el ID correcto de usuario
    
       $eliminarDetalleVenta = DB::table("GYM_DETALLE_VENTA")
        ->where("ID_VENTA", $id_venta)
        ->where("ID_PRODUCTO", $id_producto)
        ->delete();

     

        return response()->json(['status' => 'success', 'message' => 'Producto eliminado de la factura', 'productos' => $eliminarDetalleVenta]);
    }

    public function finalizarVenta(Request $request)
    {

            $id_venta = $request->id_venta;
            $total = $request->monto;
            $montoTarjeta = $request->montoTarjeta;
            $montoEfectivo = $request->montoEfectivo;
            $tipoPago = $request->tipoPago;
            $usuario_creo = $request->id_usuario;  // ID del usuario que realizó la acción
        
            // Se asegura de asignar valores correctos a montoTarjeta y montoEfectivo según el tipo de pago
            if ($tipoPago == 1) {
                $montoTarjeta = $total;
                $montoEfectivo = 0;
            } elseif ($tipoPago == 2) {
                $montoEfectivo = $total;
                $montoTarjeta = 0;
            }
        
            try {
                // Llamar al procedimiento almacenado
                DB::statement(
                    'BEGIN insertarDetallePago(:id_vent, :monto_efec, :monto_tarj, :id_tipo_pag); END;',
                    [
                        ':id_vent' => $id_venta,        // ID de la venta
                        ':monto_efec' => $montoEfectivo,  // Monto en efectivo
                        ':monto_tarj' => $montoTarjeta,   // Monto en tarjeta
                        ':id_tipo_pag' => $tipoPago       // Tipo de pago
                    ]
                );
        
                // Obtener el ID del detalle de pago recién insertado
                $getIdDetallePago = DB::table("GYM_DETALLE_PAGO")
                    ->select("ID_DETALLE_PAGO")
                    ->where("ID_VENTA", $id_venta)
                    ->orderBy("ID_DETALLE_PAGO", "DESC")
                    ->first();
        
                $idDetallePago = $getIdDetallePago->id_detalle_pago;  // Obtener el ID del último detalle de pago
        
                // Actualizar el total y el ID de detalle de pago en la venta
                $updateVenta = DB::table("GYM_VENTA")
                    ->where("ID_VENTA", $id_venta)
                    ->update([
                        'TOTAL' => $total,
                        'ID_DETALLE_PAGO' => $idDetallePago,  // Asignar el ID del detalle de pago
                    ]);
        
    



        // Obtiene los productos relacionados a la venta
        $productosIngresados = DB::table('GYM_DETALLE_VENTA as gdv')
            ->where('gdv.ID_VENTA', $id_venta)
            ->get();
    
        // Recorre los productos para actualizar el inventario
        foreach ($productosIngresados as $producto) {
            $id_producto = $producto->id_producto;
            $cantidad = $producto->cantidad;
    

                // Llamar al procedimiento almacenado
                DB::statement(
                    'BEGIN finalizarVentaActualizarInv(:id_producto, :cantidad); END;',
                    [
                        ':id_producto' => $id_producto, // Parámetro de entrada
                        ':cantidad' => $cantidad       // Parámetro de entrada
                    ]
                );

        }


    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error al actualizar el inventario: ' . $e->getMessage()
        ], 500);
    }

    
        return response()->json([
            'status' => 'success',
            'message' => 'Venta finalizada y el inventario actualizado correctamente'
        ]);
    }
    

 
  public function getLogVentas(){
        $logVentas = DB::table("VIEW_GYM_LOGS")
        ->get();

        return response()->json(['status' => 'success', 'response'=>$logVentas]);
    }

    public function getEmpleados()
    {
        $empleados = DB::table("GYM_USUARIO")
            ->leftJoin("GYM_ROL", "GYM_USUARIO.ID_ROL", "=", "GYM_ROL.ID_ROL")
            ->select("ID_USUARIO", "NOMBRE_USUARIO", "APELLIDO_USUARIO", "CORREO_USUARIO", "DIRECCION_USUARIO", "ROL","GYM_ROL.ID_ROL")
            ->get();

        return response()->json($empleados);

    }

    public function eliminarEmpleado(Request $request){
        $id_empleado = $request->id;
        $usuario_creo = $request->id_usuario;  // Asegúrate de tener el ID correcto de usuario
    
       $eliminarEmpleado = DB::table("GYM_USUARIO")
        ->where("ID_USUARIO", $id_empleado)
        ->delete();
        return response()->json(['status' => 'success', 'message' => 'Empleado eliminado correctamente']);

    }

    public function editarEmpleado(Request $request)
{
    $id_empleado = $request->id;
    $nombre_empleado = $request->nombre;
    $apellido_empleado = $request->apellido;
    $correo_empleado = $request->correo;
    $rol = $request->rol;
    $direccion_empleado = $request->direccion;
    $usuario_creo = $request->id_usuario;  // Asegúrate de tener el ID correcto del usuario logueado
    
    // Ejecutar el procedimiento almacenado en Oracle
    try {
        DB::statement("BEGIN
            actualizar_usuario(
                p_id_usuario => :id_empleado,
                p_nombre_usuario => :nombre_empleado,
                p_correo_usuario => :correo_empleado,
                p_password_usuario => NULL,  -- Puedes enviar el valor que necesites para la contraseña
                p_apellido_usuario => :apellido_empleado,
                p_dpi_usuario => NULL,      -- Si no necesitas este parámetro, puedes enviarlo como NULL o un valor vacío
                p_direccion_usuario => :direccion_empleado,
                p_id_rol => :rol,
                p_usuario_modifico => :usuario_creo
            );
        END;", [
            ':id_empleado' => $id_empleado,
            ':nombre_empleado' => $nombre_empleado,
            ':correo_empleado' => $correo_empleado,
            ':apellido_empleado' => $apellido_empleado,
            ':direccion_empleado' => $direccion_empleado,
            ':rol' => $rol,
            ':usuario_creo' => $usuario_creo
        ]);

        return response()->json(['status' => 'success', 'message' => 'Empleado actualizado correctamente']);
    } catch (\Exception $e) {
        // Manejo de errores en caso de que ocurra algún problema con el procedimiento
        return response()->json(['status' => 'error', 'message' => 'Error al actualizar el empleado', 'error' => $e->getMessage()]);
    }
}


    public function insertarUsuario(Request $request)
{
    $usuario_creo = $request->id_usuario;  // Obtener el ID del usuario logueado
    
    DB::statement('
        BEGIN
            insertar_usuario(
                :nombre_usuario, 
                :correo_usuario, 
                :password_usuario, 
                :apellido_usuario, 
                :dpi_usuario, 
                :direccion_usuario, 
                :id_rol, 
                :usuario_creo
            );
        END;
    ', [
        'nombre_usuario' => $request->nombre_usuario,
        'correo_usuario' => $request->correo_usuario,
        'password_usuario' => bcrypt($request->password_usuario),  // Encriptar la contraseña
        'apellido_usuario' => $request->apellido_usuario,
        'dpi_usuario' => $request->dpi_usuario,
        'direccion_usuario' => $request->direccion_usuario,
        'id_rol' => $request->id_rol,
        'usuario_creo' => $usuario_creo  // El ID del usuario logueado
    ]);
    
    return response()->json(data: ['message' => 'Usuario creado correctamente']);
}

public function generarReporteVenta(){
    $ventas = DB::table("GYM_REPORTE_VENTAS")
    ->whereNotNull("TIPO_PAGO")  // Correcto para filtrar donde TIPO_PAGO no sea nulo
    ->get();


    return response()->json($ventas);
}

public function getProductosEnVenta(Request $request){
    $id_venta = $request->id_venta;

    $getProductos = db::table("GYM_DETALLE_VENTA")->WHERE("id_venta",$id_venta)
    ->leftjoin("GYM_PRODUCTO as pro","pro.ID_PRODUCTO","=","GYM_DETALLE_VENTA.ID_PRODUCTO")
    ->get();

    return response()->json(data: ['response' => $getProductos]);

}

public function getVentasTotales(){


    $ventasTotales = DB::TABLE("VENTAS_TOTALES_PRODUCTO")->get();
    return response()->json($ventasTotales);



}


  
}


