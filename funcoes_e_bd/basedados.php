<?php

require_once 'mysq.connect.php';
require_once 'funcaoSeguranca.php';
require 'PHPMailer/PHPMailerAutoload.php';

// ligar base de dados
function ligar_base_dados() { //função que permite o acesso à base de dados
    $ligacao = mysqli_connect(MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD) or die('Erro ao ligar ao servidor...'); //faz a conecção à base de dados, caso contrário sai da página exibindo a seguinte mensagem
    mysqli_select_db($ligacao, MYSQL_DATABASE) or die('Erro ao selecionar a base de dados...'); //seleciona uma base de dados, caso contrário sai da página exibindo a seguinte mensagem
    return $ligacao;
}

//Validações de login de Utilizadores
function verifica_Primeiro_Acesso() {

    $ver = $_SESSION['nr_acessos'];

    if ($ver == 0) {
        $var = true;
    } else {
        ($var = false);
    }
    return $var;
}

function valida_utilizador($username, $password) {
    $username = mysqli_real_escape_string($username);
    $password = mysqli_real_escape_string(md5($password));

    $ligacao = ligar_base_dados();
    $expressao = ("SELECT * FROM `utilizador` WHERE `username` = '$username' AND `password` = '$password'");


    $resultado = mysqli_query($expressao, $ligacao);

    $valor_retorno = false;

    if ((mysqli_num_rows($resultado) === 1)) {
        $dados = mysqli_fetch_array($resultado);
        if ($dados['password'] == $password) {
            $valor_retorno = $dados;
        }
    }


    mysqli_free_result($resultado);
    mysqli_close($ligacao);

    return $valor_retorno;
}

function valida_delegadoPais($username, $password) {
    $username = mysqli_real_escape_string($username);
    $password = mysqli_real_escape_string(md5($password));

    $ligacao = ligar_base_dados();
    $expressao = ("SELECT * FROM `delegado_pais` WHERE `username` = '$username' AND `password` = '$password'");


    $resultado = mysqli_query($expressao, $ligacao);

    $valor_retorno = false;

    if ((mysqli_num_rows($resultado) === 1)) {
        $dados = mysqli_fetch_array($resultado);
        if ($dados['password'] == $password) {
            $valor_retorno = $dados;
        }
    }

    mysqli_free_result($resultado);
    mysqli_close($ligacao);

    return $valor_retorno;
}

