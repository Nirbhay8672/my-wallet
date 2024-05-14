<template>
    <nav class="navbar navbar-expand navbar-light navbar-bg fixed-top">
        <a
            class="d-none d-sm-block"
            :href="`${$page.props.url}/home`"
            style="text-decoration: none;width:260px;"
        >
            <img
                :src="`${$page.props.url}/images/favicon.png`"
                alt="logo"
                style="height: 30px; width: auto"
            />
            <strong class="align-middle ms-3 fs-5 text-dark">My Wallet</strong>
        </a>
        <a class="sidebar-toggle js-sidebar-toggle">
            <i class="hamburger align-self-center"></i>
        </a>
        <div class="navbar-collapse collapse">
            <ul class="navbar-nav navbar-align">
                <li class="nav-item dropdown">
                    <a
                        class="nav-icon dropdown-toggle d-inline-block d-sm-none"
                        href="#"
                        data-bs-toggle="dropdown"
                    >
                        <i class="align-middle" data-feather="settings"></i>
                    </a>

                    <a
                        class="nav-link d-none d-sm-inline-block"
                        href="#"
                        data-bs-toggle="dropdown"
                    >
                        <img
                            :src="
                                $page.props.auth.user.profile_path
                                    ? $page.props.auth.user.profile_path
                                    : '/images/user.png'
                            "
                            class="avatar img-fluid rounded me-1"
                            alt="profile image"
                        />
                        <span class="text-dark ms-2">{{
                            $page.props.auth.user.username
                        }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end mt-2">
                            <a
                                :href="`${$page.props.url}/users/profile`"
                                class="dropdown-item"
                            >
                            <i
                                class="align-middle me-1"
                                data-feather="user"
                            ></i>
                            Profile</a
                        >
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/logout-auth">Log out</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script setup></script>
