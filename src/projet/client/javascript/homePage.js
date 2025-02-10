document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        const response = await fetch('/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (data.success) {
            showToast('Successfully logged in!', 'success');
            updateUserInterface(data.user);
        } else {
            showToast('Invalid email or password', 'error');
        }
    } catch (error) {
        showToast('An error occurred', 'error');
    }
});

function showToast(message, type) {
    Toastify({
        text: message,
        duration: 3000,
        gravity: "top",
        position: "right",
        backgroundColor: type === 'success' ? "#2ecc71" : "#e74c3c"
    }).showToast();
}

function updateUserInterface(user) {
    document.getElementById('userType').textContent = user.name;
    document.getElementById('userRole').textContent = user.role;
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('sidebar').classList.add('visible');
    
    // Show/hide modify link based on admin status
    const modifyLink = document.querySelector('a[href="#modify"]');
    modifyLink.style.display = user.role === 'Administrator' ? 'block' : 'none';
}

// Check if user is already logged in on page load
fetch('/checkLogin.php')
    .then(response => response.json())
    .then(data => {
        if (data.loggedIn) {
            updateUserInterface(data.user);
        }
    });