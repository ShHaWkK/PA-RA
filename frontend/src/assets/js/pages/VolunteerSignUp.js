import { registerVolunteer } from "../api/Users.js";
import { getAllSkills } from "../api/Skills.js"

function addVolunteerSubmitEvent(){
    document.getElementById('registrationForm').addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const jsonData = {};

        // Convert FormData to JSON object, ignoring availabilities
        formData.forEach((value, key) => {
            if (key.includes('availabilities') || key.includes('skills')) {
                // Skip availabilities keys
                return;
            }
            if (jsonData[key]) {
                if (!Array.isArray(jsonData[key])) {
                    jsonData[key] = [jsonData[key]];
                }
                jsonData[key].push(value);
            } else {
                jsonData[key] = value;
                console.log("jsonData[key] = value", jsonData[key], value);
            }
        });

        // Handle availabilities separately
        const availabilities = [];
        const rows = document.querySelectorAll('#availabilities tr');

        rows.forEach((row, index) => {
            if (index === 0) return; // Skip the header row

            const checkbox = row.querySelector('input[type="checkbox"]');
            if (checkbox && checkbox.checked) {
                const dayOfWeek = row.querySelector('input[name*="[day_of_week]"]').value;
                const startTime = row.querySelector('input[name*="[start_time]"]').value;
                const endTime = row.querySelector('input[name*="[end_time]"]').value;

                availabilities.push({
                    day_of_week: dayOfWeek,
                    start_time: startTime,
                    end_time: endTime
                });
            }
        });

        jsonData.availabilities = availabilities;

        // Handle skills separately
        const skills = [];
        document.querySelectorAll('input[name="skills[]"]:checked').forEach(checkbox => {
            skills.push(checkbox.value);
        });

        jsonData.skills = skills;

        console.log(jsonData);

        const result = await registerVolunteer(jsonData);

        if (!result.ok) {
            switch (result.status) {
                case 409:
                    alert("User with this email already exists");
                    break;
                default:
                    console.log('Failed to register volunteer');
                    alert('Failed to register volunteer');
                    break;
            }
        } else {
            console.log('Volunteer registered successfully');
            alert('Volunteer registered successfully');
        }
    });
}

//-------------Récupérer les compétences depuis la BDD et les placer dans le select -------------
// Fonction pour mettre à jour le sélecteur HTML avec les compétences récupérées
async function populateSkillTable() {
    try {
        document.getElementById('loading-body').classList.remove('hidden');
        const response = await getAllSkills();
        const skills = await response.json();
        const skillsTable = document.getElementById('skillsTable').querySelector('tbody');

        skills.forEach(skill => {
            // Création de la ligne du tableau
            const row = document.createElement('tr');

            // Colonne pour la checkbox
            const checkboxCell = document.createElement('td');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.value = skill.id;
            checkbox.name = 'skills[]';
            checkboxCell.appendChild(checkbox);
            row.appendChild(checkboxCell);

            // Colonne pour le nom de la compétence
            const nameCell = document.createElement('td');
            nameCell.textContent = skill.name;
            row.appendChild(nameCell);

            // Colonne pour la description de la compétence
            const descriptionCell = document.createElement('td');
            descriptionCell.textContent = skill.description;
            row.appendChild(descriptionCell);

            // Ajout de la ligne au tableau
            skillsTable.appendChild(row);

            // Après avoir peuplé le tableau, afficher le contenu
            document.getElementById('registrationForm').classList.remove('hidden');
            document.getElementById('loading-body').classList.add('hidden');

        });
    } catch (error) {
        console.error('Error fetching skills:', error.message);
    }
}

// Appel de la fonction au chargement de la page ou lorsque nécessaire
document.addEventListener('DOMContentLoaded',
    function (){
        addVolunteerSubmitEvent();
        populateSkillTable();
    });

export { populateSkillTable, addVolunteerSubmitEvent };