class IndexCtrl {
    constructor() {
        this.http = new servicesHttp();
        // Bind the methods to preserve 'this' context
        this.connectSuccess = this.connectSuccess.bind(this);
        this.CallbackError = this.CallbackError.bind(this);
        this.getArmorNamesSuccess = this.getArmorNamesSuccess.bind(this);
        this.getAnnoncesSuccess = this.getAnnoncesSuccess.bind(this);
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
                window.ctrl.http.getAnnoncesForArmor(selectedId, window.ctrl.getAnnoncesSuccess, window.ctrl.CallbackError);
            } else {
                // Clear the form if no armor is selected
                $('#addArmorForm')[0].reset();
            }
        });
    }

     getAnnoncesSuccess(response) {
        console.log("Raw response:", response);
    
        // Convert string response to XML if necessary
        var parser = new DOMParser();
        var xmlDoc = parser.parseFromString(response, "application/xml");
    
        var setElement = xmlDoc.querySelector('set');
        console.log("Set element found:", setElement !== null);
        console.log("Set element content:", setElement ? setElement.innerHTML : 'No content');
    
        if (setElement && setElement.innerHTML.trim() !== '') {
            // Now process the set data
            var pkSet = setElement.querySelector('pk_set').textContent;
            var nom = setElement.querySelector('nom').textContent;
            var capNom = setElement.querySelector('cap_nom').textContent;
            var tunicNom = setElement.querySelector('tunic_nom').textContent;
            var trousersNom = setElement.querySelector('trousers_nom').textContent;
            var description = setElement.querySelector('description').textContent;
            var effet = setElement.querySelector('effet').textContent;
            var imageSet = setElement.querySelector('image_set').textContent;
    
            // Do something with the extracted data (e.g., display it)
            console.log("Armor Set Data:", {
                pkSet, nom, capNom, tunicNom, trousersNom, description, effet, imageSet
            });
        } else {
            console.log("No set data found in response");
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