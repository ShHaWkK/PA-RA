import { getProductsFromCollection, modifyProductsInCollection } from "../api/Collections.js";

document.addEventListener('DOMContentLoaded', function() {
    const collectionId = new URLSearchParams(window.location.search).get('collectionId');
    const collectionDate = new URLSearchParams(window.location.search).get('date');

    document.getElementById('collect-date-header').textContent = new Date(collectionDate).toLocaleDateString();

    if (collectionId) {
        populateCollecteSections(collectionId);
    }

    async function populateCollecteSections(collectionId) {
        const loader = document.getElementById("loadingBodyCollectedProducts");
        loader.classList.remove('hidden');

        const products = await getProductsFromCollection(collectionId);
        const collecteSections = document.getElementById('collecte-sections');

        products.forEach(product => {
            const section = document.createElement('div');
            section.classList.add('product-section');

            // Créez un wrapper pour la quantité collectée
            const quantityWrapperStyle = product.is_collected ? 'display: block;' : 'display: none;';

            section.innerHTML = `
                <h2>${product.product.name}</h2>
                <p>Date d'expiration : ${product.product.expiration_date}</p>
                <p>Quantité notifiée : ${product.notified_quantity}</p>
                <label for="collected-${product.notification_id}">Collecté</label>
                <input type="checkbox" class="collected-checkbox" id="collected-${product.notification_id}" ${product.is_collected ? 'checked' : ''} disabled>
                <div class="quantity-collected-wrapper" id="quantity-wrapper-${product.notification_id}" style="${quantityWrapperStyle}">
                    <label for="quantity-collected-${product.notification_id}">Quantité collectée :</label>
                    <input type="number" id="quantity-collected-${product.notification_id}" value="${product.quantity_collected}" disabled>
                </div>
                <button class="edit-button" data-id="${product.notification_id}">Modifier</button>
            `;
            collecteSections.appendChild(section);
        });

        // Ajouter le bouton de validation
        const validationButton = document.createElement('button');
        validationButton.classList.add("add-button");
        validationButton.id = "submitBtn";
        validationButton.textContent = "Valider";

        collecteSections.appendChild(validationButton);

        validationButton.addEventListener('click', async function() {
            const products = [];

            document.querySelectorAll('.product-section').forEach(section => {
                const notificationId = section.querySelector('.edit-button').dataset.id;
                const isCollected = section.querySelector(`#collected-${notificationId}`).checked;
                const quantityCollected = section.querySelector(`#quantity-collected-${notificationId}`).value;

                products.push({
                    notification_id: parseInt(notificationId),
                    is_collected: isCollected,
                    quantity_collected: parseInt(quantityCollected)
                });
            });

            await modifyProductsInCollection(collectionId, products);
            alert('Changements sauvegardés avec succès.');
            window.location.reload();
        });

        // Correction: Utiliser `collecteSections.querySelectorAll` au lieu de `collecteSections.document.querySelectorAll`
        collecteSections.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', function () {
                const notificationId = this.dataset.id;
                const checkbox = document.getElementById(`collected-${notificationId}`);
                const quantityWrapper = document.getElementById(`quantity-wrapper-${notificationId}`);
                const quantityInput = document.getElementById(`quantity-collected-${notificationId}`);

                // Activer les champs pour modification
                checkbox.disabled = false;
                checkbox.addEventListener('change', function () {
                    if (checkbox.checked) {
                        quantityWrapper.style.display = 'block';
                    } else {
                        quantityWrapper.style.display = 'none';
                    }
                });

                quantityInput.disabled = false;
                this.style.display = 'none';
            });
        });

        loader.classList.add('hidden');
    }
});