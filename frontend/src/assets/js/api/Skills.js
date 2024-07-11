console.log("apiEndpoint",apiEndpoint);

async function createSkill(skillData) {
    try {
        const response = await fetch(apiEndpoint + '/skills', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(skillData)
        });
        if (!response.ok) {
            throw new Error('Failed to create skill');
        }
        return await response.json();
    } catch (error) {
        console.error('Error creating skill:', error.message);
        throw error;
    }
}

async function getSkill(skillId) {
    try {
        const response = await fetch(apiEndpoint + '/skills/' + skillId, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get skill');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting skill:', error.message);
        throw error;
    }
}

async function updateSkill(skillId, skillData) {
    try {
        const response = await fetch(apiEndpoint + '/skills/' + skillId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(skillData)
        });
        if (!response.ok) {
            throw new Error('Failed to update skill');
        }
        return await response.json();
    } catch (error) {
        console.error('Error updating skill:', error.message);
        throw error;
    }
}

async function deleteSkill(skillId) {
    try {
        const response = await fetch(apiEndpoint + '/skills/' + skillId, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to delete skill');
        }
        return { message: 'Skill deleted successfully' };
    } catch (error) {
        console.error('Error deleting skill:', error.message);
        throw error;
    }
}

async function getAllSkills() {
    return await fetch(apiEndpoint + '/skills', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    });
}