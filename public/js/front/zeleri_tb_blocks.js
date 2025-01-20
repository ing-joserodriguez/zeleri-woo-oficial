!(function (e) {
  var t = {};
  function r(a) {
    if (t[a]) return t[a].exports;
    var n = (t[a] = { i: a, l: !1, exports: {} });
    return e[a].call(n.exports, n, n.exports, r), (n.l = !0), n.exports;
  }
  (r.m = e),
    (r.c = t),
    (r.d = function (e, t, a) {
      r.o(e, t) || Object.defineProperty(e, t, { enumerable: !0, get: a });
    }),
    (r.r = function (e) {
      "undefined" != typeof Symbol &&
        Symbol.toStringTag &&
        Object.defineProperty(e, Symbol.toStringTag, { value: "Module" }),
        Object.defineProperty(e, "__esModule", { value: !0 });
    }),
    (r.t = function (e, t) {
      if ((1 & t && (e = r(e)), 8 & t)) return e;
      if (4 & t && "object" == typeof e && e && e.__esModule) return e;
      var a = Object.create(null);
      if (
        (r.r(a),
        Object.defineProperty(a, "default", { enumerable: !0, value: e }),
        2 & t && "string" != typeof e)
      )
        for (var n in e)
          r.d(
            a,
            n,
            function (t) {
              return e[t];
            }.bind(null, n)
          );
      return a;
    }),
    (r.n = function (e) {
      var t =
        e && e.__esModule
          ? function () {
              return e.default;
            }
          : function () {
              return e;
            };
      return r.d(t, "a", t), t;
    }),
    (r.o = function (e, t) {
      return Object.prototype.hasOwnProperty.call(e, t);
    }),
    (r.p = ""),
    r((r.s = 5));
})([
  function (e, t) {
    e.exports = window.wp.element;
  },
  function (e, t) {
    e.exports = window.wp.htmlEntities;
  },
  function (e, t, r) {
    "use strict";
    r.r(t),
      r.d(t, "noticeHandler", function () {
        return s;
      });
    const a = new URL(window.location.href),
      n = new URLSearchParams(a.search),
      o = n.has("zeleri_status"),
      c = {
        0: {
          message:
            "La tarjeta ha sido inscrita satisfactoriamente. Aún no se realiza ningún cobro. Ahora puedes realizar el pago.",
          type: "success",
        },
        1: {
          message:
            "La inscripción fue cancelada automáticamente por estar inactiva mucho tiempo.",
          type: "error",
        },
        2: {
          message: "No se recibió el token de la inscripción.",
          type: "error",
        },
        3: {
          message:
            "El usuario canceló la inscripción en el formulario de pago.",
          type: "error",
        },
        4: {
          message: "La inscripción no se encuentra en estado inicializada.",
          type: "error",
        },
        5: {
          message: "Ocurrió un error al ejecutar la inscripción.",
          type: "error",
        },
        6: {
          message: "La inscripción de la tarjeta ha sido rechazada.",
          type: "error",
        },
      },
      i = {
        7: { message: "Transacción aprobada", type: "success" },
        8: {
          message:
            "El usuario intentó pagar esta orden nuevamente, cuando esta ya estaba pagada.",
          type: "error",
        },
        9: {
          message: "El usuario intentó pagar una orden con estado inválido.",
          type: "error",
        },
        10: {
          message:
            "La transacción fue cancelada automáticamente por estar inactiva mucho tiempo en el formulario de pago de Webpay. Puede reintentar el pago",
          type: "error",
        },
        11: {
          message:
            "El usuario canceló la transacción en el formulario de pago, pero esta orden ya estaba pagada o en un estado diferente a INICIALIZADO",
          type: "error",
        },
        12: {
          message: "Cancelaste la transacción durante el formulario de Zeleri.",
          type: "error",
        },
        13: { message: "El pago es inválido.", type: "error" },
        14: {
          message: "La transacción no se encuentra en estado inicializada.",
          type: "error",
        },
        15: {
          message: "El commit de la transacción ha sido rechazada en Zeleri",
          type: "error",
        },
        16: {
          message: "Ocurrió un error al ejecutar el commit de la transacción.",
          type: "error",
        },
        17: { message: "Ocurrió un error inesperado.", type: "error" },
      },
      s = (e) => {
        if (o) {
          const t = "transbank_oneclick_mall_rest" == e ? c : i,
            r = n.get("transbank_status");
          if (!t.hasOwnProperty(r)) return;
          const a = t[r].message;
          switch (t[r].type) {
            case "success":
              wp.data
                .dispatch("core/notices")
                .createSuccessNotice(a, { context: "wc/checkout" });
              break;
            case "error":
              wp.data
                .dispatch("core/notices")
                .createErrorNotice(a, { context: "wc/checkout" });
          }
        }
      };
  },
  function (e, t) {
    e.exports = window.wc.wcBlocksRegistry;
  },
  function (e, t) {
    e.exports = window.wc.wcSettings;
  },
  function (e, t, r) {
    "use strict";
    r.r(t);
    var a = r(0),
      n = r(3),
      o = r(1),
      c = r(4),
      i = r(2);
    const s = Object(c.getSetting)("zeleri_pay_payment_gateways_tb_data", {}),
      l = Object(o.decodeEntities)(s.title);
    Object(i.noticeHandler)(s.id);
    const u = () => Object(o.decodeEntities)(s.description),
      d = {
        name: s.id,
        label: Object(a.createElement)(() => {
          const e = Object(o.decodeEntities)(s.title),
            t = s.icon,
            r = Object(a.createElement)("img", { src: t, alt: "zeleri logo" });
          return Object(a.createElement)("div", null, e, r);
        }, null),
        content: Object(a.createElement)(u, null),
        edit: Object(a.createElement)(u, null),
        canMakePayment: () => !0,
        ariaLabel: l,
        supports: { features: s.supports },
      };
    Object(n.registerPaymentMethod)(d);
  },
]);
