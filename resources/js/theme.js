"use strict";

var calendarEl,today,mYear,weekday,mDay,m,d,calendar,allInputs,fixedPlugin,fixedPluginButton,fixedPluginButtonNav,fixedPluginCard,fixedPluginCloseButton,navbar,buttonNavbarFixed,popoverTriggerList,popoverList,tooltipTriggerList,tooltipList;

const initialComponents = function() {
    !(function () {
        var e, t;
        -1 < navigator.platform.indexOf("Win") &&
            (document.getElementsByClassName("main-content")[0] &&
                ((e = document.querySelector(".main-content")),
                new PerfectScrollbar(e)),
            document.getElementsByClassName("sidenav")[0] &&
                ((e = document.querySelector(".sidenav")), new PerfectScrollbar(e)),
            document.getElementsByClassName("navbar-collapse")[0] &&
                ((t = document.querySelector(
                    ".navbar:not(.navbar-expand-lg) .navbar-collapse"
                )),
                new PerfectScrollbar(t)),
            document.getElementsByClassName("fixed-plugin")[0]) &&
            ((t = document.querySelector(".fixed-plugin")),
            new PerfectScrollbar(t));
    })(),
        document.getElementById("navbarBlur") && navbarBlurOnScroll("navbarBlur");

    calendarEl,
    today,
    mYear,
    weekday,
    mDay,
    m,
    d,
    calendar,
    allInputs,
    fixedPlugin,
    fixedPluginButton,
    fixedPluginButtonNav,
    fixedPluginCard,
    fixedPluginCloseButton,
    navbar,
    buttonNavbarFixed,
    popoverTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="popover"]')
    ),
    popoverList = popoverTriggerList.map(function (e) {
        return new bootstrap.Popover(e);
    }),
    tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    ),
    tooltipList = tooltipTriggerList.map(function (e) {
        return new bootstrap.Tooltip(e);
    });

    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    })
};

function focused(e) {
    e.parentElement.classList.contains("input-group") &&
        e.parentElement.classList.add("focused");
}
function defocused(e) {
    e.parentElement.classList.contains("input-group") &&
        e.parentElement.classList.remove("focused");
}
function setAttributes(t, a) {
    Object.keys(a).forEach(function (e) {
        t.setAttribute(e, a[e]);
    });
}
function dropDown(e) {
    if (!document.querySelector(".dropdown-hover")) {
        event.stopPropagation(), event.preventDefault();
        for (
            var t = e.parentElement.parentElement.children, a = 0;
            a < t.length;
            a++
        )
            t[a].lastElementChild != e.parentElement.lastElementChild &&
                t[a].lastElementChild.classList.remove("show");
        e.nextElementSibling.classList.contains("show")
            ? e.nextElementSibling.classList.remove("show")
            : e.nextElementSibling.classList.add("show");
    }
}

function sidebarType(e) {
    for (
        var t = e.parentElement.children,
            a = e.getAttribute("data-class"),
            n = document.querySelector("body"),
            i = document.querySelector("body:not(.dark-version)"),
            n = n.classList.contains("dark-version"),
            l = [],
            s = 0;
        s < t.length;
        s++
    )
        t[s].classList.remove("active"),
            l.push(t[s].getAttribute("data-class"));
    e.classList.contains("active")
        ? e.classList.remove("active")
        : e.classList.add("active");
    for (
        var r, o, c, d = document.querySelector(".sidenav"), s = 0;
        s < l.length;
        s++
    )
        d.classList.remove(l[s]);
    if ((d.classList.add(a), "bg-transparent" == a || "bg-white" == a)) {
        var u = document.querySelectorAll(".sidenav .text-white");
        for (let e = 0; e < u.length; e++)
            u[e].classList.remove("text-white"),
                u[e].classList.add("text-dark");
    } else {
        var m = document.querySelectorAll(".sidenav .text-dark");
        for (let e = 0; e < m.length; e++)
            m[e].classList.add("text-white"),
                m[e].classList.remove("text-dark");
    }
    if ("bg-transparent" == a && n) {
        m = document.querySelectorAll(".navbar-brand .text-dark");
        for (let e = 0; e < m.length; e++)
            m[e].classList.add("text-white"),
                m[e].classList.remove("text-dark");
    }
    ("bg-transparent" != a && "bg-white" != a) || !i
        ? (o = (r = document.querySelector(".navbar-brand-img")).src).includes(
              "logo-ct-dark.png"
          ) && ((c = o.replace("logo-ct-dark", "logo-ct")), (r.src = c))
        : (o = (r = document.querySelector(".navbar-brand-img")).src).includes(
              "logo-ct.png"
          ) && ((c = o.replace("logo-ct", "logo-ct-dark")), (r.src = c)),
        "bg-white" == a &&
            n &&
            (o = (r = document.querySelector(".navbar-brand-img"))
                .src).includes("logo-ct.png") &&
            ((c = o.replace("logo-ct", "logo-ct-dark")), (r.src = c));
}
function navbarFixed(e) {
    var t = [
            "position-sticky",
            "blur",
            "shadow-blur",
            "mt-4",
            "left-auto",
            "top-1",
            "z-index-sticky",
        ],
        a = document.getElementById("navbarBlur");
    e.getAttribute("checked")
        ? (a.classList.remove(...t),
          a.setAttribute("data-scroll", "false"),
          navbarBlurOnScroll("navbarBlur"),
          e.removeAttribute("checked"))
        : (a.classList.add(...t),
          a.setAttribute("data-scroll", "true"),
          navbarBlurOnScroll("navbarBlur"),
          e.setAttribute("checked", "true"));
}
function navbarMinimize(e) {
    var t = document.getElementsByClassName("g-sidenav-show")[0];
    e.getAttribute("checked")
        ? (t.classList.remove("g-sidenav-hidden"),
          t.classList.add("g-sidenav-pinned"),
          e.removeAttribute("checked"))
        : (t.classList.remove("g-sidenav-pinned"),
          t.classList.add("g-sidenav-hidden"),
          e.setAttribute("checked", "true"));
}
function navbarBlurOnScroll(e) {
    const t = document.getElementById(e);
    var a,
        e = !!t && t.getAttribute("data-scroll");
    let n = ["blur", "shadow-blur", "left-auto"],
        i = ["shadow-none"];
    function l() {
        t.classList.add(...n), t.classList.remove(...i), r("blur");
    }
    function s() {
        t.classList.remove(...n), t.classList.add(...i), r("transparent");
    }
    function r(e) {
        var t = document.querySelectorAll(".navbar-main .nav-link"),
            a = document.querySelectorAll(".navbar-main .sidenav-toggler-line");
        "blur" === e
            ? (t.forEach((e) => {
                  e.classList.remove("text-body");
              }),
              a.forEach((e) => {
                  e.classList.add("bg-dark");
              }))
            : "transparent" === e &&
              (t.forEach((e) => {
                  e.classList.add("text-body");
              }),
              a.forEach((e) => {
                  e.classList.remove("bg-dark");
              }));
    }
    (window.onscroll = debounce(
        "true" == e
            ? function () {
                  (5 < window.scrollY ? l : s)();
              }
            : function () {
                  s();
              },
        10
    )),
        -1 < navigator.platform.indexOf("Win") &&
            ((a = document.querySelector(".main-content")),
            "true" == e
                ? a.addEventListener(
                      "ps-scroll-y",
                      debounce(function () {
                          (5 < a.scrollTop ? l : s)();
                      }, 10)
                  )
                : a.addEventListener(
                      "ps-scroll-y",
                      debounce(function () {
                          s();
                      }, 10)
                  ));
}
function debounce(n, i, l) {
    var s;
    return function () {
        var e = this,
            t = arguments,
            a = l && !s;
        clearTimeout(s),
            (s = setTimeout(function () {
                (s = null), l || n.apply(e, t);
            }, i)),
            a && n.apply(e, t);
    };
}


