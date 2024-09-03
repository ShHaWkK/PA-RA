<div id="editDestinationModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditDestinationModal">&times;</span>

        <h2><?php echo $data['edit_destination_title']; ?></h2>

        <form id="editDestinationForm">
            <div class="form-group">
                <label for="destination-edit-address"><?php echo $data['address_label']; ?></label>
                <input type="text" id="destination-edit-address" name="address" required>
            </div>

            <div class="form-group">
                <label for="destination-edit-recipientType"><?php echo $data['recipient_type_label']; ?></label>
                <select id="destination-edit-recipientType" name="recipient_type" required>
                    <option value="individual"><?php echo $data['recipient_individual']; ?></option>
                    <option value="association"><?php echo $data['recipient_association']; ?></option>
                    <option value="company"><?php echo $data['recipient_company']; ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="destination-edit-warehouseSelect"><?php echo $data['warehouse_label']; ?></label>
                <select name="warehouse" id="destination-edit-warehouseSelect" class="warehouseSelect" required></select>
            </div>

            <div class="form-group">
                <label for="destination-edit-comment"><?php echo $data['comment_label']; ?></label>
                <textarea id="destination-edit-comment" name="comment" rows="4" required></textarea>
            </div>

            <h3><?php echo $data['products_section_title']; ?></h3>
            <div id="productContainer"></div>

            <button type="submit" id="saveChangesButton"><?php echo $data['save_changes_button']; ?></button>
        </form>
        <?php
        $loaderId = 'loadingEditDestination';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>
    </div>
</div>
