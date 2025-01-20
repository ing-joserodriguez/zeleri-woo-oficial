(function ($) {
  "use strict";

  /**
   * All of the code for your admin-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  $(function () {
    const currentUrl = jQuery(location).attr("href");
    const url = new URL(currentUrl);
    const params = url.searchParams;

    //Valido que estoy dentro de la seccion del plugin de zeleri
    if (
      params.get("section") == "zeleri_pay_payment_gateways" ||
      params.get("section") == "zeleri_pay_payment_gateways_tb"
    ) {
      //Oculto el boton de "Guardar cambios" por defecto y muestro el del formulario de configuracion
      $("#wpbody-content .submit").hide();
      $("#wpbody-content .zeleri-button-submit").show();
    }

    $(".nav-link-zeleri").on("click", function (e) {
      e.preventDefault(); // Previene la navegación predeterminada
      const currentUrl = new URL(window.location.href);
      const hrefValue = $(this).attr("href").slice(1);
      currentUrl.searchParams.delete("tab_pane");
      currentUrl.searchParams.delete("paged");
      currentUrl.searchParams.delete("orderby");
      currentUrl.searchParams.delete("order");

      if (hrefValue != "tabZeleriInicio") {
        currentUrl.searchParams.set("tab_pane", hrefValue); // Agrega o actualiza la variable con el nuevo valor
      }

      window.history.pushState({}, "", currentUrl.href); // Actualiza la URL sin recargar la página
    });

    $(".wp-list-table .manage-column a").on("click", function (e) {
      e.preventDefault(); // Previene la navegación predeterminada
      const hrefValue = $(this).attr("href");
      const urlEnlace = new URL(hrefValue);
      urlEnlace.searchParams.set("tab_pane", "tabZeleriTransacciones");
      window.location.href = urlEnlace.href;
    });

    //Hace que el enlace de la seccion de inicio despliegue la seccion de las configuraciones
    $("#irZeleriConfiguracion").on("click", function () {
      $('.nav-tabs a[href="#tabZeleriConfiguracion"]').trigger("click");
    });
  });
})(jQuery);
