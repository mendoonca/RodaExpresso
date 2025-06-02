<?php
session_start();

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver autenticado, redirecionar para a página de login
    header("Location: ../../PgLogin.php");
    exit();
}

// Incluir a configuração de conexão com a base de dados
include '../../../base_dados/basedados.h';

// Verificar se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obter dados do formulário
    $veiculo = $_POST['veiculo'];
    $matricula = $_POST['matricula'];
    $horario = $_POST['horario'];
    $data = $_POST['data'];
    $lugares = $_POST['lugares'];
    $origem = $_POST['origem'];
    $destino = $_POST['destino'];
    $id_condutor = $_SESSION['user_id'];  // ID do condutor (utilizador logado)

    // Converter data e hora para formato datetime
    $data_hora = $data . ' ' . $horario;

    // Verificar se o id_condutor já existe na tabela condutor
    $sql_verificar_condutor = "SELECT * FROM condutor WHERE id_condutor = '$id_condutor'";
    $result = mysqli_query($conn, $sql_verificar_condutor);

    if (mysqli_num_rows($result) == 0) {
        // Obter nome e idade do condutor da tabela utilizador
        $sql_utilizador = "SELECT nome_utilizador, idade FROM utilizador WHERE id_utilizador = '$id_condutor'";
        $result_utilizador = mysqli_query($conn, $sql_utilizador);
        if ($result_utilizador) {
            $row = mysqli_fetch_assoc($result_utilizador);
            $nome_condutor = $row['nome_utilizador'];
            $idade = $row['idade'];

            // Inserir na tabela condutor
            $sql_inserir_condutor = "INSERT INTO condutor (id_condutor, nome_condutor, idade) 
                                     VALUES ('$id_condutor', '$nome_condutor', '$idade')";
            if (!mysqli_query($conn, $sql_inserir_condutor)) {
                echo "Erro ao adicionar condutor: " . mysqli_error($conn);
                exit();
            }
        } else {
            echo "Erro ao obter dados do utilizador: " . mysqli_error($conn);
            exit();
        }
    }

    // Inserir na tabela `transporte`
    $sql_transporte = "INSERT INTO transporte (tipo_veiculo, consumo, lotacao_maximo, matricula, id_condutor) 
                       VALUES ('$veiculo', 0.0, '$lugares', '$matricula', '$id_condutor')";  // Definir o consumo como 0.0 por agora

    if (mysqli_query($conn, $sql_transporte)) {
        // Obter o id do transporte inserido
        $id_transporte = mysqli_insert_id($conn);

        // Inserir na tabela `ofertatransporte`
        $sql_oferta = "INSERT INTO ofertatransporte (id_transporte, id_condutor, nome_condutor, origem, destino, data_hora, lugares_disponiveis) 
                       VALUES ('$id_transporte', '$id_condutor', '$nome_condutor', '$origem', '$destino', '$data_hora', '$lugares')";

        if (mysqli_query($conn, $sql_oferta)) {
            // Oferta criada com sucesso
            header("Location: ofertas.php");
            exit();
        } else {
            // Erro ao inserir na tabela ofertatransporte
            echo "Erro ao criar a oferta: " . mysqli_error($conn);
        }
    } else {
        // Erro ao inserir na tabela transporte
        echo "Erro ao criar transporte: " . mysqli_error($conn);
    }
}

// Fechar a conexão
mysqli_close($conn);
?>