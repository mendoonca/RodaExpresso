<?php
    session_start();

    // Verifica se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../PgLogin.php");
        exit();
    }

    // Incluir ligação à base de dados
    include '../../../base_dados/basedados.h';

    // Verifica se existe um ID de condutor na query string
    if (!isset($_GET['id'])) {
        echo "Condutor não especificado.";
        exit();
    }

    $id_condutor = intval($_GET['id']);
    $id_utilizador = intval($_SESSION['user_id']);

    // Obter dados do condutor da base de dados
    $sql_condutor = "SELECT nome_condutor, classificacao FROM condutor WHERE id_condutor = ?";
    $stmt_condutor = mysqli_prepare($conn, $sql_condutor);
    mysqli_stmt_bind_param($stmt_condutor, "i", $id_condutor);
    mysqli_stmt_execute($stmt_condutor);
    $result_condutor = mysqli_stmt_get_result($stmt_condutor);

    if (mysqli_num_rows($result_condutor) === 0) {
        echo "Condutor não encontrado.";
        exit();
    }

    $condutor = mysqli_fetch_assoc($result_condutor);

    $classificacao = $condutor['classificacao'];
    $rating_display = $classificacao ? number_format($classificacao, 1) : 'N/A';

    // Processar submissão do formulário
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nota = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
        $comentario = isset($_POST['comentario']) ? mysqli_real_escape_string($conn, $_POST['comentario']) : '';

        if ($nota >= 1 && $nota <= 5) {
            $sql = "INSERT INTO avaliacoes (aprovado, id_utilizador, id_condutor, nota, comentario) VALUES (?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);
            $aprovado = 0;
            mysqli_stmt_bind_param($stmt, 'iiiis', $aprovado, $id_utilizador, $id_condutor, $nota, $comentario);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: avaliacoes.php?msg=sucesso");
                exit();
            } else {
                echo "Erro ao submeter avaliação: " . mysqli_error($conn);
            }
        } else {
            echo "Nota inválida.";
        }
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
        .form-avaliacao {
            display: flex;
            flex-direction: row;
            gap: 30px;
            width: 100%;
            padding: 20px;
            color: white;
        }
        .comment-section {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .rating-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .rating-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        .form-avaliacao textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: none;
            resize: vertical;
            min-height: 100px;
        }
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
        }
        .rating input {
            display: none;
        }
        .rating label {
            font-size: 40px;
            color: #ccc;
            cursor: pointer;
        }
        .rating input:checked ~ label {
            color: #ffcc00;
        }
        .rating label:hover,
        .rating label:hover ~ label {
            color: #ffcc00;
        }
        .botao-avaliar {
            background-color: white;
            color: #007bff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            width: 100%;
            max-width: 200px;
        }
        .botao-avaliar:hover {
            background-color: #f0f0f0;
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
        <div class="avaliacao-container">
            <div class="avaliacao-header">
                <img src="../../../images/homem.png" alt="Foto do condutor">
                <div class="info-condutor">
                    <h2><?php echo htmlspecialchars($condutor['nome_condutor']); ?></h2>
                    <p><span class="negrito">Avaliação Média:</span> <?php echo htmlspecialchars($rating_display); ?>/5.0</p>
                </div>
            </div>
        </div>

        <!-- Formulário -->
        <form class="avaliacao-container" method="POST">
            <div class="form-avaliacao">
                <div class="comment-section">
                    <textarea name="comentario" placeholder="Deixe aqui o seu comentário"></textarea>
                </div>
                <div class="rating-section">
                    <div class="rating-container">
                        <h3>Avalie o condutor:</h3>
                        <div class="rating">
                            <input type="radio" id="star5" name="rating" value="5" required />
                            <label for="star5">★</label>
                            <input type="radio" id="star4" name="rating" value="4" />
                            <label for="star4">★</label>
                            <input type="radio" id="star3" name="rating" value="3" />
                            <label for="star3">★</label>
                            <input type="radio" id="star2" name="rating" value="2" />
                            <label for="star2">★</label>
                            <input type="radio" id="star1" name="rating" value="1" />
                            <label for="star1">★</label>
                        </div>
                        <button type="submit" class="botao-avaliar">Avaliar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>