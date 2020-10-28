<?php

use \SugarPuppy\Env;
use \SugarPuppy\DB;
use \SugarPuppy\SPException;

class PetController {
  public static function buscar($filtros) {
    $q_where = "";
    $q_join = "";

    if (isset($filtros['animal']))
      $q_where .= " AND ".$filtros['animal'];

    if (isset($filtros['raca']))
      $q_where .= " AND ".$filtros['raca'];
    
    if (isset($filtros['cor']))
      $q_where .= " AND ".$filtros['cor'];

    if (isset($filtros['estado']))
      $q_where .= " AND ".$filtros['estado'];

    if (isset($filtros['cidade']))
      $q_where .= " AND ".$filtros['cidade'];

    if (isset($filtros['nao_vinculado'])) {
      $id_usuario = Env::Get('id_usuario');
      $q_join .= "
      LEFT JOIN pet_like pl
        ON pl.id_pet = p.id
        AND p.id_usuario = {$id_usuario}";
      $q_where .= " AND pl.id_pet IS NULL";
    }

    $query = "
    SELECT p.id, p.nome, p.descricao, p.url_foto 
    FROM pet p
    {$q_join}
    WHERE p.ativo = 'S'
      {$q_where}
    LIMIT 50";

    DB::getConnection();

    $pets = DB::fetchAll($query);

    if (!$pets) {
      throw new SPException(500, "erro_query");
    }

    return $pets;
  }
  public static function buscarCaracteristicas() {
    $query_cor = "SELECT id, cor FROM pet_cor";
    $query_estado = "SELECT id, estado FROM pet_estado";
    $query_cidade = "SELECT id, cidade, id_estado FROM pet_cidade";
    $query_animal = "SELECT id, animal FROM pet_animal";
    $query_raca = "SELECT id, raca, id_animal FROM pet_raca";

    DB::getConnection();

    $cores = DB::fetchAll($query_cor);
    if (!$cores) {
      throw new SPException(500, "erro_query_cores");
    }
    $estados = DB::fetchAll($query_estado);
    if (!$estados) {
      throw new SPException(500, "erro_query_estados");
    }
    $cidades = DB::fetchAll($query_cidade);
    if (!$cidades) {
      throw new SPException(500, "erro_query_cidades");
    }
    $animais = DB::fetchAll($query_animal);
    if (!$animais) {
      throw new SPException(500, "erro_query_animais");
    }
    $racas = DB::fetchAll($query_raca);
    if (!$racas) {
      throw new SPException(500, "erro_query_racas");
    }

    return [
      'cores' => $cores,
      'estados' => $estados,
      'cidades' => $cidades,
      'animais' => $animais,
      'racas' => $racas
    ];
  }
}

?>