// Manipulação do forum
function listar_topico($id, $tbl) {
    $ligacao = ligar_base_dados();
    $id = mysqli_escape_string($ligacao,$id);
    $tbl = mysqli_escape_string($ligacao,$tbl);
    if ($tbl == "f_pergunta") {
        $sql = "SELECT * FROM $tbl WHERE id='$id'";
    } elseif ($tbl == "f_resposta") {
        $sql = "SELECT *FROM $tbl WHERE id_pergunta='$id'";
    }
    
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function ordenar_pergunta() {
    $ligacao = ligar_base_dados();
    $sql = "SELECT * FROM `f_pergunta` ORDER BY id ASC";
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function seleciona_idresposta() {
    $ligacao = ligar_base_dados();
    $sql = "SELECT MAX(r_id) AS Max_a_id FROM `f_resposta`";
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function contar_view($tbl, $id) {
    $ligacao = ligar_base_dados();
    $tbl = mysqli_escape_string($ligacao,$tbl);
    $id = mysqli_escape_string($ligacao,$id);
   
    $sql = "update $tbl set view=view+1 WHERE id='$id'";
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function contar_resposta($id) {

    $ligacao = ligar_base_dados();
    $id = mysqli_escape_string($ligacao,$id);
   
    $query = "UPDATE f_pergunta SET reply=reply+1 WHERE id='$id'";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

// Manipulação de grupos e jogos respectivos
function add_selecao_agrupo($id_grupo, $id_selecao) {
    $ligacao = ligar_base_dados();
    $id_grupo = mysqli_escape_string($ligacao,$id_grupo);
    $id_selecao = mysqli_escape_string($ligacao,$id_selecao);
   
    $query = ("UPDATE `wc_bd`.` selecao` SET `id_grupo` = '$id_grupo' WHERE ` selecao`.`id_selecao` = '$id_selecao'");
    $resultado = mysqli_query($ligacao,$query);
    mysqli_close($ligacao);
    return $resultado;
}

function redirect($url) {
   
    header("Location: $url");
}

//Modificar dados de utilizadores
function altera_dados($nome, $sexo, $idade, $password, $username, $email, $dataNasc, $endereco) {
    $nome = mysqli_escape_string($nome);
    $sexo = mysqli_escape_string($sexo);
    $idade = mysqli_escape_string($idade);
    $password = mysqli_escape_string($password);
    $email = mysqli_escape_string($email);
    $dataNasc = mysqli_escape_string($dataNasc);
    $endereco = mysqli_escape_string($endereco);
    $ligacao = ligar_base_dados();
    $query = ("UPDATE `wc_bd`.`utilizador` SET `nome` = '$nome', `password` = '$password', `email` = '$email', `sexo` = '$sexo', `data_nascimento` = '$dataNasc', `endereco` = '$endereco' WHERE `utilizador`.`username` = '$username'");
    $resultado = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $resultado;
}

function altera_dadosLOC($nome, $sexo, $idade, $password, $username, $email, $dataNasc, $endereco, $selecao) {
    $nome = mysqli_escape_string($nome);
    $sexo = mysqli_escape_string($sexo);
    $idade = mysqli_escape_string($idade);
    $password = mysqli_escape_string($password);
    $email = mysqli_escape_string($email);
    $dataNasc = mysqli_escape_string($dataNasc);
    $endereco = mysqli_escape_string($endereco);
    $selecao = mysqli_escape_string($selecao);
    $ligacao = ligar_base_dados();
    $query = ("UPDATE `wc_bd`.`delegado_pais` SET `username` = '$username', `nome` = '$nome', `selecao` = '$selecao', `email` = '$email', `sexo` = '$sexo', `password` = '$password', `endereco` = '$endereco', `data_nascimento` = '$dataNasc' WHERE `delegado_pais`.`username` = '$username'");
    $resultado = mysqli_query($query, $ligacao);

    mysqli_close($ligacao);
    return $resultado;
}

function listarElementosCd() {
    $query = ("SELECT * FROM `delegado_pais`");
    $ligacao = ligar_base_dados();
    $resultado = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $resultado;
}

function dadosElemLoc($username) {
    $username = mysqli_escape_string($username);
    $query = ("SELECT * FROM `delegado_pais` WHERE `username` = '$username'");
    $ligacao = ligar_base_dados();
    $resultado = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $resultado;
}

//Funções de manipulacao de grupos
function remove_g($id_selecao) {
    $id_selecao = mysqli_escape_string($id_selecao);
    $ligacao = ligar_base_dados();
    $query = ("UPDATE `wc_bd`.` selecao` SET `id_grupo`='' WHERE `id_selecao` = '$id_selecao'");

    if ($ligacao->query($query) === TRUE) {
        echo "Grupo deleted successfully";
    } else {
        echo "Error deleting record: " . $ligacao->error;
    }
    $ligacao->close();
}

//Manipulaçao de estadios
function listar_estadio() {


    $s = quemPode();

    if ($s == 0) {
        $final = "SELECT * FROM `estadio` WHERE `capacidade`='' or localizacao='' or nome =''";
    } else {
        ($final = "SELECT * FROM `estadio`");
    }
    $ligacao = ligar_base_dados();
    $result = mysqli_query($ligacao,$final);
    mysqli_close($ligacao);
    return $result;
}

function remover_estadio($id) {
    $id = mysqli_escape_string($id);
    $ligacao = ligar_base_dados();
    $sql = "DELETE FROM estadio WHERE id_estadio LIKE '$i'";
    mysqli_query($sql, $ligacao);
    if (!mysqli_query($sql, $ligacao)) {
        die('Erro: ' . mysqli_error());
    }
    ?> <script> alert("Removido com sucesso!");</script> <?php
}

//MAnipulação de listas de selecões
function listar_selecao($id) {
    $id = mysqli_escape_string($id);
    $var = quemPode();
    if ($var == 0) {
        $sql = "SELECT * FROM ` selecao` WHERE  id_grupo='' ";
    } else {
        ($sql = "SELECT *  FROM ` selecao` WHERE user_responsavel='$id'");
    }
    $ligacao = ligar_base_dados();
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function listar_selecao2() {
    $query = "SELECT * FROM ` selecao` ";
    $ligacao = ligar_base_dados();
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

function selecionarSelecao($idSel) {
    $idSel = mysqli_escape_string($idSel);
    $ligacao = ligar_base_dados();
    $query = "SELECT * FROM ` selecao`  WHERE id_selecao='$idSel'";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

function listarEquipasPorFase($fase) {
    $fase = mysqli_escape_string($fase);
    $query = ("SELECT * FROM ` selecao` WHERE `progresso` LIKE '$fase'");
    $ligacao = ligar_base_dados();
    $query1 = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);

    while ($row = mysqli_fetch_array($query1)) {
        ?>  <p align='center'> <?php echo $row['nome']; ?></p>
        <?php
    }
    if (mysqli_num_rows($query1) == 0) {
        ?>
        <h5 align='center'>Ainda não estao selecoes apuradas</h5>
        <?php
    }
}

//Manipulação de GOlos
function listarGolos($idJogo) {
    $idJogo = mysqli_escape_string($idJogo);
    $ligacao = ligar_base_dados();
    $query = "SELECT * FROM golo where id_jogo = $idJogo ORDER BY `minuto` ASC";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

function verificaGolos($idJogo, $eqCasa, $eqVisitante) {
    $idJogo = mysqli_escape_string($idJogo);
    $eqCasa = mysqli_escape_string($eqCasa);
    $eqVisitante = mysqli_escape_string($eqVisitante);
    $ligacao = ligar_base_dados();
    $query = "SELECT * FROM `jogo` WHERE `id_jogo` = $idJogo ";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    while ($row = mysqli_fetch_array($result)) {
        $ligacao = ligar_base_dados();
        if ($row['golo_Ecasa'] === $row['golo_Evisitante']) {
            $sql1 = "UPDATE ` selecao` SET empates=empates+1 WHERE id_selecao='$eqCasa'";
            $sql2 = "UPDATE ` selecao` SET empates=empates+1 WHERE id_selecao='$eqVisitante'";
            $sql3 = "UPDATE ` selecao` SET pontos=pontos+1 WHERE id_selecao='$eqCasa'";
            $sql4 = "UPDATE ` selecao` SET pontos=pontos+1 WHERE id_selecao='$eqVisitante'";

            mysqli_query($sql1, $ligacao);
            mysqli_query($sql2);
            mysqli_query($sql3);
            mysqli_query($sql4);

            mysqli_close($ligacao);
        } elseif ($row['golo_Ecasa'] > $row['golo_Evisitante']) {
            $ligacao = ligar_base_dados();
            $sql7 = "UPDATE ` selecao` SET pontos=pontos+3 WHERE id_selecao='$eqCasa'";
            $sql8 = ("UPDATE ` selecao` SET derrotas=derrotas+1 WHERE id_selecao='$eqVisitante'");
            $sql9 = ("UPDATE  ` selecao`  SET vitorias=vitorias+1 WHERE id_selecao='$eqCasa'");
            mysqli_query($sql7, $ligacao);
            mysqli_query($sql8);
            mysqli_query($sql9);
            mysqli_close($ligacao);
        } elseif ($row['golo_Ecasa'] < $row['golo_Evisitante']) {
            $ligacao = ligar_base_dados();
            $sql10 = "UPDATE ` selecao` SET  derrotas=derrotas+1 WHERE id_selecao='$eqCasa'";
            $sql11 = "UPDATE ` selecao` SET  vitorias=vitorias+1 WHERE id_selecao='$eqVisitante'";
            $sql12 = "UPDATE ` selecao` SET  pontos=pontos+3 WHERE id_selecao='$eqVisitante'";
            mysqli_query($sql10, $ligacao);
            mysqli_query($sql11);
            mysqli_query($sql12);
            mysqli_close($ligacao);
        }
    }
}

function addGolosMarcadosSelecaoTotal($selecao) {
    $selecao = mysqli_escape_string($selecao);
    $ligacao = ligar_base_dados();
    $query = "UPDATE ` selecao` SET totalGolosMarcados=totalGolosMarcados+1 WHERE id_selecao='$selecao'";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

function addGolosSofridosSelecaoTotal($selecao) {
    $selecao = mysqli_escape_string($selecao);
    $ligacao = ligar_base_dados();
    $query = "UPDATE ` selecao` SET totalGolosSofridos=totalGolosSofridos+1 WHERE id_selecao='$selecao'";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

function adicionarGoloSelecao($selecao) {
    $selecao = mysqli_escape_string($selecao);
    $ligacao = ligar_base_dados();
    $goloM = ("UPDATE ` selecao` SET golosMarcado=golosMarcado+1 WHERE id_selecao='$selecao'");
    mysqli_query($goloM, $ligacao);
    mysqli_close($ligacao);
}

function addGolosSofridosSelecao($selecao) {
    $selecao = mysqli_escape_string($selecao);
    $ligacao = ligar_base_dados();
    $query = "UPDATE ` selecao` SET golosSofridos=golosSofridos+1 WHERE id_selecao='$selecao'";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

function addGolosMarcados_EcasaJogo($idJogo) {
    $idJogo = mysqli_escape_string($idJogo);
    $ligacao = ligar_base_dados();
    $query = "UPDATE jogo SET golo_Ecasa=golo_Ecasa+1 WHERE id_jogo='$idJogo'";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

function addGolosMarcadoa_EvisitanteJogo($idJogo) {
    $idJogo = mysqli_escape_string($idJogo);
    $ligacao = ligar_base_dados();
    $query = "UPDATE jogo SET golo_Evisitante=golo_Evisitante+1 WHERE id_jogo='$idJogo'";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

function listar_cartoes($idJogo) {
    $idJogo = mysqli_escape_string($idJogo);
    $ligacao = ligar_base_dados();
    $query = "SELECT * FROM cartao where id_jogo = $idJogo ORDER BY `minuto` ASC";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

// Manipulação de jogadores
function listar_player($id) {
    $id = mysqli_escape_string($id);
    $ligacao = ligar_base_dados();
    $sql = "SELECT * FROM `jogador` WHERE `id_selecao`='$id'";
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function listar_jogador_porGolos() {
    $ligacao = ligar_base_dados();
    $query = "SELECT * FROM `jogador` ORDER BY `nrGolos` DESC";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

function remover_jogador($j) {
    $j = mysqli_escape_string($j);
    $ligacao = ligar_base_dados();
    $query = "DELETE FROM jogador WHERE `id_jogador`='$j'";
    if (!mysqli_query($query, $ligacao)) {
        die('Erro: ' . mysqli_error());
    }
    ?> <script> alert("Removido com sucesso!");</script> <?php
}

function adicionarGoloJogador($jogador) {
    $jogador = mysqli_escape_string($jogador);
    $ligacao = ligar_base_dados();
    $query = ("UPDATE jogador SET nrGolos=nrGolos+1 WHERE id_jogador='$jogador'");
    mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
}

function adicionarCartaoJogador_amarelo($jogador) {
    $jogador = mysqli_escape_string($jogador);
    $ligacao = ligar_base_dados();
    $query = "UPDATE jogador SET c_amarelo=c_amarelo+1 WHERE id_jogador='$jogador'";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

function adicionarCartaoJogador_vermelho($jogador) {
    $jogador = mysqli_escape_string($jogador);
    $ligacao = ligar_base_dados();
    $query = "UPDATE jogador SET c_vermelho=c_vermelho+1 WHERE id_jogador='$jogador'";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

function listarJogadoresDaSelecao($selecao) {
    $selecao = mysqli_escape_string($selecao);
    $ligacao = ligar_base_dados();
    $query = ("SELECT * FROM `jogador` WHERE `id_selecao` = '$selecao'");
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

//Manipulacao de Staff
function listar_staff($id) {
    $id = mysqli_escape_string($id);
    $ligacao = ligar_base_dados();
    $sql = "SELECT * FROM `staff/eqtecnica` WHERE `id_selecao`='$id'";
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function remover_staff($s) {
    $s = mysqli_escape_string($s);
    $ligacao = ligar_base_dados();
    $query = "DELETE FROM `staff/eqtecnica` WHERE `id_staff`='$s'";
    if (!mysqli_query($query, $ligacao)) {
        die('Erro: ' . mysqli_error());
    }
    ?> <script> alert("Removido com sucesso!");
            window.location = "index.php";</script> <?php
}

//MAnipulação de sistemas e calendario

function selecion_sistema() {
    $sql = "SELECT * FROM `calendario`";
    $ligacao = ligar_base_dados();
    $sistema = mysqli_query($sql, $ligacao);
    mysqli_close($ligacao);
    return $sistema;
}

function listar_calendario() {

    $sql = "SELECT * FROM `jogo` ORDER BY `data`,`hora` ASC";
    $ligacao = ligar_base_dados();
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function listar_calendario_porFase($fase) {
    $fase = mysqli_escape_string($fase);
    $sql = "SELECT * FROM `jogo` WHERE `fase` LIKE '$fase'ORDER BY `data`,`hora` ASC";
    $ligacao = ligar_base_dados();
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function reiniciar() {
    $ligacao = ligar_base_dados();
    $res = mysqli_query("SHOW TABLES", $ligacao);

    $tabelas = array();

    while ($row = mysqli_fetch_array($res)) {

        $tabela = "$row[0]";

        if (!(in_array($tabela, $tabelas))) {

            $sq = mysqli_query("TRUNCATE TABLE `$tabela`");

            if ($sq) {
                ?><script>alert("Reiniciado com sucesso!")
                                    window.location = "index.php";</script> <?php
            } else {
                die('Error:' . mysqli_error());
            }
        }
    }mysqli_close($ligacao);
}

//Manipulação de Grupos
function selecoes_comGrupo($grupo) {
    $grupo = mysqli_escape_string($grupo);
    $query = "SELECT * FROM ` selecao` WHERE `id_grupo` LIKE '$grupo' ORDER BY `pontos` DESC";
    $ligacao = ligar_base_dados();
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

function listar_grupos() {
    $sql = "SELECT * FROM `grupo`";
    $ligacao = ligar_base_dados();
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function verificaSeOjogoEdosgrupos($selecao1, $fase) {
    $selecao1 = mysqli_escape_string($selecao1);
    $fase = mysqli_escape_string($fase);
    $ligacao = ligar_base_dados();
    $sql = ("SELECT * FROM ` selecao` WHERE `id_selecao` = '$selecao1'");
    $resultado = mysqli_query($sql, $ligacao);
    mysqli_close($ligacao);
    while ($row = mysqli_fetch_array($resultado)) {
        $verifica = $row['id_grupo'];

        if ($verifica === $fase) {
            $E = TRUE;
        } else {
            $E = FALSE;
        }
    }
    return $E;
}

function validaGrupo($id_grupo) {
    $id_grupo = mysqli_escape_string($id_grupo);
    $linhas = 0;
    $query = ("SELECT * FROM ` selecao` WHERE `id_grupo` LIKE '$id_grupo'");
    $ligacao = ligar_base_dados();
    $result = mysqli_query($query, $ligacao);
    while ($row = mysqli_fetch_array($result)) {
        $linhas++;
    }
    if ($linhas <= 4) {
        $pode = TRUE;
    } else {
        $pode = false;
    }
    return $pode;
}

//Manipulação de JOgos

function atualizaJogos($fase, $arbito, $estadio, $data, $eqCasa, $eqVisitante, $hora) {
    $fase = mysqli_escape_string($fase);
    $arbitro = mysqli_escape_string($arbitro);
    $estadio = mysqli_escape_string($estadio);
    $data = mysqli_escape_string($data);
    $eqCasa = mysqli_escape_string($eqCasa);
    $eqVisitante = mysqli_escape_string($eqVisitante);
    $hora = mysqli_escape_string($hora);
    $ligacao = ligar_base_dados();
    $query = ("UPDATE `wc_bd`.`jogo` SET `fase` = '$fase', `id_arbito` = '$arbito', `id_estadio` = '$estadio', `$eqCasa` = '$eqVisitante', `data_nascimento` = '$eqCasa', `endereco` = '$endereco' WHERE `utilizador`.`username` = '$username'");
    $resultado = mysqli_query($query, $ligacao);

    mysqli_close($ligacao);
    return $resultado;
}

function listarSelecaoComFase($fase) {
    $fase = mysqli_escape_string($fase);
    $query = ("SELECT * FROM ` selecao` WHERE `progresso` LIKE '$fase'");
    $ligacao = ligar_base_dados();
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

function verificaDadosJogo($jogo) {
    $jogo = mysqli_escape_string($jogo);
    $query = ("SELECT * FROM `jogo` WHERE `id_jogo` = '$jogo' AND `equipaCasa` = ''");
    $ligacao = ligar_base_dados();
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);

    return $result;
}

function verificaEstadoJogo($jogo, $status) {
    $jogo = mysqli_escape_string($jogo);
    $status = mysqli_escape_string($status);
    $query = ("SELECT * FROM `jogo` WHERE `id_jogo` = $jogo AND `status` LIKE '$status'");
    $ligacao = ligar_base_dados();
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);

    return $result;
}

function adicionarCartaoAmareloJogo($idJogo) {
    $idJogo = mysqli_escape_string($idJogo);
    $ligacao = ligar_base_dados();
    $query = "UPDATE jogo SET nr_cartoesAmarelos=nr_cartoesAmarelos+1 WHERE id_jogo='$idJogo'";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

function adicionarCartoesVermelhosJogo($idJogo) {
    $idJogo = mysqli_escape_string($idJogo);
    $ligacao = ligar_base_dados();
    $query = "UPDATE jogo SET nrCartoesVermelhos=nrCartoesVermelhos+1 WHERE id_jogo='$idJogo'";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

// Manipulação do Country Delegate
function selecionarDelegado($idD) {
    $idD = mysqli_escape_string($idD);
    $ligacao = ligar_base_dados();
    $query = "SELECT * FROM `delegado_pais`  WHERE username='$idD'";
    $result = mysqli_query($query, $ligacao);
    mysqli_close($ligacao);
    return $result;
}

function gerarPassword($num_caracteres = 8) {
    $num_caracteres = mysqli_escape_string($num_caracteres);
    $password = "";

    // variável para definir quais o caractéres possíveis para a password

    $possiveis = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

    // para verificar quantos caractéres diferentes existem para gerar uma password

    $max = strlen($possiveis);

    // a password não pode ser ter mais caractéres do que os que foram predefinidos para $possiveis    

    if ($num_caracteres > $max) {

        $num_caracteres = $max;
    }

    // variável de incrementação para saber quantos caratéres já tem a password enquanto está a ser gerada

    $i = 0;

    // adiciona caracteres a $password até $num_caracteres estar completo    

    while ($i < $num_caracteres) {

        // escolhe um caracter dos possiveis

        $char = substr($possiveis, mt_rand(0, $max - 1), 1);

        // verificar se o caracter escolhido já está na $password?

        if (!strstr($password, $char)) {

            // se não estiver incluido adiciona o novo caracter...         

            $password .= $char;

            // ... e incrementa a variável $i        

            $i++;
        }
    }

    // Feito!

    return $password;
}

function listar_utilizadores() {
    $ligacao = ligar_base_dados();
    $sql = "SELECT * FROM `utilizador` ";
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function listar_jogosPorStatus($status) {
    $status = mysqli_escape_string($status);
    $sql = "SELECT * FROM `jogo` WHERE status='$status' ORDER BY `data`,`hora` ASC";
    $ligacao = ligar_base_dados();
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function listarNoticias() {
    $jogos = listar_jogosPorStatus('Realizado');
}

//Contar
function cartoesAmarelos() {
    $sql = "SELECT * FROM `cartao` WHERE `tipoCartao` LIKE 'Amarelo'";
               $ligacao = ligar_base_dados();
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function cartoesVermelhos() {
    $sql = "SELECT * FROM `cartao` WHERE `tipoCartao` LIKE 'Vermelho'";
               $ligacao = ligar_base_dados();
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function golos() {
    $sql = "SELECT * FROM `golo` WHERE `tipo` LIKE 'golo'";
               $ligacao = ligar_base_dados();
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function autogolos() {
    $sql = "SELECT * FROM `golo` WHERE `tipo` LIKE 'auto_golo'";
               $ligacao = ligar_base_dados();
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}



//Manipulação das moedas
function get_moedas() {
    $xml = simplexml_load_file("moedas.xml");

    $moedas = $xml->moeda;

    return $moedas;
}

//adiciona feed

function add_feed($titulo, $conteudo, $date) {

    $ligacao = ligar_base_dados();
    $sql = "INSERT INTO `wc_bd`.`feed` (`ID`, `titulo`, `conteudo`, `link`, `date`, `envio`) VALUES ('', '$titulo', '$conteudo', '', '$date', '')";
    mysqli_query($sql, $ligacao);
    mysqli_close($ligacao);
}

function listarFeed() {
    $ligacao = ligar_base_dados();
    $sql = "SELECT * FROM `feed` WHERE `envio` = 0";
    $result = mysqli_query($ligacao, $sql);
    mysqli_close($ligacao);
    return $result;
}

function modificarFeed() {
    
}

function MailLogin($nome, $email, $mensagem, $assunto) {
    $imagem = "<center><img src='fifa.jpg' alt='some_text'></center><br />";  //logotipo
    $mail = new PHPMailer(true);   // true - Retorna excepcÃµes
    $mensagem = utf8_decode($mensagem);  // para aparecer acentos
    $mensagem = $imagem . $mensagem;

    $mail->IsSMTP();   // Utilizador de SMTP
    try {
        $mail->Host = "smtp.sapo.pt";  // Servidor SMTP
        $mail->SMTPAuth = true;                   // Activar autenticaÃ§Ã£o SMTP
        $mail->Port = 587; // Porta
        $mail->Username = "suporte_fifa2018@sapo.pt";   // Utilizador do servidor SMTP
        $mail->Password = "suportefifa";         // Password do utilizador do SMTP

        $mail->SetFrom('fifa2018@sapo.pt', 'Fifa 2018');          // Email e nome de envio

        $mail->AddAddress($email, $nome);   // Email e nome do destinatÃ¡rio

        $mail->Subject = utf8_decode($assunto);    // Assunto da mensagem

        $mail->IsHTML(true);
        $mail->AltBody = 'O seu sistema de recepÃ§Ã£o de email nÃ£o suporta HTML';
        $mail->MsgHTML($mensagem);             // mensagem serÃ¡ enviado como HTML


        $mail->Send();
        return true;
    } catch (phpmailerException $e) {
        echo $e->errorMessage();                      // Erros provenientes do PHPMailer
        $mensagem = null;
        return false;
    } catch (Exception $e) {
        echo $e->getMessage();                        // Outros erros
        $mensagem = null;
        return false;
    }
}

function alteraFeed($id) {
    $ligacao = ligar_base_dados();
    $sql = "UPDATE `wc_bd`.`feed` SET `envio` = '1' WHERE `feed`.`ID` = $id";
    mysqli_query($sql, $ligacao);
    mysqli_close($ligacao);
}
function listar_estadios(){
       $ligacao = ligar_base_dados();
       
    $sql = "SELECT * FROM `estadio`";
    $estadio = mysqli_query($sql, $ligacao);
    mysqli_close($ligacao);
    
    return $estadio;
}