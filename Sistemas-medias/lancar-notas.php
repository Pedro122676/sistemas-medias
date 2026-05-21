<?php 
require 'config/db.php'; 
include 'includes/header.php'; 
require 'includes/functions.php';

// Salvar nota enviada por formulário (INSERT ou UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aluno_id'])) {
    $aluno_id = (int)$_POST['aluno_id'];
    $nota1 = $_POST['nota1'] !== '' ? (float)$_POST['nota1'] : null;
    $nota2 = $_POST['nota2'] !== '' ? (float)$_POST['nota2'] : null;
    $nota3 = $_POST['nota3'] !== '' ? (float)$_POST['nota3'] : null;
    $nota4 = $_POST['nota4'] !== '' ? (float)$_POST['nota4'] : null;

    // Verifica se já existe registro de notas para o aluno
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notas WHERE aluno_id = ?");
    $stmt->execute([$aluno_id]);
    $exists = $stmt->fetchColumn() > 0;

    if ($exists) {
        $stmt = $pdo->prepare("UPDATE notas SET nota1 = ?, nota2 = ?, nota3 = ?, nota4 = ? WHERE aluno_id = ?");
        $stmt->execute([$nota1, $nota2, $nota3, $nota4, $aluno_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO notas (aluno_id, nota1, nota2, nota3, nota4) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$aluno_id, $nota1, $nota2, $nota3, $nota4]);
    }

    // Redireciona para evitar reenvio do formulário
    $redir_turma = isset($_POST['turma_id']) ? (int)$_POST['turma_id'] : 0;
    header("Location: lancar-notas.php?turma_id={$redir_turma}");
    exit;
}

// Verificar se turma está fechada
if (isset($_GET['turma_id'])) {
    $turma_id = (int)$_GET['turma_id'];
    $stmt = $pdo->prepare("SELECT fechada FROM turmas WHERE id = ?");
    $stmt->execute([$turma_id]);
    $turma = $stmt->fetch();
    
    if ($turma && $turma['fechada']) {
        echo "<div class='alert alert-warning'>Esta turma está fechada. Não é possível editar notas.</div>";
    }
}
?>

<h1 class="mb-4">Lançar / Editar Notas</h1>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">

                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Selecione a Turma</label>
                            <select name="turma_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Escolha uma turma --</option>
                                <?php
                                $stmt = $pdo->query("SELECT id, nome, fechada FROM turmas ORDER BY nome");
                                while ($turma = $stmt->fetch()) {
                                    $selected = (isset($_GET['turma_id']) && $_GET['turma_id'] == $turma['id']) ? 'selected' : '';
                                    $status = $turma['fechada'] ? ' (FECHADA)' : '';
                                    echo "<option value='{$turma['id']}' $selected>" . htmlspecialchars($turma['nome']) . $status . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </form>

                <?php if (isset($_GET['turma_id'])): 
                    $turma_id = (int)$_GET['turma_id'];
                    $fechada = $turma['fechada'] ?? false;

                    $stmt = $pdo->prepare("
                        SELECT a.id, a.nome, n.nota1, n.nota2, n.nota3, n.nota4 
                        FROM alunos a 
                        LEFT JOIN notas n ON a.id = n.aluno_id 
                        WHERE a.turma_id = ? 
                        ORDER BY a.nome
                    ");
                    $stmt->execute([$turma_id]);
                    $alunos = $stmt->fetchAll();
                ?>

                <?php if ($fechada): ?>
                    <div class="alert alert-info">Turma fechada - Modo visualização apenas.</div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Aluno</th>
                                <th>Nota 1</th>
                                <th>Nota 2</th>
                                <th>Nota 3</th>
                                <th>Nota 4</th>
                                <th>Média</th>
                                <th>Conceito</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($alunos as $aluno): 
                            $n1 = $aluno['nota1'] ?? null;
                            $n2 = $aluno['nota2'] ?? null;
                            $n3 = $aluno['nota3'] ?? null;
                            $n4 = $aluno['nota4'] ?? null;
                            
                            $media = ($n1 !== null && $n2 !== null && $n3 !== null && $n4 !== null) ? 
                                     ($n1 + $n2 + $n3 + $n4) / 4 : null;
                            $conceito = $media ? conceito($media) : '';
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($aluno['nome']) ?></td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="aluno_id" value="<?= $aluno['id'] ?>">
                                    <td><input type="number" name="nota1" step="0.1" min="0" max="10" class="form-control form-control-sm" value="<?= $n1 ?>" <?= $fechada ? 'readonly' : 'required' ?>></td>
                                    <td><input type="number" name="nota2" step="0.1" min="0" max="10" class="form-control form-control-sm" value="<?= $n2 ?>" <?= $fechada ? 'readonly' : 'required' ?>></td>
                                    <td><input type="number" name="nota3" step="0.1" min="0" max="10" class="form-control form-control-sm" value="<?= $n3 ?>" <?= $fechada ? 'readonly' : 'required' ?>></td>
                                    <td><input type="number" name="nota4" step="0.1" min="0" max="10" class="form-control form-control-sm" value="<?= $n4 ?>" <?= $fechada ? 'readonly' : 'required' ?>></td>
                                    <td class="text-center fw-bold"><?= $media ? number_format($media, 2) : '-' ?></td>
                                    <td class="text-center"><?= $conceito ? "<span class='badge bg-".($conceito=='A'?'success':($conceito=='B'?'primary':($conceito=='C'?'warning':'danger')))."'>$conceito</span>" : '-' ?></td>
                                    <td>
                                        <?php if (!$fechada): ?>
                                            <button type="submit" class="btn btn-sm btn-primary">Salvar</button>
                                        <?php else: ?>
                                            <span class="text-muted">Fechada</span>
                                        <?php endif; ?>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>