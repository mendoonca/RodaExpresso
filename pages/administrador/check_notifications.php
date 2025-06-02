<?php
    session_start();
    include '../../base_dados/basedados.h';
    include '../ConstUtilizadores.php';

    header('Content-Type: application/json');

    $response = ['count' => 0];

    if (isset($_SESSION['user_id'])) {
        
        $sql = "SELECT COUNT(*) as total FROM utilizadores WHERE tipo_utilizador = '" . UTILIZADOR_POR_VALIDAR . "'";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $response['count'] = (int)$row['total'];
        }
        $conn->close();
    }

    echo json_encode($response);
?>