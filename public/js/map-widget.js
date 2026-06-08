// Ce fichier contient le code JavaScript pour gérer la carte Leaflet et les marqueurs des sites

// Variables globales pour stocker l'instance de la carte et les marqueurs
let mapInstance = null;
let allMarkers  = [];

// Fonction pour construire les marqueurs sur la carte à partir des données des sites
window.buildMarkers = function(sites) {

    // Supprimer les anciens marqueurs
    allMarkers.forEach(function(m) { mapInstance.removeLayer(m); });
    
    // Réinitialiser la liste des marqueurs
    allMarkers = [];

    // Ajouter les nouveaux marqueurs
    sites.forEach(function(site) {

        // Convertir les coordonnées en nombres
        let lat = parseFloat(site.latitude);
        let lng = parseFloat(site.longitude);

        // Ignorer les sites sans coordonnées valides
        if (isNaN(lat) || isNaN(lng)) return;

        // Masquer les sites sans votes sur la période sélectionnée
        // if (!site.hasVotes) return;

        // Créer un marqueur pour le site
        let m = L.circleMarker([lat, lng], {
            color: site.color, fillColor: site.color,
            fillOpacity: 0.8, radius: 10
        }).addTo(mapInstance);

        // Ajouter une popup avec les informations du site
        m.bindPopup(
            "<div style=min-width:160px><strong>" + site.nom + "</strong><br>" +
            "Ville: " + site.ville + "<br>Region: " + site.region + "<br>" +
            "Pays: " + site.pays + "<br>Satisfaction: <strong>" + site.taux +
            "%</strong><br>Total: " + site.total + "</div>"
        );
        m.siteData = site; // Stocker les données du site dans le marqueur pour les filtres
        allMarkers.push(m); // Ajouter le marqueur à la liste globale
    });

    // Ajuster la vue de la carte pour inclure tous les marqueurs
    if (allMarkers.length > 0) {
        mapInstance.fitBounds(L.featureGroup(allMarkers).getBounds().pad(0.2));
    }
};

// Fonction pour appliquer les filtres sélectionnés et afficher/masquer les marqueurs en conséquence
window.applyJsFilters = function() {

    // Récupérer les valeurs des filtres
    let pays   = document.getElementById("filter-pays").value;
    let region = document.getElementById("filter-region").value;
    let ville  = document.getElementById("filter-ville").value;
    let site   = document.getElementById("filter-site").value;
    
    // Liste pour stocker les marqueurs visibles après application des filtres
    let visible = [];
    
    // Parcourir tous les marqueurs et décider s'ils doivent être affichés ou masqués
    allMarkers.forEach(function(m) {
        let s = m.siteData, show = true;
        if (pays   && String(s.pays_id)   !== pays)   show = false;
        if (region && String(s.region_id) !== region) show = false;
        if (ville  && String(s.ville_id)  !== ville)  show = false;
        if (site   && String(s.id)        !== site)   show = false;
        if (show) { m.addTo(mapInstance); visible.push(m); }
        else      { mapInstance.removeLayer(m); }
    });

    // Ajuster la vue de la carte pour inclure tous les marqueurs visibles
    if (visible.length > 0) {
        mapInstance.fitBounds(L.featureGroup(visible).getBounds().pad(0.2));
    }
};

window.initLeafletMap = function() {
    if (typeof L === "undefined") { setTimeout(window.initLeafletMap, 500); return; }
    let el = document.getElementById("leaflet-map");
    if (!el) { setTimeout(window.initLeafletMap, 500); return; }
    if (el._leaflet_id) return;

    mapInstance = L.map("leaflet-map").setView([17.6078, 8.0817], 6);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(mapInstance);

    let dataEl = document.getElementById("map-data");
    if (!dataEl) return;

    let sites = JSON.parse(dataEl.textContent || "[]");
    window.buildMarkers(sites);

    ["filter-pays", "filter-region", "filter-ville", "filter-site"].forEach(function(id) {
        let el2 = document.getElementById(id);
        if (el2) el2.addEventListener("change", window.applyJsFilters);
    });

    let resetBtn = document.getElementById("btn-reset");
    if (resetBtn) {
        resetBtn.addEventListener("click", function() {
            ["filter-pays", "filter-region", "filter-ville", "filter-site"].forEach(function(id) {
                document.getElementById(id).value = "";
            });
            window.applyJsFilters();
        });
    }
};

window.addEventListener("sitesDataUpdated", function(event) {
    console.log("sitesDataUpdated recu, sites:", event.detail.sites.length);
    if (!mapInstance) return;
    window.buildMarkers(event.detail.sites);
    window.applyJsFilters();
});

setTimeout(window.initLeafletMap, 1000);