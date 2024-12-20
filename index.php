<?php
// Paramètre pris dans l'URL : ?printer_id=123
$printer_id = isset($_GET['printer_id']) ? (int)$_GET['printer_id'] : 0;

if($printer_id <= 0) {
    die("Imprimante non spécifiée ou invalide.");
}

// Connexion à la base de données (à adapter selon votre config)
$servername = "localhost";
$username = "root";
$password = "motdepasse";
$dbname = "nom_de_la_base";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupération des informations de l’imprimante
$sql = "SELECT name, image_url FROM printers WHERE id = $printer_id";
$result = $conn->query($sql);

if($result->num_rows == 0) {
    die("Aucune imprimante trouvée avec cet ID.");
}

$printer = $result->fetch_assoc();

// Traitement du formulaire
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reporter_name = $_POST['reporter_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $filament_type = $_POST['filament_type'] ?? '';
    $severity = $_POST['severity'] ?? '';
    $printer_name = $printer['name'];

    // Récupération de l'image envoyée (si vous voulez stocker l’image sur le serveur)
    // Ici, on suppose un upload de fichier classique via <input type="file">
    // On peut aussi récupérer la data du canvas si on envoie via un champ hidden.
    $uploaded_file_path = '';
    if(isset($_FILES['failed_print_photo']) && $_FILES['failed_print_photo']['error'] == 0) {
        $target_dir = "uploads/";
        if(!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $filename = time() . "_" . basename($_FILES['failed_print_photo']['name']);
        $target_file = $target_dir . $filename;

        if(move_uploaded_file($_FILES['failed_print_photo']['tmp_name'], $target_file)) {
            $uploaded_file_path = $target_file;
        }
    }

    // Envoi du mail
    $to = "admin@votresite.com";
    $subject = "Problème sur l'imprimante: $printer_name";
    $message = "Un problème a été signalé sur l'imprimante : $printer_name\n";
    $message .= "Nom du rapporteur : $reporter_name\n";
    $message .= "Description : $description\n";
    $message .= "Type de filament : $filament_type\n";
    $message .= "Sévérité : $severity\n";
    if($uploaded_file_path) {
        $message .= "Photo de l'impression ratée attachée ou disponible à : $uploaded_file_path\n";
    }

    // Pour un mail plus complexe (avec pièce jointe), vous pouvez utiliser PHPMailer.
    // Ici, simple mail() pour démonstration.
    // Assurez-vous que mail() est configuré sur votre serveur.
    mail($to, $subject, $message);

    echo "<div class='alert alert-success text-center'>Votre rapport a bien été envoyé. Merci.</div>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signaler un problème</title>
    <!-- CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-3">
    <div class="card">
        <div class="card-header text-center">
            <h5>Signaler un problème sur l'imprimante</h5>
        </div>
        <div class="card-body">
            <div class="text-center mb-3">
                <!-- Affiche la photo de l'imprimante et son nom -->
                <img src="<?php echo htmlspecialchars($printer['image_url']); ?>" alt="<?php echo htmlspecialchars($printer['name']); ?>" class="img-fluid" style="max-height:200px;">
                <h6 class="mt-2"><?php echo htmlspecialchars($printer['name']); ?></h6>
            </div>

            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="failed_print_photo" class="form-label">Photo de l'impression ratée :</label>
                    <!-- Input pour capturer une photo avec un smartphone -->
                    <input type="file" accept="image/*" capture="camera" class="form-control" id="failed_print_photo" name="failed_print_photo" required>
                </div>

                <!-- Canvas pour prévisualiser l'image sélectionnée -->
                <div class="mb-3 text-center">
                    <canvas id="previewCanvas" style="max-width:100%; border:1px solid #ccc;"></canvas>
                </div>

                <div class="mb-3">
                    <label for="reporter_name" class="form-label">Votre nom :</label>
                    <input type="text" class="form-control" id="reporter_name" name="reporter_name" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description du problème :</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="filament_type" class="form-label">Type de filament :</label>
                    <select class="form-select" id="filament_type" name="filament_type" required>
                        <option value="">Choisir le type de filament</option>
                        <option value="PLA">PLA</option>
                        <option value="ABS">ABS</option>
                        <option value="PETG">PETG</option>
                        <option value="TPU">TPU</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="severity" class="form-label">Niveau de sévérité :</label>
                    <select class="form-select" id="severity" name="severity" required>
                        <option value="">Choisir le niveau de sévérité</option>
                        <option value="Faible">Faible</option>
                        <option value="Moyen">Moyen</option>
                        <option value="Élevé">Élevé</option>
                        <option value="Critique">Critique</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- JavaScript Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Script pour prévisualiser l'image dans le Canvas
document.getElementById('failed_print_photo').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if(!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            const canvas = document.getElementById('previewCanvas');
            const ctx = canvas.getContext('2d');
            // Redimensionnement du canvas à la taille de l'image
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
        }
        img.src = e.target.result;
    }
    reader.readAsDataURL(file);
});
</script>

</body>
</html>
