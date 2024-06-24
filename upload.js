document.getElementById('file').addEventListener('change', function() {
    const fileInput = document.getElementById('file');
    const uploadBtn = document.getElementById('uploadBtn');
    if (fileInput.files.length > 0) {
        document.getElementById('fileName').innerHTML = `<b>${fileInput.files[0].name}</b>`;
        uploadBtn.disabled = false;
    } else {
        document.getElementById('fileName').innerHTML = '';
        uploadBtn.disabled = true;
    }
});

document.getElementById('uploadFileForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const { username, password } = authCredentials;
    const fileInput = document.getElementById('file');
    const statusMessage = document.getElementById('statusMessage');
    const progressBar = document.getElementById('progressBar');
    const progressRow = document.getElementById('progressRow');

    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const formData = new FormData();
        formData.append('file', file);
        formData.append('username', username);
        formData.append('password', password);
	const fileDateTime = file.lastModified;
        formData.append('fileDateTime', fileDateTime);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'upload.php', true);

        const uploadBtn = document.getElementById('uploadBtn');
        const fileBtn = document.getElementById('file');
        uploadBtn.disabled = true;
        fileBtn.disabled = true;

        const startTime = Date.now();

        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = percentComplete + '%';
                progressBar.innerText = Math.round(percentComplete) + '%';
                progressRow.style.display = 'block'; // Show the progress bar
            }
        };

        xhr.onload = function() {
            const endTime = Date.now();
            const uploadTime = (endTime - startTime) / 1000;
            const fileSize = fileInput.files[0].size;
            const uploadRate = (fileSize / 1024 / uploadTime).toFixed(2);

            if (xhr.status === 200) {
                statusMessage.innerHTML = `File <b>${fileInput.files[0].name}</b> successfully uploaded. Upload time: <b>${uploadTime.toFixed(2)}</b> seconds. Upload rate: <b>${uploadRate}</b> KBps.`;
                loadFileList();
            } else {
                statusMessage.innerHTML = 'Upload failed!';
            }
            progressBar.style.width = '0%';
            progressBar.innerText = '';
            progressRow.style.display = 'none'; // Hide the progress bar
            document.getElementById('fileName').innerHTML = '';
            uploadBtn.disabled = true;
            fileBtn.disabled = false;
            fileBtn.value = '';
        };

        statusMessage.innerHTML = `Uploading file ${fileInput.files[0].name}...`;
        xhr.send(formData);
    }
});

async function loadFileList() {
    try {
        const { username, password } = authCredentials;
        console.log('Loading file list with credentials:', { username, password }); // Debugging
        const response = await fetch('file_list.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, password }),
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const fileList = await response.json();
        console.log('File list loaded:', fileList); // Debugging: Log the file list to the console
        displayFileList(fileList);
    } catch (error) {
        console.error('Error loading file list:', error); // Debugging: Log any errors to the console
    }
}

async function deleteFile(fileName) {
    const { username, password } = authCredentials;
    try {
        const response = await fetch('file_list.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, password, delete: fileName }),
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const result = await response.json();
        if (result.success) {
            loadFileList();
        } else {
            console.error('Error deleting file:', result.error);
        }
    } catch (error) {
        console.error('Error deleting file:', error);
    }
}
