<!-- Modal Structure -->
<div id="editDestinationModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditDestinationModal">&times;</span>

        <h2>Modifier une Destination</h2>

        <form id="editDestinationForm">
            <div class="form-group">
                <label for="destination-edit-address">Adresse :</label>
                <input type="text" id="destination-edit-address" name="address" required>
            </div>

            <div class="form-group">
                <label for="destination-edit-recipientType">Type de Destinataire :</label>
                <select id="destination-edit-recipientType" name="recipient_type" required>
                    <option value="individual">Individu</option>
                    <option value="association">Association</option>
                    <option value="company">Entreprise</option>
                </select>
            </div>

            <div class="form-group">
                <label for="destination-edit-warehouseSelect">Entrepôt :</label>
                <select name="warehouse" id="destination-edit-warehouseSelect" class="warehouseSelect" required></select>
            </div>

            <div class="form-group">
                <label for="destination-edit-comment">Commentaire :</label>
                <textarea id="destination-edit-comment" name="destination-edit-comment" rows="4" required></textarea>
            </div>

            <h3>Produits</h3>
            <div id="productContainer"></div>

            <button type="submit" id="saveChangesButton">Enregistrer</button>
        </form>
        <?php
        $loaderId = 'loadingEditDestination';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>
    </div>
</div>