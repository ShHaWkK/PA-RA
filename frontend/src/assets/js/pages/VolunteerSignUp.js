document.getElementById('registrationForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    const jsonData = {};

    // Convert FormData to JSON object, ignoring availabilities
    formData.forEach((value, key) => {
        if (key.includes('availabilities')) {
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

    // Handle availabilities
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

    // Log the JSON data (you can send it to an API endpoint here)
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

//-------------Récupérer les compétences depuis la BDD et les placer dans le select -------------
// Fonction pour récupérer les compétences depuis l'API
async function fetchSkillsAndPopulateSelector() {
    try {
        const response = await getAllSkills();
        console.log("response",response);
        const skills = await response.json();
        populateSkillsSelector(skills); // Appel de la fonction pour mettre à jour le sélecteur HTML
    } catch (error) {
        console.error('Error fetching skills:', error.message);
    }
}

// Fonction pour mettre à jour le sélecteur HTML avec les compétences récupérées
function populateSkillsSelector(skills) {
    const skillsSelect = document.getElementById('skills');
    skills.forEach(skill => {
        const option = document.createElement('option');
        option.value = skill.id;
        option.textContent = `${skill.name} - ${skill.description}`;
        skillsSelect.appendChild(option);
    });
}

// Appel de la fonction au chargement de la page ou lorsque nécessaire
document.addEventListener('DOMContentLoaded', fetchSkillsAndPopulateSelector);