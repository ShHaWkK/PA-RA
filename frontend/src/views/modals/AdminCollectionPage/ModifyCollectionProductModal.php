<div id="editCollectedProductModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditProductModal">&times;</span>
        <form id="editCollectedProductForm">
            <label for="collectedCheckbox"><?php echo $data['collected_checkbox_label']; ?></label>
            <input type="checkbox" id="collectedCheckbox" name="is_collected">

            <div id="quantityCollectedContainer" style="display: none;">
                <label for="quantityCollected"><?php echo $data['quantity_collected_label']; ?></label>
                <input type="number" id="quantityCollected" name="quantity_collected">
            </div>

            <button type="submit" id="modifyCollectedProductButton"><?php echo $data['save_changes_button']; ?></button>
        </form>
        <?php
        $loaderId = 'loadingModifyProductCollection';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>
