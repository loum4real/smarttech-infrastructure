<?php
require_once 'config.php';

$filtre_statut = $_GET['statut'] ?? 'tous';
$where  = [];
$params = [];

if ($filtre_statut !== 'tous') {
    $where[]          = "statut = :statut";
    $params[':statut'] = $filtre_statut;
}

$sql = "SELECT * FROM clients";
if (!empty($where)) $sql .= " WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY modifie_le DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$clients = $stmt->fetchAll();
$total   = count($clients);

$stats = $pdo->query("
    SELECT
        COUNT(*) as total,
        SUM(CASE WHEN statut='actif'    THEN 1 ELSE 0 END) as actifs,
        SUM(CASE WHEN statut='prospect' THEN 1 ELSE 0 END) as prospects,
        SUM(CASE WHEN statut='inactif'  THEN 1 ELSE 0 END) as inactifs
    FROM clients
")->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clients — TechSupport Pro</title>
  <?php include 'style_inline.php'; ?>
</head>
<body>
<div class="wrapper">

  <!-- Brand -->
  <div class="brand">
    <div class="brand-icon">
      <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
      </svg>
    </div>
    <div>
      <div class="brand-name">TechSupport Pro</div>
      <div class="brand-sub">Gestion de la relation client</div>
    </div>
  </div>

  <!-- Page header -->
  <div class="page-header">
    <div>
      <h1>Clients</h1>
      <p class="meta">
        <?= $total ?> client<?= $total > 1 ? 's' : '' ?>
        <?= $filtre_statut === 'tous' ? 'au total' : '— filtre&nbsp;: ' . h($filtre_statut) ?>
      </p>
    </div>
    <a href="create.php" class="btn btn-primary">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path d="M12 5v14M5 12h14"/>
      </svg>
      Nouveau client
    </a>
  </div>

  <?php if (isset($_GET['msg'])): ?>
    <div class="flash flash-success">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M20 6L9 17l-5-5"/>
      </svg>
      <?= h($_GET['msg']) ?>
    </div>
  <?php endif; ?>

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="s-label">Total</div>
      <div class="s-value"><?= $stats['total'] ?></div>
    </div>
    <div class="stat-card">
      <div class="s-label">Actifs</div>
      <div class="s-value" style="color:var(--success)"><?= $stats['actifs'] ?></div>
    </div>
    <div class="stat-card">
      <div class="s-label">Prospects</div>
      <div class="s-value" style="color:var(--warn)"><?= $stats['prospects'] ?></div>
    </div>
    <div class="stat-card">
      <div class="s-label">Inactifs</div>
      <div class="s-value" style="color:var(--text-soft)"><?= $stats['inactifs'] ?></div>
    </div>
  </div>

  <!-- Filtres -->
  <div class="filters">
    <a href="index.php?statut=tous"     class="filter-btn <?= $filtre_statut === 'tous'     ? 'active' : '' ?>">Tous</a>
    <a href="index.php?statut=actif"    class="filter-btn <?= $filtre_statut === 'actif'    ? 'active' : '' ?>">Actifs</a>
    <a href="index.php?statut=prospect" class="filter-btn <?= $filtre_statut === 'prospect' ? 'active' : '' ?>">Prospects</a>
    <a href="index.php?statut=inactif"  class="filter-btn <?= $filtre_statut === 'inactif'  ? 'active' : '' ?>">Inactifs</a>
  </div>

  <!-- Tableau -->
  <div class="table-card">
    <?php if ($total === 0): ?>
      <div class="empty-state">
        <div class="icon">📋</div>
        <p>
          Aucun client <?= $filtre_statut !== 'tous' ? 'dans cette catégorie' : 'pour le moment' ?>.<br>
          <?php if ($filtre_statut === 'tous'): ?>
            <a href="create.php" style="color:var(--accent)">Ajoutez votre premier client</a> pour commencer.
          <?php else: ?>
            <a href="index.php" style="color:var(--accent)">Voir tous les clients</a>
          <?php endif; ?>
        </p>
      </div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th style="width:44px">#</th>
            <th>Entreprise</th>
            <th>Contact</th>
            <th>Email / Téléphone</th>
            <th>Secteur</th>
            <th>Statut</th>
            <th style="width:80px; text-align:right; padding-right:20px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($clients as $c): ?>
          <tr>
            <td><span class="row-num"><?= $c['id'] ?></span></td>
            <td>
              <div class="company-name"><?= h($c['nom_entreprise']) ?></div>
              <?php if ($c['adresse']): ?>
                <div class="company-addr">
                  <?= h(mb_substr($c['adresse'], 0, 38)) ?><?= mb_strlen($c['adresse']) > 38 ? '…' : '' ?>
                </div>
              <?php endif; ?>
            </td>
            <td><?= h($c['personne_contact']) ?></td>
            <td>
              <div class="contact-email"><?= h($c['email']) ?></div>
              <?php if ($c['telephone']): ?>
                <div class="contact-tel"><?= h($c['telephone']) ?></div>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($c['secteur']): ?>
                <span class="badge badge-secteur"><?= h($c['secteur']) ?></span>
              <?php else: ?>
                <span style="color:var(--text-soft)">—</span>
              <?php endif; ?>
            </td>
            <td><?= badge_statut($c['statut']) ?></td>
            <td>
              <div class="actions" style="justify-content:flex-end">
                <a href="update.php?id=<?= $c['id'] ?>" class="action-btn" title="Modifier">
                  <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </a>
                <a href="delete.php?id=<?= $c['id'] ?>" class="action-btn del" title="Supprimer"
                   onclick="return confirm('Supprimer ce client ?\n\n<?= h($c['nom_entreprise']) ?>')">
                  <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6M9 6V4h6v2"/>
                  </svg>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

</div>
</body>
</html>

