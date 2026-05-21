<?php 
require 'config/db.php'; 
include 'includes/header.php'; 

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    
    if (!empty($nome)) {
        $stmt = $pdo->prepare("INSERT INTO turmas (nome) VALUES (?)");
        $stmt->execute([$nome]);
        $mensagem = "Turma cadastrada com sucesso!";
    } else {
        $erro = "O nome da turma é obrigatório!";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Cadastrar Nova Turma</h4>
            </div>
            <div class="card-body">
                <?php if (isset($mensagem)): ?>
                    <div class="alert alert-success"><?= $mensagem ?></div>
                <?php endif; ?>
                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger"><?= $erro ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nome da Turma</label>
                        <input type="text" name="nome" class="form-control" required placeholder="Ex: 3º Ano A">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Cadastrar Turma</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>