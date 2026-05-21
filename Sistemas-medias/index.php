<?php require 'config/db.php'; ?>
<?php include 'includes/header.php'; ?>

<h1 class="mb-4">Turmas Cadastradas</h1>

<?php
$stmt = $pdo->query("SELECT t.*, COUNT(a.id) as qtd_alunos 
                     FROM turmas t 
                     LEFT JOIN alunos a ON t.id = a.turma_id 
                     GROUP BY t.id ORDER BY t.nome");
$turmas = $stmt->fetchAll();
?>

<div class="row">
<?php foreach($turmas as $t): ?>
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($t['nome']) ?></h5>
                <p class="card-text">
                    <strong>Alunos:</strong> <?= $t['qtd_alunos'] ?><br>
                    <strong>Status:</strong> 
                    <span class="badge <?= $t['fechada'] ? 'bg-secondary' : 'bg-success' ?>">
                        <?= $t['fechada'] ? 'Fechada' : 'Aberta' ?>
                    </span>
                </p>
                <a href="ver-turma.php?id=<?= $t['id'] ?>" class="btn btn-primary btn-sm me-2">Ver Turma</a>
                <a href="cadastro-aluno.php" class="btn btn-success btn-sm">+ Aluno</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>