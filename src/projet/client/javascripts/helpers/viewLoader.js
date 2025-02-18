/*
  But : charger les vues 
  Auteur : Kaya Elif
  Date :   11.08.2024 / V2.0
*/
class viewLoader {
  constructor() {}

  chargerVue(vue, callback) {
    console.log(`Loading view: views/${vue}.html`);
    $("#view").load("views/" + vue + ".html", function () {
      if (typeof callback !== "undefined") {
        console.log("View loaded, executing callback.");
        callback();
      }
    });
  }
}
