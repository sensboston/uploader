let sortDirection = {};
let sortFunctions = {
    'File Name': (a, b) => a.name.localeCompare(b.name),
    'File Size': (a, b) => a.sizeBytes - b.sizeBytes,
    'File Time': (a, b) => new Date(a.modified) - new Date(b.modified),
    'Upload Time': (a, b) => new Date(a.uploaded) - new Date(b.uploaded)
};

function displayFileList(fileList) {
    const fileListContainer = document.getElementById('fileList');
    fileListContainer.innerHTML = '';

    const table = document.createElement('table');
    const headerRow = table.insertRow();

    ['File Name', 'File Size', 'File Time', 'Upload Time', '', ''].forEach(headerText => {
        const th = document.createElement('th');
        if (headerText && headerText !== '') {
            const button = document.createElement('button');
            button.textContent = headerText;
            button.onclick = () => sortTable(headerText, fileList);
            button.style.background = 'none';
            button.style.border = 'none';
            button.style.cursor = 'pointer';
            th.appendChild(button);
        } else {
            th.textContent = headerText;
        }
        headerRow.appendChild(th);
    });

    fileList.forEach(file => {
        const row = table.insertRow();
        row.insertCell().textContent = file.name;
        row.insertCell().textContent = file.size;
        row.insertCell().textContent = file.modified;
        row.insertCell().textContent = file.uploaded;

        const linkCell = row.insertCell();
        const link = document.createElement('a');
        link.href = file.url;
        link.textContent = 'Download';
        linkCell.appendChild(link);

        const deleteCell = row.insertCell();
        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Delete';
        deleteButton.className = 'btn btn-delete';
        deleteButton.onclick = () => deleteFile(file.name);
        deleteCell.appendChild(deleteButton);
    });

    fileListContainer.appendChild(table);
}

function sortTable(column, fileList) {
    if (!sortDirection[column]) {
        sortDirection[column] = 'asc';
    } else {
        sortDirection[column] = sortDirection[column] === 'asc' ? 'desc' : 'asc';
    }

    fileList.sort(sortFunctions[column]);

    if (sortDirection[column] === 'desc') {
        fileList.reverse();
    }

    displayFileList(fileList);
}