const initForSetting = function() {
    document.addEventListener("DOMContentLoaded", function () {
        [].slice.call(document.querySelectorAll(".toast")).map(function (e) {
            return new bootstrap.Toast(e);
        });
        [].slice.call(document.querySelectorAll(".toast-btn")).map(function (t) {
            t.addEventListener("click", function () {
                var e = document.getElementById(t.dataset.target);
                e && bootstrap.Toast.getInstance(e).show();
            });
        });
    }),
        document.querySelector('[data-toggle="widget-calendar"]') &&
            ((calendarEl = document.querySelector(
                '[data-toggle="widget-calendar"]'
            )),
            (mYear = (today = new Date()).getFullYear()),
            (mDay = (weekday = [
                "Sunday",
                "Monday",
                "Tuesday",
                "Wednesday",
                "Thursday",
                "Friday",
                "Saturday",
            ])[today.getDay()]),
            (m = today.getMonth()),
            (d = today.getDate()),
            (document.getElementsByClassName("widget-calendar-year")[0].innerHTML =
                mYear),
            (document.getElementsByClassName("widget-calendar-day")[0].innerHTML =
                mDay),
            (calendar = new FullCalendar.Calendar(calendarEl, {
                contentHeight: "auto",
                initialView: "dayGridMonth",
                selectable: !0,
                initialDate: "2020-12-01",
                editable: !0,
                headerToolbar: !1,
                events: [
                    {
                        title: "Call with Dave",
                        start: "2020-11-18",
                        end: "2020-11-18",
                        className: "bg-gradient-danger",
                    },
                    {
                        title: "Lunch meeting",
                        start: "2020-11-21",
                        end: "2020-11-22",
                        className: "bg-gradient-warning",
                    },
                    {
                        title: "All day conference",
                        start: "2020-11-29",
                        end: "2020-11-29",
                        className: "bg-gradient-success",
                    },
                    {
                        title: "Meeting with Mary",
                        start: "2020-12-01",
                        end: "2020-12-01",
                        className: "bg-gradient-info",
                    },
                    {
                        title: "Winter Hackaton",
                        start: "2020-12-03",
                        end: "2020-12-03",
                        className: "bg-gradient-danger",
                    },
                    {
                        title: "Digital event",
                        start: "2020-12-07",
                        end: "2020-12-09",
                        className: "bg-gradient-warning",
                    },
                    {
                        title: "Marketing event",
                        start: "2020-12-10",
                        end: "2020-12-10",
                        className: "bg-gradient-primary",
                    },
                    {
                        title: "Dinner with Family",
                        start: "2020-12-19",
                        end: "2020-12-19",
                        className: "bg-gradient-danger",
                    },
                    {
                        title: "Black Friday",
                        start: "2020-12-23",
                        end: "2020-12-23",
                        className: "bg-gradient-info",
                    },
                    {
                        title: "Cyber Week",
                        start: "2020-12-02",
                        end: "2020-12-02",
                        className: "bg-gradient-warning",
                    },
                ],
            })).render()),
        document.querySelector(".fixed-plugin") &&
            ((fixedPlugin = document.querySelector(".fixed-plugin")),
            (fixedPluginButton = document.querySelector(".fixed-plugin-button")),
            (fixedPluginButtonNav = document.querySelector(
                ".fixed-plugin-button-nav"
            )),
            (fixedPluginCard = document.querySelector(".fixed-plugin .card")),
            (fixedPluginCloseButton = document.querySelectorAll(
                ".fixed-plugin-close-button"
            )),
            (navbar = document.getElementById("navbarBlur")),
            (buttonNavbarFixed = document.getElementById("navbarFixed")),
            fixedPluginButton &&
                (fixedPluginButton.onclick = function () {
                    fixedPlugin.classList.contains("show")
                        ? fixedPlugin.classList.remove("show")
                        : fixedPlugin.classList.add("show");
                }),
            fixedPluginButtonNav &&
                (fixedPluginButtonNav.onclick = function () {
                    fixedPlugin.classList.contains("show")
                        ? fixedPlugin.classList.remove("show")
                        : fixedPlugin.classList.add("show");
                }),
            fixedPluginCloseButton.forEach(function (e) {
                e.onclick = function () {
                    fixedPlugin.classList.remove("show");
                };
            }),
            (document.querySelector("body").onclick = function (e) {
                e.target != fixedPluginButton &&
                    e.target != fixedPluginButtonNav &&
                    e.target.closest(".fixed-plugin .card") != fixedPluginCard &&
                    fixedPlugin.classList.remove("show");
            }),
            navbar) &&
            "true" == navbar.getAttribute("data-scroll") &&
            buttonNavbarFixed &&
            buttonNavbarFixed.setAttribute("checked", "true");
};

