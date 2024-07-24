<link rel="stylesheet" href="/assets/css/modal.css">

<div id="modifyUserModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModify">&times;</span>
        <form id="modificationForm">
            <div>
                <label for="first_name_modify"><?php echo htmlspecialchars($data['first_name_label']); ?></label>
                <input type="text" id="first_name_modify" name="first_name">
            </div>
            <div>
                <label for="last_name_modify"><?php echo htmlspecialchars($data['last_name_label']); ?></label>
                <input type="text" id="last_name_modify" name="last_name">
            </div>
            <div>
                <label for="email_modify"><?php echo htmlspecialchars($data['email_label']); ?></label>
                <input type="email" id="email_modify" name="email">
            </div>
            <div>
                <label for="phone_number_modify"><?php echo htmlspecialchars($data['phone_label']); ?></label>
                <input type="tel" id="phone_number_modify" name="phone_number">
            </div>
            <div>
                <label for="password_modify"><?php echo htmlspecialchars($data['password_label2']); ?></label>
                <input type="password" id="password_modify" name="password">
            </div>

            <button type="submit"><?php echo htmlspecialchars($data['submit_button2']); ?></button>

        </form>
        <?php
        $loaderId = 'loadingModification';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>

<script src="/assets/js/modules/modals/AdminVolunteerModals.js"></script>