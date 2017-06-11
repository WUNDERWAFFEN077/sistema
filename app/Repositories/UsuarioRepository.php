<?php
namespace App\Repositories;

use Core\{Auth, Log};
use App\Helpers\{ResponseHelper,AnexGridHelper};
use App\Models\{Usuario};
use Exception;
use Illuminate\Database\Capsule\Manager as DB;

class UsuarioRepository {
    private $usuario;

    public function __construct(){
        $this->usuario = new Usuario;
    }

    public function guardar(Usuario $model) : ResponseHelper {
        $rh = new ResponseHelper;

        try {
            $this->usuario->id = $model->id;
            $this->usuario->rol_id = $model->rol_id;
            $this->usuario->nombre = $model->nombre;
            $this->usuario->apellido = $model->apellido;
            $this->usuario->correo = $model->correo;

            if(!empty($model->id)){
                /*
                 * Al setear este valor a True hacemos que Eloquent lo considere como un registro existente,
                 * por lo tanto hará un update
                 */
                $this->usuario->exists = true;

                if(!empty($model->password)) {
                    $this->usuario->password = sha1($model->password);
                }
            } else {
                $this->usuario->password = sha1($model->password);
            }

            $this->usuario->save();
            $rh->setResponse(true);
        } catch (Exception $e) {
            Log::error(UsuarioRepository::class, $e->getMessage());
        }

        return $rh;
    }

    public function obtener($id) : Usuario {
        $usuario = new Usuario;

        try {
            $usuario = $this->usuario->find($id);
        } catch (Exception $e) {
            Log::error(UsuarioRepository::class, $e->getMessage());
        }

        return $usuario;
    }

    public function listar(string $q) : string {
        $anexgrid = new AnexGridHelper;
        $q = strtolower($q);

        try {
            $sql = "SELECT usuario.id, rol_id, usuario.nombre, rol.nombre AS  rol, usuario.correo
                    FROM usuario
                    INNER JOIN rol  ON (usuario.rol_id=rol.id)
                    WHERE LOWER(usuario.nombre) LIKE '%$q%'
                    ORDER BY $anexgrid->columna $anexgrid->columna_orden
                    LIMIT  $anexgrid->pagina, $anexgrid->limite
            ";
            $result = DB::select($sql);

            $sql = "SELECT COUNT(*) as t 
                    FROM usuario
                    INNER JOIN rol  ON (usuario.rol_id=rol.id)
                    WHERE LOWER(usuario.nombre) LIKE '%$q%'
            ";
            $total = DB::select($sql);

            

            return $anexgrid->responde(
                $result,
                $total[0]->t
            );
            
        } catch (Exception $e) {
            Log::error(UsuarioRepository::class, $e->getMessage());
        }

        return "";
    }

    public function eliminar(int $id) : ResponseHelper {
        $rh = new ResponseHelper;

        try {
            if(Auth::getCurrentUser()->id == $id) {
                $rh->setResponse(false, 'No puede eliminarse usted mismo');
            } else {
                $this->usuario->destroy($id);
            }
        } catch (Exception $e) {
            Log::error(UsuarioRepository::class, $e->getMessage());
        }

        return $rh;
    }

    public function autenticar(string $correo, string $password) : ResponseHelper {
        $rh = new ResponseHelper();

        try {
            $row = $this->usuario->where('correo', $correo)
                                 ->where('password', sha1($password))
                                 ->first();

            if(is_object($row)) {
                Auth::signIn([
                    'id' => $row->id,
                    'nombre' => $row->nombre,
                    'apellido' => $row->apellido,
                    'rol_id' => $row->rol_id
                ]);

                $rh->setResponse(true);
            } else {
                $rh->setResponse(false, 'Credenciales de aunteticación no válida');
                Log::critical(UsuarioRepository::class, "Intento fallido de autenticación para $correo");
            }
        } catch (Exception $e) {
            Log::error(UsuarioRepository::class, $e->getMessage());
        }

        return $rh;
    }
}