async function getPlanning(userId, startDate = null, endDate = null) {
    try {
        const params = new URLSearchParams();
        if (userId) params.append('user_id', userId);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        const response = await fetch(`${apiEndpoint}/planning?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to get planning');
        }

        const data = await response.json();
        return {
            routes: data.routes.map(route => route), // Adjust this based on the actual response structure
            collections: data.collections.map(collection => collection) // Adjust this based on the actual response structure
        };
    } catch (error) {
        console.error('Error getting planning:', error.message);
        throw error;
    }
}

export { getPlanning };