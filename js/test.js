jQuery(function ($) {
  $().ready(function () {
    const urlauth = "https://rto.mendoza.gov.ar/api/v1/auth/login/";
    const turno = "https://rto.mendoza.gov.ar/api/v1/auth/turno/";
    const confirmTurno = "https://rto.mendoza.gov.ar/api/v1/auth/confirmar/";
    const token = localStorage.getItem("token");

    jQuery(function ($) {
      $(document).ready(function () {
        /**
         * TOMA DE QUERY DEVUELTA POR RTO.MENDOZA.GOV.AR PARA
         * EL NRO DE TURNO.
         *  */
        const urlParams = new URLSearchParams(window.location.search);
        const myParam = urlParams.get("nroturno");
        $("#turno").text(myParam);
        if ($("#turno").length == 0) {
          $("#btn-consulta-turno").trigger("click");
        }

        /**
         * TOMA DE DATOS DEL LOCALSTORAGE, PARA COMPLETAR FORMULARIO
         * DE CHECKOUT DE WOOCOMMERCE.
         *  */
        const nombre = localStorage.getItem("nombre");
        const apellido = localStorage.getItem("apellido");
        const email = localStorage.getItem("email");
        const patente = localStorage.getItem("patente");
        const telefono = localStorage.getItem("telefono");
        const turno = localStorage.getItem("turno");
        const token = localStorage.getItem("token");
        $("#billing_first_name").val(nombre);
        $("#billing_last_name").val(apellido);
        $("#billing_company").val(turno);
        $("#billing_address_2").val(patente);
        $("#billing_email").val(email);
        $("#billing_phone").val(telefono);
      });
      $("#login_form").submit(function (event) {
        event.preventDefault();
        login();
      });
      $("#nro_turno_form").submit(function (event) {
        event.preventDefault();
        consulta_turno();
      });
      $("#confirm_individual").submit(function (e) {
        e.preventDefault();
        confirm_individual();
      });
      $("#reservar_turno_form").submit(function (event) {
        event.preventDefault();
        redirect();
      });
      // EVENTOS DEL ARRAY DE TURNOS
      $("#mc-wcb-dates").click(function () {
        $(".mc-wcb-date-picker").toggleClass("visible");
      });
      $("input#mc-wcb-fetch").click(function (e) {
        e.preventDefault();
        var product_id = $("#mc-wcb-product-select").val();

        data_search = {};

        if ("" !== product_id) {
          data_search.product_id = product_id;
        }

        if ($("#mc-wcb-dates").is(":checked")) {
          var date_start = $("#mc_wcv_start_date").val();
          var date_end = $("#mc_wcv_end_date").val();
          if ("" !== date_start) {
            data_search.date_start = date_start;
          }
          if ("" !== date_end) {
            data_search.date_end = date_end;
          }
        }

        rto_get_bookings(data_search);
      });
      $("#confimaEnMasa").click(function (e) {
        e.preventDefault();
        confimEnMasa();
      });
    });

    function redirect() {
      var fLetter = $("#auto_o_moto").val();
      const letra = fLetter.charAt(0);
      const tipo_vehiculo = $("#tipo_de_vehiculo").text();
      const nombre = $("#nombre").text();
      const apellido = $("#apellido").text();
      const email = $("#email").text();
      const patente = $("#patente").text();
      const telefono = $("#telefono").text();
      const turno = $("#nro_turno").text();
      const token = $("#tokenRef").text();
      localStorage.setItem("tipo_vehiculo", tipo_vehiculo);
      localStorage.setItem("nombre", nombre);
      localStorage.setItem("apellido", apellido);
      localStorage.setItem("email", email);
      localStorage.setItem("patente", patente);
      localStorage.setItem("telefono", telefono);
      localStorage.setItem("turno", turno);
      localStorage.setItem("token", token);
      if (letra == "M") {
        window.location.href = "/producto/turnos-moto";
      } else {
        window.location.href = "/producto/auto-camioneta-camion/";
      }
    }
    function login() {
      const data = $("#login_form").serialize();
      localStorage.clear();
      $.ajax({
        type: "POST",
        url: urlauth,
        data: data,
        success: function (res) {
          if (res.access_token) {
            const response = res.access_token;
            localStorage.setItem("token", response);
            window.location.href = "/turnos";
            return response;
          } else {
            console.log("hubo un error");
          }
        },
      });
    }

    function consulta_turno() {
      const data = $("#nro_turno_form").serialize();
      $.ajax({
        type: "POST",
        url: turno,
        data: data,
        headers: { Authorization: `Bearer ${token}` },
        crossDomain: true,
        dataType: "json",
        success: function (res) {
          if (res.status == "success") {
            const response = res.turno;
            console.log(response);
            $("#nombre").html(response.nombre);
            $("#apellido").html(response.apellido);
            $("#email").html(response.email);
            $("#departamento").html(response.departamento);
            $("#telefono").html(response.telefono);
            $("#estado").html(response.estado);
            $("#fecha_creacion").html(response.fecha_creacion);
            $("#nro_turno").html(response.nro_turno);
            $("#tunro_a_confirmar").val(response.nro_turno);
            $("#nro_turno_confirmar").val(response.nro_turno);
            $("#patente").html(response.patente);
            $("#tipo_de_vehiculo").html(response.tipo_de_vehiculo);
            $("#marca").html(response.marca);
            $("#modelo").html(response.modelo);
            $("#anio").html(response.anio);
            $("#combustible").html(response.combustible);
            $("#inscripto_en_mendoza").html(response.inscripto_en_mendoza);
            $("#taller").html(response.taller);
            $("#error").html("");
          }
        },
        statusCode: {
          404: function (res) {
            $("#error").html("numero de turno inexistente");
          },
        },
      });
    }
    function confirm_individual() {
      const data = $("#tunro_a_confirmar").serialize();
      $.ajax({
        type: "POST",
        url: confirmTurno,
        data: data,
        headers: { Authorization: `Bearer ${token}` },
        crossDomain: true,
        dataType: "json",
        success: function (res) {
          if (res.status == "success") {
            const response = res.message;
            alert(response);
            console.log(response);
          } else {
            console.log(res.error);
            console.log("ocurrio un error");
          }
        },
      });
    }
    // FUNCTIONS DEL ARRAY DE TURNOS

    function rto_get_bookings(data_search) {
      var data = {
        action: "rto_find_booking",
        selected_product_id: data_search.product_id,
        date_start: data_search.date_start,
        date_end: data_search.date_end,
        security: mc_wcb_params.security,
      };

      $.get({
        type: "get",
        url: mc_wcb_params.ajax_url,
        dataType: "json",
        data: data,
        contentType: "application/json; charset=utf-8",
        beforeSend: function () {
          $("select#mc-wcb-product-select").prop("disabled", "disabled");
          $(".mc-wcb-loader").fadeIn("slow");
          $(".mc-wcb-export").fadeOut("slow");
          $(".mc-wcb-download").fadeOut("slow");
        },
        success: function (response) {
          //           console.log(response);
          const bookings = response.data;
          let res = $("#response_booking");
          for (let book of bookings) {
            const nombre = book.nombre;
            const apellido = book.apellido;
            const apellidoNombre = nombre + " " + apellido;
            res.append(`
					<tr>
                      <td><input type="checkbox" name="turno" id="checklist" value="${book.turno}"></td>
                      <td>${book.turno}</td>
                      <td>${book.patente}</td>
                      <td>${apellidoNombre}</td>
                      <td>${book.inicio}</td>
                    </tr>
				`);
            //             console.log(book);
          }
          $("select#mc-wcb-product-select").prop("disabled", false);
          $(".mc-wcb-loader").fadeOut("slow");
          if (true === response.success) {
            $(".mc-wcb-export").fadeIn("slow");
          }
        },
        error: function (response) {
          $(".mc-wcb-loader").fadeOut("slow");
          $(".mc-wcb-result")
            .hide()
            .html("<span>" + response.message + "</span>")
            .fadeIn("slow");
        },
      });
    }
    function confimEnMasa() {
      const json = [];
      $("#checklist:checked").each(function () {
        const selected = $(this).val();
        const selectedNumber = parseInt(selected);
        json.push({
          token: selectedNumber,
        });
      });
      json.forEach(function (turno, index) {
        const data = "turno=" + turno.token;
        $.ajax({
          type: "POST",
          url: confirmTurno,
          data: data,
          headers: { Authorization: `Bearer ${token}` },
          crossDomain: true,
          dataType: "json",
          success: function (res) {
            if (res.status == "success") {
              const response = res.message;
              console.log(response);
            } else {
              console.log(res.error);
              console.log("ocurrio un error");
            }
          },
        });
      });
    }
  });
});
