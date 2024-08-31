<div id="addServiceModal" class="modal">
    <div class="modal-content">
        <span id="closeAddServiceModal" class="close">&times;</span>
        <h2>Add New Service</h2>
        <form id="addServiceForm">
            <label for="serviceName">Service Name:</label>
            <input type="text" id="serviceName" name="name" required>

            <label for="serviceDescription">Description:</label>
            <textarea id="serviceDescription" name="description" required></textarea>

            <label for="serviceCapacity">Capacity:</label>
            <input type="number" id="serviceCapacity" name="capacity" required>

            <label for="serviceLocation">Location:</label>
            <input type="text" id="serviceLocation" name="location" required>

            <label for="serviceStatus">Status:</label>
            <select id="serviceStatus" name="status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <button type="submit">Add Service</button>
        </form>
    </div>
</div>
