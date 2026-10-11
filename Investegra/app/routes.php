<?php

use App\Core\Router;

/*
 * Mapa de páginas => Controller@ação.
 * As URLs foram mantidas (login.php, carteiras.php...) para não quebrar
 * links e formulários existentes (com ou sem a extensão .php).
 */

$router = new Router();

$router->add("index", "HomeController", "index");

/* Autenticação */
$router->add("login", "AuthController", "loginForm");
$router->add("processando_login", "AuthController", "login");
$router->add("cadastro", "AuthController", "cadastroForm");
$router->add("processando_cadastro", "AuthController", "cadastro");
$router->add("logout", "AuthController", "logout");

/* Carteiras */
$router->add("carteiras", "CarteiraController", "index");
$router->add("nova_carteira", "CarteiraController", "create");
$router->add("processando_carteira", "CarteiraController", "store");
$router->add("editar_carteira", "CarteiraController", "edit");
$router->add("atualizar_carteira", "CarteiraController", "update");
$router->add("excluir_carteira", "CarteiraController", "destroy");

/* Ativos */
$router->add("ativos", "AtivoController", "index");
$router->add("novo_ativo", "AtivoController", "create");
$router->add("processando_ativo", "AtivoController", "store");
$router->add("editar_ativo", "AtivoController", "edit");
$router->add("atualizar_ativo", "AtivoController", "update");
$router->add("excluir_ativo", "AtivoController", "destroy");

/* Dashboard e análises */
$router->add("dashboard", "DashboardController", "index");
$router->add("indicadores", "IndicadoresController", "index");
$router->add("analises", "AnaliseController", "index");
$router->add("analise_carteira", "AnaliseController", "carteira");
$router->add("historico", "HistoricoController", "index");

/* Simulações */
$router->add("simulacao", "SimulacaoController", "index");
$router->add("processar_simulacao", "SimulacaoController", "processar");
$router->add("historico_simulacoes", "SimulacaoController", "historico");

/* Alertas e recomendações */
$router->add("alertas", "AlertaController", "index");
$router->add("recomendacoes", "RecomendacaoController", "index");

/* Perfil de investidor */
$router->add("questionario_perfil", "PerfilController", "questionario");
$router->add("processar_questionario", "PerfilController", "processar");
$router->add("resultado_quiz", "PerfilController", "resultado");
$router->add("resultado", "PerfilController", "resultado");
$router->add("meu_perfil", "PerfilController", "perfil");

/* API (JSON) */
$router->add("api/chat", "ApiController", "chat");
$router->add("api/cotacao", "ApiController", "cotacao");
$router->add("api/buscar_ativos", "ApiController", "buscarAtivos");

return $router;
