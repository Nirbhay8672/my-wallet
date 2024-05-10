const baseUrl = document.querySelector('meta[name="url"]').content;

let permissionRoutes = {
    getRolePermissions: `${baseUrl}/permissions/get-role-permissions`,
    updateRolePermission: `${baseUrl}/permissions/update-role-permissions`,
};

export { permissionRoutes };
