<div id="addDestinationModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAddDestinationModal">&times;</span>

        <div id="addDestinationContent">
            <h2>Ajouter une Destination</h2>

            <form id="addDestinationForm">
                <div class="form-group">
                    <label for="address">Adresse :</label>
                    <input type="text" id="address" name="address" required>
                </div>

                <div class="form-group">
                    <label for="recipientType">Type de Destinataire :</label>
                    <select id="recipientType" name="recipient_type" required>
                        <option value="individual">Individu</option>
                        <option value="association">Association</option>
                        <option value="company">Entreprise</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="warehouseSelect">Entrepôt</label>
                    <select name="warehouse" id="warehouseSelect" class="warehouseSelect" required></select>
                </div>

                <div class="form-group">
                    <label for="comment">Commentaire :</label>
                    <textarea id="comment" name="comment" rows="4" required>bonjour</textarea>
                </div>

                <!-- Section dynamique pour ajouter des produits/livraisons -->
                <div id="deliveriesSection">
                    <h2>Livraisons</h2>
                    <div id="deliveryContainer">
                        <h3> Produit 0</h3>
                        <div class="delivery-item">
                            <div class="form-group">
                                <label for="productSelect_0">Produit :</label>
                                <select name="product[]" id="productSelect_0" required>
                                    <!-- Options à remplir dynamiquement -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="quantity_0">Quantité :</label>
                                <input type="number" name="quantity[]" id="quantity_0" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="status_0">Statut :</label>
                                <select name="status[]" id="status_0" required>
                                    <option value="pending">En attente</option>
                                    <option value="delivered">Livré</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="addDeliveryButton">Ajouter une Livraison</button>
                </div>

                <button type="submit" id="addDestinationsButton">Enregistrer</button>
            </form>
        </div>

        <?php
        $loaderId = 'loadingBodyAddDestination';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

    </div>
</div>
