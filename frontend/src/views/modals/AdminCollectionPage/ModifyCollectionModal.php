<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/views/includes/lang.php'); ?>

<div id="editCollectionModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>
        <h2><?php echo $data['edit_collection_title']; ?></h2>
        <form id="editCollectionForm">
            <label for="volunteerSelect"><?php echo $data['volunteer_label']; ?></label>
            <select id="volunteerSelect" name="volunteer_id" required>
                <!-- Options to be filled dynamically -->
            </select>

            <label for="vehicleSelect"><?php echo $data['vehicle_label']; ?></label>
            <select id="vehicleSelect" name="vehicle_id" required>
                <!-- Options to be filled dynamically -->
            </select>

            <label for="completionCheckbox"><?php echo $data['completion_label']; ?></label>
            <input type="checkbox" id="completionCheckbox" name="is_completed">

            <button type="submit"><?php echo $data['save_changes_button']; ?></button>
        </form>
        <?php
        $loaderId = 'loadingModifyCollection';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>