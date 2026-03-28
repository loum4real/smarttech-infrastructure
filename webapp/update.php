<?php
require_once 'config.php';
$id     = (int)($_GET['id'] ?? 0);
$errors = [];

$q = $pdo->prepare('SELECT * FROM clients WHERE id = :id');
$q->execute([':id' => $id]);
$client = $q->fetch();

if (!$client) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vals = array_map('trim', $_POST);

    if (empty($vals['nom_entreprise']))
        $errors[] = 'Le nom de l\'entreprise est requis.';
    if (empty($vals['personne_contact']))
        $errors[] = 'Le nom de la personne de contact est requis.';
    if (empty($vals['email']) || !filter_var($vals['email'], FILTER_VALIDATE_EMAIL))
        $errors[] = 'Un email valide est requis.';

    if (!empty($vals['email'])) {
        $check = $pdo->prepare("SELECT id FROM clients WHERE email = :email AND id != :id");
        $check->execute([':email' => $vals['email'], ':id' => $id]);
        if ($check->fetch()) $errors[] = 'Cet email est déjà utilisé par un autre client.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'UPDATE clients SET nom_entreprise=:nom_entreprise, personne_contact=:personne_contact,'
          . ' email=:email, telephone=:telephone, secteur=:secteur, statut=:statut,'
          . ' adresse=:adresse, notes=:notes WHERE id=:id'
        );
        $stmt->execute([
            ':nom_entreprise'   => $vals['nom_entreprise'],
            ':personne_contact' => $vals['personne_contact'],
            ':email'            => $vals['email'],
            ':telephone'        => $vals['telephone'],
            ':secteur'          => $vals['secteur'],
            ':statut'           => $vals['statut'],
            ':adresse'          => $vals['adresse'],
            ':notes'            => $vals['notes'],
            ':id'               => $id,
        ]);
        header('Location: index.php?msg=Client mis à jour');
        exit;
    }
    $client = array_merge($client, $vals);
}

$secteurs = ['Santé', 'Éducation', 'Commerce', 'Restauration', 'Services Juridiques', 'Immobilier', 'Transport', 'Autre'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier — <?= h($client['nom_entreprise']) ?></title>
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
      <h1>Modifier le client</h1>
      <p class="meta"><?= h($client['nom_entreprise']) ?> — <?= h($client['personne_contact']) ?></p>
    </div>
    <a href="index.php" class="btn btn-outline">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
      </svg>
      Retour
    </a>
  </div>

  <?php if (!empty($errors)): ?>
    <div class="flash flash-error">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>
      </svg>
      <div><?= implode('<br>', array_map('h', $errors)) ?></div>
    </div>
  <?php endif; ?>

  <div class="form-card">
    <h2>Informations du client</h2>
    <form method="POST" novalidate>

      <div class="form-group">
        <label>Nom de l'entreprise *</label>
        <input type="text" name="nom_entreprise" value="<?= h($client['nom_entreprise']) ?>" required>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Personne de contact *</label>
          <input type="text" name="personne_contact" value="<?= h($client['personne_contact']) ?>" required>
        </div>
        <div class="form-group">
          <label>Email *</label>
          <input type="email" name="email" value="<?= h($client['email']) ?>" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Téléphone</label>
          <input type="tel" name="telephone" value="<?= h($client['telephone'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Secteur d'activité</label>
          <select name="secteur">
            <option value="">— Choisir —</option>
            <?php foreach ($secteurs as $s): ?>
              <option value="<?= h($s) ?>" <?= ($client['secteur'] ?? '') === $s ? 'selected' : '' ?>>
                <?= h($s) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Statut</label>
        <select name="statut">
          <option value="prospect" <?= $client['statut'] === 'prospect' ? 'selected' : '' ?>>Prospect</option>
          <option value="actif"    <?= $client['statut'] === 'actif'    ? 'selected' : '' ?>>Actif</option>
          <option value="inactif"  <?= $client['statut'] === 'inactif'  ? 'selected' : '' ?>>Inactif</option>
        </select>
      </div>

      <div class="form-group">
        <label>Adresse complète</label>
        <textarea name="adresse"><?= h($client['adresse'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label>Notes internes</label>
        <textarea name="notes"><?= h($client['notes'] ?? '') ?></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="index.php" class="btn btn-outline">Annuler</a>
      </div>

    </form>
  </div>

</div>
</body>
</html>
