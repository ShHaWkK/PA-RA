import {getAllProducts, getProductByID} from "/assets/js/api/Products.js";
import {getWarehouseCapacity} from "/assets/js/api//Warehouse.js";
import {createStock, updateStock, getStock} from "/assets/js/api/Stocks.js";
import {selectedWarehouseId} from "/assets/js/pages/AdminStockPage.js";
import {populateStockTable} from "/assets/js/modules/tables/StockTable.js";
import {formatDateToFrench} from "../FormatDate.js";

// Variable pour les volumes des produits
let productsVolume = {};

async function populateProductDetailsInModal(productId) {
    const modalBody = document.getElementById('modalBodyProductDetail');

    // Vider le contenu précédent du corps de la modale
    modalBody.innerHTML = '';
    console.log("Selected Product ID", productId);

    document.getElementById('loadingProductDetail').classList.remove('hidden');

    try {
        // Obtenir les détails du produit
        const product = await getProductByID(productId);
        console.log(product);

        if (product) {
            // Ajouter les détails du produit au corps de la modale
            const productName = document.createElement('h3');
            productName.textContent = product.name;

            const productBarcode = document.createElement('p');
            productBarcode.textContent = `Barcode: ${product.barcode}`;

            const productExpirationDate = document.createElement('p');
            productExpirationDate.textContent = `Expiration Date: ${formatDateToFrench(new Date(product.expiration_date))}`;

            const productVolume = document.createElement('p');
            productVolume.textContent = `Individual Volume: ${product.volume} m3`;

            const productScanned = document.createElement('p');
            productScanned.textContent = `Scanned: ${product.scanned ? 'Yes' : 'No'}`;

            modalBody.appendChild(productName);
            modalBody.appendChild(productBarcode);
            modalBody.appendChild(productExpirationDate);
            modalBody.appendChild(productVolume);
            modalBody.appendChild(productScanned);
        } else {
            // Afficher le message "No product details found"
            const noProductMessage = document.createElement('p');
            noProductMessage.textContent = 'No product details found.';
            modalBody.appendChild(noProductMessage);
        }
    } catch (error) {
        console.error('Error fetching product details:', error);
        const errorMessage = document.createElement('p');
        errorMessage.textContent = 'Error fetching product details.';
        modalBody.appendChild(errorMessage);
    } finally {
        document.getElementById('loadingProductDetail').classList.add('hidden');
    }
}

// Fonction pour peupler le sélecteur de produits dans le formulaire d'ajout de stock
async function populateProductSelector() {
    try {
        // Récupérer tous les produits
        const products = await getAllProducts();

        // Vider le sélecteur de produits
        const productSelector = document.getElementById('product_selector');
        productSelector.innerHTML = '';

        // Remplir le sélecteur de produits et l'objet productsVolume
        products.forEach(product => {
            const option = document.createElement('option');
            option.value = product.id;
            option.textContent = product.name;
            productSelector.appendChild(option);

            // Stocker l'ID du produit et le volume associé dans l'objet productVolume
            console.log("products",product.volume);
            productsVolume[product.id] = product.volume || 0; // Par défaut à 0 si aucun volume disponible
            console.log("productVolume =",productsVolume)
        });

        // Initialiser le volume ajouté au chargement de la page
        updateAddedVolume();

        // Ajouter les écouteurs d'événements pour modifier le volume ajouté à chaque changement de quantité ou de produits
        productSelector.addEventListener('change', updateAddedVolume);
        document.getElementById('quantity').addEventListener('input', updateAddedVolume);

    } catch (error) {
        console.error('Erreur lors de la récupération des produits:', error.message);
    }
}


// Fonction pour ajouter un écouteur d'événement de soumission au formulaire d'ajout de stock
function addStockSubmitEvent() {
    const form = document.getElementById('addStockForm');

    // Supprime les écouteurs d'événements existants, s'il y en a, pour éviter les soumissions multiples
    const newForm = form.cloneNode(true);
    form.parentNode.replaceChild(newForm, form);

    newForm.addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const stockData = Object.fromEntries(formData.entries());
        stockData.warehouse_id = selectedWarehouseId; // Utilise la variable globale selectedWarehouseId

        console.log(stockData);

        try {
            const result = await createStock(stockData);

            console.log('Stock created successfully', result);
            alert('Stock created successfully');
            newForm.reset(); // Réinitialiser le formulaire après succès
            document.getElementById('addStockModal').style.display = 'none'; // Fermer la fenêtre modale
            await populateStockTable(selectedWarehouseId);

        } catch (error) {
            console.error('Error creating stock:', error.message);
            document.getElementById('addStockModal').style.display = 'none'; // Fermer la fenêtre modale
        }
    });
}

function updateAddedVolume() {
    const productSelector = document.getElementById('product_selector');
    const quantityInput = document.getElementById('quantity');
    const addedVolumeInput = document.getElementById('added-volume');

    const selectedProductId = parseInt(productSelector.value, 10);
    const quantity = parseInt(quantityInput.value, 10);
    const volume = productsVolume[selectedProductId] || 0;

    console.log("volume",volume);

    addedVolumeInput.value = (isNaN(quantity) || isNaN(volume)) ? 0 : quantity * volume;
    checkVolume();
}

function checkVolume() {
    const addedVolumeInput = document.getElementById('added-volume');
    const availableVolumeInput = document.getElementById('available-volume');

    const addedVolume = parseFloat(addedVolumeInput.value) || 0;
    const availableVolume = parseFloat(availableVolumeInput.value) || 0;

    // Si le volume ajouté est supérieur au volume disponible, colorer en rouge
    if (addedVolume > availableVolume) {
        addedVolumeInput.style.color = 'red';
    } else {
        addedVolumeInput.style.color = ''; // Réinitialiser la couleur par défaut
    }
}

// Fonction pour appliquer le retrait
async function applyWithdraw(stockId, withdrawQuantity) {
    try {
        const stock = await getStock(stockId);
        if (!stock) {
            alert("Stock non trouvé.");
            return;
        }

        const newQuantity = stock.quantity - withdrawQuantity;
        if (newQuantity < 0) {
            alert("Quantité insuffisante dans le stock.");
            return;
        }

        const stockData = {
            ...stock,
            quantity: newQuantity
        };

        const updatedStock = await updateStock(stockId, stockData);
        alert(`Stock mis à jour avec succès. Nouvelle quantité: ${updatedStock.quantity}`);
    } catch (error) {
        console.error('Erreur lors de la mise à jour du stock:', error.message);
        alert("Erreur lors de la mise à jour du stock.");
    }
}

function getSelectedStockId() {
    const selectedRadio = document.querySelector('input[name="selectedStock"]:checked');
    console.log(selectedRadio.value);
    return selectedRadio.value;
}

export { populateProductDetailsInModal, populateProductSelector,getSelectedStockId,applyWithdraw , checkVolume, updateAddedVolume, addStockSubmitEvent }