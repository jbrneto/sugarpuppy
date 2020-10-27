<?php

use \SugarPuppy\Env;
use \SugarPuppy\DB;
use \SugarPuppy\SPException;

class LoginController {
  private static $PRE_SALT = 'SUg4R';
  private static $POS_SALT = 'PuppY';

  private static function criarChaveAcesso($chave) {
    return password_hash(self::$PRE_SALT.$chave.self::$POS_SALT, PASSWORD_DEFAULT);
  }
  public static function validarChaveAcesso($chave, $cifra) {
    return password_verify(self::$PRE_SALT.$chave.self::$POS_SALT, $cifra);
  }

  public static function loginUsuario($dados) {
    $email = $dados['email'];
    $senha = $dados['senha'];

    $query = "
    SELECT id FROM usuario
    WHERE email = '{$email}'
      AND senha = '{$senha}'
      AND ativo = 'S'
    LIMIT 1";

    DB::getConnection();

    $result = DB::execute($query);

    if ($result->num_rows == 0) {
      throw new SPException(500, "login_invalido");
    }

    $row = $result->fetch_assoc();

    $response = [
      'access_key' => static::criarChaveAcesso($row['id_usuario']),
      'id_usuario' => $row['id_usuario']
    ];

    return $response;
  }
  public static function carregarLogin($id_usuario) {
    $query = "
    SELECT id, nome
    FROM usuario
    WHERE id = {$id_usuario}
    LIMIT 1";

    DB::getConnection();

    $result = DB::execute($query);

    if ($result->num_rows == 0) {
      throw new SPException(500, "login_invalido");
    }

    $row = $result->fetch_assoc();

    Env::Set('nome', $row['nome']);

    return $row;
  }
}

?>