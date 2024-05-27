<template>
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a
                :href="`${$page.props.url}/home`"
                class="logo d-flex align-items-center"
            >
                <img
                    :src="`${$page.props.url}/images/favicon.png`"
                    alt="logo"
                />
                <span class="d-none d-lg-block ms-2">My Wallet</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div>

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">
                <li class="nav-item d-block d-lg-none">
                    <a class="nav-link nav-icon search-bar-toggle" href="#">
                        <i class="bi bi-search"></i>
                    </a>
                </li>

                <li class="nav-item pe-3">
                    <a
                        class="nav-profile d-flex align-items-center pe-0"
                        :href="`${$page.props.url}/users/profile`"
                    >
                        <img
                            :src="
                                $page.props.auth.user.profile_path
                                    ? $page.props.auth.user.profile_path
                                    : '/images/user.png'
                            "
                            alt="Profile"
                            class="rounded-circle"
                        />
                        <span class="d-none d-md-block ps-2">{{
                            $page.props.auth.user.name
                        }}</span>
                    </a>
                </li>

                <li class="nav-item pe-3">
                    <a class="dropdown-item" href="/logout-auth"
                        ><i class="bi bi-box-arrow-right"></i
                    ></a>
                </li>
            </ul>
        </nav>
    </header>
</template>

<script setup>
    // import { toastAlert } from '../../helpers/alert';

    // document.addEventListener('contextmenu', function(event) {
    //     event.preventDefault();
    //     toastAlert({
    //         title: 'Sorry you cant open context menu.',
    //         icon: "error",
    //     });
    // });
</script>
