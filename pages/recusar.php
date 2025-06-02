<?php
    session_start();
    include '../base_dados/basedados.h';
    include 'ConstUtilizadores.php';

    // Verificar se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) {
        // Se não estiver autenticado, redirecionar para a página de login
        header("Location: ../PgLogin.php");
        exit();
    }

    // Verificar se o ID do utilizador foi passado
    if (!isset($_GET['id'])) {
        // Se não houver ID, redirecionar para a lista
        header("Location: administrador/aprovacaoRegisto.php");
        exit();
    }

    $id_utilizador = $_GET['id'];

    // Verificar a conexão
    if (!$conn) {
        die("Falha na conexão: " . mysqli_connect_error());
    }

    // Consultar os dados do utilizador
    $sql = "SELECT nome_utilizador, email FROM utilizador WHERE id_utilizador = $id_utilizador";
    $resultado = mysqli_query($conn, $sql);

    if (!$resultado || mysqli_num_rows($resultado) == 0) {
        die("Erro ao consultar o utilizador: " . mysqli_error($conn));
    }

    // Obter os dados do utilizador
    $utilizador = mysqli_fetch_assoc($resultado);

    // Atualizar o tipo de utilizador para 'utilizador'
    $sql_update = "UPDATE utilizador SET tipo_utilizador = -1 WHERE id_utilizador = $id_utilizador";

    if (mysqli_query($conn, $sql_update)) {
        // Inserir no histórico
        $nome_completo = $utilizador['nome_utilizador'];
        $email = $utilizador['email'];
        $descricao = "Aprovação de registo de utilizador";
        $data_evento = date('Y-m-d'); // Data atual
        $resultado_evento = "Recusado"; // Resultado da aprovação

        $sql_historico = "INSERT INTO historico (user_id, tipo_evento, nome_completo, email, descricao, data, resultado)
                          VALUES ($id_utilizador, 'Aprovação de Registo', '$nome_completo', '$email', '$descricao', '$data_evento', '$resultado_evento')";

        if (mysqli_query($conn, $sql_historico)) {
            echo "Utilizador aprovado com sucesso e histórico registrado.";
        } else {
            echo "Erro ao registrar no histórico: " . mysqli_error($conn);
        }
    } else {
        echo "Erro ao aprovar utilizador: " . mysqli_error($conn);
    }

    // Fechar a conexão
    mysqli_close($conn);

    // Redirecionar de volta para a página de aprovação
    header("Location: administrador/aprovacaoRegisto.php");
    exit();
?>
