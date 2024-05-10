const baseUrl = document.querySelector('meta[name="url"]').content;

let paymentRoutes = {
    datatable: `${baseUrl}/payments/datatable`,
    createPayment: `${baseUrl}/payments/create`,
    generateInvoice: (payment_id) => {
        return `${baseUrl}/payments/generate-invoice/${payment_id}`;
    }
};

export { paymentRoutes };