var sidenavToggler,sidenavShow,toggleNavbarMinimize,total;

let iconNavbarSidenav, iconSidenav, sidenav;
let body, className;

const sidenavbarToogleVariables = function() {
    sidenavToggler,sidenavShow,toggleNavbarMinimize,total = document.querySelectorAll(".nav-pills");
};

function initNavs() {
    total.forEach(function (l, e) {
        var s = document.createElement("div"),
            t = l.querySelector(".nav-link.active").cloneNode(),
            a =
                ((t.innerHTML = "-"),
                s.classList.add("moving-tab", "position-absolute", "nav-link"),
                s.appendChild(t),
                l.appendChild(s),
                l.getElementsByTagName("li").length,
                (s.style.padding = "0px"),
                (s.style.transition = ".5s ease"),
                l.querySelector(".nav-link.active").parentElement);
        if (a) {
            var n = Array.from(a.closest("ul").children),
                t = n.indexOf(a) + 1;
            let e = 0;
            if (l.classList.contains("flex-column")) {
                for (var i = 1; i <= n.indexOf(a); i++)
                    e += l.querySelector(
                        "li:nth-child(" + i + ")"
                    ).offsetHeight;
                (s.style.transform = "translate3d(0px," + e + "px, 0px)"),
                    (s.style.width =
                        l.querySelector("li:nth-child(" + t + ")").offsetWidth +
                        "px"),
                    (s.style.height = l.querySelector(
                        "li:nth-child(" + i + ")"
                    ).offsetHeight);
            } else {
                for (i = 1; i <= n.indexOf(a); i++)
                    e += l.querySelector("li:nth-child(" + i + ")").offsetWidth;
                (s.style.transform = "translate3d(" + e + "px, 0px, 0px)"),
                    (s.style.width =
                        l.querySelector("li:nth-child(" + t + ")").offsetWidth +
                        "px");
            }
        }
        l.onmouseover = function (e) {
            let i = getEventTarget(e).closest("li");
            if (i) {
                let a = Array.from(i.closest("ul").children),
                    n = a.indexOf(i) + 1;
                l.querySelector("li:nth-child(" + n + ") .nav-link").onclick =
                    function () {
                        s = l.querySelector(".moving-tab");
                        let e = 0;
                        if (l.classList.contains("flex-column")) {
                            for (var t = 1; t <= a.indexOf(i); t++)
                                e += l.querySelector(
                                    "li:nth-child(" + t + ")"
                                ).offsetHeight;
                            (s.style.transform =
                                "translate3d(0px," + e + "px, 0px)"),
                                (s.style.height = l.querySelector(
                                    "li:nth-child(" + t + ")"
                                ).offsetHeight);
                        } else {
                            for (t = 1; t <= a.indexOf(i); t++)
                                e += l.querySelector(
                                    "li:nth-child(" + t + ")"
                                ).offsetWidth;
                            (s.style.transform =
                                "translate3d(" + e + "px, 0px, 0px)"),
                                (s.style.width =
                                    l.querySelector("li:nth-child(" + n + ")")
                                        .offsetWidth + "px");
                        }
                    };
            }
        };
    });
}
function getEventTarget(e) {
    return (e = e || window.event).target || e.srcElement;
}

const initSidebarToggle = function () {
    setTimeout(function () {
        initNavs();
    }, 100),
        window.addEventListener("resize", function (e) {
            total.forEach(function (t, e) {
                t.querySelector(".moving-tab").remove();
                var a = document.createElement("div"),
                    n = t.querySelector(".nav-link.active").cloneNode(),
                    i =
                        ((n.innerHTML = "-"),
                        a.classList.add(
                            "moving-tab",
                            "position-absolute",
                            "nav-link"
                        ),
                        a.appendChild(n),
                        t.appendChild(a),
                        (a.style.padding = "0px"),
                        (a.style.transition = ".5s ease"),
                        t.querySelector(".nav-link.active").parentElement);
                if (i) {
                    var l = Array.from(i.closest("ul").children),
                        n = l.indexOf(i) + 1;
                    let e = 0;
                    if (t.classList.contains("flex-column")) {
                        for (var s = 1; s <= l.indexOf(i); s++)
                            e += t.querySelector(
                                "li:nth-child(" + s + ")"
                            ).offsetHeight;
                        (a.style.transform = "translate3d(0px," + e + "px, 0px)"),
                            (a.style.width =
                                t.querySelector("li:nth-child(" + n + ")")
                                    .offsetWidth + "px"),
                            (a.style.height = t.querySelector(
                                "li:nth-child(" + s + ")"
                            ).offsetHeight);
                    } else {
                        for (s = 1; s <= l.indexOf(i); s++)
                            e += t.querySelector(
                                "li:nth-child(" + s + ")"
                            ).offsetWidth;
                        (a.style.transform = "translate3d(" + e + "px, 0px, 0px)"),
                            (a.style.width =
                                t.querySelector("li:nth-child(" + n + ")")
                                    .offsetWidth + "px");
                    }
                }
            }),
                window.innerWidth < 991
                    ? total.forEach(function (t, e) {
                          if (!t.classList.contains("flex-column")) {
                              t.classList.remove("flex-row"),
                                  t.classList.add("flex-column", "on-resize");
                              var a =
                                      t.querySelector(
                                          ".nav-link.active"
                                      ).parentElement,
                                  n = Array.from(a.closest("ul").children);
                              n.indexOf(a);
                              let e = 0;
                              for (var i = 1; i <= n.indexOf(a); i++)
                                  e += t.querySelector(
                                      "li:nth-child(" + i + ")"
                                  ).offsetHeight;
                              var l = document.querySelector(".moving-tab");
                              (l.style.width =
                                  t.querySelector("li:nth-child(1)").offsetWidth +
                                  "px"),
                                  (l.style.transform =
                                      "translate3d(0px," + e + "px, 0px)");
                          }
                      })
                    : total.forEach(function (t, e) {
                          if (t.classList.contains("on-resize")) {
                              t.classList.remove("flex-column", "on-resize"),
                                  t.classList.add("flex-row");
                              var a =
                                      t.querySelector(
                                          ".nav-link.active"
                                      ).parentElement,
                                  n = Array.from(a.closest("ul").children),
                                  i = n.indexOf(a) + 1;
                              let e = 0;
                              for (var l = 1; l <= n.indexOf(a); l++)
                                  e += t.querySelector(
                                      "li:nth-child(" + l + ")"
                                  ).offsetWidth;
                              var s = document.querySelector(".moving-tab");
                              (s.style.transform =
                                  "translate3d(" + e + "px, 0px, 0px)"),
                                  (s.style.width =
                                      t.querySelector("li:nth-child(" + i + ")")
                                          .offsetWidth + "px");
                          }
                      });
        }),
        window.innerWidth < 991 &&
            total.forEach(function (e, t) {
                e.classList.contains("flex-row") &&
                    (e.classList.remove("flex-row"),
                    e.classList.add("flex-column", "on-resize"));
            }),
        document.querySelector(".sidenav-toggler") &&
            ((sidenavToggler =
                document.getElementsByClassName("sidenav-toggler")[0]),
            (sidenavShow = document.getElementsByClassName("g-sidenav-show")[0]),
            (toggleNavbarMinimize = document.getElementById("navbarMinimize")),
            sidenavShow) &&
            (sidenavToggler.onclick = function () {
                sidenavShow.classList.contains("g-sidenav-hidden")
                    ? (sidenavShow.classList.remove("g-sidenav-hidden"),
                      sidenavShow.classList.add("g-sidenav-pinned"),
                      toggleNavbarMinimize &&
                          (toggleNavbarMinimize.click(),
                          toggleNavbarMinimize.removeAttribute("checked")))
                    : (sidenavShow.classList.remove("g-sidenav-pinned"),
                      sidenavShow.classList.add("g-sidenav-hidden"),
                      toggleNavbarMinimize &&
                          (toggleNavbarMinimize.click(),
                          toggleNavbarMinimize.setAttribute("checked", "true")));
            });
    iconNavbarSidenav = document.getElementById("iconNavbarSidenav"),
        iconSidenav = document.getElementById("iconSidenav"),
        sidenav = document.getElementById("sidenav-main");

    body = document.getElementsByTagName("body")[0],
        className = "g-sidenav-pinned";

        iconNavbarSidenav && iconNavbarSidenav.addEventListener("click", toggleSidenav),
    iconSidenav && iconSidenav.addEventListener("click", toggleSidenav);
};

