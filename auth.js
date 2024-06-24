let authCredentials = { username: '', password: '' };

async function authenticateUser(username, password) {
    const response = await fetch('auth.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username, password }),
    });
    const result = await response.json();
    return result.authenticated;
}

function checkAuthentication() {
    if (authCredentials.username && authCredentials.password) {
        document.getElementById('loginForm').style.display = 'none';
        document.getElementById('uploadForm').style.display = 'block';
        loadFileList();
    } else {
        document.getElementById('loginForm').style.display = 'block';
        document.getElementById('uploadForm').style.display = 'none';
    }
}

document.getElementById('authForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const username = document.getElementById('loginUsername').value;
    const password = document.getElementById('loginPassword').value;

    const isAuthenticated = await authenticateUser(username, password);

    if (isAuthenticated) {
        authCredentials = { username, password };
        checkAuthentication();
    } else {
        document.getElementById('statusMessage').innerHTML = 'Incorrect username or password!';
    }
});

document.addEventListener('DOMContentLoaded', checkAuthentication);
