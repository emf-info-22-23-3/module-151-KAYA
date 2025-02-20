class IndexCtrl {
    constructor() {
        this.http = new servicesHttp();
        // Bind the methods to preserve 'this' context
        this.connectSuccess = this.connectSuccess.bind(this);
        this.callbackError = this.callbackError.bind(this);
        this.handleArmorNameSelect = this.handleArmorNameSelect.bind(this);
        this.initializeEventListeners();
        this.loadArmorNames();
    }

    initializeEventListeners() {
        $("#armorNameSelect").on("change", this.handleArmorNameSelect);
    }

    loadArmorNames() {
        this.http.getArmorNames(
            (data) => {
                const select = $("#armorNameSelect");
                select.empty();
                select.append('<option value="">Select an armor set...</option>');
                
                $(data).find("name").each(function() {
                    const name = $(this).text();
                    select.append(`<option value="${name}">${name}</option>`);
                });
            },
            this.callbackError
        );
    }

    handleArmorNameSelect() {
        const selectedName = $("#armorNameSelect").val();
        if (!selectedName) return;

        this.http.getAnnonces(
            (data) => {
                $(data).find("set").each(function() {
                    if ($(this).find("nom").text() === selectedName) {
                        // Fill form fields with set data
                        $("#armorName").val($(this).find("nom").text());
                        $("#armorCapName").val($(this).find("cap_nom").text());
                        $("#armorTunicName").val($(this).find("tunic_nom").text());
                        $("#armorTrousersName").val($(this).find("trousers_nom").text());
                        $("#armorEffect").val($(this).find("effet").text());
                        $("#armorDescription").val($(this).find("description").text());
                    }
                });
            },
            this.callbackError
        );
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

    callbackError(request, status, error) {
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