function toggleSidenav() {
    body.classList.contains(className)
        ? (body.classList.remove(className),
          setTimeout(function () {
              sidenav.classList.remove("bg-white");
          }, 100),
          sidenav.classList.remove("bg-transparent"))
        : (body.classList.add(className),
          sidenav.classList.remove("bg-transparent"),
          iconSidenav.classList.remove("d-none"));
}

let referenceButtons = '';

const navbarOnColorResided = function () {
    referenceButtons = document.querySelector("[data-class]");
};

function navbarColorOnResize() {
    sidenav &&
        (1200 < window.innerWidth
            ? referenceButtons.classList.contains("active") &&
              "bg-transparent" === referenceButtons.getAttribute("data-class")
                ? sidenav.classList.remove("bg-white")
                : document
                      .querySelector("body")
                      .classList.contains("dark-version") ||
                  sidenav.classList.add("bg-white")
            : sidenav.classList.remove("bg-transparent"));
}

function sidenavTypeOnResize() {
    var e = document.querySelectorAll('[onclick="sidebarType(this)"]');
    window.innerWidth < 1200
        ? e.forEach(function (e) {
              e.classList.add("disabled");
          })
        : e.forEach(function (e) {
              e.classList.remove("disabled");
          });
}

function notify(e) {
    var t = document.querySelector("body"),
        a = document.createElement("div");
    a.classList.add(
        "alert",
        "position-absolute",
        "top-0",
        "border-0",
        "text-white",
        "w-50",
        "end-0",
        "start-0",
        "mt-2",
        "mx-auto",
        "py-2"
    ),
        a.classList.add("alert-" + e.getAttribute("data-type")),
        (a.style.transform = "translate3d(0px, 0px, 0px)"),
        (a.style.opacity = "0"),
        (a.style.transition = ".35s ease"),
        (a.style.zIndex = "9999"),
        setTimeout(function () {
            (a.style.transform = "translate3d(0px, 20px, 0px)"),
                a.style.setProperty("opacity", "1", "important");
        }, 100),
        (a.innerHTML =
            '<div class="d-flex mb-1"><div class="alert-icon me-1"><i class="' +
            e.getAttribute("data-icon") +
            ' mt-1"></i></div><span class="alert-text"><strong>' +
            e.getAttribute("data-title") +
            '</strong></span></div><span class="text-sm">' +
            e.getAttribute("data-content") +
            "</span>"),
        t.appendChild(a),
        setTimeout(function () {
            (a.style.transform = "translate3d(0px, 0px, 0px)"),
                a.style.setProperty("opacity", "0", "important");
        }, 4e3),
        setTimeout(function () {
            e.parentElement.querySelector(".alert").remove();
        }, 4500);
}
function darkMode(e) {
    var t = document.getElementsByTagName("body")[0],
        a = document.querySelectorAll("div:not(.sidenav) > hr"),
        n = document.querySelectorAll("div:not(.bg-gradient-dark) hr"),
        i = document.querySelectorAll("button:not(.btn) > .text-dark"),
        l = document.querySelectorAll("span.text-dark, .breadcrumb .text-dark"),
        s = document.querySelectorAll(
            "span.text-white, .breadcrumb .text-white"
        ),
        r = document.querySelectorAll("strong.text-dark"),
        o = document.querySelectorAll("strong.text-white"),
        c = document.querySelectorAll("a.nav-link.text-dark"),
        d = document.querySelectorAll("a.nav-link.text-white"),
        u = document.querySelectorAll(".text-secondary"),
        m = document.querySelectorAll(".bg-gray-100"),
        g = document.querySelectorAll(".bg-gray-600"),
        f = document.querySelectorAll(
            ".btn.btn-link.text-dark, .material-icons.text-dark"
        ),
        h = document.querySelectorAll(
            ".btn.btn-link.text-white, .material-icons.text-white"
        ),
        v = document.querySelectorAll(".card.border"),
        p = document.querySelectorAll(".card.border.border-dark"),
        y = document.querySelectorAll(".main-content .container-fluid .card"),
        b = document.querySelectorAll("g");
    if (e.getAttribute("checked")) {
        t.classList.remove("dark-version");
        for (w = 0; w < a.length; w++)
            a[w].classList.contains("light") &&
                (a[w].classList.add("dark"), a[w].classList.remove("light"));
        for (w = 0; w < n.length; w++)
            n[w].classList.contains("light") &&
                (n[w].classList.add("dark"), n[w].classList.remove("light"));
        for (w = 0; w < y.length; w++)
            y[w].classList.add("blur", "shadow-blur");
        for (w = 0; w < i.length; w++)
            i[w].classList.contains("text-white") &&
                (i[w].classList.remove("text-white"),
                i[w].classList.add("text-dark"));
        for (w = 0; w < s.length; w++)
            !s[w].classList.contains("text-white") ||
                s[w].closest(".sidenav") ||
                s[w].closest(".card.bg-gradient-dark") ||
                (s[w].classList.remove("text-white"),
                s[w].classList.add("text-dark"));
        for (w = 0; w < o.length; w++)
            o[w].classList.contains("text-white") &&
                (o[w].classList.remove("text-white"),
                o[w].classList.add("text-dark"));
        for (w = 0; w < d.length; w++)
            d[w].classList.contains("text-white") &&
                !d[w].closest(".sidenav") &&
                (d[w].classList.remove("text-white"),
                d[w].classList.add("text-dark"));
        for (w = 0; w < u.length; w++)
            u[w].classList.contains("text-white") &&
                (u[w].classList.remove("text-white"),
                u[w].classList.remove("opacity-8"),
                u[w].classList.add("text-dark"));
        for (w = 0; w < g.length; w++)
            g[w].classList.contains("bg-gray-600") &&
                (g[w].classList.remove("bg-gray-600"),
                g[w].classList.add("bg-gray-100"));
        for (w = 0; w < b.length; w++)
            b[w].hasAttribute("fill") && b[w].setAttribute("fill", "#252f40");
        for (w = 0; w < h.length; w++)
            h[w].closest(".card.bg-gradient-dark") ||
                (h[w].classList.remove("text-white"),
                h[w].classList.add("text-dark"));
        for (w = 0; w < p.length; w++) p[w].classList.remove("border-dark");
        e.removeAttribute("checked");
    } else {
        t.classList.add("dark-version");
        for (var w = 0; w < a.length; w++)
            a[w].classList.contains("dark") &&
                (a[w].classList.remove("dark"), a[w].classList.add("light"));
        for (var w = 0; w < y.length; w++)
            y[w].classList.contains("blur") &&
                y[w].classList.remove("blur", "shadow-blur");
        for (var w = 0; w < n.length; w++)
            n[w].classList.contains("dark") &&
                (n[w].classList.remove("dark"), n[w].classList.add("light"));
        for (var w = 0; w < i.length; w++)
            i[w].classList.contains("text-dark") &&
                (i[w].classList.remove("text-dark"),
                i[w].classList.add("text-white"));
        for (var w = 0; w < l.length; w++)
            l[w].classList.contains("text-dark") &&
                (l[w].classList.remove("text-dark"),
                l[w].classList.add("text-white"));
        for (var w = 0; w < r.length; w++)
            r[w].classList.contains("text-dark") &&
                (r[w].classList.remove("text-dark"),
                r[w].classList.add("text-white"));
        for (var w = 0; w < c.length; w++)
            c[w].classList.contains("text-dark") &&
                (c[w].classList.remove("text-dark"),
                c[w].classList.add("text-white"));
        for (var w = 0; w < u.length; w++)
            u[w].classList.contains("text-secondary") &&
                (u[w].classList.remove("text-secondary"),
                u[w].classList.add("text-white"),
                u[w].classList.add("opacity-8"));
        for (var w = 0; w < m.length; w++)
            m[w].classList.contains("bg-gray-100") &&
                (m[w].classList.remove("bg-gray-100"),
                m[w].classList.add("bg-gray-600"));
        for (var w = 0; w < f.length; w++)
            f[w].classList.remove("text-dark"),
                f[w].classList.add("text-white");
        for (var w = 0; w < b.length; w++)
            b[w].hasAttribute("fill") && b[w].setAttribute("fill", "#fff");
        for (var w = 0; w < v.length; w++) v[w].classList.add("border-dark");
        e.setAttribute("checked", "true");
    }
}
window.addEventListener("resize", navbarColorOnResize),
    window.addEventListener("resize", sidenavTypeOnResize),
    window.addEventListener("load", sidenavTypeOnResize),
    (window.onload = function () {
        for (
            var e = document.querySelectorAll("input"), t = 0;
            t < e.length;
            t++
        )
            e[t].hasAttribute("value") &&
                e[t].parentElement.classList.add("is-filled"),
                e[t].addEventListener(
                    "focus",
                    function (e) {
                        this.parentElement.classList.add("is-focused");
                    },
                    !1
                ),
                (e[t].onkeyup = function (e) {
                    "" != this.value
                        ? this.parentElement.classList.add("is-filled")
                        : this.parentElement.classList.remove("is-filled");
                }),
                e[t].addEventListener(
                    "focusout",
                    function (e) {
                        "" != this.value &&
                            this.parentElement.classList.add("is-filled"),
                            this.parentElement.classList.remove("is-focused");
                    },
                    !1
                );
        for (
            var a = document.querySelectorAll(".btn"), t = 0;
            t < a.length;
            t++
        )
            a[t].addEventListener(
                "click",
                function (e) {
                    var t = e.target,
                        a = t.querySelector(".ripple");
                    (a = document.createElement("span")).classList.add(
                        "ripple"
                    ),
                        (a.style.width = a.style.height =
                            Math.max(t.offsetWidth, t.offsetHeight) + "px"),
                        t.appendChild(a),
                        (a.style.left = e.offsetX - a.offsetWidth / 2 + "px"),
                        (a.style.top = e.offsetY - a.offsetHeight / 2 + "px"),
                        a.classList.add("ripple"),
                        setTimeout(function () {
                            if(a.parentElement) {
                                a.parentElement.removeChild(a);
                            }
                        }, 600);
                },
                !1
            );
    }),
    document.querySelector(".datepicker") &&
        flatpickr(".datepicker", { mode: "range" });
