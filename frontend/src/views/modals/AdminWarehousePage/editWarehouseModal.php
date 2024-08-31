<div id="editWarehouseModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditWarehouseModal">&times;</span>
        <h2>Modifier l'Entrepôt</h2>
        <form id="editWarehouseForm">
            <input type="hidden" id="warehouseId" name="id">

            <label for="editNameInput">Nom de l'entrepôt :</label>
            <input type="text" id="editNameInput" name="name" required>

            <label for="editAddressInput">Adresse :</label>
            <input type="text" id="editAddressInput" name="address" required>

            <label for="editContactInfoInput">Informations de contact :</label>
            <input type="text" id="editContactInfoInput" name="contact_info">

            <label for="editCapacityInput">Capacité :</label>
            <input type="number" id="editCapacityInput" name="capacity" min="0">

            <label for="editCityInput">Ville :</label>
            <input type="text" id="editCityInput" name="city">

            <label for="editCountryInput">Pays :</label>
            <input type="text" id="editCountryInput" name="country">

            <button type="submit">Enregistrer les modifications</button>
        </form>
        <?php
        $loaderId = 'loadingEditWarehouse';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>