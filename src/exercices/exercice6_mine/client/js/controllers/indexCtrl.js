$(document).ready(function () {
    const teamsTableBody = $("#teamsTable tbody");

    // Fetch data from the server
    chargerTeam(chargerTeamSuccess, chargerTeamError);

    /**
     * Handle successful team data load
     * @param {XMLDocument} data
     */
    function chargerTeamSuccess(data) {
        teamsTableBody.empty(); // Clear existing rows
        $(data)
            .find("equipe")
            .each(function () {
                const equipe = new Equipe();
                equipe.setPk($(this).find("id").text());
                equipe.setNom($(this).find("nom").text());

                const row = `<tr>
                    <td>${equipe.getPk()}</td>
                    <td>${equipe.toString()}</td>
                </tr>`;
                teamsTableBody.append(row);
            });
    }

    /**
     * Handle errors during the AJAX request
     * @param {XMLHttpRequest} request
     * @param {String} status
     * @param {String} error
     */
    function chargerTeamError(request, status, error) {
        alert(
            "Error: " + error + ", Request: " + request.statusText + ", Status: " + status
        );
    }
});
