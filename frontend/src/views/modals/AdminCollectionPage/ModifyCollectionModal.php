<div id="editCollectionModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>
        <h2>Modifier la Collecte</h2>
        <form id="editCollectionForm">
            <label for="volunteerSelect">Chauffeur affecté :</label>
            <select id="volunteerSelect" name="volunteer_id" required>
            </select>

            <label for="vehicleSelect">Véhicule affecté :</label>
            <select id="vehicleSelect" name="vehicle_id" required>
            </select>

            <label for="completionCheckbox">Collecte terminée :</label>
            <input type="checkbox" id="completionCheckbox" name="is_completed">

            <button type="submit">Enregistrer les modifications</button>
        </form>
        <?php
        $loaderId = 'loadingModifyCollection';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>