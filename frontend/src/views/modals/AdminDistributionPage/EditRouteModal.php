<div id="editRouteModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditRouteModal">&times;</span>
        <h2><?php echo $data['edit_route_title']; ?></h2>
        <form id="editRouteForm">
            <label for="routeNameInput"><?php echo $data['route_name_label']; ?></label>
            <input type="text" id="routeNameInput" name="name" required>

            <label for="vehicleSelect"><?php echo $data['vehicle_label']; ?></label>
            <select id="vehicleSelect" name="vehicle_id" required>
                <!-- Options will be populated dynamically -->
            </select>

            <label for="driverSelect"><?php echo $data['driver_label']; ?></label>
            <select id="driverSelect" name="driver_id" required>
                <!-- Options will be populated dynamically -->
            </select>

            <label for="statusSelect"><?php echo $data['status_label']; ?></label>
            <select id="statusSelect" name="status" required>
                <option value="pending"><?php echo $data['status_pending']; ?></option>
                <option value="in_progress"><?php echo $data['status_in_progress']; ?></option>
                <option value="completed"><?php echo $data['status_completed']; ?></option>
            </select>

            <button type="submit"><?php echo $data['save_changes_button']; ?></button>
        </form>
        <div id="loadingEditRoute" class="loader hidden"></div>
    </div>
</div>