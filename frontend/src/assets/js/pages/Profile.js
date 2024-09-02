import {
    addUserSkill,
    getUser,
    getUserAvailabilities,
    getUserSkills,
    modifyUser,
    removeUserSkill
} from "../api/Users.js";
import {getCookie} from "../api/Api.js";
import { getAllSkills} from "../api/Skills.js";
import {createAvailability, deleteAvailability} from "../api/Availabilities.js";

async function populateUserProfile(userId) {
    try {
        const user = await getUser(userId);

        if (user) {
            // Peupler les champs du formulaire avec les données utilisateur
            document.getElementById('first_name').value = user.first_name;
            document.getElementById('last_name').value = user.last_name;
            document.getElementById('email').value = user.email;
            document.getElementById('phone_number').value = user.phone_number;

            // Si vous avez d'autres champs spécifiques à peupler, vous pouvez les ajouter ici
        } else {
            console.error('Utilisateur non trouvé.');
        }
    } catch (error) {
        console.error('Erreur lors du peuplement du profil utilisateur :', error.message);
    }
}

async function populateUserSkills(userId) {
    try {
        const skills = await getUserSkills(userId);

        if (skills && Array.isArray(skills)) {
            const skillsTableBody = document.querySelector('#skillsTable tbody');
            skillsTableBody.innerHTML = ''; // Vider le contenu actuel du tableau

            // Ajouter chaque compétence à la table
            skills.forEach(skill => {
                const row = document.createElement('tr');

                const selectCell = document.createElement('td');
                const selectInput = document.createElement('input');
                selectInput.type = 'checkbox';
                selectInput.name = `skills[${skill.id}][selected]`;
                selectInput.value = skill.id;
                selectInput.checked = skill.selected || true; // cocher si déjà sélectionné
                selectInput.disabled = true;
                selectCell.appendChild(selectInput);

                const nameCell = document.createElement('td');
                nameCell.textContent = skill.name;

                const descriptionCell = document.createElement('td');
                descriptionCell.textContent = skill.description;

                row.appendChild(selectCell);
                row.appendChild(nameCell);
                row.appendChild(descriptionCell);

                skillsTableBody.appendChild(row);
            });
        } else {
            console.error('Aucune compétence trouvée pour cet utilisateur.');
        }
    } catch (error) {
        console.error('Erreur lors du peuplement des compétences de l\'utilisateur :', error.message);
    }
}

async function populateUserAvailabilities(userId) {
    try {
        const availabilities = await getUserAvailabilities(userId);

        if (availabilities && Array.isArray(availabilities)) {
            const availabilitiesTableBody = document.querySelector('#availabilities tbody');
            availabilitiesTableBody.innerHTML = ''; // Vider le contenu actuel du tableau

            // Ajouter chaque disponibilité à la table
            availabilities.forEach((availability, index) => {
                const row = document.createElement('tr');

                const selectCell = document.createElement('td');
                const selectInput = document.createElement('input');
                selectInput.type = 'checkbox';
                selectInput.name = `availabilities[${index}][selected]`;
                selectInput.value = availability.id;
                selectInput.checked = availability.selected || true; // cocher si déjà sélectionné
                selectInput.disabled = true;
                selectCell.appendChild(selectInput);

                const dayOfWeekCell = document.createElement('td');
                const dayOfWeekInput = document.createElement('input');
                dayOfWeekInput.type = 'text';
                dayOfWeekInput.name = `availabilities[${index}][day_of_week]`;
                dayOfWeekInput.value = availability.day_of_week;
                dayOfWeekInput.readOnly = true;
                dayOfWeekCell.appendChild(dayOfWeekInput);

                const startTimeCell = document.createElement('td');
                const startTimeInput = document.createElement('input');
                startTimeInput.type = 'time';
                startTimeInput.name = `availabilities[${index}][start_time]`;
                startTimeInput.value = availability.start_time;
                startTimeCell.appendChild(startTimeInput);

                const endTimeCell = document.createElement('td');
                const endTimeInput = document.createElement('input');
                endTimeInput.type = 'time';
                endTimeInput.name = `availabilities[${index}][end_time]`;
                endTimeInput.value = availability.end_time;
                endTimeCell.appendChild(endTimeInput);

                row.appendChild(selectCell);
                row.appendChild(dayOfWeekCell);
                row.appendChild(startTimeCell);
                row.appendChild(endTimeCell);

                availabilitiesTableBody.appendChild(row);
            });
        } else {
            console.error('Aucune disponibilité trouvée pour cet utilisateur.');
        }
    } catch (error) {
        console.error('Erreur lors du peuplement des disponibilités de l\'utilisateur :', error.message);
    }
}

