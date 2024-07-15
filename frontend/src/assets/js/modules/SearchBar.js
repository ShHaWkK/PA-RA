export function setupSearch(users) {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('volunteerTable');
    const rows = table.getElementsByTagName('tr');

    searchInput.addEventListener('input', function() {
        const searchText = this.value.trim().toLowerCase();

        Array.from(rows).forEach(row => {
            const userId = row.dataset.userId;
            const user = users.find(u => u.id == userId);

            if (user) {
                const fullName = `${user.first_name} ${user.last_name}`.toLowerCase();
                const email = user.email.toLowerCase();

                const isVisible = fullName.includes(searchText) || email.includes(searchText);
                row.style.display = isVisible ? '' : 'none';
            }
        });
    });
}
