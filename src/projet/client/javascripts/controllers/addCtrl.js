class AddCtrl {
    constructor() {
        this.http = new servicesHttp();
        this.CallbackError = this.CallbackError.bind(this);
        this.getSourceTypesSuccess = this.getSourceTypesSuccess.bind(this);
    }

    getSourceTypesSuccess(data) {
    console.log("getSourceTypesSuccess called");
    console.log("Received dataAAAAAAAAA:", data);

    // Check if data is a string and parse it if necessary
    if (typeof data === "string") {
        console.log("Data is a string, attempting to parse...");
        data = $.parseXML(data);
    }

    const $xml = $(data);  // jQuery-wrapped XML object

    // Log the entire parsed XML structure
    console.log("Parsed XML:", $xml);

    // Try to find all sourceType elements (at any depth within sourceTypes)
    const sourceTypes = $xml.find("sourceTypes sourceType");

    console.log("Found source types:", sourceTypes.length);

    if (sourceTypes.length > 0) {
        sourceTypes.each(function() {
            const value = $(this).find("pk_type_source").text();  // Get pk_type_source
            const label = $(this).find("type").text();  // Get type (label)

            console.log("Found source type:", label);

            // Populate each of the source type selects
            $("#armorCapSourceType").append(`<option value="${value}">${label}</option>`);
            $("#armorTunicSourceType").append(`<option value="${value}">${label}</option>`);
            $("#armorTrousersSourceType").append(`<option value="${value}">${label}</option>`);
        });

        Toastify({
            text: "Source types loaded successfully",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#33cc33"
        }).showToast();
    } else {
        Toastify({
            text: "No source types found",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#ff3333"
        }).showToast();
    }
}

      

    CallbackError(request, status, error) {
        Toastify({
            text: "Error: " + error,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#ff3333"
        }).showToast();
    }
    
}

$(document).ready(function () {
    window.ctrl = new AddCtrl();

    if ($("#armorCapSourceType").length) {
        console.log("Loading source types");
        window.ctrl.http.getSourceTypes(window.ctrl.getSourceTypesSuccess, window.ctrl.CallbackError);
    }
});