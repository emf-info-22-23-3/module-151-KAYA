class IndexCtrl {
    constructor() {
        this.http = new servicesHttp();
        // Bind the methods to preserve 'this' context
        this.connectSuccess = this.connectSuccess.bind(this);
        this.CallbackError = this.CallbackError.bind(this);
    }

    connectSuccess(data, text, jqXHR) {
        console.log("connectSuccess called");
        if ($(data).find("success").text() === 'true') {
			console.log($(data));
			console.log(data);
            Toastify({
                text: "Login successful",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#33cc33"
            }).showToast();

            if($(data).find("isAdmin").text() === 'true'){
                window.location.href = "views/admin.html"; 
            } else {
                window.location.href = "views/client.html"; 
            }
        } else {
			console.log($(data));
			console.log(data);
            Toastify({
                text: "Login failed. Incorrect email or password.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#ff3333"
            }).showToast();
        }
    }

    disconnectSuccess(data, text, jqXHR) {
        Toastify({
            text: "User disconnected",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#33cc33"
        }).showToast();
        
        window.location.href = "../login.html";
    }

    CallbackError(request, status, error) {
        console.log("Error login:", error);
        Toastify({
            text: "Error login: " + error,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#ff3333"
        }).showToast();
    }
}

// Start method called after page load
$(document).ready(function () {
    window.ctrl = new IndexCtrl();

    $("#loginForm").on("submit", function(event) {
        event.preventDefault();
        var email = $("#email").val();
        var password = $("#password").val();
        console.log("Form submitted");
        console.log("Sending email:", email, "and password:", password);
        window.ctrl.http.connect(email, password, window.ctrl.connectSuccess, window.ctrl.CallbackError);
    });
});