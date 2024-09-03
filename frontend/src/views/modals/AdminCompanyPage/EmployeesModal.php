<div id="companyEmployeesModal" class="modal">
    <div class="modal-content">
        <span class="close" id ="closeEmployeesModal" >&times;</span>
        <h2><?php echo $data['company_employees']; ?>Company Employees</h2>
        <div class="modal-body" id="modalBodyCompanyEmployeesDetails">
        </div>
        <?php
        $loaderId = 'loadingCompanyEmployeesDetails';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>
