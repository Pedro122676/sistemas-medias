<?php 
require 'config/db.php'; 
include 'includes/header.php'; 

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $turma_id = (int)$_POST['turma_id'];

    if (!empty($nome) && $turma_id > 0) {
        $stmt = $pdo->prepare("INSERT INTO alunos (nome, turma_id) VALUES (?, ?)");
        $stmt->execute([$nome, $turma_id]);
        // Criar registro de notas vazio para o aluno recém-cadastrado
        $aluno_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO notas (aluno_id) VALUES (?)");
        $stmt->execute([$aluno_id]);
        $mensagem = "Aluno cadastrado com sucesso!";
    } else {
        $erro = "Preencha todos os campos corretamente!";
    }
}

// Buscar turmas abertas para o select
$stmt = $pdo->query("SELECT id, nome FROM turmas WHERE fechada = 0 ORDER BY nome");
$turmas = $stmt->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Cadastrar Novo Aluno</h4>
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
                        <label class="form-label">Nome Completo do Aluno</label>
                        <input type="text" name="nome" class="form-control" required placeholder="Ex: João Silva">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Turma</label>
                        <select name="turma_id" class="form-select" required>
                            <option value="">Selecione uma turma...</option>
                            <?php foreach($turmas as $turma): ?>
                                <option value="<?= $turma['id'] ?>"><?= htmlspecialchars($turma['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Cadastrar Aluno</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>