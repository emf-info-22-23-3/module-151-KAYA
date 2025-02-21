class IndexCtrl {
    constructor() {
        this.http = new servicesHttp();
        // Bind the methods to preserve 'this' context
        this.connectSuccess = this.connectSuccess.bind(this);
        this.CallbackError = this.CallbackError.bind(this);
        this.getArmorNamesSuccess = this.getArmorNamesSuccess.bind(this);
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

    getArmorNamesSuccess(data, text, jqXHR) {
        console.log("getArmorNamesSuccess called");
        console.log("Received data:", data);
        
        const $select = $('#armorNameSelect');
        $select.empty();
        $select.append('<option value="">Select an armor set...</option>');
        
        $(data).find("armor").each(function() {
            const id = $(this).find("id").text();
            const name = $(this).find("name").text();
            console.log("Adding armor:", id, name);
            $select.append(`<option value="${id}">${name}</option>`);
        });

        // Add change event listener to the select
        $select.off('change').on('change', function() {
            const selectedId = $(this).val();
            if (selectedId) {
                console.log("Selected armor set ID:", selectedId);
                // TODO: Later we'll implement the getAnnonces call here to fill the form
                window.ctrl.http.getAnnoncesForArmor(selectedId, window.ctrl.getAnnoncesSuccess, window.ctrl.CallbackError);
            }
        });
    }

    getAnnoncesSuccess(data, text, jqXHR) {
        console.log("getAnnoncesSuccess called");
        console.log("Received armor details:", data);
    
        // Set the form fields with the fetched armor details
        $('#armorName').val($(data).find("nom").text());
        $('#armorCapName').val($(data).find("cap_nom").text());
        $('#armorCapSource').val($(data).find("fk_cap_source").text());
        $('#armorCapSourceType').val($(data).find("capSourceType").text());
        $('#armorTunicName').val($(data).find("tunic_nom").text());
        $('#armorTunicSource').val($(data).find("fk_tunic_source").text());
        $('#armorTunicSourceType').val($(data).find("tunicSourceType").text());
        $('#armorTrousersName').val($(data).find("trousers_nom").text());
        $('#armorTrousersSource').val($(data).find("fk_trousers_source").text());
        $('#armorTrousersSourceType').val($(data).find("trousersSourceType").text());
        $('#armorEffect').val($(data).find("effet").text());
        $('#armorDescription').val($(data).find("description").text());
        
        // Optionally, handle the image upload if you have the image path in the XML
        const imageUrl = $(data).find("image").text();
        if (imageUrl) {
            $('#armorImage').val(imageUrl);
        }
    }
    

    CallbackError(request, status, error) {
        console.log("Error:", error);
        Toastify({
            text: "Error: " + error,
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

    // If we're on the admin/client page with the armor select, load the armor names
    if ($("#armorNameSelect").length) {
        console.log("Loading armor names");
        window.ctrl.http.getArmorNames(window.ctrl.getArmorNamesSuccess, window.ctrl.CallbackError);
    }
});




/*
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

*/