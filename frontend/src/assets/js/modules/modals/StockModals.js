import {getProductByID} from "/assets/js/api/Products.js";

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

            // const productQRCodePath = document.createElement('p');
            // productQRCodePath.textContent = `QR Code Path: ${product.qr_code_path}`;

            const productExpirationDate = document.createElement('p');
            productExpirationDate.textContent = `Expiration Date: ${formatDateToFrench(new Date(product.expiration_date))}`;

            const productVolume = document.createElement('p');
            productVolume.textContent = `Individual Volume: ${product.volume} m3`;

            const productScanned = document.createElement('p');
            productScanned.textContent = `Scanned: ${product.scanned ? 'Yes' : 'No'}`;

            modalBody.appendChild(productName);
            modalBody.appendChild(productBarcode);
            // modalBody.appendChild(productQRCodePath);
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

function formatDateToFrench(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleDateString('fr-FR') + ' ' + date.toLocaleTimeString('fr-FR');
}

document.addEventListener('DOMContentLoaded',
    function (){
        // Fenêtre modale de vue des détails du produit
        var productModal = document.getElementById("productDetailModal");
        var productSpan = document.getElementById("closeProductButton");

        productSpan.onclick = function() {
            productModal.style.display = "none";
        }

    });

export {populateProductDetailsInModal}