async function populateAllSkills(userId) {
    try {
        const response = await getAllSkills();
        const allSkills = await response.json();

        const skillsTableBody = document.querySelector('#skillsTable tbody');
        skillsTableBody.innerHTML = ''; // Vider la table existante

        allSkills.forEach(skill => {
            const row = document.createElement('tr');

            // Colonne de la case à cocher
            const selectCell = document.createElement('td');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = `skills[${skill.id}][selected]`;
            checkbox.value = skill.id;
            selectCell.appendChild(checkbox);
            row.appendChild(selectCell);

            // Colonne du nom de la compétence
            const nameCell = document.createElement('td');
            nameCell.textContent = skill.name;
            row.appendChild(nameCell);

            // Colonne de la description de la compétence
            const descriptionCell = document.createElement('td');
            descriptionCell.textContent = skill.description;
            row.appendChild(descriptionCell);

            // Ajouter la ligne au tableau
            skillsTableBody.appendChild(row);
        });
    } catch (error) {
        console.error('Erreur lors de la récupération des compétences :', error);
    }
}

function resetAvailabilitiesTable() {
    const availabilitiesTable = document.getElementById('availabilities');
    availabilitiesTable.innerHTML = `
            <tr>
                <th>Sélectionner</th>
                <th>Jour de la semaine</th>
                <th>Heure de début</th>
                <th>Heure de fin</th>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[0][selected]" value="1"></td>
                <td><input type="text" name="availabilities[0][day_of_week]" value="Monday" readonly></td>
                <td><input type="time" name="availabilities[0][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[0][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[1][selected]" value="2"></td>
                <td><input type="text" name="availabilities[1][day_of_week]" value="Tuesday" readonly></td>
                <td><input type="time" name="availabilities[1][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[1][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[2][selected]" value="3"></td>
                <td><input type="text" name="availabilities[2][day_of_week]" value="Wednesday" readonly></td>
                <td><input type="time" name="availabilities[2][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[2][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[3][selected]" value="4"></td>
                <td><input type="text" name="availabilities[3][day_of_week]" value="Thursday" readonly></td>
                <td><input type="time" name="availabilities[3][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[3][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[4][selected]" value="5"></td>
                <td><input type="text" name="availabilities[4][day_of_week]" value="Friday" readonly></td>
                <td><input type="time" name="availabilities[4][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[4][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[5][selected]" value="6"></td>
                <td><input type="text" name="availabilities[5][day_of_week]" value="Saturday" readonly></td>
                <td><input type="time" name="availabilities[5][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[5][end_time]" value="18:00"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="availabilities[6][selected]" value="7"></td>
                <td><input type="text" name="availabilities[6][day_of_week]" value="Sunday" readonly></td>
                <td><input type="time" name="availabilities[6][start_time]" value="06:00"></td>
                <td><input type="time" name="availabilities[6][end_time]" value="18:00"></td>
            </tr>
        `;
}

async function updateSkills() {
    const userId = getCookie('user_id');

    try {
        // Récupérer les compétences sélectionnées dans le formulaire
        const selectedSkills = [];
        const skillInputs = document.querySelectorAll('#skillsTable input[type="checkbox"]:checked');

        skillInputs.forEach(input => {
            const skillId = input.value;
            selectedSkills.push(skillId);
        });

        // Récupérer les compétences existantes de l'utilisateur
        const userSkills = await getUserSkills(userId);
        console.log("userSkills",userSkills);

        // Convertir les skill IDs en chaînes de caractères pour une comparaison correcte
        const existingSkillIds = userSkills.map(skill => String(skill.id));
        console.log("existingSkillIds", existingSkillIds);

        const skillsToAdd = selectedSkills.filter(skillId => !existingSkillIds.includes(skillId));
        console.log("skillsToAdd", skillsToAdd);

        const skillsToRemove = existingSkillIds.filter(skillId => !selectedSkills.includes(skillId));
        console.log("skillsToRemove", skillsToRemove);

        // Ajouter les nouvelles compétences
        for (const skillId of skillsToAdd) {
            await addUserSkill( userId, skillId );
        }

        // Supprimer les compétences obsolètes
        for (const skillId of skillsToRemove) {
            await removeUserSkill(userId,skillId);
        }

        alert('Compétences mises à jour avec succès !');
    } catch (error) {
        console.error('Erreur lors de la mise à jour des compétences:', error);
        alert('Une erreur est survenue lors de la mise à jour des compétences.');
    }
}

