<form id="registrationForm" class="hidden">
    <div>
        <label for="first_name"><?php echo htmlspecialchars($data['first_name_label']); ?></label>
        <input type="text" id="first_name" name="first_name" required>
    </div>
    <div>
        <label for="last_name"><?php echo htmlspecialchars($data['last_name_label']); ?></label>
        <input type="text" id="last_name" name="last_name" required>
    </div>
    <div>
        <label for="email"><?php echo htmlspecialchars($data['email_label']); ?></label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="phone_number"><?php echo htmlspecialchars($data['phone_label']); ?></label>
        <input type="tel" id="phone_number" name="phone_number" required>
    </div>
    <div>
        <label for="password"><?php echo htmlspecialchars($data['password_label2']); ?></label>
        <input type="password" id="password" name="password" required>
    </div>
    <div id="skillsTable">
        <table>
            <thead>
            <tr>
                <th><?php echo htmlspecialchars($data['select_label']); ?></th>
                <th><?php echo htmlspecialchars($data['skill_name_label']); ?></th>
                <th><?php echo htmlspecialchars($data['description_label']); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $loaderId = 'loading-body';
            include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
            <!-- Les compétences seront ajoutées ici dynamiquement -->
            </tbody>
        </table>
    </div>

    <div>
        <label><?php echo htmlspecialchars($data['availabilities_heading']); ?></label>
        <table id="availabilities">
            <tr>
                <th><?php echo htmlspecialchars($data['select_label']); ?></th>
                <th><?php echo htmlspecialchars($data['day_of_week_label']); ?></th>
                <th><?php echo htmlspecialchars($data['start_time_label']); ?></th>
                <th><?php echo htmlspecialchars($data['end_time_label']); ?></th>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[0][selected]" value="1"></td>
                <td><input type="text" name="availabilities[0][day_of_week]" value="Monday" readonly></td>
                <td><input type="time" name="availabilities[0][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[0][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[1][selected]" value="1"></td>
                <td><input type="text" name="availabilities[1][day_of_week]" value="Tuesday" readonly></td>
                <td><input type="time" name="availabilities[1][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[1][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[2][selected]" value="1"></td>
                <td><input type="text" name="availabilities[2][day_of_week]" value="Wednesday" readonly></td>
                <td><input type="time" name="availabilities[2][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[2][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[3][selected]" value="1"></td>
                <td><input type="text" name="availabilities[3][day_of_week]" value="Thursday" readonly></td>
                <td><input type="time" name="availabilities[3][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[3][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[4][selected]" value="1"></td>
                <td><input type="text" name="availabilities[4][day_of_week]" value="Friday" readonly></td>
                <td><input type="time" name="availabilities[4][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[4][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[5][selected]" value="1"></td>
                <td><input type="text" name="availabilities[5][day_of_week]" value="Saturday" readonly></td>
                <td><input type="time" name="availabilities[5][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[5][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[6][selected]" value="1"></td>
                <td><input type="text" name="availabilities[6][day_of_week]" value="Sunday" readonly></td>
                <td><input type="time" name="availabilities[6][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[6][end_time]" value="18:00"></td>
            </tr>
        </table>
    </div>
    <div>
        <button type="submit"><?php echo htmlspecialchars($data['submit_button2']); ?></button>
    </div>
</form>