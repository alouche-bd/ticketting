/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.scss";
import $ from "jquery";

// start the Stimulus application
import "./bootstrap";
import "datatables.net";
import "datatables.net-bs5";

require("@fortawesome/fontawesome-free/css/all.min.css");
require("@fortawesome/fontawesome-free/js/all.js");

global.$ = global.jQuery = $;

$(document).ready(function () {
  $("#ticketstable thead tr")
    .clone(true)
    .addClass("filters no-border")
    .appendTo("#ticketstable thead");

  $("#ticketstable").DataTable({
    orderCellsTop: true,
    fixedHeader: true,
    ordering: true,
    info: false,
    lengthChange: false,
    language: {
      paginate: {
        next: "Suivant",
        previous: "Précédent",
      },
      search: "Rechercher :",
    },
    initComplete: function () {
      var api = this.api();

      // For each column
      api
        .columns()
        .eq(0)
        .each(function (colIdx) {
          if (colIdx !== 8 && colIdx !== 9) {
            // Set the header cell to contain the input element
            var cell = $(".filters th").eq(
              $(api.column(colIdx).header()).index()
            );
            $(cell).html('<input type="text" placeholder="Rechercher" />');

            // On every keypress in this input
            $(
              "input",
              $(".filters th").eq($(api.column(colIdx).header()).index())
            )
              .off("keyup change")
              .on("keyup change", function (e) {
                e.stopPropagation();

                // Get the search value
                $(this).attr("title", $(this).val());
                var regexr = "({search})"; //$(this).parents('th').find('select').val();

                var cursorPosition = this.selectionStart;
                // Search the column for that value
                api
                  .column(colIdx)
                  .search(
                    this.value != ""
                      ? regexr.replace("{search}", "(((" + this.value + ")))")
                      : "",
                    this.value != "",
                    this.value == ""
                  )
                  .draw();

                $(this)
                  .focus()[0]
                  .setSelectionRange(cursorPosition, cursorPosition);
              });
          }
        });
    },
  });
});
