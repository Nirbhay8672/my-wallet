const baseUrl = document.querySelector('meta[name="url"]').content;

let websiteRoutes = {
    datatable: `${baseUrl}/websites/datatable`,
    details : (website_id) => {
        return `${baseUrl}/websites/details/${website_id}`;
    },
};

export { websiteRoutes };
