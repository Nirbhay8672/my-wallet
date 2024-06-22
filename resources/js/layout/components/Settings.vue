<template>
    <div class="fixed-plugin">
        <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
            <i class="material-icons py-2">settings</i>
        </a>
        <div class="card shadow-lg">
            <div class="card-header pb-0 pt-3">
                <div class="float-start">
                    <h5 class="mt-3 mb-0">UI Configurator</h5>
                    <p>See our dashboard options.</p>
                </div>
                <div class="float-end mt-4">
                    <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
                        <i class="material-icons">clear</i>
                    </button>
                </div>
            </div>
            <hr class="horizontal dark my-1">
            <div class="card-body pt-sm-3 pt-0">
                <div>
                    <h6 class="mb-0">Sidebar Colors</h6>
                </div>
                <a href="javascript:void(0)" class="switch-trigger background-color">
                    <div class="badge-colors my-2 text-start">
                        <span class="badge filter bg-gradient-primary active" data-color="primary"
                            @click="sidebarColor"></span>
                        <span class="badge filter bg-gradient-dark" data-color="dark"
                            @click="sidebarColor"></span>
                        <span class="badge filter bg-gradient-info" data-color="info"
                            @click="sidebarColor"></span>
                        <span class="badge filter bg-gradient-success" data-color="success"
                            @click="sidebarColor"></span>
                        <span class="badge filter bg-gradient-warning" data-color="warning"
                            @click="sidebarColor"></span>
                        <span class="badge filter bg-gradient-danger" data-color="danger"
                            @click="sidebarColor"></span>
                    </div>
                </a>
                <!-- Sidenav Type -->
                <div class="mt-3">
                    <h6 class="mb-0">Sidenav Type</h6>
                    <p class="text-sm">Choose between 2 different sidenav types.</p>
                </div>
                <div class="d-flex">
                    <button class="btn bg-gradient-dark px-3 mb-2 active" data-class="bg-gradient-dark"
                        @click="sidebarType">Dark</button>
                    <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent"
                        @click="sidebarType">Transparent</button>
                    <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-white"
                        @click="sidebarType">White</button>
                </div>
                <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
                <!-- Navbar Fixed -->
                <div class="mt-3 d-flex">
                    <h6 class="mb-0">Navbar Fixed</h6>
                    <div class="form-check form-switch ps-0 ms-auto my-auto">
                        <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed"
                            @click="navbarFixed">
                    </div>
                </div>
                <hr class="horizontal dark my-3">
                <div class="mt-2 d-flex">
                    <h6 class="mb-0">Light / Dark</h6>
                    <div class="form-check form-switch ps-0 ms-auto my-auto">
                        <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version"
                            @click="darkMode">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    function sidebarType(event) {

    }

    function navbarFixed(event) {

    }

    function darkMode(event) {

    }
</script>

<script setup>
    function sidebarColor (event) {

        for (
            var t,
                a = event.target.parentElement.children,
                n = event.target.getAttribute("data-color"),
                i = 0;
            i < a.length;
            i++
        )

        a[i].classList.remove("active");
        event.target.classList.contains("active") ? event.target.classList.remove("active") : event.target.classList.add("active"),
        document.querySelector(".sidenav").setAttribute("data-color", n),
        document.querySelector("#sidenavCard") &&
            ((e = [
                "card",
                "card-background",
                "shadow-none",
                "card-background-mask-" + n,
            ]),
            ((t = document.querySelector("#sidenavCard")).className = ""),
            t.classList.add(...event.target),
            (t = [
                "ni",
                "ni-diamond",
                "text-gradient",
                "text-lg",
                "top-0",
                "text-" + n,
            ]),
            ((e = document.querySelector("#sidenavCardIcon")).className = ""),
            event.target.classList.add(...t));

        const activeLink = document.getElementsByClassName('nav-link active');

        if(activeLink.length > 0) {
            document.getElementById(activeLink[0].id).classList.remove('bg-gradient-primary', 'bg-gradient-dark', 'bg-gradient-info', 'bg-gradient-success', 'bg-gradient-warning', 'bg-gradient-danger');
            document.getElementById(activeLink[0].id).classList.add(`bg-gradient-${event.target.getAttribute("data-color")}`);
        }
    }
</script>