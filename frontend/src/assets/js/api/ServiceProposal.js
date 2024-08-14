async function createServiceProposal(proposalData) {
    try {
        const response = await fetch(apiEndpoint + '/service_proposals', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(proposalData)
        });
        if (!response.ok) {
            throw new Error('Failed to create service proposal');
        }
        return await response.json();
    } catch (error) {
        console.error('Error creating service proposal:', error.message);
        throw error;
    }
}

async function getServiceProposalByID(proposal_id) {
    try {
        const response = await fetch(apiEndpoint + '/service_proposals/id/' + proposal_id, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get service proposal');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting service proposal:', error.message);
        throw error;
    }
}

async function updateServiceProposal(proposal_id, proposalData) {
    try {
        const response = await fetch(apiEndpoint + '/service_proposals/id/' + proposal_id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(proposalData)
        });
        if (!response.ok) {
            throw new Error('Failed to update service proposal');
        }
        return await response.json();
    } catch (error) {
        console.error('Error updating service proposal:', error.message);
        throw error;
    }
}

async function deleteServiceProposal(proposal_id) {
    try {
        const response = await fetch(apiEndpoint + '/service_proposals/id/' + proposal_id, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to delete service proposal');
        }
        return { message: 'Service proposal deleted successfully' };
    } catch (error) {
        console.error('Error deleting service proposal:', error.message);
        throw error;
    }
}

async function getAllServiceProposals() {
    try {
        const response = await fetch(apiEndpoint + '/service_proposals', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get all service proposals');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting all service proposals:', error.message);
        throw error;
    }
}

async function approveAndCreateSchedule(proposalId) {
    try {
        const response = await fetch(apiEndpoint + '/service_proposals/' + proposalId + '/approve_and_create_schedule', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to approve proposal and create schedule');
        }
        return await response.json();
    } catch (error) {
        console.error('Error approving proposal and creating schedule:', error.message);
        throw error;
    }
}

export { createServiceProposal, getServiceProposalByID, updateServiceProposal, deleteServiceProposal, getAllServiceProposals, approveAndCreateSchedule };
