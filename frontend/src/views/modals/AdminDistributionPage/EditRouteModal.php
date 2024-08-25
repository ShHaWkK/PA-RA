<div id="editRouteModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditRouteModal">&times;</span>
        <h2>Modifier la Route</h2>
        <form id="editRouteForm">
            <label for="routeNameInput">Nom de la Route :</label>
            <input type="text" id="routeNameInput" name="name" required>

            <label for="vehicleSelect">Véhicule affecté :</label>
            <select id="vehicleSelect" name="vehicle_id" required>
                <!-- Options will be populated dynamically -->
            </select>

            <label for="driverSelect">Chauffeur affecté :</label>
            <select id="driverSelect" name="driver_id" required>
                <!-- Options will be populated dynamically -->
            </select>

            <label for="statusSelect">Statut :</label>
            <select id="statusSelect" name="status" required>
                <option value="pending">En attente</option>
                <option value="in_progress">En cours</option>
                <option value="completed">Complété</option>
            </select>

            <button type="submit">Enregistrer les modifications</button>
        </form>
        <div id="loadingEditRoute" class="loader hidden"></div>
    </div>
</div>