async function updateAvailabilities() {
    const userId = getCookie('user_id');

    try {
        // Récupérer les disponibilités sélectionnées dans le formulaire
        const selectedAvailabilities = [];
        const table = document.getElementById('availabilities');
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach((row) => {
            const checkbox = row.querySelector('input[type="checkbox"]');
            if (checkbox && checkbox.checked) {
                const dayOfWeek = row.querySelector('input[name$="[day_of_week]"]').value;
                const startTime = row.querySelector('input[name$="[start_time]"]').value;
                const endTime = row.querySelector('input[name$="[end_time]"]').value;

                selectedAvailabilities.push({
                    user_id: userId,
                    day_of_week: dayOfWeek,
                    start_time: startTime,
                    end_time: endTime
                });
            }
        });

        // Récupérer les disponibilités existantes de l'utilisateur
        const userAvailabilities = await getUserAvailabilities(userId);
        console.log("userAvailabilities", userAvailabilities);

        // Déterminer les disponibilités à ajouter et à supprimer
        const existingAvailabilityIds = userAvailabilities.map(avail => `${avail.day_of_week}-${avail.start_time}-${avail.end_time}`);
        console.log("existingAvailabilityIds", existingAvailabilityIds);

        const availabilitiesToAdd = selectedAvailabilities.filter(selected => {
            const key = `${selected.day_of_week}-${selected.start_time}-${selected.end_time}`;
            return !existingAvailabilityIds.includes(key);
        });
        console.log("availabilitiesToAdd", availabilitiesToAdd);

        const availabilitiesToRemove = userAvailabilities.filter(existing => {
            const key = `${existing.day_of_week}-${existing.start_time}-${existing.end_time}`;
            return !selectedAvailabilities.some(selected =>
                selected.day_of_week === existing.day_of_week &&
                selected.start_time === existing.start_time &&
                selected.end_time === existing.end_time
            );
        }).map(avail => avail.id);
        console.log("availabilitiesToRemove", availabilitiesToRemove);

        // Ajouter les nouvelles disponibilités
        for (const availability of availabilitiesToAdd) {
            await createAvailability(availability);
        }

        // Supprimer les disponibilités obsolètes
        for (const availability of availabilitiesToRemove) {
            await deleteAvailability(availability);
        }

        alert('Disponibilités mises à jour avec succès !');
    } catch (error) {
        console.error('Erreur lors de la mise à jour des disponibilités:', error);
        alert('Une erreur est survenue lors de la mise à jour des disponibilités.');
    }
}

document.addEventListener('DOMContentLoaded', async function () {
    const loader = document.getElementById('loading-body');
    const registrationForm = document.getElementById('registrationForm');
    const volunteerFile = document.getElementById('volunteerFile');
    const modifyButton = document.querySelector('.modify-button');
    const submitButton = document.getElementById('volunteerFormSubmit');

    registrationForm.classList.add('hidden');
    volunteerFile.classList.add('hidden');
    submitButton.classList.add('hidden');

    loader.classList.remove('hidden');

    disableFormFields(registrationForm);

    const userId = getCookie('user_id');

    try {
        // Attendre que toutes les fonctions asynchrones soient terminées
        await populateUserProfile(userId);
        await populateUserSkills(userId);
        await populateUserAvailabilities(userId);
    } catch (error) {
        console.error('Erreur lors du chargement des données utilisateur :', error);
    } finally {
        // Masquer le loader et afficher les formulaires
        loader.classList.add('hidden');
        registrationForm.classList.remove('hidden');
    }

    // Activer les champs lorsque l'utilisateur clique sur le bouton "Modifier"
    modifyButton.addEventListener('click', async function () {
        enableFormFields(registrationForm);
        await populateAllSkills(userId);
        resetAvailabilitiesTable();
        submitButton.classList.remove('hidden');
    });

    document.getElementById('registrationForm').addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = {
            first_name: document.getElementById('first_name').value,
            last_name: document.getElementById('last_name').value,
            email: document.getElementById('email').value,
            phone_number: document.getElementById('phone_number').value,
            password: document.getElementById('password').value
        };

        console.log("formData",formData);

        try {
            const result = await modifyUser(userId, formData);
            async function update(){
                updateSkills();
                updateAvailabilities();
            }
            await update();
            console.log('Modification succeeded', result);
            alert('Modification réussie');

            window.location.reload();

        } catch (error) {
            console.error('Erreur lors de la modification de l\'utilisateur:', error);
            alert('Modification failed');
        }
    });
});

function disableFormFields(form) {
    const fields = form.querySelectorAll('input, select, textarea, checkbox');
    fields.forEach(field => {
        field.disabled = true;
    });
}

function enableFormFields(form) {
    const fields = form.querySelectorAll('input, select, textarea');
    fields.forEach(field => {
        field.disabled = false;
    });
}