const form = document.getElementById("form"),
    username = document.getElementById("username"),
    email = document.getElementById("email"),
    password = document.getElementById("password"),
    password2 = document.getElementById("confirm_password");
function showError(e, t) {
    e = e.parentElement;
    (e.className = "input-group input-group-outline my-5 is-invalid is-filled"),
        (e.querySelector("small").innerText = t);
}
function showSucces(e) {
    e.parentElement.className =
        "input-group input-group-outline my-5 is-valid is-filled";
}
function checkEmail(e) {
    /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
        e.value.trim()
    )
        ? showSucces(e)
        : showError(e, "Email is not invalid");
}
function checkRequired(e) {
    e.forEach(function (e) {
        "" === e.value.trim()
            ? showError(e, getFieldName(e) + " is required")
            : showSucces(e);
    });
}
function checkLength(e, t, a) {
    e.value.length < t
        ? showError(e, getFieldName(e) + ` must be at least ${t} characters`)
        : e.value.length > a
        ? showError(e, getFieldName(e) + ` must be les than ${a} characters`)
        : showSucces(e);
}
function getFieldName(e) {
    return e.id.charAt(0).toUpperCase() + e.id.slice(1);
}
function checkPasswordMatch(e, t) {
    e.value !== t.value && showError(t, "Passwords do not match");
}
var material = {
    initFullCalendar: function () {
        document.addEventListener("DOMContentLoaded", function () {
            var e = document.getElementById("fullCalendar"),
                t = new Date(),
                a = t.getFullYear(),
                n = t.getMonth(),
                t = t.getDate(),
                i = new FullCalendar.Calendar(e, {
                    initialView: "dayGridMonth",
                    selectable: !0,
                    headerToolbar: {
                        left: "title",
                        center: "dayGridMonth,timeGridWeek,timeGridDay",
                        right: "prev,next today",
                    },
                    select: function (a) {
                        Swal.fire({
                            title: "Create an Event",
                            html: '<div class="form-group"><input class="form-control text-default" placeholder="Event Title" id="input-field"></div>',
                            showCancelButton: !0,
                            customClass: {
                                confirmButton: "btn btn-primary",
                                cancelButton: "btn btn-danger",
                            },
                            buttonsStyling: !1,
                        }).then(function (e) {
                            var t =
                                document.getElementById("input-field").value;
                            t &&
                                ((t = {
                                    title: t,
                                    start: a.startStr,
                                    end: a.endStr,
                                }),
                                i.addEvent(t));
                        });
                    },
                    editable: !0,
                    events: [
                        {
                            title: "All Day Event",
                            start: new Date(a, n, 1),
                            className: "event-default",
                        },
                        {
                            id: 999,
                            title: "Repeating Event",
                            start: new Date(a, n, t - 4, 6, 0),
                            allDay: !1,
                            className: "event-rose",
                        },
                        {
                            id: 999,
                            title: "Repeating Event",
                            start: new Date(a, n, t + 3, 6, 0),
                            allDay: !1,
                            className: "event-rose",
                        },
                        {
                            title: "Meeting",
                            start: new Date(a, n, t - 1, 10, 30),
                            allDay: !1,
                            className: "event-green",
                        },
                        {
                            title: "Lunch",
                            start: new Date(a, n, t + 7, 12, 0),
                            end: new Date(a, n, t + 7, 14, 0),
                            allDay: !1,
                            className: "event-red",
                        },
                        {
                            title: "Md-pro Launch",
                            start: new Date(a, n, t - 2, 12, 0),
                            allDay: !0,
                            className: "event-azure",
                        },
                        {
                            title: "Birthday Party",
                            start: new Date(a, n, t + 1, 19, 0),
                            end: new Date(a, n, t + 1, 22, 30),
                            allDay: !1,
                            className: "event-azure",
                        },
                        {
                            title: "Click for Creative Tim",
                            start: new Date(a, n, 21),
                            end: new Date(a, n, 22),
                            url: "http://www.creative-tim.com/",
                            className: "event-orange",
                        },
                        {
                            title: "Click for Google",
                            start: new Date(a, n, 23),
                            end: new Date(a, n, 23),
                            url: "http://www.creative-tim.com/",
                            className: "event-orange",
                        },
                    ],
                });
            i.render();
        });
    },
    datatableSimple: function () {
        var t = {
            columnDefs: [
                { field: "athlete", minWidth: 150, sortable: !0, filter: !0 },
                { field: "age", maxWidth: 90, sortable: !0, filter: !0 },
                { field: "country", minWidth: 150, sortable: !0, filter: !0 },
                { field: "year", maxWidth: 90, sortable: !0, filter: !0 },
                { field: "date", minWidth: 150, sortable: !0, filter: !0 },
                { field: "sport", minWidth: 150, sortable: !0, filter: !0 },
                { field: "gold" },
                { field: "silver" },
                { field: "bronze" },
                { field: "total" },
            ],
            rowSelection: "multiple",
            rowMultiSelectWithClick: !0,
            rowData: [
                {
                    athlete: "Ronald Valencia",
                    age: 23,
                    country: "United States",
                    year: 2008,
                    date: "24/08/2008",
                    sport: "Swimming",
                    gold: 8,
                    silver: 0,
                    bronze: 0,
                    total: 8,
                },
                {
                    athlete: "Lorand Frentz",
                    age: 19,
                    country: "United States",
                    year: 2004,
                    date: "29/08/2004",
                    sport: "Swimming",
                    gold: 6,
                    silver: 0,
                    bronze: 2,
                    total: 8,
                },
                {
                    athlete: "Michael Phelps",
                    age: 27,
                    country: "United States",
                    year: 2012,
                    date: "12/08/2012",
                    sport: "Swimming",
                    gold: 4,
                    silver: 2,
                    bronze: 0,
                    total: 6,
                },
                {
                    athlete: "Natalie Coughlin",
                    age: 25,
                    country: "United States",
                    year: 2008,
                    date: "24/08/2008",
                    sport: "Swimming",
                    gold: 1,
                    silver: 2,
                    bronze: 3,
                    total: 6,
                },
                {
                    athlete: "Aleksey Nemov",
                    age: 24,
                    country: "Russia",
                    year: 2e3,
                    date: "01/10/2000",
                    sport: "Gymnastics",
                    gold: 2,
                    silver: 1,
                    bronze: 3,
                    total: 6,
                },
                {
                    athlete: "Alicia Coutts",
                    age: 24,
                    country: "Australia",
                    year: 2012,
                    date: "12/08/2012",
                    sport: "Swimming",
                    gold: 1,
                    silver: 3,
                    bronze: 1,
                    total: 5,
                },
                {
                    athlete: "Missy Franklin",
                    age: 17,
                    country: "United States",
                    year: 2012,
                    date: "12/08/2012",
                    sport: "Swimming",
                    gold: 4,
                    silver: 0,
                    bronze: 1,
                    total: 5,
                },
                {
                    athlete: "Ryan Lochte",
                    age: 27,
                    country: "United States",
                    year: 2012,
                    date: "12/08/2012",
                    sport: "Swimming",
                    gold: 2,
                    silver: 2,
                    bronze: 1,
                    total: 5,
                },
                {
                    athlete: "Allison Schmitt",
                    age: 22,
                    country: "United States",
                    year: 2012,
                    date: "12/08/2012",
                    sport: "Swimming",
                    gold: 3,
                    silver: 1,
                    bronze: 1,
                    total: 5,
                },
                {
                    athlete: "Natalie Coughlin",
                    age: 21,
                    country: "United States",
                    year: 2004,
                    date: "29/08/2004",
                    sport: "Swimming",
                    gold: 2,
                    silver: 2,
                    bronze: 1,
                    total: 5,
                },
                {
                    athlete: "Ian Thorpe",
                    age: 17,
                    country: "Australia",
                    year: 2e3,
                    date: "01/10/2000",
                    sport: "Swimming",
                    gold: 3,
                    silver: 2,
                    bronze: 0,
                    total: 5,
                },
                {
                    athlete: "Dara Torres",
                    age: 33,
                    country: "United States",
                    year: 2e3,
                    date: "01/10/2000",
                    sport: "Swimming",
                    gold: 2,
                    silver: 0,
                    bronze: 3,
                    total: 5,
                },
                {
                    athlete: "Cindy Klassen",
                    age: 26,
                    country: "Canada",
                    year: 2006,
                    date: "26/02/2006",
                    sport: "Speed Skating",
                    gold: 1,
                    silver: 2,
                    bronze: 2,
                    total: 5,
                },
                {
                    athlete: "Nastia Liukin",
                    age: 18,
                    country: "United States",
                    year: 2008,
                    date: "24/08/2008",
                    sport: "Gymnastics",
                    gold: 1,
                    silver: 3,
                    bronze: 1,
                    total: 5,
                },
                {
                    athlete: "Marit Bjørgen",
                    age: 29,
                    country: "Norway",
                    year: 2010,
                    date: "28/02/2010",
                    sport: "Cross Country Skiing",
                    gold: 3,
                    silver: 1,
                    bronze: 1,
                    total: 5,
                },
                {
                    athlete: "Sun Yang",
                    age: 20,
                    country: "China",
                    year: 2012,
                    date: "12/08/2012",
                    sport: "Swimming",
                    gold: 2,
                    silver: 1,
                    bronze: 1,
                    total: 4,
                },
            ],
        };
        document.addEventListener("DOMContentLoaded", function () {
            var e = document.querySelector("#datatableSimple");
            new agGrid.Grid(e, t);
        });
    },
    initVectorMap: function () {
        am4core.ready(function () {
            am4core.useTheme(am4themes_animated);
            var e = am4core.create("chartdiv", am4maps.MapChart),
                t =
                    ((e.geodata = am4geodata_worldLow),
                    (e.projection = new am4maps.projections.Miller()),
                    e.series.push(new am4maps.MapPolygonSeries())),
                t =
                    ((t.exclude = ["AQ"]),
                    (t.useGeodata = !0),
                    t.mapPolygons.template);
            (t.tooltipText = "{name}"), (t.polygon.fillOpacity = 0.6);
            t.states.create("hover").properties.fill = e.colors.getIndex(0);
            (t = e.series.push(new am4maps.MapImageSeries())),
                (t.mapImages.template.propertyFields.longitude = "longitude"),
                (t.mapImages.template.propertyFields.latitude = "latitude"),
                (t.mapImages.template.tooltipText = "{title}"),
                (t.mapImages.template.propertyFields.url = "url"),
                (e = t.mapImages.template.createChild(am4core.Circle)),
                (e.radius = 3),
                (e.propertyFields.fill = "color"),
                (e = t.mapImages.template.createChild(am4core.Circle));
            (e.radius = 3),
                (e.propertyFields.fill = "color"),
                e.events.on("inited", function (e) {
                    !(function t(e) {
                        e = e.animate(
                            [
                                { property: "scale", from: 1, to: 5 },
                                { property: "opacity", from: 1, to: 0 },
                            ],
                            1e3,
                            am4core.ease.circleOut
                        );
                        e.events.on("animationended", function (e) {
                            t(e.target.object);
                        });
                    })(e.target);
                });
            e = new am4core.ColorSet();
            t.data = [
                {
                    title: "Brussels",
                    latitude: 50.8371,
                    longitude: 4.3676,
                    color: e.next(),
                },
                {
                    title: "Copenhagen",
                    latitude: 55.6763,
                    longitude: 12.5681,
                    color: e.next(),
                },
                {
                    title: "Paris",
                    latitude: 48.8567,
                    longitude: 2.351,
                    color: e.next(),
                },
                {
                    title: "Reykjavik",
                    latitude: 64.1353,
                    longitude: -21.8952,
                    color: e.next(),
                },
                {
                    title: "Moscow",
                    latitude: 55.7558,
                    longitude: 37.6176,
                    color: e.next(),
                },
                {
                    title: "Madrid",
                    latitude: 40.4167,
                    longitude: -3.7033,
                    color: e.next(),
                },
                {
                    title: "London",
                    latitude: 51.5002,
                    longitude: -0.1262,
                    url: "http://www.google.co.uk",
                    color: e.next(),
                },
                {
                    title: "Peking",
                    latitude: 39.9056,
                    longitude: 116.3958,
                    color: e.next(),
                },
                {
                    title: "New Delhi",
                    latitude: 28.6353,
                    longitude: 77.225,
                    color: e.next(),
                },
                {
                    title: "Tokyo",
                    latitude: 35.6785,
                    longitude: 139.6823,
                    url: "http://www.google.co.jp",
                    color: e.next(),
                },
                {
                    title: "Ankara",
                    latitude: 39.9439,
                    longitude: 32.856,
                    color: e.next(),
                },
                {
                    title: "Buenos Aires",
                    latitude: -34.6118,
                    longitude: -58.4173,
                    color: e.next(),
                },
                {
                    title: "Brasilia",
                    latitude: -15.7801,
                    longitude: -47.9292,
                    color: e.next(),
                },
                {
                    title: "Ottawa",
                    latitude: 45.4235,
                    longitude: -75.6979,
                    color: e.next(),
                },
                {
                    title: "Washington",
                    latitude: 38.8921,
                    longitude: -77.0241,
                    color: e.next(),
                },
                {
                    title: "Kinshasa",
                    latitude: -4.3369,
                    longitude: 15.3271,
                    color: e.next(),
                },
                {
                    title: "Cairo",
                    latitude: 30.0571,
                    longitude: 31.2272,
                    color: e.next(),
                },
                {
                    title: "Pretoria",
                    latitude: -25.7463,
                    longitude: 28.1876,
                    color: e.next(),
                },
            ];
        });
    },
    showSwal: function (e) {
        if ("basic" == e)
            Swal.mixin({
                customClass: { confirmButton: "btn bg-gradient-info" },
            }).fire({ title: "Sweet!" });
        else if ("title-and-text" == e)
            Swal.mixin({
                customClass: {
                    confirmButton: "btn bg-gradient-success",
                    cancelButton: "btn bg-gradient-danger",
                },
            }).fire({
                title: "Sweet!",
                text: "Modal with a custom image.",
                imageUrl: "https://unsplash.it/400/200",
                imageWidth: 400,
                imageAlt: "Custom image",
            });
        else if ("success-message" == e)
            Swal.fire("Good job!", "You clicked the button!", "success");
        else if ("warning-message-and-confirmation" == e) {
            const t = Swal.mixin({
                customClass: {
                    confirmButton: "btn bg-gradient-success",
                    cancelButton: "btn bg-gradient-danger",
                },
                buttonsStyling: !1,
            });
            t.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                type: "warning",
                showCancelButton: !0,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: !0,
            }).then((e) => {
                e.value
                    ? t.fire(
                          "Deleted!",
                          "Your file has been deleted.",
                          "success"
                      )
                    : e.dismiss === Swal.DismissReason.cancel &&
                      t.fire(
                          "Cancelled",
                          "Your imaginary file is safe :)",
                          "error"
                      );
            });
        } else if ("warning-message-and-cancel" == e)
            Swal.mixin({
                customClass: {
                    confirmButton: "btn bg-gradient-success",
                    cancelButton: "btn bg-gradient-danger",
                },
                buttonsStyling: !1,
            })
                .fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: !0,
                    confirmButtonText: "Yes, delete it!",
                })
                .then((e) => {
                    e.isConfirmed &&
                        Swal.fire(
                            "Deleted!",
                            "Your file has been deleted.",
                            "success"
                        );
                });
        else if ("custom-html" == e)
            Swal.mixin({
                customClass: {
                    confirmButton: "btn bg-gradient-success",
                    cancelButton: "btn bg-gradient-danger",
                },
                buttonsStyling: !1,
            }).fire({
                title: "<strong>HTML <u>example</u></strong>",
                icon: "info",
                html: 'You can use <b>bold text</b>, <a href="//sweetalert2.github.io">links</a> and other HTML tags',
                showCloseButton: !0,
                showCancelButton: !0,
                focusConfirm: !1,
                confirmButtonText: '<i class="fa fa-thumbs-up"></i> Great!',
                confirmButtonAriaLabel: "Thumbs up, great!",
                cancelButtonText: '<i class="fa fa-thumbs-down"></i>',
                cancelButtonAriaLabel: "Thumbs down",
            });
        else if ("rtl-language" == e)
            Swal.mixin({
                customClass: {
                    confirmButton: "btn bg-gradient-success",
                    cancelButton: "btn bg-gradient-danger",
                },
                buttonsStyling: !1,
            }).fire({
                title: "هل تريد الاستمرار؟",
                icon: "question",
                iconHtml: "؟",
                confirmButtonText: "نعم",
                cancelButtonText: "لا",
                showCancelButton: !0,
                showCloseButton: !0,
            });
        else if ("auto-close" == e) {
            let e;
            Swal.fire({
                title: "Auto close alert!",
                html: "I will close in <b></b> milliseconds.",
                timer: 2e3,
                timerProgressBar: !0,
                didOpen: () => {
                    Swal.showLoading(),
                        (e = setInterval(() => {
                            var e = Swal.getHtmlContainer();
                            e &&
                                (e = e.querySelector("b")) &&
                                (e.textContent = Swal.getTimerLeft());
                        }, 100));
                },
                willClose: () => {
                    clearInterval(e);
                },
            }).then((e) => {
                e.dismiss, Swal.DismissReason.timer;
            });
        } else
            "input-field" == e &&
                Swal.mixin({
                    customClass: {
                        confirmButton: "btn bg-gradient-success",
                        cancelButton: "btn bg-gradient-danger",
                    },
                    buttonsStyling: !1,
                })
                    .fire({
                        title: "Submit your Github username",
                        input: "text",
                        inputAttributes: { autocapitalize: "off" },
                        showCancelButton: !0,
                        confirmButtonText: "Look up",
                        showLoaderOnConfirm: !0,
                        preConfirm: (e) =>
                            fetch("//api.github.com/users/" + e)
                                .then((e) => {
                                    if (e.ok) return e.json();
                                    throw new Error(e.statusText);
                                })
                                .catch((e) => {
                                    Swal.showValidationMessage(
                                        "Request failed: " + e
                                    );
                                }),
                        allowOutsideClick: () => !Swal.isLoading(),
                    })
                    .then((e) => {
                        e.isConfirmed &&
                            Swal.fire({
                                title: e.value.login + "'s avatar",
                                imageUrl: e.value.avatar_url,
                            });
                    });
    },
};

const initComponent = function () {
    initialComponents();
    initForSetting();
    sidenavbarToogleVariables();
    initSidebarToggle();
    navbarOnColorResided();
};

export {
    initComponent,
}
