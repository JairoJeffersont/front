function carregarDadosAPI(endpoint, params, callback) {

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
            console.log(error);

            if (error.code == 'ERR_NETWORK') {
                showAlert('danger', 'API Offline', 0);
            } else {
                showAlert('danger', error.response.data.message, 0);
                callback(error.response.data);
            }
        });
}