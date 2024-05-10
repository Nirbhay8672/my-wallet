const baseUrl = document.querySelector('meta[name="url"]').content;

let clientRoutes = {
    datatable: `${baseUrl}/clients/datatable`,
    payments : `${baseUrl}/clients/payments`,
    delete : (client_id) => {
        return `${baseUrl}/clients/delete/${client_id}`;
    },
    createOrUpdateClient : (client_id) => {
        if(client_id) {
            return `${baseUrl}/clients/create-or-update/${client_id}`;
        }
        return `${baseUrl}/clients/create-or-update`;
    }
};

export { clientRoutes };
