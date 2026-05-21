<?php 
require 'config/db.php'; 

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $stmt = $pdo->prepare("UPDATE turmas SET fechada = 1 WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: ver-turma.php?id=$id&fechada=1");
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>