<div id="vehicleDetailsModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeVehicleDetailsButton">&times;</span>
        <div id="modalBodyVehicleDetails">
        </div>
        <?php
        $loaderId = 'loadingVehicleDetails';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div><?php