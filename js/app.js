function carregarDados(endpoint, params, callback) {

    showModal();

    axios.get(`${config.apiUrl}/${endpoint}`, {
        headers: {
            'Authorization': `Bearer ${config.token}`
        },
        params: params
    })
        .then(response => {
            callback(response.data);
            hideModal();
        })
        .catch(error => {
            hideModal();
            callback(error.response.data);
            showAlert('danger', error.response.data, 0);
        });
}