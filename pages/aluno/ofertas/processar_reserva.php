<?php
    session_start();
    
    // Verificar se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../PgLogin.php");
        exit();
    }

    // Incluir a configuração de conexão com a base de dados
    include '../../../base_dados/basedados.h';

    // Verificar se o formulário foi submetido
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verificar se as variáveis necessárias estão definidas
        if (isset($_POST['id_servico']) && isset($_POST['lugares_reservados'])) {
            $id_sugestao = $_POST['id_servico'];
            $lugares_reservados = $_POST['lugares_reservados'];
            $user_id = $_SESSION['user_id'];
            
            // Obter dados adicionais do formulário
            $data_hora = $_POST['data_hora'];
            $origem = $_POST['origem'];
            $destino = $_POST['destino'];
            $tipo_veiculo = $_POST['tipo_veiculo'];
            $matricula = $_POST['matricula'];
            $id_condutor = $_POST['id_condutor'];

            // Obter o nome do condutor
            $sql_condutor = "SELECT u.nome_utilizador 
                             FROM utilizador u
                             JOIN condutor c ON u.id_utilizador = c.id_condutor
                             WHERE c.id_condutor = ?";
            $stmt_condutor = mysqli_prepare($conn, $sql_condutor);
            mysqli_stmt_bind_param($stmt_condutor, "i", $id_condutor);
            mysqli_stmt_execute($stmt_condutor);
            $result_condutor = mysqli_stmt_get_result($stmt_condutor);
            $condutor = mysqli_fetch_assoc($result_condutor);
            
            if (!$condutor) {
                echo "Condutor não encontrado.";
                exit();
            }

            $nome_completo = $condutor['nome_utilizador'];
            $descricao = "Viagem de " . $origem . " para " . $destino;
            $resultado = "Confirmado para " . $lugares_reservados . " lugares.";

            // Inserir no histórico
            $sql_inserir = "INSERT INTO historico (user_id, tipo_evento, nome_completo, descricao, data, resultado)
                            VALUES (?, 'Reserva', ?, ?, ?, ?)";

            $stmt_inserir = mysqli_prepare($conn, $sql_inserir);
            if (!$stmt_inserir) {
                echo "Erro na preparação da consulta: " . mysqli_error($conn);
                exit();
            }

            // Corrigir a ordem dos parâmetros
            mysqli_stmt_bind_param($stmt_inserir, "issss", 
                $user_id, 
                $nome_completo,
                $descricao, 
                $data_hora, 
                $resultado
            );

            if (mysqli_stmt_execute($stmt_inserir)) {
                // Atualizar lugares disponíveis
                $sql_atualizar = "UPDATE ofertatransporte 
                                  SET lugares_disponiveis = lugares_disponiveis - ? 
                                  WHERE id_sugestao = ?";
                $stmt_atualizar = mysqli_prepare($conn, $sql_atualizar);
                mysqli_stmt_bind_param($stmt_atualizar, "ii", $lugares_reservados, $id_sugestao);
                mysqli_stmt_execute($stmt_atualizar);
                
                header("Location: ../PgInicialAluno.php");
                exit();
            } else {
                echo "Erro ao confirmar a viagem: " . mysqli_error($conn);
            }
        } else {
            echo "Erro: Parâmetros de reserva não encontrados.";
            exit();
        }
    } else {
        echo "Método de requisição inválido.";
        exit();
    }
?>