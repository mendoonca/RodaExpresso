<?php
session_start();

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver autenticado, redirecionar para a página de login
    header("Location: ../../PgLogin.php");
    exit(); // Important to stop script execution after redirect
}

// Incluir a configuração de conexão com a base de dados
include '../../../base_dados/basedados.h';

// Query para obter os condutores e seus veículos
$query = "
SELECT 
    c.id_condutor,
    c.nome_condutor,
    c.classificacao,
    t.tipo_veiculo,
    t.matricula,
    AVG(a.nota) AS media_avaliacao
FROM condutor c
JOIN transporte t ON c.id_condutor = t.id_condutor
LEFT JOIN avaliacoes a ON c.id_condutor = a.id_condutor AND a.aprovado = 1
GROUP BY c.id_condutor, c.nome_condutor, c.classificacao, t.tipo_veiculo, t.matricula
ORDER BY c.classificacao DESC
";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Erro na consulta: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliações | Roda Expresso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            padding-left: 50px;
            font-weight: bold;
            font-style: italic;
            font-size: 36px;
        }
        .menu {
            display: flex;
            gap: 50px;
            padding-right: 150px;
            font-size: 25px;
        }
        .menu a {
            text-decoration: none;
            color: white;
        }
        .negrito {
            font-weight: bold;
            font-size: 26px;
        }
        .container {
            width: 80%;
            margin: auto;
            margin-top: 50px;
        }
        .avaliacao-container {
            background-color: #007bff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 30px;
        }
        .avaliacao-header {
            display: flex;
            align-items: center;
            gap: 30px;
        }
        .avaliacao-header img {
            width: 250px;
            height: 250px;
            object-fit: cover;
            border: 3px solid white;
        }
        .info-condutor {
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
        }
        .info-condutor p {
            color:rgb(255, 255, 255);
            font-size: 21px;
            margin-left: 50px;
        }
        .avaliacao-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        .avaliacao-info h2 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .avaliacao-info p {
            margin: 8px 0;
            font-size: 20px;
        }
        .avaliacao-info .avaliacao {
            font-size: 22px;
        }
        .avaliacao-info .botao-avaliacao {
            display: inline-block;
            padding: 12px 24px;
            margin-top: 15px;
            border: 2px solid white;
            color: white;
            background-color: transparent;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            align-self: center;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="../PgInicialAluno.php" style="text-decoration: none; color: white;">
            <h1>Roda Expresso</h1>
        </a>
        <div class="menu">
            <a href="../horarios.php">Horários</a>
            <a href="avaliacoes.php" class="negrito">Avaliações</a>
            <a href="../ofertas/ofertas.php">Ofertas</a>
            <a href="../conta/conta.php">Conta</a>
        </div>
    </div>

    <div class="container">
        <?php
        // Mostrar cada condutor dinamicamente
        while ($row = mysqli_fetch_assoc($result)) {
            // Determinar a imagem com base no gênero (simplificado - na prática você teria um campo no banco)
            $image = (strpos($row['nome_condutor'], 'Arminda') !== false ? 
                     '../../../images/mulher.png' : '../../../images/homem.png');
            
            // Formatar a classificação
            if ($row['media_avaliacao'] !== null) {
                $media_avaliacao = number_format($row['media_avaliacao'], 1);
            } else {
                $media_avaliacao = "Sem avaliações";
            }
        ?>
        <!-- Avaliação dinâmica -->
        <div class="avaliacao-container">
            <div class="avaliacao-header">
                <img src="<?php echo $image; ?>">
                <div class="info-condutor">
                    <h2><?php echo htmlspecialchars($row['nome_condutor']); ?></h2>
                    
                    <!-- Linha 1: Veículo e Matrícula lado a lado -->
                    <div class="linha-superior" style="display: flex; justify-content: space-between;">
                        <p><span class="negrito">Veículo:</span> <?php echo htmlspecialchars($row['tipo_veiculo']); ?></p>
                        <p><span class="negrito">Matrícula:</span> <?php echo htmlspecialchars($row['matricula']); ?></p>
                    </div>
                    
                    <!-- Linha 2: Avaliação centralizada -->
                    <div class="linha-inferior" style="text-align: center; margin-top: 8px;">
                        <p><span class="negrito">Avaliação:</span> <?php echo is_numeric($media_avaliacao) ? $media_avaliacao . "/5.0" : $media_avaliacao; ?></p>
                    </div>
                </div>
            </div>
            <div class="avaliacao-info">
                <a href="darAvaliacao.php?id=<?php echo $row['id_condutor']; ?>" class="botao-avaliacao">
                    Dar Avaliação
                </a>
            </div>
        </div>
        <?php
        }
        
        // Liberar o resultado
        mysqli_free_result($result);
        ?>
    </div>
</body>
</html>