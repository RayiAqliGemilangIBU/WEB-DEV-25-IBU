// Fetch the student data from the JSON file
fetch('../static/students.json')
  .then(response => response.json())
  .then(data => {
    const tableBody = document.getElementById('tableBody');
    const tableHeader = document.querySelector('#studentTable thead tr');

    if (data.length > 0) {
      // Generate Table Headers dynamically based on JSON keys
      const headers = Object.keys(data[0]); // Extract the keys (fields) from the first student object
      headers.forEach(header => {
        const th = document.createElement('th');
        th.classList.add('px-3', 'py-2', 'whitespace-nowrap');
        th.textContent = header.charAt(0).toUpperCase() + header.slice(1).replace(/_/g, ' '); // Capitalize and replace underscores
        tableHeader.appendChild(th);
      });

      // Add a column for Actions (Edit & Remove)
      const thActions = document.createElement('th');
      thActions.classList.add('px-3', 'py-2', 'whitespace-nowrap');
      thActions.textContent = 'Actions';
      tableHeader.appendChild(thActions);

      // Populate Table Rows dynamically
      data.forEach(student => {
        const row = document.createElement('tr');
        headers.forEach(header => {
          const td = document.createElement('td');
          td.classList.add('px-3', 'py-2', 'whitespace-nowrap');
          td.textContent = student[header] || ''; // Populate the cell with the value from JSON
          row.appendChild(td);
        });

        // Add Action buttons (Edit & Remove)
        const actionCell = document.createElement('td');
        actionCell.classList.add('px-3', 'py-2', 'whitespace-nowrap');
        
        // Edit Button
        const editButton = document.createElement('button');
        editButton.classList.add('bg-black', 'text-white', 'px-3', 'py-1', 'rounded');
        editButton.textContent = 'Edit';
        editButton.onclick = () => openEditModal(student);
        actionCell.appendChild(editButton);
        
        // Remove Button
        const removeButton = document.createElement('button');
        removeButton.classList.add('bg-black', 'text-white', 'px-3', 'py-1', 'rounded', 'ml-2');
        removeButton.textContent = 'Remove';
        removeButton.onclick = () => openDeleteConfirmation(student);
        actionCell.appendChild(removeButton);

        row.appendChild(actionCell);
        tableBody.appendChild(row);
      });
    }
  })
  .catch(error => console.error('Error loading student data:', error));

// Open Edit Modal (You can replace this with your modal code)
function openEditModal(student) {
  alert('Edit student: ' + student.name);
}

// Open Delete Confirmation
function openDeleteConfirmation(student) {
  if (confirm(`Are you sure you want to delete student ${student.name}? This action is permanent.`)) {
    console.log(`Deleted student: ${student.name}`);
    // You can implement the actual deletion logic here, for example by updating the JSON or removing from the UI.
  }
}
