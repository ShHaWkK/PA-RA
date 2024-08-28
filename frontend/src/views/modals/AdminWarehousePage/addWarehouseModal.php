<div id="addWarehouseModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAddWarehouseModal">&times;</span>
        <h2>Ajouter un Entrepôt</h2>
        <form id="addWarehouseForm">
            <label for="addNameInput">Nom de l'entrepôt :</label>
            <input type="text" id="addNameInput" name="name" required>

            <label for="addAddressInput">Adresse :</label>
            <input type="text" id="addAddressInput" name="address" required>

            <label for="addContactInfoInput">Informations de contact :</label>
            <input type="text" id="addContactInfoInput" name="contact_info">

            <label for="addCapacityInput">Capacité :</label>
            <input type="number" id="addCapacityInput" name="capacity" min="0">

            <label for="addCityInput">Ville :</label>
            <input type="text" id="addCityInput" name="city">

            <label for="addCountryInput">Pays :</label>
            <input type="text" id="addCountryInput" name="country">

            <button type="submit">Ajouter l'entrepôt</button>
        </form>
        <?php
        $loaderId = 'loadingAddWarehouse';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>
