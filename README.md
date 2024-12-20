# Tromázo - Page de Signalement de Problème sur une Imprimante 3D

Ce projet propose une page Web en PHP/MySQL permettant de signaler un problème rencontré sur une imprimante 3D. L’idée est d’associer à chaque imprimante un QR code qui, lorsqu’il est scanné (notamment avec un smartphone), redirige directement vers cette page avec le bon identifiant d’imprimante en paramètre.

## Fonctionnalités

- **Paramètre d'URL (printer_id)** : La page utilise un paramètre `printer_id` dans l’URL pour identifier l’imprimante concernée. Exemple : `https://votre-site.com/index.php?printer_id=123`
- **Affichage des informations de l'imprimante** : Une fois l’ID fourni, la page va chercher en base de données (MySQL) le nom et une image illustrant l’imprimante.
- **Formulaire de rapport de problème** : Le formulaire permet de :
  - Importer ou prendre une photo de l’impression ratée via le smartphone.
  - Saisir le nom de la personne qui signale le problème.
  - Décrire le problème rencontré.
  - Sélectionner le type de filament utilisé.
  - Choisir le niveau de sévérité du problème.
- **Envoi d’e-mail** : Une fois le formulaire soumis, un e-mail est envoyé à l’administrateur du site avec toutes les informations fournies, y compris un lien ou une pièce jointe vers la photo.
- **Interface responsive** : La page est construite avec Bootstrap, ce qui garantit une interface adaptée aux écrans de smartphones et tablettes.

## Utilisation

1. **Installation et configuration** :  
   - Clonez ce dépôt sur votre serveur Web :  
     ```bash
     git clone https://github.com/your-username/your-repo.git
     ```  
   - Assurez-vous que PHP et MySQL sont disponibles et configurés sur votre serveur.
   - Créez une base de données et importez-y votre table `printers` (ou adaptez le code pour qu’il corresponde à votre base).
   - Dans le fichier `index.php`, modifiez les variables `$servername`, `$username`, `$password`, et `$dbname` avec vos informations de connexion MySQL.

2. **Mise en place des imprimantes** :  
   - Ajoutez dans la table `printers` les informations de vos imprimantes (nom, URL de l’image, etc.).
   - Pour chaque imprimante, générez un QR code contenant l’URL de la page avec le bon `printer_id`. Par exemple :  
     ```
     https://votre-site.com/index.php?printer_id=123
     ```
   - Placez le QR code sur l’imprimante 3D correspondante.

3. **Accès à la page** :  
   - Lorsqu’un problème survient, l’utilisateur scanne le QR code avec son smartphone.
   - Le QR code le redirige vers la page. Le nom et la photo de l’imprimante s’affichent.
   - L’utilisateur remplit le formulaire et envoie le rapport.

4. **Envoi du rapport** :  
   - Le formulaire envoie un e-mail à l’administrateur du site.
   - Vous pouvez adapter la fonction `mail()` ou utiliser une solution tierce (PHPMailer, SendGrid, etc.) selon votre besoin.

## Technologies utilisées

- **PHP** : Pour la logique côté serveur.
- **MySQL** : Pour le stockage des informations sur les imprimantes.
- **HTML/CSS/JS** : Construction de l’interface utilisateur.
- **Bootstrap** : Framework CSS pour un design responsive et moderne.
- **Canvas** : Pour la prévisualisation de l’image avant envoi.

## Personnalisation

- Vous pouvez ajouter ou retirer des champs du formulaire.
- Adapter le style CSS via Bootstrap et votre propre fichier CSS.
- Améliorer le système d’envoi de mail, par exemple avec PHPMailer.
- Gérer la sauvegarde des images sur votre serveur ou un service externe.

## Licence

Ce projet est publié sous [votre licence choisie]. Consultez le fichier `LICENSE` pour plus d’informations.
