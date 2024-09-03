<div id="editWarehouseModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditWarehouseModal">&times;</span>
        <h2><?php echo $data['edit_warehouse_modal_title']; ?></h2>
        <form id="editWarehouseForm">
            <input type="hidden" id="warehouseId" name="id">

            <label for="editNameInput"><?php echo $data['edit_warehouse_form_name_label']; ?></label>
            <input type="text" id="editNameInput" name="name" required>

            <label for="editAddressInput"><?php echo $data['edit_warehouse_form_address_label']; ?></label>
            <input type="text" id="editAddressInput" name="address" required>

            <label for="editContactInfoInput"><?php echo $data['edit_warehouse_form_contact_label']; ?></label>
            <input type="text" id="editContactInfoInput" name="contact_info">

            <label for="editCapacityInput"><?php echo $data['edit_warehouse_form_capacity_label']; ?></label>
            <input type="number" id="editCapacityInput" name="capacity" min="0">

            <label for="editCityInput"><?php echo $data['edit_warehouse_form_city_label']; ?></label>
            <input type="text" id="editCityInput" name="city">

            <label for="editCountryInput"><?php echo $data['edit_warehouse_form_country_label']; ?></label>
            <input type="text" id="editCountryInput" name="country">

            <button type="submit"><?php echo $data['save_changes_button']; ?></button>
        </form>
        <?php
        $loaderId = 'loadingEditWarehouse';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>