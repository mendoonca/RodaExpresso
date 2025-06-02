<?php
    session_start();
    include '../base_dados/basedados.h';
    include 'ConstUtilizadores.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prevenir SQL Injection
        $email = mysqli_real_escape_string($conn, $email);
        $password = mysqli_real_escape_string($conn, $password);
        $password_hashed = md5($password);

        // Verificar se o utilizador existe
        $sql = "SELECT * FROM Utilizador WHERE email = '$email' AND password = '$password_hashed'";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            die("Erro na consulta: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $user['id_utilizador'];
            $_SESSION['user_name'] = $user['nome_utilizador'];
            $_SESSION['user_type'] = $user['tipo_utilizador'];

            // Redirecionar com base no tipo de utilizador
            switch ($user['tipo_utilizador']) {
                case UTILIZADOR_POR_VALIDAR:
                    header("Location: PgNaoValidados.php");
                    break;
                case UTILIZADOR:
                    header("Location: aluno/PgInicialAluno.php");
                    break;
                case ADMINISTRADOR:
                    header("Location: administrador/PgInicialAdministrador.php");
                    break;
                case GESTOR:
                    header("Location: gestor/PgInicialGestor.php");
                    break;
                default:
                    header("Location: PgInicialAluno.php");
                    break;
            }
            exit;
        } else {
            echo "<script>alert('Email ou password incorretos!'); window.location.href='PgLogin.php';</script>";
        }
    } else {
        session_destroy();
        header("Location: PgLogin.php");
        exit;
    }
?>