<?php 
require 'config/db.php'; 
include 'includes/header.php'; 
require 'includes/functions.php';

$turma_id = (int)($_GET['id'] ?? 0);

if ($turma_id <= 0) {
    echo "<div class='alert alert-danger'>Turma inválida!</div>";
    include 'includes/footer.php';
    exit;
}

// Buscar dados da turma
$stmt = $pdo->prepare("SELECT * FROM turmas WHERE id = ?");
$stmt->execute([$turma_id]);
$turma = $stmt->fetch();

if (!$turma) {
    echo "<div class='alert alert-danger'>Turma não encontrada!</div>";
    include 'includes/footer.php';
    exit;
}

// Processar nota de recuperação
// Processar nota de recuperação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recuperacao'])) {
    $aluno_id = (int)$_POST['aluno_id'];
    $nota_rec = (float)$_POST['nota_recuperacao'];

    // Verifica se já existe registro de notas
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notas WHERE aluno_id = ?");
    $stmt->execute([$aluno_id]);
    $exists = $stmt->fetchColumn() > 0;

    if ($exists) {
        $stmt = $pdo->prepare("UPDATE notas SET nota_recuperacao = ? WHERE aluno_id = ?");
        $stmt->execute([$nota_rec, $aluno_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO notas (aluno_id, nota_recuperacao) VALUES (?, ?)");
        $stmt->execute([$aluno_id, $nota_rec]);
    }

    echo "<div class='alert alert-success'>Nota de recuperação salva com sucesso!</div>";
}

// Buscar alunos com notas
$stmt = $pdo->prepare("
    SELECT a.id, a.nome, 
           n.nota1, n.nota2, n.nota3, n.nota4, n.nota_recuperacao
    FROM alunos a 
    LEFT JOIN notas n ON a.id = n.aluno_id 
    WHERE a.turma_id = ? 
    ORDER BY a.nome
");
$stmt->execute([$turma_id]);
$alunos = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Turma: <?= htmlspecialchars($turma['nome']) ?></h1>
    <?php if (!$turma['fechada']): ?>
        <a href="fechar-turma.php?id=<?= $turma_id ?>" 
           class="btn btn-danger"
           onclick="return confirm('Tem certeza? Após fechar não será possível editar as notas!')">
            <i class="fas fa-lock"></i> Fechar Turma
        </a>
    <?php else: ?>
        <span class="badge bg-secondary fs-5">TURMA FECHADA</span>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Aluno</th>
                        <th>N1</th>
                        <th>N2</th>
                        <th>N3</th>
                        <th>N4</th>
                        <th>Média</th>
                        <th>Conceito</th>
                        <th>Recuperação</th>
                        <th>Status Final</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($alunos as $aluno): 
                    $n1 = $aluno['nota1'];
                    $n2 = $aluno['nota2'];
                    $n3 = $aluno['nota3'];
                    $n4 = $aluno['nota4'];
                    $rec = $aluno['nota_recuperacao'];

                    $media = null;
                    $conceito = '';
                    $status = '—';

                    if ($n1 !== null && $n2 !== null && $n3 !== null && $n4 !== null) {
                        $media = ($n1 + $n2 + $n3 + $n4) / 4;
                        $conceito = conceito($media);

                        if ($conceito === 'C' && $rec !== null) {
                            $soma = $media + $rec;
                            $status = ($soma >= 10) ? 
                                "<span class='badge bg-success'>APROVADO NA RECUPERAÇÃO</span>" : 
                                "<span class='badge bg-danger'>REPROVADO</span>";
                        } elseif ($conceito === 'C') {
                            $status = "<span class='badge bg-warning text-dark'>EM RECUPERAÇÃO</span>";
                        } elseif ($conceito === 'D') {
                            $status = "<span class='badge bg-danger'>REPROVADO</span>";
                        } else {
                            $status = "<span class='badge bg-success'>APROVADO</span>";
                        }
                    }
                ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($aluno['nome']) ?></strong></td>
                        <td><?= $n1 ?? '—' ?></td>
                        <td><?= $n2 ?? '—' ?></td>
                        <td><?= $n3 ?? '—' ?></td>
                        <td><?= $n4 ?? '—' ?></td>
                        <td class="fw-bold"><?= $media ? number_format($media, 2) : '—' ?></td>
                        <td><?= $conceito ? "<span class='badge bg-".($conceito=='A'?'success':($conceito=='B'?'primary':($conceito=='C'?'warning':'danger')))." fs-6'>$conceito</span>" : '—' ?></td>
                        <td>
                            <?php if ($conceito === 'C' && !$turma['fechada']): ?>
                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="aluno_id" value="<?= $aluno['id'] ?>">
                                    <input type="hidden" name="recuperacao" value="1">
                                    <input type="number" name="nota_recuperacao" step="0.1" min="0" max="10" 
                                           class="form-control form-control-sm" style="width: 100px;" 
                                           value="<?= $rec ?>" placeholder="Nota" required>
                                    <button type="submit" class="btn btn-sm btn-success">Salvar</button>
                                </form>
                            <?php else: ?>
                                <?= $rec ?? '—' ?>
                            <?php endif; ?>
                        </td>
                        <td><?= $status ?></td>
                    </tr>

                    <!-- Mensagem motivacional -->
                    <?php if ($conceito): ?>
                    <tr class="table-light">
                        <td colspan="9">
                            <em><?= mensagemConceito($conceito) ?></em>
                        </td>
                    </tr>
                    <?php endif; ?>

                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>