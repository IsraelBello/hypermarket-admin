src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
// script.js

  function ajouterAuPanier(nom, prix, image) {
    let panier = JSON.parse(localStorage.getItem("panier")) || [];
    panier.push({ nom, prix, image });
    localStorage.setItem("panier", JSON.stringify(panier));
    mettreAJourCompteur();
    alert(nom + " ajouté au panier !");
  }

  function mettreAJourCompteur() {
    const panier = JSON.parse(localStorage.getItem("panier")) || [];
    const compteur = document.getElementById("panier-count");
    if (compteur) {
      compteur.textContent = panier.length;
    }
  }

  function afficherProduitsSelectionnes() {
    const liste = document.getElementById("liste-produits");
    const totalDiv = document.getElementById("total");
    liste.innerHTML = "";

    let panier = JSON.parse(localStorage.getItem("panier")) || [];

    if (panier.length === 0) {
      liste.innerHTML = "<p class='text-center'>Votre panier est vide.</p>";
      totalDiv.textContent = "";
      return;
    }

    let total = 0;
    panier.forEach((produit, index) => {
      total += produit.prix;
      const div = document.createElement("div");
      div.className = "produit-item";
      div.innerHTML = `
        <div class="d-flex align-items-center gap-3">
          <img src="${produit.image}" alt="${produit.nom}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
          <div>
            <h6 class="mb-1">${produit.nom}</h6>
            <p class="mb-0 text-muted">${produit.prix.toLocaleString()} FCFA</p>
          </div>
        </div>
        <button class="btn btn-danger btn-sm" onclick="supprimerProduit(${index})">Supprimer</button>
      `;
      liste.appendChild(div);
    });

    totalDiv.textContent = "Total : " + total.toLocaleString() + " FCFA";
  }

  function supprimerProduit(index) {
    let panier = JSON.parse(localStorage.getItem("panier")) || [];
    panier.splice(index, 1);
    localStorage.setItem("panier", JSON.stringify(panier));
    afficherProduitsSelectionnes();
    mettreAJourCompteur();
  }

  function viderPanier() {
    if (confirm("Voulez-vous vraiment vider le panier ?")) {
      localStorage.removeItem("panier");
      afficherProduitsSelectionnes();
      mettreAJourCompteur();
    }
  }

  function validerCommande() {
    let panier = JSON.parse(localStorage.getItem("panier")) || [];
    if (panier.length === 0) {
      alert("Votre panier est vide !");
      return;
    }

    localStorage.setItem("commande_validee", JSON.stringify(panier));
    localStorage.removeItem("panier");
    window.location.href = "confirmation.html";
  }

  // Initialisation au chargement
  document.addEventListener("DOMContentLoaded", () => {
    mettreAJourCompteur();
    afficherProduitsSelectionnes();
  });
  // Initialiser le panier sous forme de tableau
  let panier = [];

  // Fonction pour ajouter un produit au panier
  function ajouterAuPanier(nom, prix) {
    panier.push({ nom: nom, prix: prix });
    mettreAJourPanier();
  }

  // Fonction pour vider le panier
  function viderPanier() {
    panier = [];
    mettreAJourPanier();
  }

  // Fonction pour mettre à jour l'affichage du panier
  function mettreAJourPanier() {
    // Afficher le contenu du panier
    const listeProduits = document.getElementById("liste-produits");
    const totalElement = document.getElementById("total");
    const panierCount = document.getElementById("panier-count");

    listeProduits.innerHTML = ""; // Réinitialiser l'affichage des produits
    let total = 0;

    panier.forEach((produit, index) => {
      const div = document.createElement("div");
      div.classList.add("produit-item");

      div.innerHTML = `
        <span>${produit.nom}</span>
        <span>${produit.prix} €</span>
        <button class="btn btn-sm btn-danger" onclick="supprimerProduit(${index})">Supprimer</button>
      `;
      listeProduits.appendChild(div);

      total += produit.prix;
    });

    // Afficher le total
    totalElement.textContent = `Total: ${total.toFixed(2)} €`;

    // Mettre à jour le nombre d'articles dans le badge
    panierCount.textContent = panier.length;
  }

  // Fonction pour supprimer un produit du panier
  function supprimerProduit(index) {
    panier.splice(index, 1);
    mettreAJourPanier();
  }

  // Fonction pour valider la commande (envoi ou redirection vers une page de paiement)
  function validerCommande() {
    if (panier.length === 0) {
      alert("Votre panier est vide. Ajoutez des produits avant de valider la commande.");
    } else {
      // Ici vous pouvez ajouter un code pour valider la commande (ex: rediriger vers une page de paiement)
      alert("Commande validée avec succès !");
    }
  }

  // Exemple d'ajout de produit (cela peut être déclenché sur la page des produits)
  ajouterAuPanier("Chaise en bois", 50);
  ajouterAuPanier("Table en verre", 150);