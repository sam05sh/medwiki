<?php
// Il $id è stato definito in index.php
$conn->query("UPDATE malattie SET visite = visite + 1 WHERE id = $id");
$stmt = $conn->prepare("SELECT m.*, c.nome as cat_nome FROM malattie m LEFT JOIN categorieMalattie c ON m.categoria_id = c.id WHERE m.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$malattia = $stmt->get_result()->fetch_assoc();

if (!$malattia) { echo "Malattia non trovata"; return; }
$jsonDettagli = json_decode($malattia['dettagli'], true);
?>

<div>
    <nav class="text-sm text-slate-500 mb-4"><?php echo htmlspecialchars($malattia['cat_nome']); ?></nav>
    <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white mb-8 border-b pb-4"><?php echo htmlspecialchars($malattia['nome']); ?></h1>
    
    <div class="space-y-8">
        <?php if ($jsonDettagli): foreach($jsonDettagli as $k => $v): ?>
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                <h2 class="text-2xl font-bold mb-3 text-medical-600"><?php echo htmlspecialchars($k); ?></h2>
                <div class="prose dark:prose-invert"><?php echo nl2br(htmlspecialchars($v)); ?></div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>
