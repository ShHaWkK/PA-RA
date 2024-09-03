<div id="addWarehouseModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAddWarehouseModal">&times;</span>
        <h2><?php echo $data['add_warehouse_modal_title']; ?></h2>
        <form id="addWarehouseForm">
            <label for="addNameInput"><?php echo $data['add_warehouse_form_name_label']; ?></label>
            <input type="text" id="addNameInput" name="name" required>

            <label for="addAddressInput"><?php echo $data['add_warehouse_form_address_label']; ?></label>
            <input type="text" id="addAddressInput" name="address" required>

            <label for="addContactInfoInput"><?php echo $data['add_warehouse_form_contact_label']; ?></label>
            <input type="text" id="addContactInfoInput" name="contact_info">

            <label for="addCapacityInput"><?php echo $data['add_warehouse_form_capacity_label']; ?></label>
            <input type="number" id="addCapacityInput" name="capacity" min="0">

            <label for="addCityInput"><?php echo $data['add_warehouse_form_city_label']; ?></label>
            <input type="text" id="addCityInput" name="city">

            <label for="addCountryInput"><?php echo $data['add_warehouse_form_country_label']; ?></label>
            <input type="text" id="addCountryInput" name="country">

            <button type="submit"><?php echo $data['add_warehouse_button']; ?></button>
        </form>
        <?php
        $loaderId = 'loadingAddWarehouse';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>
